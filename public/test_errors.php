<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Lib/errors.php';

// Namerni WARNING (undefined var)
echo $nePostoji;

// Namerni WARNING (include fail)
@include __DIR__ . '/ne_postoji.php';

// Namerni EXCEPTION
throw new RuntimeException('Namerni test exception (Dan 6).');
