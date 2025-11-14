<?php
declare(strict_types=1);

require_once __DIR__ . '/../Lib/db.php';

/** ========== POMOĆNE ========== */
function authors_all(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, name FROM authors ORDER BY name");
    return $stmt->fetchAll();
}

/** ========== VALIDACIJA ========== */
function book_validate(array $in): array {
    $errors = [];

    // title
    $title = trim((string)($in['title'] ?? ''));
    if ($title === '' || mb_strlen($title) < 2 || mb_strlen($title) > 255) {
        $errors['title'] = 'Naslov je obavezan (2–255).';
    }

    // author_id
    $author_id = filter_var($in['author_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$author_id || $author_id < 1) {
        $errors['author_id'] = 'Izaberite autora.';
    }

    // price
    $price = (string)($in['price'] ?? '');
    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors['price'] = 'Cena mora biti nenegativan broj.';
    }

    // stock_qty
    $stock_qty = $in['stock_qty'] ?? '';
    if ($stock_qty === '' || filter_var($stock_qty, FILTER_VALIDATE_INT) === false || (int)$stock_qty < 0) {
        $errors['stock_qty'] = 'Količina mora biti nenegativan ceo broj.';
    }

    // isbn13 (opciono)
    $isbn13 = trim((string)($in['isbn13'] ?? ''));
    if ($isbn13 !== '' && !preg_match('/^\d{10,13}$/', $isbn13)) {
        $errors['isbn13'] = 'ISBN treba da bude 10–13 cifara (bez crtica).';
    }

    // published_at (opciono YYYY-MM-DD)
    $published_at = trim((string)($in['published_at'] ?? ''));
    if ($published_at !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $published_at)) {
        $errors['published_at'] = 'Datum mora biti u formatu YYYY-MM-DD.';
    }

    return [$errors, [
        'title'        => $title,
        'author_id'    => (int)$author_id,
        'price'        => number_format((float)$price, 2, '.', ''), // canonical
        'stock_qty'    => (int)$stock_qty,
        'isbn13'       => $isbn13 !== '' ? $isbn13 : null,
        'published_at' => $published_at !== '' ? $published_at : null,
    ]];
}

/** ========== READ ========== */
function books_all(PDO $pdo): array {
    $sql = "SELECT b.id, b.title, a.name AS author, b.price, b.stock_qty
            FROM books b
            JOIN authors a ON a.id = b.author_id
            ORDER BY b.title";
    return $pdo->query($sql)->fetchAll();
}

function book_find(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** ========== CREATE ========== */
function book_create(PDO $pdo, array $data): int {
    [$errors, $clean] = book_validate($data);
    if ($errors) { throw new InvalidArgumentException(json_encode($errors, JSON_UNESCAPED_UNICODE)); }

    $sql = "INSERT INTO books (title, author_id, price, stock_qty, isbn13, published_at)
            VALUES (:title, :author_id, :price, :stock_qty, :isbn13, :published_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'        => $clean['title'],
        ':author_id'    => $clean['author_id'],
        ':price'        => $clean['price'],
        ':stock_qty'    => $clean['stock_qty'],
        ':isbn13'       => $clean['isbn13'],
        ':published_at' => $clean['published_at'],
    ]);
    return (int)$pdo->lastInsertId();
}

/** ========== UPDATE ========== */
function book_update(PDO $pdo, int $id, array $data): void {
    [$errors, $clean] = book_validate($data);
    if ($errors) { throw new InvalidArgumentException(json_encode($errors, JSON_UNESCAPED_UNICODE)); }

    $sql = "UPDATE books
            SET title=:title, author_id=:author_id, price=:price, stock_qty=:stock_qty,
                isbn13=:isbn13, published_at=:published_at
            WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'        => $clean['title'],
        ':author_id'    => $clean['author_id'],
        ':price'        => $clean['price'],
        ':stock_qty'    => $clean['stock_qty'],
        ':isbn13'       => $clean['isbn13'],
        ':published_at' => $clean['published_at'],
        ':id'           => $id,
    ]);
}

/** ========== DELETE ========== */
function book_delete(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id=:id");
    $stmt->execute([':id' => $id]);
}
