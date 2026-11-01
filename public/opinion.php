<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$u = require_login();
header('Cache-Control: private, no-store');
$review = q_one('SELECT * FROM testimonials WHERE user_id=?', [$u['id']]);
$errors = [];
$name = testimonial_first_name($u['name']);
$body = $review['body'] ?? '';
if (is_post()) {
    csrf_check();
    if (post_str('action', 20) === 'withdraw') {
        q('DELETE FROM testimonials WHERE user_id=?', [$u['id']]);
        flash('ok', 'Retiramos tu opinión de la página.');
        redirect('/opinion.php');
    }
    $body = post_str('body', 1500);
    if (mb_strlen($body) < 20) $errors[] = 'Escribí una opinión de al menos 20 caracteres.';
    if (post_str('consent', 2) !== '1') $errors[] = 'Para enviarla, autorizá la publicación de tu nombre público y comentario.';
    if (!$errors) {
        if ($review) q("UPDATE testimonials SET display_name=?,body=?,status='pending',consent_at=?,updated_at=?,reviewed_at=NULL,reviewed_by=NULL WHERE user_id=?", [$name,$body,now_utc(),now_utc(),$u['id']]);
        else q('INSERT INTO testimonials(user_id,display_name,body,consent_at,updated_at) VALUES(?,?,?,?,?)', [$u['id'],$name,$body,now_utc(),now_utc()]);
        flash('ok', 'Gracias por compartir tu experiencia. Tu opinión quedó pendiente de revisión.');
        redirect('/opinion.php');
    }
}
render('opinion', compact('u','review','name','body','errors'), 'Mi opinión');
