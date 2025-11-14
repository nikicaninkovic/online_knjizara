<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Lib/auth.php';
require_once __DIR__ . '/../../src/Lib/helpers.php';
require_once __DIR__ . '/../../src/Lib/errors.php'; // opciono
require_once __DIR__ . '/../../src/Models/Books.php';

session_init();
require_login();

$pdo = db();
$books = books_all($pdo);

$page_title = 'Administracija — Knjige';
require __DIR__ . '/../../Views/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<p>
    <a href="/online_knjizara/public/admin/books_form.php">+ Dodaj knjigu</a>
</p>

<table>
    <thead>
    <tr><th>Naslov</th><th>Autor</th><th>Cena</th><th>Na stanju</th><th>Akcije</th></tr>
    </thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= esc_html($b['title']) ?></td>
            <td><?= esc_html($b['author']) ?></td>
            <td><?= esc_html(number_format((float)$b['price'], 2, ',', '.')) ?></td>
            <td><?= (int)$b['stock_qty'] ?></td>
            <td>
                <a href="/online_knjizara/public/admin/books_form.php?id=<?= (int)$b['id'] ?>">Izmeni</a>
                |
                <form method="post" action="/online_knjizara/public/admin/books_delete.php" style="display:inline;" onsubmit="return confirm('Obrisati ovu knjigu?');">
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <input type="hidden" name="csrf" value="<?= esc_html($_SESSION['csrf'] ?? ($_SESSION['csrf']=bin2hex(random_bytes(16)))) ?>">
                    <button>Obriši</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../Views/partials/footer.php'; ?>
