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

/** JPEG RGB de public/assets/images/certificates/<nombre>.jpg, o null si falta (el certificado se emite igual). */
function certificate_jpeg(string $name): ?array
{
    if (!preg_match('/^[a-z0-9]+$/', $name)) return null;
    $path = dirname(__DIR__) . '/public/assets/images/certificates/' . $name . '.jpg';
    $info = is_file($path) ? @getimagesize($path) : false;
    if (!$info || $info[2] !== IMAGETYPE_JPEG || ($info['channels'] ?? 3) !== 3) return null;
    return ['width'=>$info[0], 'height'=>$info[1], 'data'=>(string)file_get_contents($path)];
}

/** Texto UTF-8 → Windows-1252 apto para un string literal de PDF (sin escapar). */
function certificate_pdf_text(string $value): string
{
    $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value);
    return (string)iconv('UTF-8', 'Windows-1252//TRANSLIT', $value);
}

/** Ancho en puntos de un texto Windows-1252 en una fuente estándar. */
function certificate_text_width(string $raw, string $font, float $size): float
{
    static $widths = null;
    $widths ??= certificate_font_widths();
    $total = 0;
    foreach (str_split($raw) as $ch) {
        $code = ord($ch);
        $total += $code >= 32 ? $widths[$font][$code - 32] : 0;
    }
    return $total * $size / 1000;
}

/**
 * Certificado A4 apaisado tipo diploma: fondo de alta resolución (marco, guilloche, título y sello,
 * diseñado en docs/certificado/fondo.html) y encima los datos del alumno como texto real del PDF.
 */
function certificate_pdf(array $certificate): string
{
    require_once __DIR__ . '/certificate_fonts.php';
    $fonts = ['Helvetica'=>'F1', 'Times-Roman'=>'F2', 'Times-Bold'=>'F3', 'Times-Italic'=>'F4', 'Times-BoldItalic'=>'F5'];
    $navy = '0.090 0.220 0.373'; $muted = '0.294 0.353 0.439';
    $stream = '';
    // Segmentos [fuente, texto, color] centrados en $cx sobre la línea base $y.
    $centered = function (array $segments, float $cx, float $y, float $size) use (&$stream, $fonts): void {
        $raw = array_map(fn($s) => certificate_pdf_text($s[1]), $segments);
        $width = 0;
        foreach ($segments as $i => $s) $width += certificate_text_width($raw[$i], $s[0], $size);
        $stream .= sprintf("BT 1 0 0 1 %.2F %.2F Tm\n", $cx - $width / 2, $y);
        foreach ($segments as $i => $s) {
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $raw[$i]);
            $stream .= sprintf("%s rg /%s %.2F Tf (%s) Tj\n", $s[2], $fonts[$s[0]], $size, $escaped);
        }
        $stream .= "ET\n";
    };

    $background = certificate_jpeg('fondo');
    $icon = certificate_jpeg((string)$certificate['level_code']);
    if ($background) $stream .= "q 842 0 0 595 0 0 cm /Im0 Do Q\n";
    else $stream .= "0.98 0.97 0.95 rg 0 0 842 595 re f\n$navy RG 8 w 14 14 814 567 re S\n";
    if ($icon) $stream .= "q 62 0 0 62 390 132 cm /Im1 Do Q\n";

    // Nombre: tan grande como entre sobre la línea dorada (450 pt), sin cortarlo.
    $name = trim((string)$certificate['student_name']);
    $size = 38;
    while ($size > 14 && certificate_text_width(certificate_pdf_text($name), 'Times-BoldItalic', $size) > 440) $size -= 1;
    $centered([['Times-BoldItalic', $name, $navy]], 421, 318, $size);

    $level = strtoupper((string)$certificate['level_code']) . ' · ' . $certificate['level_name'];
    $centered([['Times-Roman', 'por haber completado el nivel ', $muted], ['Times-Bold', $level, $navy]], 421, 268, 15);
    $centered([['Times-Italic', 'del programa de MyTeacher, con ' . (int)$certificate['exercise_count'] . ' ejercicios resueltos.', $muted]], 421, 248, 13);

    $date = DateTime::createFromFormat('Y-m-d', substr((string)$certificate['issued_at'], 0, 10));
    $centered([['Helvetica', $date ? $date->format('d/m/Y') : substr((string)$certificate['issued_at'], 0, 10), $navy]], 198, 134, 12);
    $centered([['Helvetica', trim(chunk_split((string)$certificate['reference'], 4, ' ')), $navy]], 644, 134, 9.5);

    $fontObjects = [];
    foreach ($fonts as $font => $id) $fontObjects[] = "/$id " . (4 + count($fontObjects)) . ' 0 R';
    $images = array_filter(['Im0'=>$background, 'Im1'=>$icon]);
    $firstImage = 4 + count($fonts) + 1;
    $xobjects = [];
    foreach (array_keys($images) as $i => $key) $xobjects[] = "/$key " . ($firstImage + $i) . ' 0 R';
    $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << ' . implode(' ', $fontObjects) . ' >>'
            . ($xobjects ? ' /XObject << ' . implode(' ', $xobjects) . ' >>' : '') . ' >> /Contents ' . (4 + count($fonts)) . ' 0 R >>'];
    foreach (array_keys($fonts) as $font) $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /$font /Encoding /WinAnsiEncoding >>";
    $objects[] = '<< /Length ' . strlen($stream) . ">>\nstream\n" . $stream . 'endstream';
    foreach ($images as $image) $objects[] = '<< /Type /XObject /Subtype /Image /Width ' . $image['width'] . ' /Height ' . $image['height']
        . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($image['data']) . ">>\nstream\n" . $image['data'] . "\nendstream";

    $size = count($objects) + 1;
    $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n"; $offsets = [];
    foreach ($objects as $i => $object) { $offsets[] = strlen($pdf); $pdf .= ($i + 1) . " 0 obj\n$object\nendobj\n"; }
    $xref = strlen($pdf);
    $pdf .= "xref\n0 $size\n0000000000 65535 f \n";
    foreach ($offsets as $offset) $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
    return $pdf . "trailer\n<< /Size $size /Root 1 0 R /Info << /Title (Certificado MyTeacher) /Producer (MyTeacher) >> >>\nstartxref\n$xref\n%%EOF\n";
}
