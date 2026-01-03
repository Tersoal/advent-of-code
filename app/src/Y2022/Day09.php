<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day09 extends DayBase
{
    protected const int TEST_1 = 13;
    protected const int TEST_2 = 1;

    private array $directions = [
        self::DIRECTION_RIGHT => [0, 1],
        self::DIRECTION_DOWN => [1, 0],
        self::DIRECTION_LEFT => [0, -1],
        self::DIRECTION_UP => [-1, 0],
    ];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

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
        $tailPositions = array_fill(0, 2, [0, 0]);
        $tailSteps = ['0|0' => 1]; // Staring position counts as tail step

        $tailSteps = $this->getTailSteps($tailPositions, $tailSteps);

        if ($this->test) {
            print_r($tailSteps);
            echo PHP_EOL;
        }

        return count($tailSteps);
    }

    private function getPart2(): int
    {
        $tailPositions = array_fill(0, 10, [0, 0]);
        $tailSteps = ['0|0' => 1]; // Staring position counts as tail step

        $tailSteps = $this->getTailSteps($tailPositions, $tailSteps);

        if ($this->test) {
            print_r($tailSteps);
            echo PHP_EOL;
        }

        return count($tailSteps);
    }

    private function getTailSteps(array $tailPositions, array $tailSteps): array
    {
        foreach ($this->data as $move) {
            $parts = explode(' ', $move);
            $direction = $this->directions[$parts[0]];
            $steps = (int)$parts[1];

            for ($i = 0; $i < $steps; $i++) {
                foreach ($tailPositions as $key => $tailPosition) {
                    if ($key === array_key_first($tailPositions)) {
                        $tailPositions[$key][0] += $direction[0];
                        $tailPositions[$key][1] += $direction[1];

                        continue;
                    }

                    $diffY = $tailPositions[$key - 1][0] - $tailPosition[0];
                    $diffX = $tailPositions[$key - 1][1] - $tailPosition[1];

                    if ((abs($diffY) <= 1) && (abs($diffX) <= 1)) {
                        continue;
                    }

                    $tailPositions[$key][0] += $diffY <=> 0;
                    $tailPositions[$key][1] += $diffX <=> 0;

                    if ($key !== array_key_last($tailPositions)) {
                        continue;
                    }

                    $stepKey = $tailPositions[$key][0] . '|' . $tailPositions[$key][1];

                    if (!isset($tailSteps[$stepKey])) {
                        $tailSteps[$stepKey] = 0;
                    }

                    $tailSteps[$stepKey] += 1;
                }
            }
        }

        return $tailSteps;
    }
}
