<?php
// Run with DB_DRIVER=sqlite and a disposable SQLITE_PATH; never against production.
if (getenv('DB_DRIVER') !== 'sqlite' || !getenv('SQLITE_PATH') || is_file(getenv('SQLITE_PATH'))) {
    fwrite(STDERR, "Use a new disposable SQLite database.\n"); exit(1);
}
require dirname(__DIR__) . '/src/bootstrap.php';
function verify(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
}
$expected = 0;
foreach (content_files() as $file) {
    $level = load_level($file);
    foreach ($level['units'] as $unit) verify(!empty($unit['theory']), 'Missing theory: '.$unit['slug']);
    $expected += count($level['units']);
}
verify($expected === 345, 'Unexpected source inventory');
verify((int)q_val('SELECT COUNT(*) FROM units') === $expected, 'Initial sync incomplete');
verify(meta_get('content_sig') === content_signature(), 'Signature not committed');
$user = create_user('sync@example.test', 'Sync Test', 'Local-test-only-123');
$uid = is_array($user) ? $user['id'] : $user;
$exercise = q_one('SELECT * FROM exercises ORDER BY id LIMIT 1');
q('INSERT INTO attempts(user_id,exercise_id,is_correct,created_at) VALUES(?,?,1,?)', [$uid,$exercise['id'],now_utc()]);
q('INSERT INTO exercise_status(user_id,exercise_id,solved,tries,updated_at) VALUES(?,?,1,1,?)', [$uid,$exercise['id'],now_utc()]);
q('UPDATE units SET is_published=0 WHERE id=?', [$exercise['unit_id']]);
$admin = q_one('SELECT id FROM units WHERE id<>? ORDER BY id LIMIT 1', [$exercise['unit_id']]);
q("UPDATE units SET source_hash='admin',title='Custom lesson' WHERE id=?", [$admin['id']]);
$before = q_all('SELECT id,unit_id,position,answer_json FROM exercises ORDER BY id');
$retained = q_all('SELECT id,unit_id,position,answer_json FROM exercises WHERE unit_id<=129 ORDER BY id');
// Simulate the old 129-unit catalog in this disposable database only.
q('DELETE FROM exercises WHERE unit_id>129');
q('DELETE FROM units WHERE id>129');
verify((int)q_val('SELECT COUNT(*) FROM units') === 129, 'Legacy fixture not ready');
$upgrade = seed_content(false);
verify($upgrade['units_new'] === 216, 'Missing units not imported');
verify((int)q_val('SELECT COUNT(*) FROM units') === 345, 'Upgrade incomplete');
verify(q_all('SELECT id,unit_id,position,answer_json FROM exercises WHERE unit_id<=129 ORDER BY id') === $retained, 'Existing exercise identities changed');
$before = q_all('SELECT id,unit_id,position,answer_json FROM exercises ORDER BY id');
$stats = seed_content(false);
verify($stats['units_new'] === 0 && $stats['units_updated'] === 0, 'Sync is not idempotent');
verify(q_all('SELECT id,unit_id,position,answer_json FROM exercises ORDER BY id') === $before, 'Exercise identities changed');
verify((int)q_val('SELECT COUNT(*) FROM attempts') === 1, 'Attempt lost');
verify((int)q_val('SELECT solved FROM exercise_status WHERE exercise_id=?',[$exercise['id']]) === 1, 'Progress lost');
verify((int)q_val('SELECT is_published FROM units WHERE id=?',[$exercise['unit_id']]) === 0, 'Publication overwritten');
verify(q_val('SELECT title FROM units WHERE id=?',[$admin['id']]) === 'Custom lesson', 'Admin edit overwritten');
echo "PASS: 345 units imported; repeat sync preserves exercises, attempts, progress, publication and admin edits.\n";
