<?php
require dirname(__DIR__) . '/src/bootstrap.php';
http_response_code(404);
render('notfound', [], 'No encontrado');
