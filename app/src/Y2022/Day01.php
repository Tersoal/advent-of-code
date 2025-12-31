<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day01 extends DayBase
{
    protected const int TEST_1 = 24000;
    protected const int TEST_2 = 45000;

    private array $calories = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $index = 0;

        foreach ($this->data as $line) {
            if (empty($line)) {
                $index++;

                continue;
            }

            $this->calories[$index][] = (int)$line;
        }

        if ($this->test) {
            print_r($this->data);
            print_r($this->calories);
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
        return $this->getTotalCalories(1);
    }

    private function getPart2(): int
    {
        return $this->getTotalCalories(3);
    }

    private function getTotalCalories(int $mostCaloriesCount): int
    {
        $totalCalories = array_map(fn($calories) => array_sum($calories), $this->calories);

        sort($totalCalories);

        return array_sum(array_slice($totalCalories, -$mostCaloriesCount));
    }
}
