<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day03 extends DayBase
{
    protected const int TEST_1 = 357;
    protected const int TEST_2 = 3121910778619;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $key => $row) {
            $this->data[$key] = str_split($row);
        }

//        print_r($this->data);
//        echo PHP_EOL;
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
        if ($this->test) {
            echo '=========================' . PHP_EOL;
            echo 'Test 1' . PHP_EOL;
            echo '=========================' . PHP_EOL;
        }

        return array_sum($this->getMaximumJoltages(2));
    }

    private function getPart2(): int
    {
        if ($this->test) {
            echo '=========================' . PHP_EOL;
            echo 'Test 2' . PHP_EOL;
            echo '=========================' . PHP_EOL;
        }

        return array_sum($this->getMaximumJoltages(12));
    }

    private function getMaximumJoltages(int $numberOfDigits): array
    {
        $joltages = [];

        foreach ($this->data as $digits) {
            $joltages[] = $this->findLargestJoltageNumber($digits, $numberOfDigits);
        }

        if ($this->test) {
            print_r($joltages);
        }

        return $joltages;
    }

    private function findLargestJoltageNumber(array $digits, int $numberOfDigits): int
    {
        if ($this->test) {
            echo '-----------------------------------------' . PHP_EOL;
            echo 'Digits: ' . implode('', $digits) . PHP_EOL;
            echo '-----------------------------------------' . PHP_EOL;
        }

        $numbers = [];
        $init = 0;

        for ($i = $numberOfDigits; $i > 0; $i--) {
            $digitArrange = array_slice($digits, $init, count($digits) - $init - $i + 1, true);

            if ($this->test) {
                echo 'Digits arrange slice init: ' . $init . PHP_EOL;
                echo 'Digits arrange slice length: ' . (count($digits) - $init - $i + 1) . PHP_EOL;
                echo 'Digits arrange: ' . implode('', $digitArrange) . PHP_EOL;
            }

            arsort($digitArrange);

            $init = array_key_first($digitArrange);
            $numbers[] = $digitArrange[$init];

            $init++;
        }

        return (int) implode('', $numbers);
    }
}
