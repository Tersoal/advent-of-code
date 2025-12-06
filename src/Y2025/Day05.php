<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day05 extends DayBase
{
    protected const int TEST_1 = 3;
    protected const int TEST_2 = 14;

    protected array $freshIngredientIdRanges = [];
    protected array $ingredientIds = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $line) {
            if (empty($line)) {
                continue;
            }

            if (str_contains($line, '-')) {
                $range = explode('-', $line);
                $this->freshIngredientIdRanges[] = ['from'  => (int)$range[0], 'to' => (int)$range[1]];
            } else {
                $this->ingredientIds[] = (int)$line;
            }
        }

//        print_r($this->freshIngredientIdRanges);
//        print_r($this->ingredientIds);
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
        $fresh = [];

        foreach ($this->ingredientIds as $ingredientId) {
            foreach ($this->freshIngredientIdRanges as $range) {
                if ($ingredientId < $range['from']) {
                    continue;
                }

                if ($ingredientId > $range['to']) {
                    continue;
                }

                $fresh[] = $ingredientId;
                break;
            }
        }

        return count($fresh);
    }

    private function getPart2(): int
    {
        $uniqueFreshRanges = [];

        foreach ($this->freshIngredientIdRanges as $index => $range) {
            $from = $range['from'];
            $to = $range['to'];

            if (empty($uniqueFreshRanges)) {
                $uniqueFreshRanges[] = ['from' => $from, 'to' => $to];

                continue;
            }

            $this->getUniqueFreshRanges($index, $from, $to, $uniqueFreshRanges);
        }

        print_r($uniqueFreshRanges);

        $freshCount = 0;

        foreach ($uniqueFreshRanges as $uniqueFreshRange) {
            $freshCount += $uniqueFreshRange['to'] - $uniqueFreshRange['from'] + 1;
        }

        return $freshCount;
    }

    private function getUniqueFreshRanges(int $index, int $from, int $to, array &$uniqueFreshRanges): void
    {
        foreach ($uniqueFreshRanges as $uniqueFreshRange) {
            if ($from >= $uniqueFreshRange['from'] && $from <= $uniqueFreshRange['to'] && $to <= $uniqueFreshRange['to']) {
                return;
            }

            if ($from < $uniqueFreshRange['from'] && $to > $uniqueFreshRange['to']) {

                echo 'Range ' . $from . ' - ' . $to . ' at line ' . $index + 1 . ' is wider than ' . $uniqueFreshRange['from'] . ' - ' . $uniqueFreshRange['to'] . PHP_EOL;

                $this->getUniqueFreshRanges($index, $from, $uniqueFreshRange['from'] - 1, $uniqueFreshRanges);
                $this->getUniqueFreshRanges($index, $uniqueFreshRange['to'] + 1, $to, $uniqueFreshRanges);

                return;
            }

            if ($from > $uniqueFreshRange['to']) {
                continue;
            }

            if ($from < $uniqueFreshRange['from'] && $to < $uniqueFreshRange['from']) {
                continue;
            }

            if ($from < $uniqueFreshRange['from'] && $to <= $uniqueFreshRange['to']) {

                echo 'Range ' . $from . ' - ' . $to . ' at line ' . $index + 1 . ' overlaps ' . $uniqueFreshRange['from'] . ' - ' . $uniqueFreshRange['to'] . PHP_EOL;

                $this->getUniqueFreshRanges($index, $from, $uniqueFreshRange['from'] - 1, $uniqueFreshRanges);

                return;
            }

            if ($from >= $uniqueFreshRange['from'] && $from <= $uniqueFreshRange['to'] && $to > $uniqueFreshRange['to']) {

                echo 'Range ' . $from . ' - ' . $to . ' at line ' . $index + 1 . ' overlaps ' . $uniqueFreshRange['from'] . ' - ' . $uniqueFreshRange['to'] . PHP_EOL;

                $this->getUniqueFreshRanges($index, $uniqueFreshRange['to'] + 1, $to, $uniqueFreshRanges);

                return;
            }
        }

        $uniqueFreshRanges[] = ['from' => $from, 'to' => $to];
    }
}
