"""Local integration checks. Owns a disposable SQLite database and PHP server."""
from pathlib import Path
import http.cookiejar, os, re, socket, subprocess, tempfile, time, urllib.request, urllib.parse, urllib.error
ROOT = Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='inglespro-test-') as tmp:
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=str(Path(tmp)/'test.sqlite'), ADMIN_EMAILS='')
    fixture = """<?php
require 'src/bootstrap.php';
create_user('admin@example.test','Test Admin','Local-test-123',['role'=>'admin']);
create_user('manual@example.test','Manual','Local-test-123',['sub_status'=>'manual','access_until'=>'2099-01-01 00:00:00']);
create_user('active@example.test','Active','Local-test-123',['sub_status'=>'active','access_until'=>'2099-01-01 00:00:00']);
create_user('expired@example.test','Expired','Local-test-123',['sub_status'=>'active','access_until'=>'2000-01-01 00:00:00']);
q("UPDATE units SET is_published=0 WHERE id=(SELECT MIN(id) FROM units)");
"""
    subprocess.run(['php'], input=fixture, text=True, cwd=ROOT, env=env, check=True, capture_output=True)
    with socket.socket() as s:
        s.bind(('127.0.0.1',0)); port=s.getsockname()[1]
    base=f'http://127.0.0.1:{port}'
    env['APP_URL']=base
    process=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    jar=http.cookiejar.CookieJar(); client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    def request(path, data=None):
        try:
            r=client.open(base+path, data=None if data is None else urllib.parse.urlencode(data).encode(),timeout=5)
        except urllib.error.HTTPError as e: r=e
        return r.status,r.read().decode(),r.geturl()
    def token(html): return re.search(r'name="csrf" value="([a-f0-9]+)"',html)[1]
    try:
        for _ in range(40):
            try: status,home,_=request('/'); break
            except OSError: time.sleep(.1)
        else: raise AssertionError('Local PHP server did not start')
        assert status==200 and 'Entendé el inglés' in home
        assert home.count('class="program-card"')==6 and home.count('<li><strong>')==23
        assert '<strong>23</strong> unidades publicadas' in home
        assert 'name="robots"' not in home and 'rel="canonical"' in home
        print('PASS: public syllabus and counts exclude unpublished units; canonical metadata')
        status,form,_=request('/register.php')
        assert status==200 and 'novalidate' not in form and 'name="terms" value="1" required' in form and 'noindex' in form
        assert request('/register.php',{'name':'Invalid'})[0]==419
        print('PASS: registration constraints and CSRF rejection')
        assert request('/admin/')[2].endswith('/login.php')
        _,login,_=request('/login.php')
        status,html,url=request('/login.php',{'csrf':token(login),'email':'admin@example.test','password':'Local-test-123'})
        assert status==200 and '/login.php' not in url
        _,admin,_=request('/admin/')
        assert re.search(r'<strong>2</strong>.*?Con acceso vigente',admin,re.S)
        assert re.search(r'<strong>1</strong>.*?Suscripciones activas',admin,re.S)
        assert '$30.000' in admin
        assert 'Hasta 100 usuarios' in admin
        assert 'No encontramos usuarios' in request('/admin/?s=missing@example.test')[1]
        print('PASS: admin login, subscription metrics exclude manual/expired users, empty search')
        status,xml,_=request('/sitemap.php')
        import xml.etree.ElementTree as ET
        assert status==200 and len(ET.fromstring(xml))==3
        assert request('/robots.txt')[0]==200
        print('PASS: sitemap XML and robots response')
        assert request('/admin/',{'action':'grant','user_id':2,'days':30})[0]==419
        print('PASS: privileged mutation rejects missing CSRF; no payment requests executed')
    finally:
        process.terminate(); process.wait(timeout=5)
