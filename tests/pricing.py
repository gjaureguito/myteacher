"""Verify the price sent to a local fake payment service. No real payments."""
from pathlib import Path
import http.server, json, os, subprocess, tempfile, threading
ROOT=Path(__file__).resolve().parents[1]
requests=[]
class PaymentFixture(http.server.BaseHTTPRequestHandler):
    def do_POST(self):
        requests.append((self.path,json.loads(self.rfile.read(int(self.headers['Content-Length'])))))
        body=json.dumps({'id':'test-preapproval','init_point':'https://example.test/checkout'}).encode()
        self.send_response(201);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(body)
    def log_message(self,*args):pass
server=http.server.HTTPServer(('127.0.0.1',0),PaymentFixture)
threading.Thread(target=server.serve_forever,daemon=True).start()
try:
    with tempfile.TemporaryDirectory() as td:
        env=dict(os.environ,DB_DRIVER='sqlite',SQLITE_PATH=str(Path(td)/'price.sqlite'),PLAN_PRICE='50000',APP_NAME='MyTeacher',ADMIN_EMAILS='',MP_ACCESS_TOKEN='local-fixture-only',MP_API_BASE=f'http://127.0.0.1:{server.server_port}')
        code="""<?php require 'src/bootstrap.php'; require 'src/mercadopago.php';
$user=create_user('price@example.test','Price Test','Local-test-123',[]);
if (!is_array($user)) $user=q_one('SELECT * FROM users WHERE email=?',['price@example.test']);
echo mp_create_subscription($user);
"""
        result=subprocess.run(['php','-d','display_errors=stderr'],input=code,text=True,cwd=ROOT,env=env,capture_output=True,check=True)
        assert result.stdout=='https://example.test/checkout',result.stdout
        assert len(requests)==1 and requests[0][0]=='/preapproval'
        payload=requests[0][1]
        assert payload['auto_recurring']=={'frequency':1,'frequency_type':'months','transaction_amount':50000,'currency_id':'ARS'}
        assert payload['status']=='pending' and payload['reason']=='MyTeacher — Suscripción mensual'
        print('PASS: new subscription sends ARS 50000 per month to a local mock; no live payment requests')
finally:server.shutdown();server.server_close()
