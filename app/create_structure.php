<?php

namespace App;

require __DIR__ . '/vendor/autoload.php';

if ($argc != 3) {
    echo "Execute command as: `php create_structure.php <year> <day>`" . PHP_EOL;
    exit(1);
}

$year = (int)$argv[1];
$day = (int)$argv[2];

if ($day < 0 || $day > 25) {
    echo "Day must be 1 to 25, or 0 to create all days" . PHP_EOL;
    exit(1);
}

createDataStructure($year, $day);
createClassStructure($year, $day);

function createDataStructure(int $year, int $day): void
{
    $yearDataFolderPath = __DIR__ . "/data/$year";
    if (!is_dir($yearDataFolderPath)) {
        mkdir($yearDataFolderPath);
    }

    $days = [$day];
    if ($day === 0) {
        $days = range(1, 25);
    }

    foreach ($days as $day) {
        $dayName = strlen($day) === 1 ? "0" . $day : (string)$day;
        $dayDataFolderPath = $yearDataFolderPath . "/day$dayName";

        if (!is_dir($dayDataFolderPath)) {
            mkdir($dayDataFolderPath);
        }

        $testFilePath = $dayDataFolderPath . "/day$dayName-test.txt";
        if (!is_file($testFilePath)) {
            touch($testFilePath);
        }

        $filePath = $dayDataFolderPath . "/day$dayName.txt";
        if (!is_file($filePath)) {
            touch($filePath);
        }
    }
}

function createClassStructure(int $year, int $day): void
{
    $yearFolderPath = __DIR__ . "/src/Y$year";
    if (!is_dir($yearFolderPath)) {
        mkdir($yearFolderPath);
    }

    $days = [$day];
    if ($day === 0) {
        $days = range(1, 25);
    }

    $classTplFilePath = __DIR__ . "/templates/DayXX.tpl";
    $classTplContent = file_get_contents($classTplFilePath);

    foreach ($days as $day) {
        $dayName = strlen($day) === 1 ? "0" . $day : (string)$day;
        $classFilePath = $yearFolderPath . "/Day$dayName.php";

        if (is_file($classFilePath)) {
            continue;
        }

        $classFileContent = $classTplContent;
        $classFileContent = str_replace("class DayXX extends DayBase", "class Day$dayName extends DayBase", $classFileContent);
        $classFileContent = str_replace("namespace App\YXXXX;", "namespace App\Y$year;", $classFileContent);

        file_put_contents($classFilePath, $classFileContent);
    }
}


