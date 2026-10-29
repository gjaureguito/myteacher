<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';
require ROOT . '/src/contract.php';
require ROOT . '/src/billing.php';

$u = require_login();
if (!is_post()) redirect('/account.php');
csrf_check();

if (!mp_enabled()) {
    flash('info', 'El pago online todavía no está disponible. Escribinos y te habilitamos el acceso.');
    redirect('/account.php');
}

$mode = mp_mode();
$want = post_str('kind', 20) === 'once' ? 'once' : 'subscription';
if ($mode !== 'both') $want = $mode;

$contract=purchase_contract($u,$want);
$review=$_SESSION['purchase_review']??null;
if (!legal_provider_ready() || post_str('agreement',2)!=='1' || !is_array($review)
    || ($review['expires']??0)<time() || ($review['kind']??'')!==$want
    || !hash_equals($review['nonce']??'',post_str('review_nonce',64))
    || !hash_equals($contract['hash'],post_str('document_hash',64))
    || !hash_equals($contract['hash'],$review['hash']??'')) {
    flash('info','Revisá y aceptá las condiciones vigentes antes de iniciar el pago.');
    redirect('/checkout.php?kind='.$want);
}

if ($want === 'subscription' && has_full_access($u) && $u['sub_status'] === 'active') {
    flash('info', 'Ya tenés una suscripción activa.');
    redirect('/account.php');
}

try {
    $billing=billing_validate($_POST,(float)$contract['amount']);
} catch (RuntimeException $e) {
    $_SESSION['billing_draft']=[];
    foreach (['condition','name','document_type','document','address'] as $key) {
        $value=$_POST['billing_'.$key]??'';
        $_SESSION['billing_draft']['billing_'.$key]=is_string($value)?substr($value,0,500):'';
    }
    flash('error',$e->getMessage());
    redirect('/checkout.php?kind='.$want);
}

try {
    // Consume this review once, before any payment API request (including retries).
    unset($_SESSION['purchase_review']);
    db()->beginTransaction();
    q('INSERT INTO purchase_agreements(user_id,nonce,version,document_hash,document_text,amount,currency,kind,days,accepted_at) VALUES(?,?,?,?,?,?,?,?,?,?)',
      [$u['id'],$review['nonce'],$contract['version'],$contract['hash'],$contract['text'],$contract['amount'],'ARS',$want,$contract['days'],now_utc()]);
    q('INSERT INTO purchase_billing(nonce,user_id,billing_data,created_at) VALUES(?,?,?,?)',
      [$review['nonce'],$u['id'],json_encode($billing,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),now_utc()]);
    db()->commit();
    unset($_SESSION['billing_draft']);
    redirect($want === 'once' ? mp_create_one_time($u) : mp_create_subscription($u));
} catch (RuntimeException $e) {
    if(db()->inTransaction()) db()->rollBack();
    flash('error', $e->getMessage());
    redirect('/account.php');
}
