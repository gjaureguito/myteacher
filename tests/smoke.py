"""Local integration checks. Owns a disposable SQLite database and PHP server."""
from pathlib import Path
import http.cookiejar, json, os, re, socket, subprocess, tempfile, time, urllib.request, urllib.parse, urllib.error
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
        assert status==200 and 'Abrí tu mundo.' in home
        assert home.count('class="track-row"')==6
        _,programme,_=request('/program.php')
        assert programme.count('class="unit-index"')==29
        _,library,_=request('/library.php')
        assert library.count('class="library-card"')==5
        assert request('/reading.php?slug=a1-verb-to-be')[0]==404
        assert request('/reading.php?slug=a1-present-simple')[0]==404
        assert request('/reading.php?slug=../../src/bootstrap')[0]==404
        _,sample,_=request('/reading.php?slug=a2-past-simple')
        assert 'The day the plan changed' in sample and 'name="robots"' not in sample
        assert 'reading.php?slug=a2-past-simple' in sample
        assert 'class="writing-draft"' in sample and sample.count('data-reading-question')==2
        assert '<strong>29</strong><span>Unidades publicadas</span>' in home
        assert '<strong>23</strong><span>Talleres de aplicación</span>' in home
        assert '<strong>5</strong><span>Lecturas abiertas</span>' in home
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
        _,inventory,_=request('/admin/learning.php')
        assert inventory.count('preguntas + explicación')==24
        lab_slugs=sorted(p.stem for p in (ROOT/'content/labs').glob('*.json'))
        out=subprocess.run(['php','-r','require "src/bootstrap.php"; echo json_encode(array_column(q_all("SELECT slug, id FROM units WHERE is_published=1"), "id", "slug"));'],cwd=ROOT,env=env,capture_output=True,text=True,check=True).stdout
        ids=json.loads(out)
        with_lab=[s for s in lab_slugs if s in ids]
        assert len(with_lab)==23 and len(ids)==29
        for slug in with_lab:
            status,unit,_=request(f'/unit.php?id={ids[slug]}')
            assert status==200 and 'id="t-workshop"' in unit
            assert 'data-reading-question' in unit
        for slug,uid in ids.items():
            status,unit,_=request(f'/unit.php?id={uid}')
            assert status==200 and 'class="passage-card"' in unit and 'data-type="write"' in unit and 'data-type="speak"' in unit, slug
        status,stats,_=request('/stats.php')
        assert status==200 and 'Listening' in stats and 'Writing' in stats and 'Speaking' in stats
        assert request('/review.php')[0]==200
        print('PASS: 23 workshops render; all 29 published units have reading, writing and speaking; stats and review pages load')
        status,xml,_=request('/sitemap.php')
        import xml.etree.ElementTree as ET
        assert status==200 and len(ET.fromstring(xml))==10
        assert request('/robots.txt')[0]==200
        print('PASS: sitemap XML and robots response')
        assert request('/admin/',{'action':'grant','user_id':2,'days':30})[0]==419
        print('PASS: privileged mutation rejects missing CSRF; no payment requests executed')
    finally:
        process.terminate(); process.wait(timeout=5)
