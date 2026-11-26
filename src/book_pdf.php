<?php
declare(strict_types=1);

function study_pdf_plain(string $value): string
{
    $value = preg_replace('/<\s*li\b[^>]*>/i', "\n- ", $value);
    $value = preg_replace('/<\s*br\s*\/?>|<\/\s*(?:p|h[1-6]|div|section|li|tr|blockquote)\s*>/i', "\n", $value);
    $value = strip_tags($value);
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace('/[ \t]+/u', ' ', $value);
    $value = preg_replace('/\n[ \t]+/u', "\n", $value);
    $value = preg_replace('/\n{3,}/u', "\n\n", $value);
    return trim($value);
}

function study_pdf_ansi(string $text): string
{
    $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
    return $encoded === false ? '' : $encoded;
}

function study_pdf_generate(array $unit, string $level, ?array $chapter, ?array $visual): string
{
    $pages = [''];
    $pageIndex = 0;
    $y = 785;
    $sectionNumber = 0;

    $decoratePage = static function () use (&$pages, &$pageIndex, $unit, $level): void {
        $pages[$pageIndex] .= "q 0.10 0.31 0.39 rg 0 834 595 8 re f Q\n";
        $brand = strtoupper($level) . '  /  ENGLISH STUDY NOTES';
        $brandHex = strtoupper(bin2hex(study_pdf_ansi($brand)));
        $pages[$pageIndex] .= "BT /F2 8 Tf 0.24 0.36 0.42 rg 54 816 Td <{$brandHex}> Tj ET\n";
        $tag = strtoupper($level) . '  /  ' . str_pad((string) ($unit['position'] ?? ''), 2, '0', STR_PAD_LEFT);
        $tagHex = strtoupper(bin2hex(study_pdf_ansi($tag)));
        $pages[$pageIndex] .= "q 0.91 0.96 0.96 rg 468 804 73 20 re f Q\n";
        $pages[$pageIndex] .= "BT /F2 8 Tf 0.10 0.31 0.39 rg 477 811 Td <{$tagHex}> Tj ET\n";
    };
    $decoratePage();

    $newPage = static function () use (&$pages, &$pageIndex, &$y, $decoratePage): void {
        $pages[] = '';
        $pageIndex++;
        $y = 785;
        $decoratePage();
    };

    $line = static function (string $text, string $font = 'F1', int $size = 11, int $leading = 16, string $rgb = '0.18 0.24 0.32', int $x = 54) use (&$pages, &$pageIndex, &$y, $newPage): void {
        $text = trim($text);
        if ($text === '') { $y -= max(5, $leading - 7); return; }
        $maxChars = max(30, (int) ((541 - $x) / max(4.5, $size * 0.52)));
        $words = preg_split('/\s+/u', $text) ?: [];
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if ($current !== '' && strlen(study_pdf_ansi($candidate)) > $maxChars) {
                if ($y < 58) $newPage();
                $hex = strtoupper(bin2hex(study_pdf_ansi($current)));
                $pages[$pageIndex] .= "BT /{$font} {$size} Tf {$rgb} rg {$x} {$y} Td <{$hex}> Tj ET\n";
                $y -= $leading;
                $current = $word;
            } else { $current = $candidate; }
        }
        if ($current !== '') {
            if ($y < 58) $newPage();
            $hex = strtoupper(bin2hex(study_pdf_ansi($current)));
            $pages[$pageIndex] .= "BT /{$font} {$size} Tf {$rgb} rg {$x} {$y} Td <{$hex}> Tj ET\n";
            $y -= $leading;
        }
    };

    $paragraph = static function (string $text) use ($line, &$y): void {
        foreach (preg_split('/\R/u', $text) ?: [] as $part) $line($part, 'F1', 11, 16);
        $y -= 5;
    };
    $heading = static function (string $title, string $text) use (&$pages, &$pageIndex, &$y, &$sectionNumber, $newPage, $paragraph): void {
        if (trim($text) === '') return;
        if ($y < 90) $newPage();
        $sectionNumber++;
        $number = str_pad((string) $sectionNumber, 2, '0', STR_PAD_LEFT);
        $numberHex = strtoupper(bin2hex(study_pdf_ansi($number)));
        $titleHex = strtoupper(bin2hex(study_pdf_ansi($title)));
        $pages[$pageIndex] .= "q 0.12 0.49 0.52 rg 54 " . ($y - 4) . " 3 15 re f Q\n";
        $pages[$pageIndex] .= "BT /F2 8 Tf 0.12 0.49 0.52 rg 64 {$y} Td <{$numberHex}> Tj ET\n";
        $pages[$pageIndex] .= "BT /F2 12 Tf 0.10 0.22 0.33 rg 87 {$y} Td <{$titleHex}> Tj ET\n";
        $y -= 20;
        $paragraph($text);
    };

    $line('APUNTES DE GRAMÁTICA INGLESA', 'F2', 9, 15, '0.12 0.49 0.52');
    $line((string) $unit['title'], 'F2', 22, 30, '0.07 0.20 0.32');
    $line('Guía teórica para estudiar y consultar', 'F1', 10, 16, '0.38 0.46 0.52');
    $paragraph((string) ($unit['summary'] ?? ''));

    if ($chapter) {
        $heading('Propósito de aprendizaje', (string) ($chapter['goal'] ?? ''));
        $heading('Desarrollo teórico: reglas y estructura', (string) ($chapter['explain'] ?? ''));
        $heading('Uso, forma y diferencias importantes', (string) ($chapter['contrast'] ?? ''));
        if ($visual && !empty($visual['foundation'])) {
            $heading('Fundamento: cómo interpretar la estructura', (string) $visual['foundation']);
        }
        $heading('Ejemplo original trabajado', (string) ($chapter['example'] ?? ''));
        $heading('Análisis paso a paso', (string) ($chapter['reason'] ?? ''));

        // Reuse only explanatory notes from the practice data as additional theory.
        // Never print exercise prompts, answer keys, writing assignments, or speaking tasks.
        $applicationNotes = [];
        if (!empty($chapter['practice']) && is_array($chapter['practice'])) {
            foreach ($chapter['practice'] as $item) {
                if (!is_array($item) || empty($item[2]) || !is_string($item[2])) continue;
                $note = trim($item[2]);
                if ($note !== '' && !in_array($note, $applicationNotes, true)) $applicationNotes[] = $note;
            }
        }
        if ($applicationNotes) {
            $line('Notas teóricas para aplicar la regla', 'F2', 14, 20);
            foreach ($applicationNotes as $note) $paragraph('• ' . $note);
        }
    } else {
        $theory = study_pdf_plain((string) ($unit['theory_html'] ?? ''));
        $heading('Teoría gramatical de la unidad', $theory !== '' ? $theory : (string) ($unit['summary'] ?? ''));
    }
    $line('Material de autoestudio · myteacher.com.ar', 'F1', 9, 13);

    $count = count($pages);
    $objects = [
        1 => '<< /Type /Catalog /Pages 2 0 R >>',
        3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
    ];
    $kids = [];
    foreach ($pages as $i => $body) {
        $pageId = 5 + ($i * 2);
        $streamId = $pageId + 1;
        $kids[] = $pageId . ' 0 R';
        $footer = 'MYTEACHER  /  APUNTES DE GRAMÁTICA INGLESA';
        $footerHex = strtoupper(bin2hex(study_pdf_ansi($footer)));
        $pageNumber = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) . ' / ' . str_pad((string) $count, 2, '0', STR_PAD_LEFT);
        $pageNumberHex = strtoupper(bin2hex(study_pdf_ansi($pageNumber)));
        $stream = "q 0.78 0.84 0.86 RG 54 51 m 541 51 l S Q\n" . $body;
        $stream .= "BT /F1 8 Tf 0.38 0.46 0.52 rg 54 35 Td <{$footerHex}> Tj ET\n";
        $stream .= "BT /F2 8 Tf 0.10 0.31 0.39 rg 500 35 Td <{$pageNumberHex}> Tj ET\n";
        $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $streamId . ' 0 R >>';
        $objects[$streamId] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
    }
    $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . $count . ' >>';
    ksort($objects);

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $number => $object) {
        $offsets[$number] = strlen($pdf);
        $pdf .= $number . " 0 obj\n" . $object . "\nendobj\n";
    }
    $xref = strlen($pdf);
    $size = max(array_keys($objects)) + 1;
    $pdf .= "xref\n0 {$size}\n0000000000 65535 f \n";
    for ($i = 1; $i < $size; $i++) {
        $pdf .= sprintf("%010d 00000 %s \n", $offsets[$i] ?? 0, isset($offsets[$i]) ? 'n' : 'f');
    }
    return $pdf . "trailer\n<< /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
}
