<?php
// Healthcheck para Railway: responde 200 si la base está accesible.
require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: text/plain');
echo q_val('SELECT COUNT(*) FROM levels') !== null ? 'ok' : 'db?';
