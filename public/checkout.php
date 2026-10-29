<?php
require dirname(__DIR__).'/src/bootstrap.php';
require ROOT.'/src/mercadopago.php';
require ROOT.'/src/contract.php';
require ROOT.'/src/billing.php';
$u=require_login();
header('Cache-Control: private, no-store');
$raw=$_GET['kind']??'subscription';
$kind=purchase_kind(is_string($raw)?$raw:'subscription');
$contract=purchase_contract($u,$kind);
$billing=$_SESSION['billing_draft']??['billing_condition'=>'consumer','billing_name'=>$u['name'],'billing_document_type'=>'DNI','billing_document'=>'','billing_address'=>''];
$nonce=bin2hex(random_bytes(24));
$_SESSION['purchase_review']=['nonce'=>$nonce,'hash'=>$contract['hash'],'kind'=>$kind,'expires'=>time()+1800];
render('checkout',compact('u','contract','nonce','billing'),'Revisar contratación');
