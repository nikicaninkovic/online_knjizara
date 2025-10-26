<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Namerno izazivamo nekoliko grešaka da proverimo prikaz i log
echo $nePostoji;                           // Notice/Warning
include 'fajl_koji_ne_postoji.php';        // Warning
strpos();                                  // Warning: missing arguments

// Fatal primer – otkomentariši ovu liniju da vidiš fatalnu grešku
// nepoznata_funkcija();
