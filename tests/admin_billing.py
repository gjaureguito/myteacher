"""Admin-only billing dashboard checks with disposable data."""
from pathlib import Path
import os,subprocess,tempfile,socket,time,urllib.request,urllib.parse,urllib.error,http.cookiejar,re
ROOT=Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='admin-billing-') as tmp:
    env=dict(os.environ,DB_DRIVER='sqlite',SQLITE_PATH=str(Path(tmp)/'test.sqlite'),ADMIN_EMAILS='')
    fixture=r"""<?php
require 'src/bootstrap.php';
create_user('admin@example.test','Admin','Local-test-123',['role'=>'admin']);
$id=create_user('student@example.test','Student','Local-test-123',['sub_status'=>'active','access_until'=>'2099-01-01 00:00:00']);
create_user('legacy@example.test','Legacy','Local-test-123',['sub_status'=>'pending']);
function declare_billing($id,$nonce,$name,$doc,$address) {
 q('INSERT INTO purchase_agreements (user_id,nonce,version,document_hash,document_text,amount,currency,kind,days,accepted_at) VALUES (?,?,?,?,?,?,?,?,?,?)',[$id,$nonce,'v1','hash','Test',30000,'ARS','subscription',30,now_utc()]);
 q('INSERT INTO purchase_billing (nonce,user_id,billing_data,created_at) VALUES (?,?,?,?)',[$nonce,$id,json_encode(['condition'=>'monotributo','name'=>$name,'document_type'=>'CUIT','document'=>$doc,'address'=>$address]),now_utc()]);
}
declare_billing($id,'old','OLD NAME','20111111112','OLD ADDRESS');
declare_billing($id,'new','Example Company','20123456786','<script>alert(1)</script> Calle 123');
for($i=0;$i<26;$i++) create_user('pending'.$i.'@example.test','Pending '.$i,'Local-test-123',['sub_status'=>'pending']);
"""
    result=subprocess.run(['php'],input=fixture,text=True,encoding='utf-8',cwd=ROOT,env=env,capture_output=True)
    assert result.returncode==0,result.stdout+result.stderr
    with socket.socket() as sock: sock.bind(('127.0.0.1',0));port=sock.getsockname()[1]
    env['APP_URL']=base=f'http://127.0.0.1:{port}'
    server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    def client(): return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(c,path,data=None):
        try:r=c.open(base+path,None if data is None else urllib.parse.urlencode(data).encode(),timeout=10)
        except urllib.error.HTTPError as err:r=err
        return r.status,r.read().decode(),r.url,r.headers
    def login(c,email):
        _,html,_,_=request(c,'/login.php');token=re.search(r'name="csrf" value="([a-f0-9]+)"',html)[1]
        assert request(c,'/login.php',dict(csrf=token,email=email,password='Local-test-123'))[0]==200
    try:
        for _ in range(40):
            try:request(client(),'/login.php');break
            except OSError:time.sleep(.1)
        c=client();assert '/login.php' in request(c,'/admin/billing.php')[2]
        c=client();login(c,'student@example.test');assert request(c,'/admin/billing.php')[0]==403
        c=client();login(c,'admin@example.test')
        status,html,_,headers=request(c,'/admin/?s=20123456786')
        assert status==200 and 'Example Company' in html and 'OLD NAME' not in html
        assert 'private, no-store' in headers['Cache-Control']
        status,html,_,_=request(c,'/admin/billing.php?s=20-12345678-6')
        assert status==200 and 'Example Company' in html and 'student@example.test' in html
        assert '&lt;script&gt;' in html and '<script>alert(1)</script>' not in html
        assert 'OLD ADDRESS' not in html and 'ARS 30.000,00' in html
        assert 'Responsable' not in html and 'Monotributista' in html
        _,html,_,_=request(c,'/admin/billing.php?status=active')
        assert 'Example Company' in html and 'legacy@example.test' not in html
        _,html,_,_=request(c,'/admin/billing.php?status=missing&page=2')
        assert 'legacy@example.test' in html and 'Sin datos fiscales declarados' in html and 'Example Company' not in html
        assert 'No encontramos registros' in request(c,'/admin/billing.php?s=nonexistent')[1]
        assert request(c,'/admin/billing.php?s[]=bad&status[]=bad&page[]=bad')[0]==200
        print('PASS: admin authorization, latest snapshot, document search, fiscal fields, XSS escaping, private cache, status filters, pagination, missing data and malformed parameters')
    finally:server.terminate();server.wait(timeout=5)
