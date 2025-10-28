<?php
declare(strict_types=1);
/** @var array $items */

require_once __DIR__ . '/../src/Lib/helpers.php'; // bezbedno, jednom
$page_title = 'Knjige';

require __DIR__ . '/partials/header.php';
?>
<h1><?= esc_html($page_title) ?></h1>

<form method="get">
    <header class="controls">
        <input name="q" placeholder="pretraga (naslov/autor)" value="<?= esc_html(get_qs('q')) ?>">

        <?php
        $sortBy  = get_qs('sort_by', 'naslov');
        $sortDir = get_qs('sort_dir', 'asc');
        ?>

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

<table>
    <thead>
    <tr>
        <th>Naslov</th>
        <th>Autor</th>
        <th class="price">Cena (KM)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $b): ?>
        <tr>
            <td><?= esc_html($b['naslov']) ?></td>
            <td><?= esc_html($b['autor']) ?></td>
            <td class="price"><?= esc_html(format_cena((float)$b['cena'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p style="margin-top:10px;color:#666">
    Izvor: <b>PHP niz</b>. U sledećim danima prelazimo na MySQL (isti UI).
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>
