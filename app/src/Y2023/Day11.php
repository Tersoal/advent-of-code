<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day11 extends DayBase
{
    protected const int TEST_1 = 374;
    protected const int TEST_2 = 0;

    protected array $map = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);

//        $this->printMap($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getSumOfLengths(2), $this->getSumOfLengths(1000000)];
    }

    private function getSumOfLengths(int $expandRate): int
    {
        $rowsToExpand = $this->getRowsToExpand();
        $colsToExpand = $this->getColsToExpand();
        $galaxies = $this->getGalaxies();
//        print_r($galaxies);

        $lengths = [];

        foreach ($galaxies as $g => $currentGalaxy) {
            foreach ($galaxies as $key => $galaxy) {
                if ($g === $key) {
                    continue;
                }

                $pair = $g . '-' . $key;
                $pairReverse = $key . '-' . $g;
                if (array_key_exists($pair, $lengths) || array_key_exists($pairReverse, $lengths)) {
                    continue;
                }

                $length = abs($currentGalaxy[0] - $galaxy[0]) + abs($currentGalaxy[1] - $galaxy[1]);

                $rowsRange = range($currentGalaxy[0], $galaxy[0]);
                foreach ($rowsToExpand as $row) {
                    if (in_array($row, $rowsRange)) {
                        $length += $expandRate - 1;
                    }
                }

                $colsRange = range($currentGalaxy[1], $galaxy[1]);
                foreach ($colsToExpand as $col) {
                    if (in_array($col, $colsRange)) {
                        $length += $expandRate - 1;
                    }
                }

                $lengths[$pair] = $length;
            }
        }

//        print_r($lengths);
//        echo PHP_EOL;
//        echo 'Son ' . count($lengths) . PHP_EOL;
//        echo PHP_EOL;

        return array_sum($lengths);
    }

    private function getRowsToExpand(): array
    {
        $rowsToExpand = [];

        foreach ($this->data as $key => $row) {
            if (array_all($row, fn(string $pos): bool => $pos === self::FREE)) {
                $rowsToExpand[] = $key;
            }
        }

        return $rowsToExpand;
    }

    private function getColsToExpand(): array
    {
        $colsToExpand = [];

        for ($i = 0; $i < count($this->data[0]); $i++) {
            if (array_all(array_column($this->data, $i), fn(string $pos): bool => $pos === self::FREE)) {
                $colsToExpand[] = $i;
            }
        }

        return $colsToExpand;
    }

    private function getGalaxies(): array
    {
        $galaxies = [];

        for ($y = 0; $y < count($this->data); $y++) {
            for ($x = 0; $x < count($this->data[$y]); $x++) {
                if ($this->data[$y][$x] !== self::WALL) {
                    continue;
                }

                $galaxies[] = [$y, $x];
            }
        }

        return $galaxies;
    }
}
