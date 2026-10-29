<?php
declare(strict_types=1);

/** Published curriculum, including empty units so they cannot accidentally qualify. */
function learning_path(int $userId): array
{
    $progress = unit_progress($userId);
    $levels = q_all('SELECT * FROM levels ORDER BY position');
    foreach ($levels as &$level) {
        $level['units'] = q_all('SELECT id, title, summary, position FROM units WHERE level_code=? AND is_published=1 ORDER BY position,id', [$level['code']]);
        $level['complete'] = count($level['units']) > 0;
        $level['total'] = $level['solved'] = 0;
        foreach ($level['units'] as &$unit) {
            $p = $progress[(int)$unit['id']] ?? ['total'=>0, 'solved'=>0];
            $unit += $p;
            $unit['complete'] = $p['total'] > 0 && $p['solved'] === $p['total'];
            $level['complete'] = $level['complete'] && $unit['complete'];
            $level['total'] += $p['total'];
            $level['solved'] += $p['solved'];
        }
        unset($unit);
    }
    unset($level);
    return $levels;
}

/** Immutable award snapshot. UNIQUE(user_id,level_code) makes retries idempotent. */
function award_completed_levels(array $user, ?array $path = null): void
{
    foreach ($path ?? learning_path((int)$user['id']) as $level) {
        if (!$level['complete']) continue;
        if (q_val('SELECT id FROM certificates WHERE user_id=? AND level_code=?', [$user['id'], $level['code']])) continue;
        try {
            q('INSERT INTO certificates (user_id,level_code,student_name,level_name,issued_at,reference,exercise_count,concepts_json) VALUES (?,?,?,?,?,?,?,?)',
                [$user['id'], $level['code'], $user['name'], $level['name'], now_utc(), strtoupper(bin2hex(random_bytes(12))), $level['total'], json_encode(array_column($level['units'], 'title'), JSON_UNESCAPED_UNICODE)]);
        } catch (PDOException $e) {
            if (!q_val('SELECT id FROM certificates WHERE user_id=? AND level_code=?', [$user['id'], $level['code']])) throw $e;
        }
    }
}

/** A self-contained, single-page PDF using standard embedded PDF font references. */
function certificate_pdf(array $certificate): string
{
    $text = function(string $value): string {
        $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], iconv('UTF-8', 'Windows-1252//TRANSLIT', $value));
    };
    $stream = "0.97 0.98 1 rg 0 0 842 595 re f\n0.09 0.22 0.39 RG 3 w 28 28 786 539 re S\n0.95 0.5 0.17 rg 55 506 64 6 re f\n";
    $line = function(string $value, int $y, int $size = 16) use (&$stream, $text): void {
        $stream .= "0.09 0.22 0.39 rg BT /F1 $size Tf 55 $y Td (" . $text($value) . ") Tj ET\n";
    };
    $line('MyTeacher', 530, 24);
    $line('CERTIFICADO DE FINALIZACIÓN', 458, 30);
    $line('Se deja constancia de que', 408);
    $name = $certificate['student_name'];
    // Conservatively fit long names in the available width without truncating them.
    foreach (mb_str_split($name, 45) as $i => $part) $line($part, 375 - $i * 24, 16);
    $line('completó el nivel ' . strtoupper($certificate['level_code']), 284, 20);
    $line($certificate['level_name'], 260, 14);
    $line($certificate['exercise_count'] . ' ejercicios del programa publicados al momento de la emisión.', 231, 14);
    $line('Fecha de emisión: ' . substr($certificate['issued_at'], 0, 10) . ' (UTC)', 190, 13);
    $line('Código único: ' . $certificate['reference'], 162, 13);
    $line('Constancia de finalización del programa de MyTeacher.', 101, 12);
    $line('No equivale a una certificación oficial de dominio del MCER.', 80, 12);
    $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
        '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        '<< /Length ' . strlen($stream) . ">>\nstream\n" . $stream . 'endstream'];
    $pdf = "%PDF-1.4\n"; $offsets = [0];
    foreach ($objects as $i => $object) { $offsets[] = strlen($pdf); $pdf .= ($i+1) . " 0 obj\n$object\nendobj\n"; }
    $xref = strlen($pdf); $pdf .= "xref\n0 6\n0000000000 65535 f \n";
    foreach (array_slice($offsets, 1) as $offset) $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
    return $pdf . "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";
}
