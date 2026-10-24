"""End-to-end multimedia grading, migration, access and persistence checks."""
from pathlib import Path
import os, json, subprocess, tempfile, socket, time, urllib.request, urllib.parse, urllib.error, http.cookiejar, re, sqlite3
ROOT = Path(__file__).resolve().parents[1]
scenes = json.loads((ROOT/'content/scenes.json').read_text(encoding='utf-8'))
assert len(scenes)==10 and {s['level'] for s in scenes}=={'A1','A2'}
for scene in scenes:
    assert len(scene['questions'])==5 and len(scene['lines'])==4
    assert (ROOT/f"public/assets/images/scene-{scene['id']}.jpg").stat().st_size>10000
with tempfile.TemporaryDirectory() as td:
    database=Path(td)/'test.sqlite'
    env=dict(os.environ,DB_DRIVER='sqlite',SQLITE_PATH=str(database),ADMIN_EMAILS='')
    fixture="""<?php require 'src/bootstrap.php';
create_user('one@example.test','One','Local-test-123',['role'=>'admin']);
create_user('two@example.test','Two','Local-test-123',[]);
q("UPDATE meta SET v='3' WHERE k='schema_version'");
"""
    subprocess.run(['php'],input=fixture,text=True,cwd=ROOT,env=env,check=True,capture_output=True)
    with socket.socket() as sock: sock.bind(('127.0.0.1',0)); port=sock.getsockname()[1]
    base=f'http://127.0.0.1:{port}';env['APP_URL']=base
    server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(path,data=None,token=None):
        headers={}
        if token is not None:
            data=json.dumps(data).encode();headers={'Content-Type':'application/json','X-CSRF-Token':token}
        elif data is not None: data=urllib.parse.urlencode(data).encode()
        try:r=client.open(urllib.request.Request(base+path,data=data,headers=headers),timeout=10)
        except urllib.error.HTTPError as e:r=e
        return r.status,r.read().decode(errors="replace")
    def login(email):
        _,html=request('/login.php'); token=re.search(r'name="csrf" value="([a-f0-9]+)"',html)[1]
        status,html=request('/login.php',{'csrf':token,'email':email,'password':'Local-test-123'})
        assert status==200
        return re.search(r'<meta name="csrf" content="([a-f0-9]+)"',html)[1]
    def answer(scene,index,value,token):
        return request('/api/scene.php',{'scene':scene,'question':index,'answer':value},token)
    try:
        for _ in range(40):
            try:request('/');break
            except OSError:time.sleep(.1)
        assert request('/api/scene.php')[0]==405
        assert answer('cafe',0,1,'')[0]==401
        token=login('one@example.test')
        assert answer('cafe',0,1,'')[0]==419
        assert answer('missing',0,1,token)[0]==404
        assert answer('cafe',99,1,token)[0]==404
        db=sqlite3.connect(database)
        assert db.execute("SELECT v FROM meta WHERE k='schema_version'").fetchone()[0]=='4'
        for scene in scenes:
            unit_id=db.execute('SELECT id FROM units WHERE slug=?',(scene['unit'],)).fetchone()[0]
            status,html=request(f'/unit.php?id={unit_id}')
            assert status==200 and f'data-scene="{scene["id"]}"' in html
            assert 'data-es hidden' in html and 'data-scene-play' in html
            assert 'scene-reference' in html and 'scene-workspace' in html
            assert 'data-edge-shortcut' in html and 'edge-access.js' in html
            assert request(f'/assets/images/scene-{scene["id"]}.jpg')[0]==200
            for i,q in enumerate(scene['questions']):
                status,body=answer(scene['id'],i,-99 if q['type']=='mcq' else 'wrong',token)
                d=json.loads(body);assert status==200 and not d['correct'] and d['answer'] is None
                status,body=answer(scene['id'],i,q['answer'],token)
                d=json.loads(body);assert status==200 and d['correct'] and d['solved']==i+1, (scene['id'],i,status,d)
            _,body=answer(scene['id'],0,scene['questions'][0]['answer'],token)
            assert json.loads(body)['score']==100
            assert '5/5 · 100/100 puntos' in request(f'/unit.php?id={unit_id}')[1]
        print('PASS: 10 photographs, 50 exercises, wrong/correct answers, retry scoring, reload persistence, migration 3 to 4')
        request('/logout.php',{'csrf':token})
        token=login('two@example.test')
        assert answer('shopping',0,1,token)[0]==403
        status,body=answer('cafe',0,0,token)
        assert status==200 and json.loads(body)['score']==0
        assert db.execute('SELECT COUNT(*) FROM scene_status WHERE user_id=1 AND solved=1').fetchone()[0]==len(scenes)*5
        db.execute("UPDATE units SET is_published=0 WHERE slug='a1-verb-to-be'");db.commit()
        assert answer('cafe',0,1,token)[0]==404
        print('PASS: authentication, CSRF, subscription, unpublished lessons, user isolation; existing accounts retained')
        db.close()
    finally:
        if "db" in locals(): db.close()
        server.terminate();server.wait(timeout=5)
