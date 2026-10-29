"""Exercise award rules, PDF downloads, ownership and route rendering on disposable SQLite."""
from pathlib import Path
import os, subprocess, tempfile, socket, time, urllib.request, urllib.parse, urllib.error, http.cookiejar, re

ROOT = Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='myteacher-certificates-') as tmp:
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=str(Path(tmp)/'db.sqlite'), ADMIN_EMAILS='')
    def php(code):
        result = subprocess.run(['php'], input='<?php\nrequire "src/bootstrap.php";\n'+code, text=True, encoding='utf-8', cwd=ROOT, env=env, capture_output=True)
        if result.returncode: raise AssertionError(result.stdout + result.stderr)
        return result.stdout
    php('''
function check($ok) { if (!$ok) throw new Exception('Award assertion failed'); }
$uid=create_user('learner@example.test','María José Pérez','Local-test-123');
$u=q_one('SELECT * FROM users WHERE id=?',[$uid]);
create_user('other@example.test','Other','Local-test-123');
award_completed_levels($u); check((int)q_val('SELECT COUNT(*) FROM certificates')===0);
$ids=q_all("SELECT e.id FROM exercises e JOIN units u ON u.id=e.unit_id WHERE u.level_code='a1' AND u.is_published=1");
foreach ($ids as $ex) q('INSERT INTO exercise_status (user_id,exercise_id,solved,tries,updated_at) VALUES (?,?,?,?,?)',[$u['id'],$ex['id'],1,1,now_utc()]);
$last=end($ids)['id'];
q('UPDATE exercise_status SET solved=0 WHERE exercise_id=?',[$last]);
award_completed_levels($u); check((int)q_val('SELECT COUNT(*) FROM certificates')===0);
q('UPDATE exercise_status SET solved=1 WHERE exercise_id=?',[$last]);
q("INSERT INTO units (level_code,slug,position,title,summary,theory_html,vocab_json,is_published,updated_at) VALUES ('a1','empty-check',99,'Empty','','','[]',1,?)",[now_utc()]);
award_completed_levels($u); check((int)q_val('SELECT COUNT(*) FROM certificates')===0);
q("UPDATE units SET is_published=0 WHERE slug='empty-check'");
award_completed_levels($u); award_completed_levels($u);
check((int)q_val('SELECT COUNT(*) FROM certificates')===1);
$before=q_one('SELECT * FROM certificates');
q('UPDATE exercise_status SET solved=0 WHERE user_id=?',[$u['id']]);
q('UPDATE users SET name=? WHERE id=?',['Changed',$u['id']]);
award_completed_levels($u); check(q_one('SELECT * FROM certificates')===$before);
check(count(json_decode($before['concepts_json'],true))>0);
''')
    print('PASS: incomplete and empty-unit rejection; exact completion; idempotent durable snapshot')
    with socket.socket() as sock:
        sock.bind(('127.0.0.1',0)); port=sock.getsockname()[1]
    env['APP_URL']=base=f'http://127.0.0.1:{port}'
    server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    def client(): return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def get(c,path,data=None):
        try: return c.open(base+path,None if data is None else urllib.parse.urlencode(data).encode(),timeout=5)
        except urllib.error.HTTPError as e: return e
    def login(c,email):
        html=get(c,'/login.php').read().decode()
        csrf=re.search(r'name="csrf" value="([a-f0-9]+)"',html)[1]
        assert get(c,'/login.php',dict(csrf=csrf,email=email,password='Local-test-123')).status==200
    try:
        for _ in range(40):
            try: get(client(),'/login.php'); break
            except OSError: time.sleep(.1)
        anon=client()
        assert '/login.php' in get(anon,'/certificate.php?level=a1').url
        c=client(); login(c,'learner@example.test')
        page=get(c,'/learning-path.php').read().decode()
        assert 'María José Pérez' in page and 'Descargar certificado PDF' in page and 'Por aprender' in page
        pdf=get(c,'/certificate.php?level=a1'); data=pdf.read()
        assert pdf.headers['Content-Type']=='application/pdf' and 'attachment' in pdf.headers['Content-Disposition']
        assert data.startswith(b'%PDF-1.4') and data.endswith(b'%%EOF\n')
        assert b'Mar\xeda Jos\xe9 P\xe9rez' in data
        assert get(c,'/certificate.php?level=a2').status==404
        other=client(); login(other,'other@example.test')
        assert get(other,'/certificate.php?level=a1&user_id=1').status==404
        assert 'Descargar certificado PDF' not in get(other,'/learning-path.php').read().decode()
        assert get(c,'/certificate.php?level[]=a1').status==404
        print('PASS: authenticated PDF download, Spanish accents, cross-user isolation, malformed level, route rendering')
        # Optional rendering tool in the bundled runtime; keeps dependencies out of the app.
        try:
            import fitz
            doc=fitz.open(stream=data,filetype='pdf')
            assert len(doc)==1 and 'María José Pérez' in doc[0].get_text()
            output=ROOT/'storage'/'certificate-preview.png'
            doc[0].get_pixmap().save(str(output))
            print('PASS: PDF parsed and rendered to',output)
        except ImportError: pass
    finally:
        server.terminate(); server.wait(timeout=5)
