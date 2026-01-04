<?php

namespace App\Y2022;

use App\Model\DayBase;
use Exception;

class Day12 extends DayBase
{
    protected const int TEST_1 = 31;
    protected const int TEST_2 = 29;

    private array $directions = [
        self::DIRECTION_ARROW_RIGHT => [0, 1],
        self::DIRECTION_ARROW_BOTTOM => [1, 0],
        self::DIRECTION_ARROW_LEFT => [0, -1],
        self::DIRECTION_ARROW_TOP => [-1, 0],
    ];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

        if ($this->test) {
            print_r($this->data);
            echo PHP_EOL;
        }
    }

    public function getResult(): array
    {
        $startPosition = $this->getInitialPositions($this->data, [self::END_POSITION])[0];
        $this->data[$startPosition[0]][$startPosition[1]] = 'z';
        $endPosition = $this->getInitialPositions($this->data, [self::START_POSITION])[0];
        $this->data[$endPosition[0]][$endPosition[1]] = 'a';

        return [
            $this->getPart1($startPosition, $endPosition),
            $this->getPart2($startPosition, $endPosition)
        ];
    }

    private function getPart1(array $startPosition, array $endPosition): int
    {
//        return $this->getMinSteps();
        return $this->bfsShortestPath($startPosition, $endPosition);
    }

    private function getPart2(array $startPosition, array $endPosition): int
    {
        $endPositions = $this->getInitialPositions($this->data, ['a']);
        $steps = [];

        foreach ($endPositions as $endPosition) {
            $steps[] = $this->bfsShortestPath($startPosition, $endPosition);
        }

        $steps = array_filter($steps);

        print_r($steps);

        return min($steps);
    }

    /**
     * BFS optimized from end to start with queue.
     */
    private function bfsShortestPath(array $startPosition, array $endPosition): int
    {
        $queue = [[$startPosition, 0]];
        $visited = [$startPosition[0] . ',' . $startPosition[1] => true];

        while (!empty($queue)) {
            [$position, $steps] = array_shift($queue);

            if ($position === $endPosition) {
                echo "# Shortest path steps = " . $steps . "\n";
                return $steps;
            }

            foreach ($this->directions as $direction) {
                $newPosition = [$position[0] + $direction[0], $position[1] + $direction[1]];

                if (!isset($this->data[$newPosition[0]][$newPosition[1]])) {
                    continue;
                }

                $key = $newPosition[0] . ',' . $newPosition[1];
                if (isset($visited[$key])) {
                    continue;
                }

                if (ord($this->data[$newPosition[0]][$newPosition[1]]) - ord($this->data[$position[0]][$position[1]]) < -1) {
                    continue;
                }

                $visited[$key] = true;
                $queue[] = [$newPosition, $steps + 1];
            }
        }

        return 0;
    }

//    private function getMinSteps(): int
//    {
//        $initialPosition = $this->getInitialPosition($this->data, self::END_POSITION);
//        $this->data[$initialPosition[0]][$initialPosition[1]] = 'z';
//        $endPosition = $this->getInitialPosition($this->data, self::START_POSITION);
//        $this->data[$endPosition[0]][$endPosition[1]] = 'a';
//        $positionsVisited = [];
//        $bestPath = [];
//
//        $this->makePath($initialPosition, [], $endPosition, [], $positionsVisited, $bestPath);
//
//        $map = $this->data;
//        $map[$initialPosition[0]][$initialPosition[1]] = self::END_POSITION;
//        $map[$endPosition[0]][$endPosition[1]] = self::START_POSITION;
//
//        foreach ($bestPath as $step) {
//            if ($map[$step[0]][$step[1]] !== self::START_POSITION && $map[$step[0]][$step[1]] !== self::END_POSITION) {
//                $map[$step[0]][$step[1]] = self::WALL;
//            }
//        }
//
//        $this->printMap($map);
//
//        echo "# Unique Moves Total = " . count($bestPath) . "\n";
//
//        return count($bestPath);
//    }
//
//    /**
//     * BFS non optimized with recursive function is too slow, even from end to start
//     */
//    public function makePath(array $position, array $prevPosition, array $endPosition, array $path, array &$positionsVisited, array &$bestPath): void
//    {
//        if ($position === $endPosition) {
//            if (empty($bestPath) || count($bestPath) > count($path)) {
//                $bestPath = $path;
//            }
//
//            return;
//        }
//
////        if (count($positionsVisited) > 10) {
////            return;
////        }
//
//        if (isset($positionsVisited[$position[0]][$position[1]]) && $positionsVisited[$position[0]][$position[1]] < count($path)) {
//            return;
//        }
//
//        if (!empty($bestPath) && count($bestPath) < count($path)) {
//            return;
//        }
//
//        $path[] = $position;
//        $positionsVisited[$position[0]][$position[1]] = count($path);
//
//        foreach ($this->directions as $key => $direction) {
//            $newPosition = [$position[0] + $direction[0], $position[1] + $direction[1]];
//
//            if (!isset($this->data[$newPosition[0]][$newPosition[1]])) {
//                continue;
//            }
//
//            if ($newPosition === $prevPosition) {
//                continue;
//            }
//
////            if (in_array($newPosition, $path)) {
////                continue;
////            }
//
////            echo 'Char ' . $this->data[$position[0]][$position[1]] . ' value: ' . ord($this->data[$position[0]][$position[1]]) . PHP_EOL;
////            echo 'New Char ' . $this->data[$newPosition[0]][$newPosition[1]] . ' value: ' . ord($this->data[$newPosition[0]][$newPosition[1]]) . PHP_EOL;
//
//            if (ord($this->data[$newPosition[0]][$newPosition[1]]) - ord($this->data[$position[0]][$position[1]]) < -1) {
//                continue;
//            }
//
////            echo 'New Position: ' . $newPosition[0] . ',' . $newPosition[1] . PHP_EOL;
//
//            $this->makePath($newPosition, $position, $endPosition, $path, $positionsVisited, $bestPath);
//        }
//    }

    /**
     * @throws Exception
     */
    public function getInitialPositions(array $data, array $marks): array
    {
        $initialPositions = [];

        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if (!in_array($data[$y][$x], $marks)) {
                    continue;
                }

                $initialPositions[] = [$y, $x];
            }
        }

        return $initialPositions;
    }
}
