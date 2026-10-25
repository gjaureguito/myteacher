"""OAuth "Conectar Mercado Pago" flow against a local fake API (no real accounts).

Covers: connect redirect with state + PKCE, callback rejecting a wrong state,
code exchange storing encrypted tokens, account shown in the panel, automatic
refresh of an expiring token, and checkout using the OAuth token.
"""
from pathlib import Path
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import base64, hashlib, http.cookiejar, json, os, re, socket, sqlite3, subprocess, tempfile, threading, time
import urllib.request, urllib.parse, urllib.error

ROOT = Path(__file__).resolve().parents[1]
CALLS = []
TOKENS = {'access': 'APP_USR-oauth-access-1', 'refresh': 'TG-refresh-1'}


class FakeMP(BaseHTTPRequestHandler):
    def log_message(self, *a):
        pass

    def reply(self, code, data):
        body = json.dumps(data).encode()
        self.send_response(code); self.send_header('Content-Type', 'application/json'); self.end_headers()
        self.wfile.write(body)

    def do_GET(self):
        if self.path == '/users/me' and self.headers.get('Authorization', '').startswith('Bearer APP_USR-oauth-access'):
            return self.reply(200, {'id': 7, 'nickname': 'MYTEACHER', 'email': 'cobros@example.test'})
        return self.reply(401, {'message': 'invalid token'})

    def do_POST(self):
        data = json.loads(self.rfile.read(int(self.headers.get('Content-Length', 0))) or b'{}')
        CALLS.append((self.path, data, self.headers.get('Authorization')))
        if self.path == '/oauth/token':
            if data.get('client_id') != 'cid' or data.get('client_secret') != 'csecret':
                return self.reply(401, {'message': 'bad client'})
            if data['grant_type'] == 'authorization_code':
                challenge = base64.urlsafe_b64encode(hashlib.sha256(data['code_verifier'].encode()).digest()).rstrip(b'=').decode()
                if data['code'] != 'good-code' or challenge != STATE['challenge']:
                    return self.reply(400, {'message': 'invalid_grant'})
                return self.reply(200, {'access_token': TOKENS['access'], 'refresh_token': TOKENS['refresh'], 'expires_in': 15552000})
            if data['grant_type'] == 'refresh_token' and data['refresh_token'] == TOKENS['refresh']:
                return self.reply(200, {'access_token': 'APP_USR-oauth-access-2', 'refresh_token': 'TG-refresh-2', 'expires_in': 15552000})
            return self.reply(400, {'message': 'invalid_grant'})
        if self.path == '/checkout/preferences':
            return self.reply(201, {'id': 'pref', 'init_point': 'https://mp.example.test/pay'})
        return self.reply(404, {})


STATE = {}


def free_port():
    with socket.socket() as s:
        s.bind(('127.0.0.1', 0)); return s.getsockname()[1]


with tempfile.TemporaryDirectory(prefix='mp-oauth-') as tmp:
    db_file = str(Path(tmp) / 't.sqlite')
    mp_port = free_port()
    mp = ThreadingHTTPServer(('127.0.0.1', mp_port), FakeMP)
    threading.Thread(target=mp.serve_forever, daemon=True).start()
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=db_file, ADMIN_EMAILS='', APP_KEY='k',
               MP_API_BASE=f'http://127.0.0.1:{mp_port}', MP_CLIENT_ID='cid', MP_CLIENT_SECRET='csecret')
    env.pop('MP_ACCESS_TOKEN', None)
    subprocess.run(['php'], input="<?php require 'src/bootstrap.php'; create_user('admin@example.test','A','Local-test-123',['role'=>'admin']); create_user('alumno@example.test','B','Local-test-123',[]);",
                   text=True, cwd=ROOT, env=env, check=True, capture_output=True)
    port = free_port(); base = f'http://127.0.0.1:{port}'; env['APP_URL'] = base
    server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public'], cwd=ROOT, env=env,
                              stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

    class Stop(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, req, fp, code, msg, headers, newurl):
            if not newurl.startswith(base):
                raise urllib.error.HTTPError(newurl, code, msg, headers, fp)
            return super().redirect_request(req, fp, code, msg, headers, newurl)

    def client():
        return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), Stop)

    def get(c, path, data=None):
        try:
            r = c.open(base + path, data=None if data is None else urllib.parse.urlencode(data).encode(), timeout=10)
            return r.status, r.read().decode(), r.geturl()
        except urllib.error.HTTPError as e:
            return e.code, '', e.headers.get('Location') or e.filename

    def token(html):
        return re.search(r'name="csrf" value="([a-f0-9]+)"', html)[1]

    def login(c, email):
        _, h, _ = get(c, '/login.php'); get(c, '/login.php', {'csrf': token(h), 'email': email, 'password': 'Local-test-123'})

    try:
        for _ in range(50):
            try: urllib.request.urlopen(base + '/health.php', timeout=1); break
            except OSError: time.sleep(.1)
        con = sqlite3.connect(db_file)
        admin = client(); login(admin, 'admin@example.test')
        _, page, _ = get(admin, '/admin/payments.php')
        assert 'Conectar Mercado Pago' in page and 'disabled' not in page.split('Conectar Mercado Pago')[0][-200:]

        _, _, loc = get(admin, '/admin/mp_connect.php', {'csrf': token(page)})
        q = urllib.parse.parse_qs(urllib.parse.urlparse(loc).query)
        assert loc.startswith('https://auth.mercadopago.com/authorization?'), loc
        assert q['client_id'] == ['cid'] and q['code_challenge_method'] == ['S256']
        assert q['redirect_uri'] == [base + '/admin/mp_callback.php']
        STATE['challenge'] = q['code_challenge'][0]

        _, page, _ = get(admin, '/admin/mp_callback.php?' + urllib.parse.urlencode({'state': 'wrong', 'code': 'good-code'}))
        assert con.execute("SELECT COUNT(*) FROM settings WHERE k='mp_access_token'").fetchone()[0] == 0, 'wrong state must not connect'
        _, page, _ = get(admin, '/admin/payments.php')
        _, _, loc = get(admin, '/admin/mp_connect.php', {'csrf': token(page)})
        q = urllib.parse.parse_qs(urllib.parse.urlparse(loc).query); STATE['challenge'] = q['code_challenge'][0]
        _, page, _ = get(admin, '/admin/mp_callback.php?' + urllib.parse.urlencode({'state': q['state'][0], 'code': 'good-code'}))
        stored = dict(con.execute("SELECT k, v FROM settings").fetchall())
        assert stored['mp_access_token'].startswith('enc1:') and stored['mp_refresh_token'].startswith('enc1:')
        assert 'MYTEACHER' in page and TOKENS['access'] not in page
        print('PASS: OAuth connect uses state + PKCE, rejects a wrong state, stores tokens encrypted')

        con.execute("UPDATE settings SET v='1' WHERE k='mp_token_expires'"); con.commit()
        student = client(); login(student, 'alumno@example.test')
        _, acc, _ = get(student, '/account.php')
        assert 'Suscribirme con Mercado Pago' in acc, 'connected account must enable payments'
        con.execute("INSERT OR REPLACE INTO settings (k, v, updated_at) VALUES ('mp_mode','once','2026-01-01')"); con.commit()
        _, acc, _ = get(student, '/account.php')
        _, _, loc = get(student, '/subscribe.php', {'csrf': token(acc), 'kind': 'once'})
        assert loc == 'https://mp.example.test/pay', loc
        refreshed = [c for c in CALLS if c[0] == '/oauth/token' and c[1]['grant_type'] == 'refresh_token']
        pref = [c for c in CALLS if c[0] == '/checkout/preferences'][-1]
        assert refreshed and pref[2] == 'Bearer APP_USR-oauth-access-2', 'expired token must be refreshed before charging'
        print('PASS: expiring token is renewed automatically and used for checkout')
    finally:
        server.terminate(); mp.shutdown()
