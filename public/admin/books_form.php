<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Lib/auth.php';
require_once __DIR__ . '/../../src/Lib/helpers.php';
require_once __DIR__ . '/../../src/Lib/errors.php'; // opciono
require_once __DIR__ . '/../../src/Models/Books.php';

session_init();
require_login();
$pdo = db();

// CSRF token
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$errors = [];
$authors = authors_all($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $errors['_'] = 'Neispravan CSRF token.';
    } else {
        $payload = [
            'title'        => $_POST['title'] ?? '',
            'author_id'    => $_POST['author_id'] ?? '',
            'price'        => $_POST['price'] ?? '',
            'stock_qty'    => $_POST['stock_qty'] ?? '',
            'isbn13'       => $_POST['isbn13'] ?? '',
            'published_at' => $_POST['published_at'] ?? '',
        ];

        try {
            if ($editing) {
                book_update($pdo, $id, $payload);
            } else {
                $newId = book_create($pdo, $payload);
                $id = $newId; $editing = true;
            }
            header('Location: /online_knjizara/public/admin/books_admin.php');
            exit;
        } catch (InvalidArgumentException $ex) {
            $errors = json_decode($ex->getMessage(), true) ?: ['_' => 'Neispravni podaci.'];
        } catch (Throwable $e) {
            $errors = ['_' => 'Greška pri čuvanju: ' . $e->getMessage()];
        }
    }
}

$values = [
    'title'        => '',
    'author_id'    => '',
    'price'        => '',
    'stock_qty'    => '',
    'isbn13'       => '',
    'published_at' => '',
];

if ($editing && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $row = book_find($pdo, $id);
    if (!$row) {
        header('Location: /online_knjizara/public/admin/books_admin.php'); exit;
    }
    $values = [
        'title'        => $row['title'],
        'author_id'    => (string)$row['author_id'],
        'price'        => (string)$row['price'],
        'stock_qty'    => (string)$row['stock_qty'],
        'isbn13'       => (string)($row['isbn13'] ?? ''),
        'published_at' => (string)($row['published_at'] ?? ''),
    ];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // vrati "sticky" vrednosti iz POST-a
    foreach ($values as $k => $_) { $values[$k] = (string)($_POST[$k] ?? ''); }
}

$page_title = $editing ? 'Izmena knjige' : 'Nova knjiga';
require __DIR__ . '/../../Views/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<?php if ($errors): ?>
    <div style="color:#c62828; margin-bottom:8px;">
        <?php foreach ($errors as $msg) echo esc_html($msg) . '<br>'; ?>
    </div>
<?php endif; ?>

<form method="post" style="max-width:520px">
    <input type="hidden" name="csrf" value="<?= esc_html($csrf) ?>">

    <label>Naslov<br>
        <input name="title" value="<?= esc_html($values['title']) ?>" required style="width:100%">
    </label><br><br>

    <label>Autor<br>
        <select name="author_id" required style="width:100%">
            <option value="">-- izaberi --</option>
            <?php foreach ($authors as $a): ?>
                <option value="<?= (int)$a['id'] ?>" <?= ((string)$a['id'] === $values['author_id']) ? 'selected' : '' ?>>
                    <?= esc_html($a['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Cena (KM)<br>
        <input name="price" type="number" step="0.01" min="0" value="<?= esc_html($values['price']) ?>" required>
    </label><br><br>

    <label>Količina na stanju<br>
        <input name="stock_qty" type="number" step="1" min="0" value="<?= esc_html($values['stock_qty']) ?>" required>
    </label><br><br>

    <label>ISBN-13 (opciono, 10–13 cifara)<br>
        <input name="isbn13" value="<?= esc_html($values['isbn13']) ?>">
    </label><br><br>

    <label>Datum objave (YYYY-MM-DD, opc.)<br>
        <input name="published_at" placeholder="YYYY-MM-DD" value="<?= esc_html($values['published_at']) ?>">
    </label><br><br>

    <button>Sačuvaj</button>
    &nbsp; <a href="/online_knjizara/public/admin/books_admin.php">Nazad</a>
</form>

<?php require __DIR__ . '/../../Views/partials/footer.php'; ?>
