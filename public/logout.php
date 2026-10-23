<?php
require dirname(__DIR__) . '/src/bootstrap.php';

if (is_post()) {
    csrf_check();
    $_SESSION = [];
    session_destroy();
}
redirect('/');
