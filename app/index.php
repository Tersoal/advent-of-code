<?php

namespace App;

require __DIR__ . '/vendor/autoload.php';

if ($argc != 4) {
    echo "Execute command as: `php index.php <year> <day> <test>`" . PHP_EOL;
    exit(1);
}

$year = (int)$argv[1];
$day = (int)$argv[2];
$test = (string)$argv[3];

$app = new Main($year, $day, $test);
$app->execute();
