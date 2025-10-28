<?php
declare(strict_types=1);

/**
 * Filtrira po pojmu (naslov ili autor).
 */
function books_filter(array $data, ?string $q): array {
    if ($q === null || $q === '') return array_values($data);
    $q = mb_strtolower($q);
    return array_values(array_filter($data, function($b) use ($q) {
        return mb_stripos($b['naslov'], $q) !== false
            || mb_stripos($b['autor'], $q) !== false;
    }));
}

/**
 * Sortira po: naslov | autor | cena ; smer: asc | desc
 */
function books_sort(array $data, string $by = 'naslov', string $dir = 'asc'): array {
    $by = in_array($by, ['naslov','autor','cena'], true) ? $by : 'naslov';
    $dir = $dir === 'desc' ? 'desc' : 'asc';

    usort($data, function($a, $b) use ($by, $dir) {
        $av = $a[$by]; $bv = $b[$by];
        $cmp = is_string($av) ? strcasecmp((string)$av, (string)$bv) : ($av <=> $bv);
        return $dir === 'asc' ? $cmp : -$cmp;
    });
    return $data;
}
