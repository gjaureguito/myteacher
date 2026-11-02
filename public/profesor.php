<?php
require dirname(__DIR__).'/src/bootstrap.php';
$user=require_login();
header('Cache-Control: no-store');
if(tutor_dialog_access($user)){render('tutor_dialogo',compact('user'),'Tu profesor con IA');exit;}
$enrolled=(bool)q_one('SELECT user_id FROM tutor_members WHERE user_id=?',[$user['id']]);
$quota=tutor_quota((int)$user['id']);
$settings=tutor_settings();
$balance=tutor_registered_seconds((int)$user['id']);
$payments=q_all('SELECT reference,amount_cents,currency,seconds,status,created_at FROM tutor_payments WHERE user_id=? ORDER BY id DESC LIMIT 50',[$user['id']]);
render('tutor',compact('user','enrolled','quota','settings','balance','payments'),'Tu profesor con IA');
