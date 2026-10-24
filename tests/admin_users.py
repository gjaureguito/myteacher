"""Admin can create users from the panel (local SQLite + PHP server, no external calls)."""
from pathlib import Path
import http.cookiejar, os, re, socket, sqlite3, subprocess, tempfile, time, urllib.request, urllib.parse, urllib.error

ROOT = Path(__file__).resolve().parents[1]
ADMIN = 'gjaureguito@gmail.com'

with tempfile.TemporaryDirectory(prefix='admin-users-') as tmp:
    db_file = str(Path(tmp) / 'test.sqlite')
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=db_file, ADMIN_EMAILS=ADMIN)
    subprocess.run(['php'], input=f"<?php require 'src/bootstrap.php'; create_user('{ADMIN}','Guillermo','Local-test-123'); create_user('alumno@example.test','Alumno','Local-test-123');",
                   text=True, cwd=ROOT, env=env, check=True, capture_output=True)
    with socket.socket() as s:
        s.bind(('127.0.0.1', 0)); port = s.getsockname()[1]
    base = f'http://127.0.0.1:{port}'
    env['APP_URL'] = base
    server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public'], cwd=ROOT, env=env,
                              stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

    def client():
        return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def request(c, path, data=None):
        try:
            r = c.open(base + path, data=None if data is None else urllib.parse.urlencode(data).encode(), timeout=10)
        except urllib.error.HTTPError as e:
            r = e
        return r.status, r.read().decode()

    def token(html):
        return re.search(r'name="csrf" value="([a-f0-9]+)"', html)[1]

    def login(c, email, password='Local-test-123'):
        _, html = request(c, '/login.php')
        return request(c, '/login.php', {'csrf': token(html), 'email': email, 'password': password})

    try:
        for _ in range(50):
            try:
                urllib.request.urlopen(base + '/health.php', timeout=1); break
            except OSError:
                time.sleep(.1)
        con = sqlite3.connect(db_file)

        student = client(); login(student, 'alumno@example.test')
        status, _ = request(student, '/admin/')
        assert status == 403, 'students must not reach the admin panel'

        admin = client(); login(admin, ADMIN)
        _, page = request(admin, '/admin/')
        assert 'Agregar usuario' in page
        _, page = request(admin, '/admin/', {'csrf': token(page), 'action': 'create', 'email': 'Nueva@Example.test',
                                             'name': 'Nueva Alumna', 'password': '', 'days': '30', 'role': 'student'})
        pw = re.search(r'Contraseña generada: (\S+) ', page)
        assert pw, 'generated password must be shown once'
        row = con.execute("SELECT role, sub_status, access_until FROM users WHERE email='nueva@example.test'").fetchone()
        assert row and row[0] == 'student' and row[1] == 'manual' and row[2], row
        _, page = request(admin, '/admin/', {'csrf': token(page), 'action': 'create', 'email': 'nueva@example.test',
                                             'password': '', 'days': '0', 'role': 'student'})
        assert 'Ya existe un usuario' in page
        assert con.execute("SELECT COUNT(*) FROM users WHERE email='nueva@example.test'").fetchone()[0] == 1
        status, _ = login(client(), 'nueva@example.test', pw[1])
        new = client(); login(new, 'nueva@example.test', pw[1])
        _, dash = request(new, '/account.php')
        assert 'Activa hasta' in dash, 'new user must log in with the generated password and have access'
        status, _ = request(student, '/admin/', {'csrf': 'x', 'action': 'create', 'email': 'hack@example.test', 'days': '365', 'role': 'admin'})
        assert con.execute("SELECT COUNT(*) FROM users WHERE email='hack@example.test'").fetchone()[0] == 0
        print('PASS: admin creates users with access; duplicates rejected; students cannot create users')
    finally:
        server.terminate()
