<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Lib/auth.php';

// Pokreni sesiju (da pristupiš sessionu)
session_init();

// Logout
logout_user();

// Preusmeri na login ili početnu
header('Location: /online_knjizara/public/login_mock.php');
exit;
