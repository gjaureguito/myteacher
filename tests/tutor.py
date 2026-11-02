"""Pilot policy checks; no live API calls or credentials needed."""
from pathlib import Path
import concurrent.futures, os, subprocess, tempfile, sqlite3
import http.cookiejar, json, re, socket, time, urllib.request, urllib.parse, urllib.error
from contextlib import closing

ROOT = Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory() as td:
    database = str(Path(td)/'tutor.sqlite')
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=database, TUTOR_ENABLED='0')
    def php(code,runtime_env=env):
        r=subprocess.run(['php'],input="<?php require 'src/bootstrap.php'; "+code,cwd=ROOT,env=runtime_env,text=True,encoding='utf-8',capture_output=True)
        assert r.returncode==0, r.stderr+r.stdout
        return r.stdout.strip()
    php("q('SELECT 1');")
    # Reconstruct version 8: USD price and payments, no ARS price/currency columns.
    legacy_db=str(Path(td)/'legacy.sqlite')
    legacy_env=dict(env,SQLITE_PATH=legacy_db)
    php("q('SELECT 1');",legacy_env)
    with closing(sqlite3.connect(legacy_db)) as d, d:
        d.execute('ALTER TABLE tutor_budget DROP COLUMN price_ars_cents')
        d.execute('ALTER TABLE tutor_payments DROP COLUMN currency')
        d.execute('UPDATE tutor_budget SET reserved_cents=317,price_cents=50')
        d.execute("INSERT INTO tutor_payments(user_id,reference,amount_cents,seconds,status,evidence,created_by,updated_by,created_at,updated_at) VALUES(77,'legacy-usd-123',50,300,'verified_manual','Historical fixture',1,1,'2026-01-01','2026-01-01')")
        d.execute("UPDATE meta SET v='8' WHERE k='schema_version'")
    migrated=json.loads(php("echo json_encode([tutor_settings(),q_one('SELECT * FROM tutor_payments')]);",legacy_env))
    assert migrated[0]['price_ars_cents'] is None and migrated[0]['price_cents']==50
    assert migrated[0]['reserved_cents']==317
    assert migrated[1]['currency']=='USD' and migrated[1]['amount_cents']==50
    assert php("echo tutor_registered_seconds(77);",legacy_env)=='300'
    print('PASS: version 8 migration preserves USD history and reserves; ARS final price stays unset')
    with closing(sqlite3.connect(database)) as d, d:
        d.executemany('INSERT INTO tutor_members (user_id,created_at) VALUES (?,?)',[(i,'2026-01-01') for i in range(1,35)])
    assert php("echo tutor_enabled()?'on':'off';")=='off'
    assert php("putenv('TUTOR_ENABLED=1');putenv('OPENAI_API_KEY=test-only');echo tutor_enabled()?'on':'off';")=='off'
    php("q('UPDATE tutor_budget SET pilot_open=1');")
    assert php("try{tutor_reserve(100,'unregistered-key');}catch(RuntimeException $e){echo 'blocked';}")=='blocked'
    assert php("echo tutor_reserve(1,'same-request-key')['cached']?'cached':'new';")=='new'
    assert php("echo tutor_reserve(1,'same-request-key')['cached']?'cached':'new';")=='cached'
    assert php("try{tutor_reserve(1,'new-request-key');}catch(RuntimeException $e){echo 'blocked';}")=='blocked'
    with closing(sqlite3.connect(database)) as d, d:
        assert d.execute('SELECT reserved_cents FROM tutor_budget').fetchone()[0]==1
        for i in range(5):d.execute("INSERT INTO tutor_calls(user_id,request_key,status,created_at) VALUES(2,?,'uncertain',datetime('now','-2 minutes'))",(f'daily-{i}',))
        for i in range(25):d.execute("INSERT INTO tutor_calls(user_id,request_key,status,created_at) VALUES(3,?,'uncertain','2025-01-01')",(f'total-{i}',))
    for uid in [2,3]:
        assert php(f"try{{tutor_reserve({uid},'quota-request-key');}}catch(RuntimeException $e){{echo 'blocked';}}")=='blocked'
    with closing(sqlite3.connect(database)) as d, d:d.execute('UPDATE tutor_budget SET reserved_cents=499')
    def reserve(uid):
        return php(f"try{{tutor_reserve({uid},'parallel-request-key');echo 'ok';}}catch(Throwable $e){{echo 'blocked';}}")
    with concurrent.futures.ThreadPoolExecutor(max_workers=12) as pool:
        results=list(pool.map(reserve,range(10,30)))
    assert results.count('ok')==1,results
    with closing(sqlite3.connect(database)) as d, d:
        assert d.execute('SELECT reserved_cents FROM tutor_budget').fetchone()[0]==500
    assert reserve(30)=='blocked'
    payload=php("echo json_encode(tutor_payload(1,str_repeat('a',2000)));")
    assert 'gpt-6-luna' in payload and 'max_output_tokens' in payload
    php("create_user('pilot@example.test','Pilot','Local-test-123',[]);")
    with socket.socket() as s:
        s.bind(('127.0.0.1',0));port=s.getsockname()[1]
    base=f'http://127.0.0.1:{port}'
    server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=dict(env,APP_URL=base),stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
    client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(path,data=None,csrf=None):
        headers={}
        if csrf is not None:
            data=json.dumps(data).encode();headers={'Content-Type':'application/json','X-CSRF-Token':csrf}
        elif data is not None:data=urllib.parse.urlencode(data).encode()
        try:r=client.open(urllib.request.Request(base+path,data=data,headers=headers),timeout=10)
        except urllib.error.HTTPError as e:r=e
        return r.status,r.read().decode()
    try:
        for _ in range(40):
            try:request('/login.php');break
            except OSError:time.sleep(.1)
        assert request('/api/profesor.php',{'action':'enroll'},'')[0]==401
        _,page=request('/login.php')
        token=re.search(r'<meta name="csrf" content="([a-f0-9]+)"',page)[1]
        request('/login.php',{'csrf':token,'email':'pilot@example.test','password':'Local-test-123'})
        status,page=request('/profesor.php');assert status==200 and 'tutor-enroll' in page
        token=re.search(r'<meta name="csrf" content="([a-f0-9]+)"',page)[1]
        assert request('/api/profesor.php',{'action':'enroll'},'invalid')[0]==419
        for _ in range(2):assert request('/api/profesor.php',{'action':'enroll'},token)[0]==200
        for _ in range(2):assert request('/api/profesor.php',{'action':'recharge_interest'},token)[0]==200
        assert php("echo q_val('SELECT COUNT(*) FROM tutor_members WHERE recharge_requested_at IS NOT NULL');")=='1'
        assert request('/api/profesor.php',{'action':'turn'},token)[0]==503
        assert request('/admin/tutor.php')[0]==403
        assert request('/admin/tutor.php',{'csrf':token,'action':'settings'})[0]==403
        php("q(\"UPDATE users SET role='admin' WHERE email='pilot@example.test'\");")
        assert request('/admin/tutor.php')[0]==200
        assert request('/admin/tutor.php',{'csrf':'wrong','action':'settings'})[0]==419
        settings={'csrf':token,'action':'settings','budget_usd':'0.00','daily_limit':'2','total_limit':'30','max_students':'0','price_ars':'','pack_minutes':'5','pilot_open':'1','enroll_open':'1'}
        assert request('/admin/tutor.php',settings)[0]==200
        assert php("echo tutor_settings()['budget_cents'].':'.tutor_settings()['reserved_cents'];")=='0:500'
        assert reserve(30)=='blocked'
        status,page=request('/profesor.php');assert status==200 and 'pendiente de definir' in page and '2 intentos' in page and 'USD 0,50' not in page
        assert php("echo tutor_settings()['price_ars_cents']===null?'pending':'set';")=='pending'
        # Explicit arbitrary test price; never a converted USD rate or production value.
        settings['price_ars']='1234.56'
        assert request('/admin/tutor.php',settings)[0]==200
        page=request('/profesor.php')[1];assert 'ARS 1.234,56 por 5 minutos, impuestos incluidos' in page
        assert php("echo tutor_settings()['price_ars_cents'];")=='123456'
        assert request('/admin/tutor.php',dict(settings,price_ars='-1'))[0]==200
        assert php("echo tutor_settings()['price_ars_cents'];")=='123456'
        old_form=dict(settings,price_usd='0.50',budget_usd='9.00');old_form.pop('price_ars')
        request('/admin/tutor.php',old_form)
        assert php("echo tutor_settings()['budget_cents'];")=='0'
        assert php("echo tutor_settings()['price_cents'];")=='50'
        settings['budget_usd']='6.00'
        request('/admin/tutor.php',settings)
        uid=int(php("echo q_val(\"SELECT id FROM users WHERE email='pilot@example.test'\");"))
        request('/admin/tutor.php',{'csrf':token,'action':'member','user_id':str(uid),'daily_limit':'1','total_limit':'26','blocked':'1'})
        assert php(f"try{{tutor_reserve({uid},'member-blocked-key');}}catch(RuntimeException $e){{echo $e->getMessage();}}")=='Tu acceso al piloto está pausado.'
        request('/admin/tutor.php',{'csrf':token,'action':'member','user_id':str(uid),'daily_limit':'','total_limit':''})
        assert php(f"echo tutor_quota({uid})['daily_limit'];")=='2'
        payment={'csrf':token,'action':'record_payment','user_id':str(uid),'reference':'test-receipt-123','amount_ars':'1234.56','minutes':'5','evidence':'Temporary test fixture; no real payment'}
        request('/admin/tutor.php',payment)
        request('/admin/tutor.php',payment)
        assert php("echo q_val('SELECT COUNT(*) FROM tutor_payments');")=='1'
        assert php("echo q_val('SELECT currency FROM tutor_payments').':'.q_val('SELECT amount_cents FROM tutor_payments');")=='ARS:123456'
        assert php("echo tutor_settings()['budget_cents'];")=='600'
        assert php(f"echo tutor_registered_seconds({uid});")=='0'
        pid=php("echo q_val('SELECT id FROM tutor_payments');")
        confirm={'csrf':token,'action':'payment_status','payment_id':pid,'status':'verified_manual'}
        request('/admin/tutor.php',confirm)
        assert php(f"echo tutor_registered_seconds({uid});")=='0'
        request('/admin/tutor.php',dict(confirm,confirmed='1'))
        request('/admin/tutor.php',dict(confirm,confirmed='1'))
        assert php(f"echo tutor_registered_seconds({uid});")=='300'
        page=request('/profesor.php')[1];assert 'test-receipt-123' in page and '5,00 minutos' in page
        request('/admin/tutor.php',dict(confirm,confirmed='1',status='voided'))
        assert php(f"echo tutor_registered_seconds({uid});")=='0'
        assert int(php("echo q_val('SELECT COUNT(*) FROM tutor_audit');"))>=7
        # A schema upgrade must not reset earlier budget reservations or records.
        php("meta_set('schema_version','8');")
        assert php("echo tutor_settings()['reserved_cents'].':'.q_val('SELECT COUNT(*) FROM tutor_payments');")=='500:1'
        assert php("echo q_val('SELECT currency FROM tutor_payments');")=='ARS'
        # Enrollment limits are checked on the same global reservation lock.
        settings['max_students']='1'
        request('/admin/tutor.php',settings)
        assert php("try{tutor_enroll(100);}catch(RuntimeException $e){echo 'blocked';}")=='blocked'
        settings['max_students']='0';settings.pop('enroll_open')
        request('/admin/tutor.php',settings)
        assert php("try{tutor_enroll(100);}catch(RuntimeException $e){echo 'blocked';}")=='blocked'
        print('PASS: HTTP login, CSRF, enrollment, recharge interest, disabled provider and admin-only metrics')
        print('PASS: persistent admin settings, safe lower budget, member limits, manual payment states, duplicates, consent, balances, audit, migration and enrollment caps')
    finally:
        server.terminate();server.wait(timeout=10)
    print('PASS: enrollment, disabled default, duplicate protection, minute/day/lifetime quotas, 20 concurrent reservations stop at USD 5; no API spend')
