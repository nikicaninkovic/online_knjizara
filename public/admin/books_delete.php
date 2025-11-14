<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Lib/auth.php';
require_once __DIR__ . '/../../src/Models/Books.php';

session_init();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /online_knjizara/public/admin/books_admin.php'); exit;
}

if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    header('Location: /online_knjizara/public/admin/books_admin.php'); exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id) {
    $pdo = db();
    try {
        book_delete($pdo, (int)$id);
    } catch (Throwable $e) {
        // po želji: flash poruka u sesiji
    }
}

header('Location: /online_knjizara/public/admin/books_admin.php');
exit;
