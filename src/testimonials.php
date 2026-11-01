<?php
declare(strict_types=1);

/** Public attribution uses only the first whitespace-separated given name. */
function testimonial_first_name(string $name): string
{
    $parts = preg_split('/[\s\p{Z}]+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    return mb_substr($parts[0] ?? 'Alumno', 0, 80);
}
