<?php
require dirname(__DIR__).'/src/billing.php';
function check(bool $ok): void { if(!$ok) throw new RuntimeException('Billing check failed'); }
$base=['billing_condition'=>'consumer','billing_name'=>'Persona de prueba','billing_document_type'=>'DNI','billing_document'=>'','billing_address'=>''];
check(billing_validate($base,30000)['document']==='');
$invalid=[['billing_condition'=>'other'],['billing_name'=>''],['billing_document'=>'123'],['billing_document_type'=>'CUIT','billing_document'=>'20123456780'],['billing_condition'=>'registered'],['billing_name'=>['bad']]];
foreach($invalid as $override){$rejected=false;try{billing_validate(array_replace($base,$override),30000);}catch(RuntimeException $e){$rejected=true;}check($rejected);}
$rejected=false;try{billing_validate($base,10000000);}catch(RuntimeException $e){$rejected=true;}check($rejected);
$business=array_replace($base,['billing_condition'=>'registered','billing_document_type'=>'CUIT','billing_document'=>'20-12345678-6','billing_address'=>'Domicilio de prueba']);
check(billing_validate($business,30000)['document']==='20123456786');
// Exercise the actual schema on an isolated in-memory database, including upgrades.
function db_is_sqlite(): bool{return true;}
function db(): PDO{static $pdo;return $pdo??=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);}
require dirname(__DIR__).'/src/schema.php';
run_schema();run_schema();
$stmt=db()->prepare('INSERT INTO purchase_billing(nonce,user_id,billing_data,created_at) VALUES(?,?,?,?)');
$stmt->execute(['test',1,json_encode(billing_validate($business,30000)),'2026-10-29 00:00:00']);
$saved=json_decode(db()->query('SELECT billing_data FROM purchase_billing')->fetchColumn(),true);
check($saved['document']==='20123456786');
check(str_contains(billing_summary($saved),'Responsable inscripto'));
echo "PASS: validation, CUIT checksum, required fields, consumer threshold, repeatable schema and stored snapshot\n";
