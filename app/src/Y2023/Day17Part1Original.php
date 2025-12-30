<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day17Part1Original extends DayBase
{
    protected const int TEST_1 = 102;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

        foreach ($this->data as $y => $row) {
            foreach ($row as $x => $value) {
                $this->data[$y][$x] = (int)$value;
            }
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getMinHeatLoss(), 0];
    }

    public function getMinHeatLoss(): int
    {
        $startPosition = [0, 0];
        $endPosition = [(count($this->data) - 1), (count($this->data[0]) - 1)];
        $heatLoss = null;
        $positionsVisited = [];
        $bestPath = [];

        $this->moveCrucible($startPosition, $startPosition, $endPosition, [], [], ['moves' => [], 'heatLoss' => 0, 'sameDirectionCount' => 0], $heatLoss, $positionsVisited, $bestPath);

//        print_r($bestPath);
//        echo PHP_EOL;

        return $heatLoss;
    }

    public function moveCrucible(array $position, array $startPosition, array $endPosition, array $prevPosition, array $direction, array $path, ?int &$heatLoss, array &$positionsVisited, array &$bestPath): void
    {
        if ($position === $endPosition) {
            $positionMap = $position[0] . ':' . $position[1];

            $path['moves'][$positionMap] = $position;
            $path['heatLoss'] += $this->data[$position[0]][$position[1]];

            if ($heatLoss === null || $path['heatLoss'] < $heatLoss) {
                $bestPath = $path;
                $heatLoss = $path['heatLoss'];
            }

            return;
        }

        if (
            isset($positionsVisited[$position[0]][$position[1]]) &&
            $positionsVisited[$position[0]][$position[1]] < $path['heatLoss']
        ) {
            return;
        }

        $positionMap = $position[0] . ':' . $position[1];
        $path['moves'][$positionMap] = $position;
        $path['sameDirectionCount']++;

        if ($position !== $startPosition) {
            $path['heatLoss'] += $this->data[$position[0]][$position[1]];

            $positionsVisited[$position[0]][$position[1]] = $path['heatLoss'];
        }

        $newDirections = [
            [0, 1], // Right
            [1, 0], // Bottom
            [0, -1], // Left
            [-1, 0], // Top
        ];

        foreach ($newDirections as $newDirection) {
            $newY = $position[0] + $newDirection[0];
            $newX = $position[1] + $newDirection[1];
            $newPosition = [$newY, $newX];
            $newPositionMap = $newY . ':' . $newX;

            if (!isset($this->data[$newY][$newX])) {
                continue;
            }

            if ($newPosition === $prevPosition) {
                continue;
            }

            if ($newDirection === $direction && $path['sameDirectionCount'] === 3) {
                continue;
            }

            if (isset($path['moves'][$newPositionMap])) {
                continue;
            }

            if ($heatLoss !== null && $path['heatLoss'] > $heatLoss) {
                continue;
            }

            $newPath = $path;

            if ($newDirection !== $direction) {
                $newPath['sameDirectionCount'] = 0; // because this move takes account to best-direction limit.
            }

            $this->moveCrucible($newPosition, $startPosition, $endPosition, $position, $newDirection, $newPath, $heatLoss, $positionsVisited, $bestPath);
        }
    }
}
