"""Mercado Pago end-to-end check against a local fake API (no real payments).

Covers: admin settings (token validated and stored encrypted), price override,
one-time checkout redirect, return page crediting access, and webhook idempotency.
"""
from pathlib import Path
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import http.cookiejar, json, os, re, socket, sqlite3, subprocess, tempfile, threading, time, urllib.request, urllib.parse, urllib.error

ROOT = Path(__file__).resolve().parents[1]
TOKEN = 'TEST-1234567890-abcdefghijklmnopqrstuvwx'
STATE = {'payment_status': 'approved', 'user_id': None, 'preferences': [], 'subscriptions': []}


class FakeMP(BaseHTTPRequestHandler):
    def log_message(self, *a):
        pass

    def reply(self, code, data):
        body = json.dumps(data).encode()
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(body)

    def authed(self):
        return self.headers.get('Authorization') == 'Bearer ' + TOKEN

    def do_GET(self):
        if not self.authed():
            return self.reply(401, {'message': 'invalid access token'})
        if self.path == '/users/me':
            return self.reply(200, {'id': 99, 'nickname': 'ACADEMIA', 'email': 'cobros@example.test'})
        if self.path == '/v1/payments/555000111':
            return self.reply(200, {'id': 555000111, 'status': STATE['payment_status'], 'transaction_amount': 45000,
                                    'external_reference': f"user:{STATE['user_id']}:once:30"})
        return self.reply(404, {'message': 'not found'})

    def do_POST(self):
        if not self.authed():
            return self.reply(401, {'message': 'invalid access token'})
        data = json.loads(self.rfile.read(int(self.headers.get('Content-Length', 0))) or b'{}')
        if self.path == '/preapproval':
            STATE['subscriptions'].append(data)
            return self.reply(201, {'id':'subscription-1','init_point':'https://mp.example.test/subscribe'})
        if self.path == '/checkout/preferences':
            STATE['preferences'].append(data)
            return self.reply(201, {'id': 'pref-1', 'init_point': 'https://mp.example.test/live',
                                    'sandbox_init_point': 'https://mp.example.test/sandbox'})
        return self.reply(404, {'message': 'not found'})


def free_port():
    with socket.socket() as s:
        s.bind(('127.0.0.1', 0))
        return s.getsockname()[1]


with tempfile.TemporaryDirectory(prefix='mp-test-') as tmp:
    db_file = str(Path(tmp) / 'test.sqlite')
    mp_port = free_port()
    mp = ThreadingHTTPServer(('127.0.0.1', mp_port), FakeMP)
    threading.Thread(target=mp.serve_forever, daemon=True).start()
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=db_file, ADMIN_EMAILS='',
               MP_API_BASE=f'http://127.0.0.1:{mp_port}', APP_KEY='test-key')
    env.update(LEGAL_OWNER='Demo Provider',LEGAL_CUIT='20-00000000-1',LEGAL_ADDRESS='Test address',CONTACT_EMAIL='legal@example.test')
    env.pop('MP_ACCESS_TOKEN', None)
    fixture = """<?php
require 'src/bootstrap.php';
create_user('admin@example.test','Admin','Local-test-123',['role'=>'admin']);
echo create_user('alumno@example.test','Alumno','Local-test-123',[]);
"""
    out = subprocess.run(['php'], input=fixture, text=True, cwd=ROOT, env=env, check=True, capture_output=True).stdout
    STATE['user_id'] = int(re.findall(r'\d+', out)[-1]) if re.findall(r'\d+', out) else None
    port = free_port()
    base = f'http://127.0.0.1:{port}'
    env['APP_URL'] = base
    server = subprocess.Popen(['php', '-d', 'error_reporting=E_ALL & ~E_DEPRECATED', '-S', f'127.0.0.1:{port}', '-t', 'public'], cwd=ROOT, env=env,
                              stdout=subprocess.DEVNULL, stderr=(ROOT.parent/'audit/mp-test.log').open('w'))

    class NoRedirect(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, req, fp, code, msg, headers, newurl):
            if not newurl.startswith(base):
                raise urllib.error.HTTPError(newurl, code, msg, headers, fp)
            return super().redirect_request(req, fp, code, msg, headers, newurl)

    def client():
        jar = http.cookiejar.CookieJar()
        return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar), NoRedirect)

    def request(c, path, data=None):
        try:
            r = c.open(base + path, data=None if data is None else urllib.parse.urlencode(data).encode(), timeout=10)
            return r.status, r.read().decode(), r.geturl()
        except urllib.error.HTTPError as e:
            return e.code, e.read().decode(), e.headers.get('Location') or e.filename

    def token(html):
        return re.search(r'name="csrf" value="([a-f0-9]+)"', html)[1]

    def login(c, email):
        _, html, _ = request(c, '/login.php')
        request(c, '/login.php', {'csrf': token(html), 'email': email, 'password': 'Local-test-123'})

    try:
        for _ in range(50):
            try:
                urllib.request.urlopen(base + '/health.php', timeout=1); break
            except OSError:
                time.sleep(.1)
        con = sqlite3.connect(db_file)
        if STATE['user_id'] is None:
            STATE['user_id'] = con.execute("SELECT id FROM users WHERE email='alumno@example.test'").fetchone()[0]

        student = client(); login(student, 'alumno@example.test')
        _, acc, _ = request(student, '/account.php')
        assert 'todavía no está disponible' in acc, 'without a token payments must be off'

        admin = client(); login(admin, 'admin@example.test')
        _, page, _ = request(admin, '/admin/payments.php')
        assert 'Sin configurar' in page and '/webhook/mercadopago.php' in page
        form = {'csrf': token(page), 'action': 'save', 'plan_price': '45000', 'mp_mode': 'both',
                'mp_once_days': '30', 'mp_enabled': '1'}
        request(admin, '/admin/payments.php', dict(form, mp_access_token='TEST-0000000000-wrongwrongwrongwrongwr'))
        assert con.execute("SELECT COUNT(*) FROM settings WHERE k='mp_access_token'").fetchone()[0] == 0, 'rejected token must not be saved'
        request(admin, '/admin/payments.php', dict(form, mp_access_token=TOKEN))
        stored = con.execute("SELECT v FROM settings WHERE k='mp_access_token'").fetchone()[0]
        assert stored.startswith('enc1:') and TOKEN not in stored, 'token must be stored encrypted'
        _, page, _ = request(admin, '/admin/payments.php')
        assert 'Conectado' in page and 'ACADEMIA' in page and TOKEN not in page, 'panel must mask the token'
        print('PASS: admin validates, encrypts and masks the Mercado Pago token')

        _, acc, _ = request(student, '/account.php')
        assert '$45.000' in acc and '/checkout.php' in acc and 'pagar 30 días' in acc
        # Missing acceptance must never contact the payment API.
        request(student, '/subscribe.php', {'csrf': token(acc), 'kind': 'once'})
        assert not STATE['preferences']
        _, review, _ = request(student, '/checkout.php?kind=once')
        fields=dict(re.findall(r'name="([^"]+)" value="([^"]*)"',review))
        payment={k:fields[k] for k in ['csrf','kind','review_nonce','document_hash']}
        payment['agreement']='1';payment.update(billing_condition='consumer',billing_name='Alumno',billing_document_type='DNI',billing_document='12345678',billing_address='')
        status, failure_body, location = request(student, '/subscribe.php', payment)
        assert location == 'https://mp.example.test/sandbox', f'checkout: {status} {location} {failure_body[:1000]}'
        assert con.execute('SELECT COUNT(*) FROM purchase_agreements').fetchone()[0] == 1
        assert con.execute('SELECT COUNT(*) FROM purchase_billing').fetchone()[0] == 1
        assert '12345678' in request(student, '/agreement.php?id=1')[1]
        assert request(student, '/admin/billing.php')[0] == 403
        assert '12345678' in request(admin, '/admin/billing.php')[1]
        saved=con.execute('SELECT document_text,document_hash FROM purchase_agreements').fetchone()
        import hashlib
        assert hashlib.sha256(saved[0].encode()).hexdigest()==saved[1]
        assert 'Demo Provider' in saved[0]
        request(student, '/subscribe.php', payment)
        assert len(STATE['preferences'])==1, 'replay must not initiate another checkout'
        pref = STATE['preferences'][-1]
        assert pref['items'][0]['unit_price'] == 45000 and pref['external_reference'] == f"user:{STATE['user_id']}:once:30"
        print('PASS: student sees the configured price and is sent to Checkout Pro')

        _, review, _ = request(student, '/checkout.php?kind=subscription')
        fields=dict(re.findall(r'name="([^"]+)" value="([^"]*)"',review))
        payment={k:fields[k] for k in ['csrf','kind','review_nonce','document_hash']};payment['agreement']='1';payment.update(billing_condition='consumer',billing_name='Alumno',billing_document_type='DNI',billing_document='12345678',billing_address='')
        status, _, location = request(student, '/subscribe.php',payment)
        assert location=='https://mp.example.test/subscribe'
        assert STATE['subscriptions'][-1]['auto_recurring']['transaction_amount']==45000
        assert con.execute('SELECT COUNT(*) FROM purchase_agreements').fetchone()[0]==2
        print('PASS: monthly and one-time payments require separate recorded acceptance')

        request(student, '/payment_return.php?payment_id=555000111&status=approved')
        until1 = con.execute('SELECT access_until, sub_status FROM users WHERE id=?', (STATE['user_id'],)).fetchone()
        assert until1[0] and until1[1] == 'paid', until1
        for _ in range(2):
            req = urllib.request.Request(base + '/webhook/mercadopago.php', method='POST',
                                         data=json.dumps({'type': 'payment', 'data': {'id': '555000111'}}).encode(),
                                         headers={'Content-Type': 'application/json'})
            assert urllib.request.urlopen(req, timeout=10).status == 200
        until2 = con.execute('SELECT access_until FROM users WHERE id=?', (STATE['user_id'],)).fetchone()[0]
        assert until2 == until1[0], 'repeated notifications must not add days twice'
        print('PASS: approved payment grants access once; webhook is idempotent')
    finally:
        con.close(); server.terminate(); server.wait(timeout=5); mp.shutdown()
