<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day14 extends DayBase
{
    protected const int TEST_1 = 136;
    protected const int TEST_2 = 64;
    protected const string CUBE_ROCK = self::OBSTACLE;
    protected const string ROUNDED_ROCK = 'O';
    protected array $directions = [[-1, 0], [0, -1], [1, 0], [0, 1]];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);

//        $this->printMap($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [
            $this->getTotalLoad([$this->directions[0]], 1, 1),
            $this->getTotalLoad($this->directions, 1, 1_000_000_000),
        ];
    }

    public function getTotalLoad(array $directions, int $cycle, int $cycles): int
    {
        $cubeRocks = $this->getRocks(self::CUBE_ROCK);
        $cubeMap = $this->toMap($cubeRocks);
        $roundedRocks = $this->getRocks(self::ROUNDED_ROCK);
        $cachedRocksByCycle = [];
        $movedRocks = $this->moveRocks($roundedRocks, $cubeMap, $directions, $cycle, $cycles, $cachedRocksByCycle);

        $this->printNewMap($movedRocks, $cubeRocks, $cycles);

        $maxY = count($this->data);
        $total = 0;

        foreach ($movedRocks as $movedRock) {
            $total += $maxY - $movedRock[0];
        }

        return $total;
    }

    private function getRocks(string $type): array
    {
        $rocks = [];

        for ($y = 0; $y < count($this->data); $y++) {
            for ($x = 0; $x < count($this->data[$y]); $x++) {
                if ($this->data[$y][$x] !== $type) {
                    continue;
                }

                $rocks[] = [$y, $x];
            }
        }

        return $rocks;
    }

    private function moveRocks(array &$roundedRocks, array &$cubeMap, array &$directions, int &$cycle, int &$cycles, array &$cachedRocksByCycle): array
    {
        foreach ($directions as $direction) {
            $this->sortRocks($roundedRocks, $direction);
            $roundedMap = [];

            foreach ($roundedRocks as $key => $rock) {
                $newRock = $this->moveRock($rock, $roundedMap, $cubeMap, $direction);
                $roundedRocks[$key] = $newRock;
                $roundedMap[$newRock[0] . ':' . $newRock[1]] = true;
            }
        }

        if ($cycle === $cycles) {
            return $roundedRocks;
        }

        $hash = json_encode($roundedRocks);

        if (isset($cachedRocksByCycle[$hash])) {
            $repeatedInCycle = $cachedRocksByCycle[$hash];
            $loopLength = $cycle - $repeatedInCycle;
            $remaining = ($cycles - $cycle) % $loopLength;

            echo 'Cached rocks reached after ' . $cycle . ' cycles in the cycle ' . $repeatedInCycle . '.' . PHP_EOL . PHP_EOL;

            foreach ($cachedRocksByCycle as $cachedHash => $cachedCycle) {
                if ($cachedCycle === $repeatedInCycle + $remaining) {
                    return json_decode($cachedHash);
                }
            }
        }

        $cachedRocksByCycle[$hash] = $cycle;
        $cycle++;

        return $this->moveRocks($roundedRocks, $cubeMap, $directions, $cycle, $cycles, $cachedRocksByCycle);
    }

    private function moveRock(array &$rock, array $movedMap, array $cubeMap, array $direction): array
    {
        while (true) {
            $newY = $rock[0] + $direction[0];
            $newX = $rock[1] + $direction[1];
            $key = "$newY:$newX";

            if (
                !isset($this->data[$newY][$newX]) ||
                isset($movedMap[$key]) ||
                isset($cubeMap[$key])
            ) {
                break;
            }

            $rock = [$newY, $newX];
        }

        return $rock;
    }

    private function toMap(array $rocks): array
    {
        $map = [];

        foreach ($rocks as [$y, $x]) {
            $map["$y:$x"] = true;
        }

        return $map;
    }

    /**
     * We need to move rocks to the direction and sort first rocks of that side.
     */
    private function sortRocks(array &$roundedRocks, array $direction): void
    {
        usort($roundedRocks, function ($a, $b) use ($direction) {
            if ($direction === [-1, 0]) { // North
                return $a[0] <=> $b[0];
            } elseif ($direction === [0, -1]) { // West
                return $a[1] <=> $b[1];
            } elseif ($direction === [1, 0]) { // South
                return $b[0] <=> $a[0];
            } elseif ($direction === [0, 1]) { // East
                return $b[1] <=> $a[1];
            }

            return 0;
        });
    }

    private function printNewMap(array $roundedRocks, array $cubeRocks, int $cycle): void
    {
        $mapRow = array_fill(0, count($this->data[0]), self::FREE);
        $map = array_fill(0, count($this->data), $mapRow);

        foreach ($cubeRocks as $cubeRock) {
            $map[$cubeRock[0]][$cubeRock[1]] = self::CUBE_ROCK;
        }

        foreach ($roundedRocks as $roundedRock) {
            $map[$roundedRock[0]][$roundedRock[1]] = self::ROUNDED_ROCK;
        }

        echo 'After ' . $cycle . ' cycles' . PHP_EOL;
        $this->printMap($map);
        echo PHP_EOL;
    }
}
