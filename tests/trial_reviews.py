"""Isolated integration checks for trial expiry, account linking and moderated consent."""
from pathlib import Path
import http.cookiejar, os, re, socket, sqlite3, subprocess, tempfile, time
import urllib.request, urllib.parse, urllib.error
ROOT=Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='myteacher-promotion-') as tmp:
    db=Path(tmp)/'test.sqlite'
    env=dict(os.environ,DB_DRIVER='sqlite',SQLITE_PATH=str(db),ADMIN_EMAILS='')
    def php(code):
        r=subprocess.run(['php'],input="<?php require 'src/bootstrap.php';\n"+code,text=True,encoding='utf-8',cwd=ROOT,env=env,capture_output=True)
        assert r.returncode==0, r.stdout+r.stderr
        return r.stdout
    php(r'''
function check($ok) {if (!$ok) throw new RuntimeException('Assertion failed');}
check(trial_end('2026-01-31 12:30:00')==='2026-02-28 12:30:00');
check(trial_end('2028-01-31 12:30:00')==='2028-02-29 12:30:00');
check(trial_end('2026-12-15 12:30:00')==='2027-01-15 12:30:00');
$id=create_user('student@example.test','Lucía ApellidoPrivado','Local-test-123');
check(testimonial_first_name('Lucía ApellidoPrivado')==='Lucía');
check(testimonial_first_name("Lucía\u{00a0}ApellidoPrivado")==='Lucía');
$u=q_one('SELECT * FROM users WHERE id=?',[$id]); check(has_full_access($u));
check(oauth_login('google','test-sub','student@example.test','Student')===$id);
check(q_val('SELECT trial_ends_at FROM users WHERE id=?',[$id])===$u['trial_ends_at']);
$new=oauth_login('microsoft','ms-sub','new@example.test','New');
check(trial_active(q_one('SELECT * FROM users WHERE id=?',[$new])));
q("UPDATE users SET trial_ends_at=? WHERE id=?",[now_utc(),$id]);
check(!has_full_access(q_one('SELECT * FROM users WHERE id=?',[$id])));
q("UPDATE users SET access_until='2099-01-01 00:00:00',sub_status='paid' WHERE id=?",[$id]);
check(has_full_access(q_one('SELECT * FROM users WHERE id=?',[$id])));
q("UPDATE users SET access_until=NULL,sub_status='none' WHERE id=?",[$id]);
create_user('admin@example.test','Admin','Local-test-123',['role'=>'admin']);
create_user('legacy@example.test','Legacy','Local-test-123',['sub_status'=>'none']);
check(!trial_active(q_one("SELECT * FROM users WHERE email='legacy@example.test'")));
meta_set('schema_version','6');
''')
    php("db_migrate_if_needed(); if(q_val(\"SELECT trial_ends_at FROM users WHERE email='legacy@example.test'\")!==null) throw new Exception('Legacy changed');")
    with socket.socket() as s: s.bind(('127.0.0.1',0)); port=s.getsockname()[1]
    base=f'http://127.0.0.1:{port}'; env['APP_URL']=base
    process=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    def client(): return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    student,admin,guest=client(),client(),client()
    def req(c,path,data=None):
        try: r=c.open(base+path,data=None if data is None else urllib.parse.urlencode(data).encode(),timeout=10)
        except urllib.error.HTTPError as e: r=e
        return r.status,r.read().decode()
    def token(h): return re.search(r'name="csrf" value="([a-f0-9]+)"',h)[1]
    def login(c,email):
        _,h=req(c,'/login.php'); code,h=req(c,'/login.php',dict(csrf=token(h),email=email,password='Local-test-123')); assert code==200
    try:
        for _ in range(60):
            try: code,home=req(guest,'/'); break
            except OSError: time.sleep(.1)
        else: raise RuntimeError('Local PHP server unavailable')
        assert code==200 and 'Tu primer mes gratis' in home
        login(student,'student@example.test'); login(admin,'admin@example.test')
        assert req(student,'/admin/promotion.php')[0]==403
        assert req(student,'/opinion.php',dict(body='Bad CSRF'))[0]==419
        _,h=req(student,'/opinion.php'); csrf=token(h)
        body='Aprendí a comprender diálogos y practicar cada día. <script>alert(1)</script>'
        code,h=req(student,'/opinion.php',dict(csrf=csrf,display_name='Alumno',body=body)); assert 'autorizá' in h
        code,h=req(student,'/opinion.php',dict(csrf=csrf,display_name='Alumno',body=body,consent=1)); assert code==200 and 'Pendiente de revisión' in h
        assert 'Aprendí a comprender' not in req(guest,'/')[1]
        _,h=req(admin,'/admin/promotion.php'); assert 'Pruebas iniciadas' in h
        review_hash=re.search(r'name="review_hash" value="([a-f0-9]+)"',h)[1]
        code,h=req(admin,'/admin/promotion.php',dict(csrf=token(h),user_id=1,review_hash=review_hash,action='approve')); assert code==200
        home=req(guest,'/')[1]; assert 'Aprendí a comprender' in home and '&lt;script&gt;' in home and 'student@example.test' not in home
        assert 'Lucía' in home and 'ApellidoPrivado' not in home and '<strong>Alumno</strong>' not in home
        # Editing immediately unpublishes; an old admin review cannot approve the new content.
        _,h=req(student,'/opinion.php'); req(student,'/opinion.php',dict(csrf=token(h),display_name='Alumno',body=body+' Nueva versión.',consent=1))
        assert 'Aprendí a comprender' not in req(guest,'/')[1]
        _,h=req(admin,'/admin/promotion.php'); _,h=req(admin,'/admin/promotion.php',dict(csrf=token(h),user_id=1,review_hash=review_hash,action='approve')); assert 'cambió' in h
        _,h=req(student,'/opinion.php'); req(student,'/opinion.php',dict(csrf=token(h),action='withdraw'))
        checkdb=sqlite3.connect(db)
        assert checkdb.execute('SELECT COUNT(*) FROM testimonials').fetchone()[0]==0
        checkdb.close()
        # Public registration gets trial; premium unit unlocks then locks at expiry without deleting progress.
        c=client(); _,h=req(c,'/register.php')
        code,h=req(c,'/register.php',dict(csrf=token(h),name='Trial User',email='trial@example.test',password='Local-test-123',terms=1)); assert code==200
        con=sqlite3.connect(db); uid=con.execute("SELECT id FROM users WHERE email='trial@example.test'").fetchone()[0]
        unit=con.execute('SELECT id FROM units WHERE position>1 AND is_published=1 LIMIT 1').fetchone()[0]
        code,h=req(c,f'/unit.php?id={unit}'); assert code==200 and 'role="tablist"' in h
        con.execute("UPDATE users SET trial_ends_at='2000-01-01 00:00:00' WHERE id=?",(uid,)); con.commit()
        code,h=req(c,f'/unit.php?id={unit}'); assert 'role="tablist"' not in h
        assert 'Tu mes gratis terminó' in req(c,'/account.php')[1]
        con.close()
        print('PASS: calendar month, expiry, paid access, OAuth linking, legacy migration, registration, protected lessons, consent, moderation, XSS escaping, edit invalidation, withdrawal and admin access')
    finally:
        process.terminate(); process.wait(timeout=10)
