<?php
declare(strict_types=1);
date_default_timezone_set('Europe/Sarajevo');

function esc_html(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$today = date('d-m-Y H:i:s');
?>
<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <title>Hello, PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
        code { background: #f6f8fa; padding: 2px 4px; border-radius: 4px; }
    </style>
</head>
<body>
<h1>Hello, PHP 👋</h1>
<p>Danas je: <strong><?= esc_html($today) ?></strong></p>
<p>Testiraj PHP info stranicu: <a href="./phpinfo.php">phpinfo()</a></p>
</body>
</html>
