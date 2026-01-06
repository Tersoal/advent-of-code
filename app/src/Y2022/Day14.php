<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day14 extends DayBase
{
    protected const int TEST_1 = 24;
    protected const int TEST_2 = 93;

    protected const string SAND_START = '+';

    private array $sandStartPosition = [500, 0];
    private array $rockPositions = [];
    private array $sandPositions = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $line) {
            $points = explode(' -> ', $line);

            for ($i = 1; $i < count($points); $i++) {
                [$x1, $y1] = explode(',', $points[$i - 1]);
                [$x2, $y2] = explode(',', $points[$i]);

                foreach (range($x1, $x2) as $x) {
                    foreach (range($y1, $y2) as $y) {
                        $this->rockPositions[$x . '-' . $y] = 1;
                    }
                }
            }
        }

        if ($this->test) {
            print_r($this->data);
            print_r($this->rockPositions);
            print_r($this->sandPositions);
            echo PHP_EOL;
            $this->printNewMap(false, 0, false);
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
        echo '****************************' . PHP_EOL;
        echo 'Part 1' . PHP_EOL;
        echo '****************************' . PHP_EOL;

        $this->sandPositions = [];

        $this->moveSand(false);

        return count($this->sandPositions);
    }

    private function getPart2(): int
    {
        echo '****************************' . PHP_EOL;
        echo 'Part 2' . PHP_EOL;
        echo '****************************' . PHP_EOL;

        $this->sandPositions = [];

        $this->moveSand(true);

        return count($this->sandPositions) + 1;
    }

    private function moveSand(bool $isPart2): void
    {
        $rockPositions = array_map(fn (string $pos) => array_map('intval', explode('-', $pos)), array_keys($this->rockPositions));
        $maxY = max(array_column($rockPositions, 1));

        if ($isPart2) {
            $maxY += 2;
        }

        $cycles = 0;
        $isEnd = false;

        while (!$isEnd) {
            $position = $this->moveSandUnit($this->sandStartPosition, $maxY, $isPart2);

            if (empty($position)) {
                echo 'Sand is falling forever!!!!!!!!!!!!!!' . PHP_EOL;

                $isEnd = true;
            } else {
                $this->sandPositions[$position[0] . '-' . $position[1]] = 1;
            }

            $cycles++;

            if ($this->test && ($isEnd || in_array($cycles, [1, 2, 5, 22]))) {
                echo PHP_EOL;
                $this->printNewMap(true, $cycles, $isPart2);
                echo PHP_EOL;
            }
        }
    }

    private function moveSandUnit(array $position, int $maxY, bool $isPart2): array
    {
        $downPosition = [$position[0], $position[1] + 1];
        $leftDownPosition = [$position[0] - 1, $position[1] + 1];
        $rightDownPosition = [$position[0] + 1, $position[1] + 1];

        if ($isPart2 && $position[1] === 0 &&
            isset($this->sandPositions[$downPosition[0] . '-' . $downPosition[1]]) &&
            isset($this->sandPositions[$leftDownPosition[0] . '-' . $leftDownPosition[1]]) &&
            isset($this->sandPositions[$rightDownPosition[0] . '-' . $rightDownPosition[1]])
        ) {
            return [];
        }

        if ($isPart2 && $downPosition[1] === $maxY) {
            return $position;
        }

        if (!$isPart2 && $position[1] > $maxY) {
            echo 'Position Y: ' . $position[1] . PHP_EOL;
            echo 'Max Y: ' . $maxY . PHP_EOL;

            return [];
        }

        if (!isset($this->rockPositions[$downPosition[0] . '-' . $downPosition[1]]) && !isset($this->sandPositions[$downPosition[0] . '-' . $downPosition[1]])) {
            return $this->moveSandUnit($downPosition, $maxY, $isPart2);
        }

        if (!isset($this->rockPositions[$leftDownPosition[0] . '-' . $leftDownPosition[1]]) && !isset($this->sandPositions[$leftDownPosition[0] . '-' . $leftDownPosition[1]])) {
            return $this->moveSandUnit($leftDownPosition, $maxY, $isPart2);
        }

        if (!isset($this->rockPositions[$rightDownPosition[0] . '-' . $rightDownPosition[1]]) && !isset($this->sandPositions[$rightDownPosition[0] . '-' . $rightDownPosition[1]])) {
            return $this->moveSandUnit($rightDownPosition, $maxY, $isPart2);
        }

        return $position;
    }

    private function printNewMap(bool $withMargins, int $cycles, bool $isPart2): void
    {
        $rockPositions = array_map(fn (string $pos) => array_map('intval', explode('-', $pos)), array_keys($this->rockPositions));
        $sandPositions = array_map(fn (string $pos) => array_map('intval', explode('-', $pos)), array_keys($this->sandPositions));

        $minX = min(...[$this->sandStartPosition[0]], ...array_column($rockPositions, 0));
        $maxX = max(...[$this->sandStartPosition[0]], ...array_column($rockPositions, 0));
        $minY = min(...[$this->sandStartPosition[1]], ...array_column($rockPositions, 1));
        $maxY = max(...[$this->sandStartPosition[1]], ...array_column($rockPositions, 1));

        if ($isPart2) {
            $maxY += 2;
        }

        if (!empty($sandPositions)) {
            $minX = min(...[$minX], ...array_column($sandPositions, 0));
            $maxX = max(...[$maxX], ...array_column($sandPositions, 0));
            $minY = min(...[$minY], ...array_column($sandPositions, 1));
            $maxY = max(...[$maxY], ...array_column($sandPositions, 1));
        }

        if ($withMargins) {
            $minX -= 2;
            $maxX += 2;
            $minY -= 2;
            $maxY += 2;
        }

        $row = array_fill(0, $maxX - $minX + 1, self::FREE);
        $map = array_fill(0, $maxY - $minY + 1, $row);

        $map[$this->sandStartPosition[1] - $minY][$this->sandStartPosition[0] - $minX] = self::SAND_START;

        foreach ($rockPositions as $pos) {
            $map[$pos[1] - $minY][$pos[0] - $minX] = self::OBSTACLE;
        }

        foreach ($sandPositions as $pos) {
            $map[$pos[1] - $minY][$pos[0] - $minX] = self::POSITION;
        }

        if ($isPart2) {
            foreach (range($minX, $maxX) as $x) {
                $map[$maxY][$x - $minX] = self::OBSTACLE;
            }
        }

        echo PHP_EOL;
        echo '----------------------------' . PHP_EOL;
        echo 'Cycles: ' . $cycles . PHP_EOL;
        echo '----------------------------' . PHP_EOL;

        $this->printMap($map);
    }
}
