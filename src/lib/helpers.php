<?php
declare(strict_types=1);

/**
 * Bezbedan HTML ispis (XSS zaštita).
 */
function esc_html(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Format cene (npr. 1234.5 -> "1.234,50").
 */
function format_cena(float $v): string {
    return number_format($v, 2, ',', '.');
}

/**
 * Čitanje GET parametra uz podrazumevanu vrednost.
 */
function get_qs(string $key, ?string $default = ''): string {
    return isset($_GET[$key]) ? (string)$_GET[$key] : (string)$default;
}

/**
 * Prosta pomoćna za aktivnu opciju u <select>.
 */
function selected(string $value, string $current): string {
    return $value === $current ? 'selected' : '';
}
