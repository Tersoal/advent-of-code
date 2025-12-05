<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day04 extends DayBase
{
    protected const int TEST_1 = 13;
    protected const int TEST_2 = 43;

    protected const string ROLL = '@';
    protected const string REMOVED_ROLL = 'x';

    protected array $directions = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];


    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

//        $this->printMap($this->data);
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
            $this->printMap($this->data);
        }

        $accessibleRolls = $this->getAccesibleRollsOnMap($this->data);

        if ($this->test) {
            $map = $this->getNewMap($this->data, $accessibleRolls);

            $this->printMap($map);
        }

        return count($accessibleRolls);
    }

    private function getPart2(): int
    {
        $accessibleRollsRemoved = 0;
        $end = false;
        $map = $this->data;

        if ($this->test) {
            $this->printMap($map);
        }

        while (!$end) {
            $accessibleRolls = $this->getAccesibleRollsOnMap($map);
            $accessibleRollsCount = count($accessibleRolls);

            if ($accessibleRollsCount === 0) {
                $end = true;
            } else {
                $accessibleRollsRemoved += $accessibleRollsCount;

                $map = $this->getNewMap($map, $accessibleRolls);

                if ($this->test) {
                    $this->printMap($map);
                }
            }
        }

        return $accessibleRollsRemoved;
    }

    private function getAccesibleRollsOnMap(array $map): array
    {
        $accessibleRolls = [];

        for ($y = 0; $y < count($map); $y++) {
            for ($x = 0; $x < count($map[$y]); $x++) {
                if ($map[$y][$x] === self::ROLL) {
                    if ($this->isRollAccessible($map, $y, $x)) {
                        $accessibleRolls[] = [$y, $x];
                    }
                }
            }
        }

        if ($this->test) {
            print_r($accessibleRolls);
        }

        return $accessibleRolls;
    }

    private function isRollAccessible(array $map, int $y, int $x): bool
    {
        $adjacentRolls = 0;

        foreach ($this->directions as $direction) {
            $newY = $y + $direction[0];
            $newX = $x + $direction[1];

            if (!array_key_exists($newY, $map) || !array_key_exists($newX, $map[$newY])) {
                continue;
            }

            if ($map[$newY][$newX] !== self::ROLL) {
                continue;
            }

            $adjacentRolls++;
        }

        return $adjacentRolls < 4;
    }

    private function getNewMap(array $map, array $removedRolls): array
    {
        for ($y = 0; $y < count($map); $y++) {
            for ($x = 0; $x < count($map[$y]); $x++) {
                if ($map[$y][$x] === self::REMOVED_ROLL) {
                    $map[$y][$x] = self::GROUND;
                }
            }
        }

        foreach ($removedRolls as $roll) {
            $map[$roll[0]][$roll[1]] = self::REMOVED_ROLL;
        }

        return $map;
    }
}
