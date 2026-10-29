<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$u = require_login();
header('Cache-Control: private, no-store');
$code = is_string($_GET['level'] ?? null) ? $_GET['level'] : '';
$certificate = q_one('SELECT * FROM certificates WHERE user_id=? AND level_code=?', [$u['id'], $code]);
if (!$certificate) { http_response_code(404); exit('Certificado no disponible. Consultá tu ruta de aprendizaje.'); }
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="MyTeacher-' . $certificate['level_code'] . '.pdf"');
echo certificate_pdf($certificate);
