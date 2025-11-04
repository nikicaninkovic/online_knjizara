<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Lib/auth.php';
require_once __DIR__ . '/../src/Lib/helpers.php';

// POKRENI sesiju sa sigurnim parametrima
session_init();

// --- Mock korisnik (NE koristiti u produkciji) ---
$MOCK_USER = [
    'id' => 1,
    'email' => 'admin@example.com',
    // u realnom svetu nikad ne čuvaj plain tekst lozinke
    'password' => 'tajna123' // samo za test/dev
];

// Ako je već prijavljen, možeš preusmeriti na zaštićenu stranicu
if (is_logged_in()) {
    header('Location: /online_knjizara/public/');
    exit;
}

$errors = [];
$posted = ['email'=>'', 'password'=>''];

// Obrada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted['email'] = trim((string)($_POST['email'] ?? ''));
    $posted['password'] = trim((string)($_POST['password'] ?? ''));

    if ($posted['email'] === '' || $posted['password'] === '') {
        $errors[] = 'Popunite email i lozinku.';
    } else {
        // PROSTA proverka protiv mock korisnika
        if ($posted['email'] === $MOCK_USER['email'] && $posted['password'] === $MOCK_USER['password']) {
            // login success
            login_user($MOCK_USER['id'], 'Administrator');
            header('Location: /online_knjizara/public/');
            exit;
        } else {
            $errors[] = 'Neispravan email ili lozinka.';
        }
    }
}

// View (jednostavan)
$page_title = 'Prijava (mock)';
require __DIR__ . '/../Views/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<?php if ($errors): ?>
    <div style="color:#c62828; margin-bottom:8px;">
        <?php foreach ($errors as $e) echo esc_html($e) . '<br>'; ?>
    </div>
<?php endif; ?>

<form method="post" style="max-width:420px">
    <div style="margin-bottom:8px;">
        <label>Email<br>
            <input type="email" name="email" value="<?= esc_html($posted['email']) ?>" style="width:100%">
        </label>
    </div>

    <div style="margin-bottom:8px;">
        <label>Lozinka<br>
            <input type="password" name="password" value="" style="width:100%">
        </label>
    </div>

    <button>Prijavi se</button>
</form>

<p style="margin-top:12px;color:#666">
    Mock nalog: <b>admin@example.com</b> / <b>tajna123</b> (samo za razvoj).
</p>

<?php require __DIR__ . '/../Views/partials/footer.php'; ?>
