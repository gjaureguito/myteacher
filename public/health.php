<?php
// Healthcheck para Railway: responde 200 si la base y la sincronización del catálogo están listas.
require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: text/plain');
$ready = q_val('SELECT COUNT(*) FROM levels') !== null
    && meta_get('content_sig') === content_signature();
http_response_code($ready ? 200 : 503);
echo $ready ? 'ok' : 'not ready';
