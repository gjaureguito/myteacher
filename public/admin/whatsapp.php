<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/whatsapp_sales.php';
$admin = require_admin();
header('Cache-Control: private, no-store');

whatsapp_sales_storage();

if (is_post()) {
    csrf_check();
    $action = post_str('action', 20);
    $threadId = max(0, (int) ($_POST['thread_id'] ?? 0));
    if ($action === 'delete' && $threadId > 0) {
        q('DELETE FROM whatsapp_messages WHERE thread_id = ?', [$threadId]);
        q('DELETE FROM whatsapp_threads WHERE id = ?', [$threadId]);
        flash('ok', 'Conversación eliminada.');
    }
    redirect('/admin/whatsapp.php');
}

$search = preg_replace('/[^0-9]/', '', post_str('q', 32));
if (isset($_GET['q']) && is_string($_GET['q'])) $search = preg_replace('/[^0-9]/', '', mb_substr($_GET['q'], 0, 32));
$from = isset($_GET['from']) && is_string($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) && is_string($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) ? $_GET['to'] : '';

$threadWhere = [];
$threadParams = [];
if ($search !== '') {
    $threadWhere[] = 't.wa_id LIKE ?';
    $threadParams[] = '%' . $search . '%';
}
if ($from !== '' || $to !== '') {
    $range = [];
    $rangeParams = [];
    if ($from !== '') { $range[] = 'm.created_at >= ?'; $rangeParams[] = $from . ' 00:00:00'; }
    if ($to !== '') { $range[] = 'm.created_at <= ?'; $rangeParams[] = $to . ' 23:59:59'; }
    $threadWhere[] = 'EXISTS (SELECT 1 FROM whatsapp_messages m WHERE m.thread_id=t.id AND ' . implode(' AND ', $range) . ')';
    array_push($threadParams, ...$rangeParams);
}
$whereSql = $threadWhere ? implode(' AND ', $threadWhere) : '1=1';

$threads = q_all(
    "SELECT t.id,t.wa_id,t.created_at,t.last_message_at,
        (SELECT m.body FROM whatsapp_messages m WHERE m.thread_id=t.id ORDER BY m.id DESC LIMIT 1) AS last_body,
        (SELECT COUNT(*) FROM whatsapp_messages m WHERE m.thread_id=t.id AND m.direction='in') AS inbound_count
     FROM whatsapp_threads t
     WHERE $whereSql
     ORDER BY t.last_message_at DESC
     LIMIT 100",
    $threadParams
);

$totalThreads = (int) q_val("SELECT COUNT(*) FROM whatsapp_threads t WHERE $whereSql", $threadParams);
$messageRange = [];
$messageParams = [];
if ($from !== '') { $messageRange[] = 'created_at >= ?'; $messageParams[] = $from . ' 00:00:00'; }
if ($to !== '') { $messageRange[] = 'created_at <= ?'; $messageParams[] = $to . ' 23:59:59'; }
$messageWhereSql = $messageRange ? ' WHERE ' . implode(' AND ', $messageRange) : '';
$incomingCount = (int) q_val("SELECT COUNT(*) FROM whatsapp_messages$messageWhereSql" . ($messageWhereSql ? " AND direction='in'" : " WHERE direction='in'"), $messageParams);
$outgoingCount = (int) q_val("SELECT COUNT(*) FROM whatsapp_messages$messageWhereSql" . ($messageWhereSql ? " AND direction='out'" : " WHERE direction='out'"), $messageParams);

$selectedId = max(0, (int) ($_GET['thread'] ?? 0));
$selectedThread = null;
$conversationMessages = [];
if ($selectedId > 0) {
    $selectedThread = q_one('SELECT id,wa_id,created_at,last_message_at FROM whatsapp_threads WHERE id=?', [$selectedId]);
    if ($selectedThread) $conversationMessages = q_all('SELECT direction,body,created_at FROM whatsapp_messages WHERE thread_id=? ORDER BY id', [$selectedId]);
}

$metrics = [
    'Conversaciones' => $totalThreads,
    'Mensajes recibidos' => $incomingCount,
    'Respuestas enviadas' => $outgoingCount,
];
render('admin_whatsapp', compact('threads','selectedThread','conversationMessages','metrics','search','from','to'), 'Conversaciones de WhatsApp');
