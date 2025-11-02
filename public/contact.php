<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../src/Lib/helpers.php';

// 1) CSRF token (osnovna zaštita forme)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

// 2) Priprema
$errors = [];
$data = [
    'name'    => '',
    'email'   => '',
    'subject' => '',
    'message' => '',
    // honeypot (protiv botova; korisnici ga ne vide)
    'website' => '',
];

$sent = false;

// 3) Obrada POST-a
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF provera
    $postedCsrf = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, $postedCsrf)) {
        $errors['csrf'] = 'Neispravan bezbednosni token. Osvežite stranicu i pokušajte ponovo.';
    }

    // Učitavanje i trim (FILTER_UNSAFE_RAW + ručni trim; escape radimo pri ispisu)
    $data['name']    = trim((string)($_POST['name']    ?? ''));
    $data['email']   = trim((string)($_POST['email']   ?? ''));
    $data['subject'] = trim((string)($_POST['subject'] ?? ''));
    $data['message'] = trim((string)($_POST['message'] ?? ''));
    $data['website'] = trim((string)($_POST['website'] ?? '')); // honeypot

    // Honeypot: ako je popunjen, tretiraj kao spam
    if ($data['website'] !== '') {
        $errors['website'] = 'Greška u formi.';
    }

    // Validacija
    if ($data['name'] === '' || mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 60) {
        $errors['name'] = 'Ime je obavezno (2–60 karaktera).';
    }

    if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Unesite ispravan e-mail.';
    }

    if ($data['subject'] !== '' && mb_strlen($data['subject']) > 120) {
        $errors['subject'] = 'Naslov je predugačak (max 120).';
    }

    if ($data['message'] === '' || mb_strlen($data['message']) < 10 || mb_strlen($data['message']) > 2000) {
        $errors['message'] = 'Poruka je obavezna (10–2000 karaktera).';
    }

    // Ako nema grešaka: upiši u log i prikaži poruku o uspehu
    if (!$errors) {
        // Minimalan audit log (za master rad): /storage/logs/contact.log
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/contact.log';

        $row = [
            'ts'      => date('Y-m-d H:i:s'),
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '-',
            'name'    => $data['name'],
            'email'   => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ];
        @file_put_contents($logFile, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

        $sent = true;

        // Po želji: reset polja posle slanja
        $data['subject'] = '';
        $data['message'] = '';
        // (ime i email često ostavljamo sticky)
    }
}

// View vars
$page_title = 'Kontakt';
require __DIR__ . '/../Views/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<?php if ($sent): ?>
    <div style="padding:10px; background:#e8f5e9; border:1px solid #a5d6a7; margin:12px 0;">
        Poruka je uspešno poslata. Hvala na kontaktu!
    </div>
<?php endif; ?>

<?php if ($errors && !$sent): ?>
    <div style="padding:10px; background:#fff3e0; border:1px solid #ffcc80; margin:12px 0;">
        <strong>Molimo ispravite sledeće:</strong>
        <ul style="margin:6px 0 0 18px;">
            <?php foreach ($errors as $field => $msg): ?>
                <li><?= esc_html($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" novalidate>
    <input type="hidden" name="csrf" value="<?= esc_html($csrf) ?>">
    <!-- honeypot polje (sakriveno CSS-om) -->
    <div style="position:absolute; left:-9999px;">
        <label>Website <input type="text" name="website" value="<?= esc_html($data['website']) ?>"></label>
    </div>

    <div style="margin-bottom:10px;">
        <label>Ime i prezime<br>
            <input type="text" name="name" required
                   value="<?= esc_html($data['name']) ?>"
                   style="width:100%; max-width:480px;">
        </label>
        <?php if (isset($errors['name'])): ?>
            <div style="color:#c62828;"><?= esc_html($errors['name']) ?></div>
        <?php endif; ?>
    </div>

    <div style="margin-bottom:10px;">
        <label>E-mail<br>
            <input type="email" name="email" required
                   value="<?= esc_html($data['email']) ?>"
                   style="width:100%; max-width:480px;">
        </label>
        <?php if (isset($errors['email'])): ?>
            <div style="color:#c62828;"><?= esc_html($errors['email']) ?></div>
        <?php endif; ?>
    </div>

    <div style="margin-bottom:10px;">
        <label>Naslov (opciono)<br>
            <input type="text" name="subject"
                   value="<?= esc_html($data['subject']) ?>"
                   style="width:100%; max-width:480px;">
        </label>
        <?php if (isset($errors['subject'])): ?>
            <div style="color:#c62828;"><?= esc_html($errors['subject']) ?></div>
        <?php endif; ?>
    </div>

    <div style="margin-bottom:10px;">
        <label>Poruka<br>
            <textarea name="message" required rows="6"
                      style="width:100%; max-width:640px;"><?= esc_html($data['message']) ?></textarea>
        </label>
        <?php if (isset($errors['message'])): ?>
            <div style="color:#c62828;"><?= esc_html($errors['message']) ?></div>
        <?php endif; ?>
    </div>

    <button>Pošalji</button>
</form>

<p style="margin-top:12px; color:#666;">
    *Svi prikazi podataka idu kroz <code>htmlspecialchars</code> (<code>esc_html()</code>) radi XSS zaštite.
</p>

<?php require __DIR__ . '/../Views/partials/footer.php'; ?>
