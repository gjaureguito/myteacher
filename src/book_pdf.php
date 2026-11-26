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
    $y = 786;
    $sectionNumber = 0;

    $ink = '0.10 0.20 0.30';
    $teal = '0.08 0.40 0.44';
    $bodyColor = '0.20 0.26 0.32';
    $muted = '0.42 0.49 0.54';
    $blueTint = '0.92 0.96 0.97';
    $warmTint = '0.99 0.96 0.90';
    $lineColor = '0.82 0.87 0.88';

    $wrap = static function (string $text, int $size, int $width): array {
        $text = trim($text);
        if ($text === '') return [];
        $maxChars = max(20, (int) ($width / max(4.3, $size * 0.48)));
        $lines = [];
        foreach (preg_split('/\\R/u', $text) ?: [] as $paragraph) {
            $words = preg_split('/\\s+/u', trim($paragraph)) ?: [];
            $current = '';
            foreach ($words as $word) {
                $candidate = $current === '' ? $word : $current . ' ' . $word;
                if ($current !== '' && strlen(study_pdf_ansi($candidate)) > $maxChars) {
                    $lines[] = $current;
                    $current = $word;
                } else {
                    $current = $candidate;
                }
            }
            if ($current !== '') $lines[] = $current;
            if (trim($paragraph) === '') $lines[] = '';
        }
        return $lines;
    };

    $rawText = static function (string $text, string $font, int $size, int $x, int $baseline, string $rgb) use (&$pages, &$pageIndex): void {
        $hex = strtoupper(bin2hex(study_pdf_ansi($text)));
        $pages[$pageIndex] .= "BT /{$font} {$size} Tf {$rgb} rg {$x} {$baseline} Td <{$hex}> Tj ET\n";
    };

    $rectangle = static function (int $x, int $bottom, int $width, int $height, string $rgb) use (&$pages, &$pageIndex): void {
        $pages[$pageIndex] .= "q {$rgb} rg {$x} {$bottom} {$width} {$height} re f Q\n";
    };

    $decoratePage = static function () use (&$pages, &$pageIndex, $unit, $level, $rawText, $rectangle, $ink, $teal): void {
        $rectangle(0, 834, 595, 8, $ink);
        $rectangle(54, 810, 3, 20, '0.91 0.55 0.25');
        $rawText('MYTEACHER  /  ENGLISH LEARNING', 'F2', 8, 66, 817, $teal);
        $tag = strtoupper($level) . '  /  UNIT ' . str_pad((string) ($unit['position'] ?? ''), 2, '0', STR_PAD_LEFT);
        $rectangle(452, 804, 89, 22, '0.92 0.96 0.97');
        $rawText($tag, 'F2', 8, 460, 812, $teal);
    };
    $decoratePage();

    $newPage = static function () use (&$pages, &$pageIndex, &$y, $decoratePage): void {
        $pages[] = '';
        $pageIndex++;
        $y = 786;
        $decoratePage();
    };

    $writeLines = static function (string $text, string $font, int $size, string $rgb, int $leading, int $width, int $x = 54) use (&$y, &$pages, &$pageIndex, $wrap, $newPage, $rawText): int {
        $lines = $wrap($text, $size, $width);
        foreach ($lines as $lineText) {
            if ($lineText === '') { $y -= max(5, $leading - 7); continue; }
            if ($y < 68) $newPage();
            $rawText($lineText, $font, $size, $x, $y, $rgb);
            $y -= $leading;
        }
        return count($lines);
    };

    $paragraph = static function (string $text, string $rgb = '0.20 0.26 0.32', int $size = 11) use (&$y, $writeLines): void {
        if (trim($text) === '') return;
        $writeLines($text, 'F1', $size, $rgb, 16, 487);
        $y -= 5;
    };

    $sectionHeader = static function (string $label) use (&$pages, &$pageIndex, &$y, &$sectionNumber, $newPage, $rawText, $rectangle, $ink, $teal): void {
        if ($y < 92) $newPage();
        $sectionNumber++;
        $number = str_pad((string) $sectionNumber, 2, '0', STR_PAD_LEFT);
        $rectangle(54, $y - 4, 3, 14, '0.91 0.55 0.25');
        $rawText($number, 'F2', 8, 64, $y, $teal);
        $rawText(strtoupper($label), 'F2', 10, 86, $y, $ink);
        $y -= 22;
    };

    $card = static function (string $label, string $text, string $tint = '0.92 0.96 0.97', string $font = 'F1', int $size = 11, string $rgb = '0.20 0.26 0.32') use (&$y, &$pages, &$pageIndex, $wrap, $newPage, $rectangle, $writeLines, $teal): void {
        if (trim($text) === '') return;
        $bodyLines = $wrap($text, $size, 451);
        $height = 15 + 12 + (count($bodyLines) * 15) + 12;
        if ($y - $height < 68) $newPage();
        $top = $y;
        $rectangle(54, $top - $height, 487, $height, $tint);
        $rectangle(54, $top - $height, 4, $height, $teal);
        $y = $top - 15;
        $writeLines(strtoupper($label), 'F2', 8, $teal, 11, 451, 70);
        $y -= 2;
        $writeLines($text, $font, $size, $rgb, 15, 451, 70);
        $y = $top - $height - 12;
    };

    $line = static function (int $x1, int $y1, int $x2, int $y2, string $rgb) use (&$pages, &$pageIndex): void {
        $pages[$pageIndex] .= "q {$rgb} RG {$x1} {$y1} m {$x2} {$y2} l S Q\n";
    };

    $writeLines((string) ($unit['title'] ?? 'English study notes'), 'F4', 25, $ink, 32, 487);
    $writeLines('A clear guide for study and reference', 'F1', 10, $muted, 15, 487);
    $y -= 2;
    $paragraph((string) ($unit['summary'] ?? ''), $muted, 10);
    $line(54, $y + 2, 541, $y + 2, $lineColor);
    $y -= 15;

    if ($chapter) {
        $card('Learning goal', (string) ($chapter['goal'] ?? ''), $warmTint);
        $sectionHeader('Grammar focus');
        $paragraph((string) ($chapter['explain'] ?? ''), $bodyColor, 11);
        $card('Form and use', (string) ($chapter['contrast'] ?? ''), $blueTint);
        if (!empty($chapter['foundation'])) {
            $sectionHeader('The idea behind the grammar');
            $paragraph((string) $chapter['foundation'], $bodyColor, 11);
        }
        $card('Model sentence', (string) ($chapter['example'] ?? ''), $warmTint, 'F3', 14, $ink);
        $sectionHeader('Read the example');
        $paragraph((string) ($chapter['reason'] ?? ''), $bodyColor, 11);

        $applicationNotes = [];
        if (!empty($chapter['practice']) && is_array($chapter['practice'])) {
            foreach ($chapter['practice'] as $item) {
                if (!is_array($item) || empty($item[2]) || !is_string($item[2])) continue;
                $note = trim($item[2]);
                if ($note !== '' && !in_array($note, $applicationNotes, true)) $applicationNotes[] = $note;
            }
        }
        if ($applicationNotes) {
            $notesText = implode('   •   ', $applicationNotes);
            $card('Language notes', $notesText, $blueTint);
        }
    } else {
        $theory = study_pdf_plain((string) ($unit['theory_html'] ?? ''));
        $card('Grammar focus', $theory !== '' ? $theory : (string) ($unit['summary'] ?? ''), $blueTint);
    }

    $count = count($pages);
    $objects = [
        1 => '<< /Type /Catalog /Pages 2 0 R >>',
        3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
        5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman /Encoding /WinAnsiEncoding >>',
        6 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold /Encoding /WinAnsiEncoding >>',
    ];
    $kids = [];
    foreach ($pages as $i => $body) {
        $pageId = 7 + ($i * 2);
        $streamId = $pageId + 1;
        $kids[] = $pageId . ' 0 R';
        $footer = 'MYTEACHER  /  ENGLISH GRAMMAR STUDY GUIDE';
        $footerHex = strtoupper(bin2hex(study_pdf_ansi($footer)));
        $pageNumber = 'PAGE ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) . '  /  ' . str_pad((string) $count, 2, '0', STR_PAD_LEFT);
        $pageNumberHex = strtoupper(bin2hex(study_pdf_ansi($pageNumber)));
        $stream = $body . "q 0.82 0.87 0.88 RG 54 52 m 541 52 l S Q\n";
        $stream .= "BT /F1 8 Tf 0.42 0.49 0.54 rg 54 35 Td <{$footerHex}> Tj ET\n";
        $stream .= "BT /F2 8 Tf 0.08 0.40 0.44 rg 474 35 Td <{$pageNumberHex}> Tj ET\n";
        $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R /F4 6 0 R >> >> /Contents ' . $streamId . ' 0 R >>';
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
