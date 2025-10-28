<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title><?= esc_html($page_title ?? 'Online knjižara') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .wrap { max-width: 880px; margin:auto; }
        header.controls { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:16px; }
        input, select, button, a { padding:6px 8px; }
        table { border-collapse: collapse; width:100%; }
        th, td { border:1px solid #ddd; padding:8px; }
        th { background:#f7f7f7; text-align:left; }
        .price { text-align:right; white-space:nowrap; }
    </style>
</head>
<body>
<div class="wrap">
