<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day04 extends DayBase
{
    protected const int TEST_1 = 2;
    protected const int TEST_2 = 4;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->data = array_map(function ($line) {
            $pairs = explode(',', $line);
            return array_map(fn ($pair) => explode('-', $pair), $pairs);
        }, $this->data);

        if ($this->test) {
            print_r($this->data);
            echo PHP_EOL;
        }
    }

    public function getResult(): array
    {
        return [
            $this->getPart1(),
            $this->getPart2()
        ];
    }

    private function getPart1(): int
    {
        $containingPairsCount = 0;

        foreach ($this->data as $pairs) {
            $pair1 = range($pairs[0][0], $pairs[0][1]);
            $pair2 = range($pairs[1][0], $pairs[1][1]);

            if (array_all($pair1, fn ($value) => in_array($value, $pair2)) || array_all($pair2, fn ($value) => in_array($value, $pair1))) {
                $containingPairsCount++;
            }
        }

        return $containingPairsCount;
    }

    private function getPart2(): int
    {
        $overlapPairsCount = 0;

        foreach ($this->data as $pairs) {
            $pair1 = range($pairs[0][0], $pairs[0][1]);
            $pair2 = range($pairs[1][0], $pairs[1][1]);

            if (array_intersect($pair1, $pair2)) {
                $overlapPairsCount++;
            }
        }

        return $overlapPairsCount;
    }
}
