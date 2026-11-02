<?php
require dirname(__DIR__,2).'/src/bootstrap.php';
$admin=require_admin();
header('Cache-Control: no-store');
require ROOT.'/src/tutor_admin.php';
if(is_post()) {
    csrf_check();
    try{tutor_admin_apply((int)$admin['id'],$_POST);flash('ok','Cambios guardados. No se realizó ningún cobro externo.');}
    catch(InvalidArgumentException $e){flash('error',$e->getMessage());}
    catch(Throwable $e){error_log('Tutor admin write failed');flash('error','No pudimos guardar los cambios. No se modificó ningún cobro externo.');}
    redirect('/admin/tutor.php');
}
$members=q_all('SELECT m.user_id,u.email,m.created_at,COUNT(c.id) AS calls,SUM(CASE WHEN c.status=\'ok\' THEN 1 ELSE 0 END) AS successes,COALESCE(SUM(c.cost_microusd),0) AS cost FROM tutor_members m JOIN users u ON u.id=m.user_id LEFT JOIN tutor_calls c ON c.user_id=m.user_id GROUP BY m.user_id,u.email,m.created_at ORDER BY m.created_at DESC');
$reserved=(int)q_val('SELECT reserved_cents FROM tutor_budget WHERE id=1');
$known=(int)q_val('SELECT COALESCE(SUM(cost_microusd),0) FROM tutor_calls');
$unknown=(int)q_val('SELECT COUNT(*) FROM tutor_calls WHERE cost_microusd IS NULL');
$settings=tutor_settings();
$payments=q_all('SELECT p.*,u.email FROM tutor_payments p JOIN users u ON u.id=p.user_id ORDER BY p.id DESC LIMIT 100');
$audit=q_all('SELECT a.id,a.action,a.created_at,u.email FROM tutor_audit a JOIN users u ON u.id=a.admin_id ORDER BY a.id DESC LIMIT 30');
render('admin_tutor',compact('members','reserved','known','unknown','settings','payments','audit'),'Piloto IA');
