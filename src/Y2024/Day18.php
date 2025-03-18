<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day18 extends DayBase
{
    private const int MAX_X = 70;
    private const int MAX_Y = 70;
    private const int MAX_CYCLES = 1024;
    private const int TEST_MAX_X = 6;
    private const int TEST_MAX_Y = 6;
    private const int TEST_MAX_CYCLES = 12;
    protected const int TEST_1 = 22;
    protected const int TEST_2 = 0;

    private int $maxX = 0;
    private int $maxY = 0;
    private int $maxCycles = 0;
    private array $map = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n", $data);

        $callback = fn(string $row): array => explode(',', $row);
        $this->data = array_map($callback, $data);
    }

    public function getResult(): array
    {
        $this->getMap();

        $initialPosition = [0, 0];
        $steps = null;
        $positionsVisited = [];

        $this->getSteps($initialPosition, [], 'R', ['moves' => [], 'steps' => 0], $steps, $positionsVisited);

        return [$steps - 1, 0];
    }

    public function getSteps(array $position, array $prevPosition, $direction, array $path, ?int &$steps, array &$positionsVisited): void
    {
        if ($position[0] === $this->maxY && $position[1] === $this->maxX) {
            $path['moves'][] = $position;
            $path['steps']++;

            if ($steps === null || $path['steps'] < $steps) {
                $steps = $path['steps'];
            }

            return;
        }

        if (
            array_key_exists($position[0], $positionsVisited) &&
            array_key_exists($position[1], $positionsVisited[$position[0]]) &&
            $positionsVisited[$position[0]][$position[1]] <= $path['steps']
        ) {
            return;
        }

//        $newDirections = ['R' => [0, 1], 'B' => [1, 0], 'L' => [0, -1], 'T' => [-1, 0]];
//
//        $stop = false;
//        while (!$stop) {
//            $position = [$position[0] + $newDirections[$direction][0], $position[1] + $newDirections[$direction][1]];
//            $path['moves'][] = $position;
//            $path['steps']++;
//
//            $positionsVisited[$position[0]][$position[1]] = $path['steps'];
//
//            if (!array_key_exists($position[0], $this->map) || !array_key_exists($position[1], $this->map[$position[0]]) || $this->map[$position[0]][$position[1]] === self::WALL) {
//                $stop = true;
//            }
//
//            if ($position[0] === $this->maxY && $position[1] === $this->maxX) {
//                $stop = true;
//
//                if ($steps === null || $path['steps'] < $steps) {
//                    $steps = $path['steps'];
//                }
//            }
//        }

        $right = [$position[0], $position[1] + 1];
        $bottom = [$position[0] + 1, $position[1]];
        $left = [$position[0], $position[1] - 1];
        $top = [$position[0] - 1, $position[1]];
        $newPositions = ['R' => $right, 'B' => $bottom, 'L' => $left, 'T' => $top];

//        echo "Position = " . json_encode($position) . PHP_EOL;

        foreach ($newPositions as $key => $newPosition) {
//            echo "New Position in direction $key = " . json_encode($newPosition) . PHP_EOL;

            if (!array_key_exists($newPosition[0], $this->map) || !array_key_exists($newPosition[1], $this->map[$newPosition[0]])) {
                continue;
            }

            if ($this->map[$newPosition[0]][$newPosition[1]] === self::WALL) {
                continue;
            }

            if ($newPosition === $prevPosition) {
                continue;
            }

            if (in_array($newPosition, $path['moves'])) {
                continue;
            }

            if ($steps !== null && $path['steps'] >= $steps) {
                continue;
            }

            $newPath = $path;
            $newPath['moves'][] = $position;
            $newPath['steps']++;

            $positionsVisited[$position[0]][$position[1]] = $newPath['steps'];

            $this->getSteps($newPosition, $position, $key, $newPath, $steps, $positionsVisited);
        }
    }

    public function getMap(): void
    {
        $this->maxX = $this->test ? self::TEST_MAX_X : self::MAX_X;
        $this->maxY = $this->test ? self::TEST_MAX_Y : self::MAX_Y;
        $this->maxCycles = $this->test ? self::TEST_MAX_CYCLES : self::MAX_CYCLES;

        $mapRow = array_fill(0, $this->maxX + 1, self::FREE);
        $this->map = array_fill(0, $this->maxY + 1, $mapRow);

        for ($i = 0; $i < $this->maxCycles; $i++) {
            [$x, $y] = $this->data[$i];
            $this->map[$y][$x] = self::WALL;
        }

        echo "Initial map \n";
        echo "=========================== \n";
        $this->printMap($this->map);
        echo "\n";
    }
}
