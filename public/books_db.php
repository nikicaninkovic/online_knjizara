<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Lib/db.php';
require_once __DIR__ . '/../src/Lib/helpers.php';

try {
    $pdo = db();
    $sql = "SELECT b.id, b.title, a.name AS author, b.price
            FROM books b
            JOIN authors a ON a.id = b.author_id
            ORDER BY b.title ASC";
    $stmt = $pdo->query($sql);
    $books = $stmt->fetchAll();
} catch (Throwable $e) {
    die("Greška pri čitanju baze: " . htmlspecialchars($e->getMessage()));
}
?>
<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <title>Knjige (iz baze)</title>
    <style>
        body { font-family: system-ui; margin: 2rem; }
        table { border-collapse: collapse; width: 60%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
<h1>📚 Knjige iz baze</h1>

<table>
    <thead>
    <tr><th>Naslov</th><th>Autor</th><th>Cena (KM)</th></tr>
    </thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= esc_html($b['title']) ?></td>
            <td><?= esc_html($b['author']) ?></td>
            <td><?= esc_html(format_cena((float)$b['price'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
