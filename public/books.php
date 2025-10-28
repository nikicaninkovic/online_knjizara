<?php
declare(strict_types=1);

require __DIR__ . '/../src/Lib/books_data.php';
require __DIR__ . '/../src/Lib/books_query.php';

$q        = $_GET['q']        ?? null;
$sort_by  = $_GET['sort_by']  ?? 'naslov';
$sort_dir = $_GET['sort_dir'] ?? 'asc';

$items = books_filter($BOOKS, $q);
$items = books_sort($items, $sort_by, $sort_dir);

require __DIR__ . '/../Views/books_table.php';
