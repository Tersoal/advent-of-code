<?php

namespace App;

require_once 'autoload.php';

if ($argc != 4) {
    echo "Execute command as: php app.php <year> <day> <test>\n";
    exit(1);
}

$year = (int)$argv[1];
$day = (int)$argv[2];
$test = (string)$argv[3];

$app = new Main($year, $day, $test);
$app->execute();
