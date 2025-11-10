<?php
declare(strict_types=1);

// Osnovni include-i
require_once __DIR__ . '/../src/Lib/auth.php';      // session_init()
require_once __DIR__ . '/../src/Lib/helpers.php';   // esc_html(), selected(), ...
require_once __DIR__ . '/../src/Lib/books_data.php';// $BOOKS

// 1) Sesija
session_init();

// Inicijalizuj wishlist ako ne postoji
if (!isset($_SESSION['wishlist']) || !is_array($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// 2) CSRF token (da POST akcije nisu CSRF ranjive)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

// 3) Pomoćne funkcije
function wl_get(): array {
    return $_SESSION['wishlist'] ?? [];
}
function wl_has(int $id): bool {
    return in_array($id, $_SESSION['wishlist'] ?? [], true);
}
function wl_add(int $id): void {
    if (!wl_has($id)) {
        $_SESSION['wishlist'][] = $id;
    }
}
function wl_remove(int $id): void {
    $_SESSION['wishlist'] = array_values(array_filter(wl_get(), fn($x) => $x !== $id));
}
function wl_clear(): void {
    $_SESSION['wishlist'] = [];
}

// 4) Obrada akcija (POST): add / remove / clear
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedCsrf = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, $postedCsrf)) {
        http_response_code(400);
        die('Neispravan CSRF token.');
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id !== null && $id >= 0 && $id < count($BOOKS)) {
            wl_add($id);
        }
    } elseif ($action === 'remove') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id !== null) {
            wl_remove($id);
        }
    } elseif ($action === 'clear') {
        wl_clear();
    }

    // Post-Redirect-Get da se izbegne re-submit forme
    header('Location: /online_knjizara/public/wishlist.php' . (!empty($_GET) ? '?' . http_build_query($_GET) : ''));
    exit;
}

// 5) Priprema podataka za prikaz
$wishlistIds = wl_get();             // npr. [0, 3]
$wishlistCnt = count($wishlistIds);  // broj stavki

// Opciona pretraga i sort da zadržiš isti UX kao books.php (minimalno)
$q        = $_GET['q']        ?? null;
$sortBy   = $_GET['sort_by']  ?? 'naslov';
$sortDir  = $_GET['sort_dir'] ?? 'asc';

// Minimalna inline sort/filter (da ne uvlačimo dodatne fajlove)
$items = $BOOKS;
if ($q !== null && $q !== '') {
    $qLower = mb_strtolower($q);
    $items = array_values(array_filter($items, function($b) use ($qLower) {
        return mb_stripos($b['naslov'], $qLower) !== false
            || mb_stripos($b['autor'],  $qLower) !== false;
    }));
}
$allowed = ['naslov','autor','cena'];
$sortBy  = in_array($sortBy, $allowed, true) ? $sortBy : 'naslov';
usort($items, function($a, $b) use ($sortBy, $sortDir) {
    $av = $a[$sortBy]; $bv = $b[$sortBy];
    $cmp = is_string($av) ? strcasecmp((string)$av, (string)$bv) : ($av <=> $bv);
    return $sortDir === 'desc' ? -$cmp : $cmp;
});

// 6) Prikaz
$page_title = 'Lista želja (sesija)';
require __DIR__ . '/../Views/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<form method="get">
    <header class="controls">
        <input name="q" placeholder="pretraga (naslov/autor)" value="<?= esc_html($_GET['q'] ?? '') ?>">
        <select name="sort_by">
            <option value="naslov" <?= selected('naslov', $sortBy) ?>>Naslov</option>
            <option value="autor"  <?= selected('autor',  $sortBy) ?>>Autor</option>
            <option value="cena"   <?= selected('cena',   $sortBy) ?>>Cena</option>
        </select>
        <select name="sort_dir">
            <option value="asc"  <?= selected('asc',  $sortDir) ?>>Rastuće</option>
            <option value="desc" <?= selected('desc', $sortDir) ?>>Opadajuće</option>
        </select>
        <button>Primeni</button>
        <a href="?">Reset</a>
    </header>
</form>

<div style="margin:10px 0; padding:8px; background:#f5f5f5; border:1px solid #ddd;">
    <strong>Wishlist:</strong> <?= (int)$wishlistCnt ?> stavki
    <?php if ($wishlistCnt > 0): ?>
        <form method="post" style="display:inline-block; margin-left:12px;">
            <input type="hidden" name="csrf" value="<?= esc_html($csrf) ?>">
            <input type="hidden" name="action" value="clear">
            <button>Očisti wishlist</button>
        </form>
    <?php endif; ?>
</div>

<table>
    <thead>
    <tr>
        <th>Naslov</th>
        <th>Autor</th>
        <th class="price">Cena (KM)</th>
        <th>Akcija</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $idx => $b): ?>
        <tr>
            <td><?= esc_html($b['naslov']) ?></td>
            <td><?= esc_html($b['autor']) ?></td>
            <td class="price"><?= esc_html(number_format((float)$b['cena'], 2, ',', '.')) ?></td>
            <td>
                <?php if (wl_has($idx)): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= esc_html($csrf) ?>">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="id" value="<?= (int)$idx ?>">
                        <button>Ukloni</button>
                    </form>
                <?php else: ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?= esc_html($csrf) ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?= (int)$idx ?>">
                        <button>Dodaj</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p style="margin-top:10px;color:#666">
    *Stanje liste želja se čuva u sesiji: <code>$_SESSION['wishlist']</code> = [indeksi knjiga].
</p>

<?php require __DIR__ . '/../Views/partials/footer.php'; ?>
