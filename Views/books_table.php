<?php
declare(strict_types=1);
/** @var array $items */
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Knjige</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .wrap { max-width: 880px; margin:auto; }
        header { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom: 16px; }
        input, select, button, a { padding:6px 8px; }
        table { border-collapse: collapse; width:100%; }
        th, td { border:1px solid #ddd; padding:8px; }
        th { background:#f7f7f7; text-align:left; }
        .price { text-align:right; white-space:nowrap; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Knjige</h1>

    <form method="get">
        <header>
            <input name="q" placeholder="pretraga (naslov/autor)" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <select name="sort_by">
                <?php
                $sortBy = $_GET['sort_by'] ?? 'naslov';
                foreach (['naslov'=>'Naslov','autor'=>'Autor','cena'=>'Cena'] as $k=>$label) {
                    $sel = $k===$sortBy ? 'selected' : '';
                    echo "<option value=\"$k\" $sel>$label</option>";
                }
                ?>
            </select>
            <select name="sort_dir">
                <?php
                $sortDir = $_GET['sort_dir'] ?? 'asc';
                foreach (['asc'=>'Rastuće','desc'=>'Opadajuće'] as $k=>$label) {
                    $sel = $k===$sortDir ? 'selected' : '';
                    echo "<option value=\"$k\" $sel>$label</option>";
                }
                ?>
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
                <td><?= htmlspecialchars($b['naslov']) ?></td>
                <td><?= htmlspecialchars($b['autor']) ?></td>
                <td class="price"><?= number_format((float)$b['cena'], 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top:10px;color:#666">
        Izvor: <b>PHP niz</b>. U sledećim danima prelazimo na MySQL (isti UI).
    </p>
</div>
</body>
</html>
