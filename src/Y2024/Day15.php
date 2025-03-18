<?php

namespace App\Y2024;

use App\Model\DayBase;
use Exception;

class Day15 extends DayBase
{
    private const string BOX = 'O';
    private const string BOX2 = '[';
    private const string BOX3 = ']';
    private const string ROBOT = '@';
    private const int Y_FACTOR = 100;
    private const int X_FACTOR = 1;
    protected const int TEST_1 = 10092;
    protected const int TEST_2 = 9021;

    private array $moves = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);

        $pathInfo = pathinfo($filePath);
        $movesFilePath = $pathInfo['dirname'] . '/' . ($this->test ? 'day15-moves-test.txt' : 'day15-moves.txt');

        $data = file_get_contents($movesFilePath);
        $data = str_replace(self::BOM,'', $data);
        $this->moves = str_split($data);
    }

    public function getResult(): array
    {
        return [
            $this->getGpsSum(false),
            $this->getGpsSum(true)
        ];
    }

    private function getGpsSum(bool $isLargeWarehouse): int
    {
        $map = $this->getMap($this->data, $isLargeWarehouse);

        $this->printMap($map);

        $initialPosition = $this->getInitialPosition($map);
        $newMap = $this->moveRobot($map, $initialPosition);
        $boxes = $this->getBoxesPositions($newMap);

        $this->printMap($newMap);

        return array_reduce($boxes, function ($sum, $box) {
            return $sum + ($box[0] * self::Y_FACTOR) + ($box[1] * self::X_FACTOR);
        }, 0);
    }

    public function moveRobot(array $map, array $position): array
    {
        for ($i = 0; $i < count($this->moves); $i++) {
//        for ($i = 0; $i < 1200; $i++) {
            $direction = $this->getDirection($this->moves[$i]);
            $newMoves = [];

            $this->getNewMoves($map, $position, $this->moves[$i], $direction, $newMoves, 0);

            if (!empty($newMoves) && !in_array(null, $newMoves)) {
                krsort($newMoves);

                foreach ($newMoves as $newMoveLevel) {
                    foreach ($newMoveLevel as $newMove) {
                        $map[$newMove[0] + $direction[0]][$newMove[1] + $direction[1]] = $map[$newMove[0]][$newMove[1]];
                        $map[$newMove[0]][$newMove[1]] = self::FREE;
                    }
                }

                $position = [$position[0] + $direction[0], $position[1] + $direction[1]];
            }

            echo "Step $i move: " . $this->moves[$i] . "\n";
            if (count($newMoves) > 2) {
                var_dump($newMoves);
                $this->printMap($map);
            }
        }

        return $map;
    }

//    public function getNewMoves(array $map, array $position, string $directionMarker, array $direction, array &$newMoves, int $level): void
//    {
//        $nextPosition = [$position[0] + $direction[0], $position[1] + $direction[1]];
//        $nextPosition2 = null;
//
//        if (in_array($directionMarker, ['^', 'v'])) {
//            if ($map[$position[0]][$position[1]] === self::BOX2 || $map[$nextPosition[0]][$nextPosition[1]] === self::BOX2) {
//                $nextPosition2 = [$nextPosition[0], $nextPosition[1] + 1]; // We move right side of the box also.
//            } elseif ($map[$position[0]][$position[1]] === self::BOX3 || $map[$nextPosition[0]][$nextPosition[1]] === self::BOX3) {
//                $nextPosition2 = [$nextPosition[0], $nextPosition[1] - 1]; // We move left side of the box also.
//            }
//        }
//
//        //echo "position = " . json_encode($position) . "\n";
//        //echo "nextPosition = " . json_encode($nextPosition) . "\n";
//        //echo "nextPosition2 = " . json_encode($nextPosition2) . "\n";
//
//        if ($map[$position[0]][$position[1]] === self::WALL || $map[$nextPosition[0]][$nextPosition[1]] === self::WALL || ($nextPosition2 && $map[$nextPosition2[0]][$nextPosition2[1]] === self::WALL)) {
//            $newMoves[] = null;
//
//            return;
//        }
//
//        if (($map[$nextPosition[0]][$nextPosition[1]] === self::FREE && !$nextPosition2) ||
//            ($nextPosition2 && $map[$nextPosition[0]][$nextPosition[1]] === self::FREE && $map[$nextPosition2[0]][$nextPosition2[1]] === self::FREE)
//        ) {
//            $newMoves[$level][] = $position;
//
//            if (in_array($directionMarker, ['^', 'v'])) {
//                if ($map[$position[0]][$position[1]] === self::BOX2) {
//                    $newMoves[$level][] = [$position[0], $position[1] + 1]; // We move right side of the box also.
//                } elseif ($map[$position[0]][$position[1]] === self::BOX3) {
//                    $newMoves[$level][] = [$position[0], $position[1] - 1]; // We move left side of the box also.
//                }
//            }
//
//            return;
//        }
//
//        if ($map[$nextPosition[0]][$nextPosition[1]] === self::BOX) {
//            $newMoves[$level][] = $position;
//
//            $this->getNewMoves($map, $nextPosition, $directionMarker, $direction, $newMoves, $level + 1);
//        }
//
//        if (in_array($map[$nextPosition[0]][$nextPosition[1]], [self::BOX2, self::BOX3], true) ||
//            ($nextPosition2 && in_array($map[$nextPosition2[0]][$nextPosition2[1]], [self::BOX2, self::BOX3], true))
//        ) {
//            $newMoves[$level][] = $position;
//
//            if (in_array($directionMarker, ['^', 'v'])) {
//                if ($map[$position[0]][$position[1]] === self::BOX2) {
//                    $newMoves[$level][] = [$position[0], $position[1] + 1]; // We move right side of the box also.
//
//                    if ($map[$nextPosition[0]][$nextPosition[1] + 1] !== $map[$position[0]][$position[1] + 1] && $map[$nextPosition[0]][$nextPosition[1] + 1] !== self::FREE) {
//                        $this->getNewMoves($map, [$nextPosition[0], $nextPosition[1] + 1], $directionMarker, $direction, $newMoves, $level + 1);
//                    }
//                } elseif ($map[$position[0]][$position[1]] === self::BOX3) {
//                    $newMoves[$level][] = [$position[0], $position[1] - 1]; // We move left side of the box also.
//
//                    if ($map[$nextPosition[0]][$nextPosition[1] - 1] !== $map[$position[0]][$position[1] - 1] && $map[$nextPosition[0]][$nextPosition[1] - 1] !== self::FREE) {
//                        $this->getNewMoves($map, [$nextPosition[0], $nextPosition[1] - 1], $directionMarker, $direction, $newMoves, $level + 1);
//                    }
//                }
//            }
//
//            $this->getNewMoves($map, $nextPosition, $directionMarker, $direction, $newMoves, $level + 1);
//        }
//    }

//    public function getNewMoves(array $map, array $position, string $directionMarker, array $direction, array &$newMoves, int $level): void
//    {
//        if (array_key_exists($level, $newMoves) && is_array($newMoves[$level]) && in_array($position, $newMoves[$level], true)) {
//            return;
//        }
//
//        $nextPosition = [$position[0] + $direction[0], $position[1] + $direction[1]];
//
//        //echo "position = " . json_encode($position) . "\n";
//        //echo "nextPosition = " . json_encode($nextPosition) . "\n";
//
//        if ($map[$nextPosition[0]][$nextPosition[1]] === self::WALL) {
//            $newMoves[] = null;
//
//            return;
//        }
//
//        if ($map[$nextPosition[0]][$nextPosition[1]] === self::FREE) {
//            $newMoves[$level][] = $position;
//
//            return;
//        }
//
//        if (in_array($map[$nextPosition[0]][$nextPosition[1]], [self::BOX, self::BOX2, self::BOX3], true)) {
//            $newMoves[$level][] = $position;
//
//            $this->getNewMoves($map, $nextPosition, $directionMarker, $direction, $newMoves, $level + 1);
//
//            if (in_array($directionMarker, ['^', 'v'])) {
//                if ($map[$nextPosition[0]][$nextPosition[1]] === self::BOX2) {
//                    $this->getNewMoves($map, [$nextPosition[0], $nextPosition[1] + 1], $directionMarker, $direction, $newMoves, $level + 1);
//                } elseif ($map[$nextPosition[0]][$nextPosition[1]] === self::BOX3) {
//                    $this->getNewMoves($map, [$nextPosition[0], $nextPosition[1] - 1], $directionMarker, $direction, $newMoves, $level + 1);
//                }
//            }
//        }
//    }

    public function getNewMoves(array $map, array $position, string $directionMarker, array $direction, array &$newMoves, int $level): void
    {
        if (array_key_exists($level, $newMoves) && is_array($newMoves[$level]) && in_array($position, $newMoves[$level], true)) {
            return;
        }

        if ($map[$position[0]][$position[1]] === self::WALL) {
            $newMoves[] = null;

            return;
        }

        if ($map[$position[0]][$position[1]] === self::FREE) {
            return;
        }

        $newMoves[$level][] = $position;
        $nextPosition = [$position[0] + $direction[0], $position[1] + $direction[1]];

        $this->getNewMoves($map, $nextPosition, $directionMarker, $direction, $newMoves, $level + 1);

        if (in_array($directionMarker, ['^', 'v'])) {
            if ($map[$position[0]][$position[1]] === self::BOX2) {
                $newMoves[$level][] = [$position[0], $position[1] + 1];
                $nextPosition2 = [$position[0] + $direction[0], $position[1] + $direction[1] + 1];

                $this->getNewMoves($map, $nextPosition2, $directionMarker, $direction, $newMoves, $level + 1);
            } elseif ($map[$position[0]][$position[1]] === self::BOX3) {
                $newMoves[$level][] = [$position[0], $position[1] - 1];
                $nextPosition2 = [$position[0] + $direction[0], $position[1] + $direction[1] - 1];

                $this->getNewMoves($map, $nextPosition2, $directionMarker, $direction, $newMoves, $level + 1);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getBoxesPositions(array $map): array
    {
        $boxes = [];

        for ($y = 0; $y < count($map); $y++) {
            for ($x = 0; $x < count($map[$y]); $x++) {
                // We exclude box 3 because in part 2, measurement is with left side of the box
                if (!in_array($map[$y][$x], [self::BOX, self::BOX2], true)) {
                    continue;
                }

                $boxes[] = [$y, $x];
            }
        }

        return $boxes;
    }

    public function getMap(array $data, bool $isLargeWarehouse): array
    {
        if (!$isLargeWarehouse) {
            return $data;
        }

        $map = [];

        for ($y = 0; $y < count($data); $y++) {
            $row = [];

            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] === self::WALL) {
                    $row[] = self::WALL;
                    $row[] = self::WALL;
                }

                if ($data[$y][$x] === self::BOX) {
                    $row[] = self::BOX2;
                    $row[] = self::BOX3;
                }

                if ($data[$y][$x] === self::FREE) {
                    $row[] = self::FREE;
                    $row[] = self::FREE;
                }

                if ($data[$y][$x] === self::ROBOT) {
                    $row[] = self::ROBOT;
                    $row[] = self::FREE;
                }
            }

            $map[] = $row;
        }

        return $map;
    }

    /**
     * @throws Exception
     */
    public function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] !== self::ROBOT) {
                    continue;
                }

                return [$y, $x];
            }
        }

        throw new Exception("Invalid position");
    }

    public function getDirection(string $value): array
    {
        return match ($value) {
            '^' => [-1, 0],
            '<' => [0, -1],
            '>' => [0, 1],
            default => [1, 0],
        };
    }
}
