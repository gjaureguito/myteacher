<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
$admin = require_admin();
header('Cache-Control: private, no-store');
if (is_post()) {
    csrf_check();
    $status = post_str('action',20) === 'approve' ? 'approved' : 'hidden';
    // Match the content shown to the reviewer; an intervening student edit needs fresh review.
    $uid = (int)($_POST['user_id'] ?? 0);
    $review = q_one('SELECT * FROM testimonials WHERE user_id=?', [$uid]);
    $hash = $review ? hash('sha256', $review['display_name']."\n".$review['body']."\n".$review['consent_at']) : '';
    if (!$review || !hash_equals($hash, post_str('review_hash',64))) {
        flash('error','La opinión cambió o fue retirada. Revisá la versión actual.');
    } else {
        $changed = q('UPDATE testimonials SET status=?,reviewed_at=?,reviewed_by=? WHERE user_id=? AND body=? AND display_name=? AND consent_at=?',
            [$status,now_utc(),$admin['id'],$uid,$review['body'],$review['display_name'],$review['consent_at']])->rowCount();
        flash($changed ? 'ok' : 'info', $changed ? 'Estado de la opinión actualizado.' : 'La opinión cambió. Volvé a revisarla.');
    }
    redirect('/admin/promotion.php');
}
settings_ensure();
$now = now_utc();
$started = (int)q_val('SELECT COUNT(*) FROM users WHERE trial_started_at IS NOT NULL');
$expired = (int)q_val('SELECT COUNT(*) FROM users WHERE trial_started_at IS NOT NULL AND trial_ends_at <= ?', [$now]);
$paidSql = "EXISTS (SELECT 1 FROM mp_payments p WHERE p.user_id=u.id AND p.status='approved' AND p.credited=1 AND p.created_at>=u.trial_started_at)";
$paid = (int)q_val("SELECT COUNT(*) FROM users u WHERE trial_started_at IS NOT NULL AND $paidSql");
$expiredPaid = (int)q_val("SELECT COUNT(*) FROM users u WHERE trial_started_at IS NOT NULL AND trial_ends_at<=? AND $paidSql", [$now]);
$metrics = [
    'Pruebas iniciadas'=>$started,
    'Pruebas vigentes'=>(int)q_val('SELECT COUNT(*) FROM users WHERE trial_ends_at>?',[$now]),
    'Pruebas vencidas'=>$expired,
    'Alumnos que practicaron'=>(int)q_val('SELECT COUNT(*) FROM users u WHERE trial_started_at IS NOT NULL AND (EXISTS(SELECT 1 FROM attempts a WHERE a.user_id=u.id AND a.created_at>=u.trial_started_at) OR EXISTS(SELECT 1 FROM scene_status s WHERE s.user_id=u.id AND s.updated_at>=u.trial_started_at))'),
    'Con pago único aprobado'=>$paid,
    'Con suscripción autorizada'=>(int)q_val("SELECT COUNT(*) FROM users WHERE trial_started_at IS NOT NULL AND sub_status='active'"),
];
$page = max(1,(int)($_GET['page']??1));
$offset = ($page-1)*30;
$reviews=q_all("SELECT t.*,u.email FROM testimonials t JOIN users u ON u.id=t.user_id ORDER BY CASE WHEN t.status='pending' THEN 0 ELSE 1 END,t.updated_at DESC LIMIT 31 OFFSET $offset");
$more=count($reviews)>30; $reviews=array_slice($reviews,0,30);
render('admin_promotion',compact('metrics','expired','expiredPaid','reviews','page','more'),'Pruebas y opiniones');
