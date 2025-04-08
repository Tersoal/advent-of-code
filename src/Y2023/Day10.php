<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day10 extends DayBase
{
    protected const int TEST_1 = 8;
    protected const int TEST_2 = 8;
    protected const int TEST_3 = 10;
    protected const string START = 'S';
    protected const string OUTSIDE = '0';

    protected const string DIR_N_S = '|'; // is a vertical pipe connecting north and south.
    protected const string DIR_E_W = '-'; // is a horizontal pipe connecting east and west.
    protected const string DIR_N_E = 'L'; // is a 90-degree bend connecting north and east.
    protected const string DIR_N_W = 'J'; // is a 90-degree bend connecting north and west.
    protected const string DIR_S_W = '7'; // is a 90-degree bend connecting south and west.
    protected const string DIR_S_E = 'F'; // is a 90-degree bend connecting south and east.

    protected array $data2 = [];
    protected array $map = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);

        $this->data2 = $this->data;

        if ($this->test) {
            $data = $this->data;

            $filename = __DIR__ . "/../../data/2023/day10/day10-test2.txt";

            $this->loadDataAsArrayMap($filename);

            $this->data2 = $this->data;
            $this->data = $data;
        }

//        print_r($this->data);
    }

    public function getResult(): array
    {
        return [$this->getFarthestPosition($this->data), $this->getEnclosedTilesCount($this->data2)];
    }

    private function getFarthestPosition(array $data): int
    {
        return (count($this->getSteps($data)) + 1) / 2;
    }

    private function getSteps(array $data): array
    {
        $initialPosition = $this->getInitialPosition($data);
        $steps = [];

        $this->makePath($data, $initialPosition, $this->getInitialStep($data, $initialPosition), $steps);

//        print_r($steps);

        return $steps;
    }

    private function makePath(array $data, array $initialPosition, array $currentStep, array &$steps): void
    {
        $steps[] = $currentStep;
        $nextStep = $this->getNextStep($data, $currentStep);

        if (!$nextStep) {
            return;
        }

        if ($nextStep[0] === $initialPosition[0] && $nextStep[1] === $initialPosition[1]) {
            return;
        }

        $this->makePath($data, $initialPosition, $nextStep, $steps);
    }

    private function getNextStep(array $data, array $currentStep): ?array
    {
        $y = $currentStep[0];
        $x = $currentStep[1];

        if (empty($data[$y][$x])) {
            return null;
        }

        switch ($data[$y][$x]) {
            case self::DIR_N_S:
            case self::DIR_E_W:
                return [$y + $currentStep[2][0], $x + $currentStep[2][1], [$currentStep[2][0], $currentStep[2][1]]];
            case self::DIR_N_E:
                if ($currentStep[2][0] === 1) {
                    return [$y, $x + 1, [0, 1]];
                }

                return [$y - 1, $x, [-1, 0]];
            case self::DIR_N_W:
                if ($currentStep[2][0] === 1) {
                    return [$y, $x - 1, [0, -1]];
                }

                return [$y - 1, $x, [-1, 0]];
            case self::DIR_S_W:
                if ($currentStep[2][0] === -1) {
                    return [$y, $x - 1, [0, -1]];
                }

                return [$y + 1, $x, [1, 0]];
            case self::DIR_S_E:
                if ($currentStep[2][0] === -1) {
                    return [$y, $x + 1, [0, 1]];
                }

                return [$y + 1, $x, [1, 0]];
            default:
                throw new \Exception("Invalid next step");
        }
    }

    private function getInitialStep(array $data, array $position): array
    {
        $y = $position[0];
        $x = $position[1] + 1;
        if (!empty($data[$y][$x]) && in_array($data[$y][$x], [self::DIR_E_W, self::DIR_N_W, self::DIR_S_W], true)) {
            return [$y, $x, [0, 1]];
        }

        $y = $position[0];
        $x = $position[1] - 1;
        if (!empty($data[$y][$x]) && in_array($data[$y][$x], [self::DIR_E_W, self::DIR_N_E, self::DIR_S_E], true)) {
            return [$y, $x, [0, -1]];
        }

        $y = $position[0] + 1;
        $x = $position[1];
        if (!empty($data[$y][$x]) && in_array($data[$y][$x], [self::DIR_N_S, self::DIR_N_E, self::DIR_N_W], true)) {
            return [$y, $x, [1, 0]];
        }

        $y = $position[0] - 1;
        $x = $position[1];
        if (!empty($data[$y][$x]) && in_array($data[$y][$x], [self::DIR_N_S, self::DIR_S_E, self::DIR_S_W], true)) {
            return [$y, $x, [-1, 0]];
        }

        throw new \Exception("Invalid step");
    }

    private function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] !== self::START) {
                    continue;
                }

                return [$y, $x];
            }
        }

        throw new \Exception("Invalid position");
    }

    private function getEnclosedTilesCount(array $data): int
    {
        $initialPosition = $this->getInitialPosition($data);
        $steps = $this->getSteps($data);

        $this->map = $data;
        $this->map[$initialPosition[0]][$initialPosition[1]] = self::WALL;

        foreach ($steps as $step) {
            $this->map[$step[0]][$step[1]] = self::WALL;
        }

        for ($y = 0; $y < count($this->map); $y++) {
            for ($x = 0; $x < count($this->map[$y]); $x++) {
                if ($this->map[$y][$x] === self::WALL) {
                    break;
                }

                $this->map[$y][$x] = self::OUTSIDE;
            }
        }

        for ($y = count($this->map) - 1; $y >= 0; $y--) {
            for ($x = count($this->map[$y]) - 1; $x >= 0; $x--) {
                if ($this->map[$y][$x] === self::WALL) {
                    break;
                }

                $this->map[$y][$x] = self::OUTSIDE;
            }
        }

        $tiles = [];
        foreach ($this->map as $y => $row) {
            $rowTiles = array_filter($row, fn ($tile) => !in_array($tile, [self::WALL, self::OUTSIDE]));
            $tiles = array_merge($tiles, $rowTiles);
        }





//        foreach ($this->map as $y => $row) {
//            foreach ($row as $x => $tile) {
//                if ($tile === 'X') {
//                    $isEnclosed = !$isEnclosed;
//                } elseif ($isEnclosed) {
//                    $this->map[$y][$x] = 'I';
//                }
//            }
//        }

        $this->printMap($this->map);
        echo PHP_EOL;

        return count($tiles);
    }

//    private function getEnclosedTilesCount(array $data): int
//    {
//        $steps = $this->getSteps($data);
//
//        $this->map = $data;
//
//        foreach ($steps as $step) {
//            $this->map[$step[0]][$step[1]] = self::WALL;
//        }
//
//        for ($y = 0; $y < count($this->map); $y++) {
//            for ($x = 0; $x < count($this->map[$y]); $x++) {
//                if ($this->map[$y][$x] === self::WALL) {
//                    break;
//                }
//
//                $this->map[$y][$x] = self::OUTSIDE;
//            }
//        }
//
//        for ($y = count($this->map) - 1; $y >= 0; $y--) {
//            for ($x = count($this->map[$y]) - 1; $x >= 0; $x--) {
//                if ($this->map[$y][$x] === self::WALL) {
//                    break;
//                }
//
//                $this->map[$y][$x] = self::OUTSIDE;
//            }
//        }
//
//        $tiles = [];
//        foreach ($this->map as $y => $row) {
//            $rowTiles = array_filter($row, fn ($tile) => !in_array($tile, [self::START, self::WALL, self::OUTSIDE]));
//            $tiles = array_merge($tiles, $rowTiles);
//        }
//
//
//
//
//
////        foreach ($this->map as $y => $row) {
////            foreach ($row as $x => $tile) {
////                if ($tile === 'X') {
////                    $isEnclosed = !$isEnclosed;
////                } elseif ($isEnclosed) {
////                    $this->map[$y][$x] = 'I';
////                }
////            }
////        }
//
//        $this->printMap($this->map);
//        echo PHP_EOL;
//
//        return count($tiles);
//    }
}
