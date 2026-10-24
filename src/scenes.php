<?php
declare(strict_types=1);
function scenes(): array {
    return json_decode(file_get_contents(ROOT . '/content/scenes.json'), true, 512, JSON_THROW_ON_ERROR);
}
function scene_progress(int $userId, string $scene): array {
    return q_all('SELECT question_id, solved, tries FROM scene_status WHERE user_id = ? AND scene_id = ?', [$userId, $scene]);
}
