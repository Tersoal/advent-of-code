<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day06 extends DayBase
{
    protected const int TEST_1 = 41;
    protected const int TEST_2 = 6;

    public int $yLimit;
    public int $xLimit;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);

        $this->yLimit = count($this->data);
        $this->xLimit = count($this->data[0]);
    }

    public function getResult(): array
    {
        return [$this->getStepsCount(true), $this->getObstructionPositionsCount()];
    }

    /**
     * @throws \Exception
     */
    public function getStepsCount($unique = true): int
    {
        $steps = [];
        $exit = false;
        $position = $this->getInitialPosition($this->data);

        while (!$exit) {
            [$position, $newSteps] = $this->goAheadAndReturnSteps($this->data, $position);
            $steps = array_merge($steps, $newSteps);
            $exit = $this->isExit($position);
            $position[2] = $this->turnRightAndGetNewDirection($position[2]);

//            echo 'Go Ahead' . "\n";
//            var_dump($position);
//
//            echo 'Steps Count' . "\n";
//            var_dump(count($steps));
        }

        if ($unique) {
            return count(array_unique($steps));
        }

        return count($steps);
    }

    public function getObstructionPositionsCount(): int
    {
        $steps = [];
        $exit = false;
        $position = $this->getInitialPosition($this->data);

        while (!$exit) {
            [$position, $newSteps] = $this->goAheadAndReturnSteps($this->data, $position);
            $steps = array_merge($steps, $newSteps);
            $exit = $this->isExit($position);
            $position[2] = $this->turnRightAndGetNewDirection($position[2]);

//            echo 'Go Ahead' . "\n";
//            var_dump($position);
//
//            echo 'Steps Count' . "\n";
//            var_dump(count($steps));
        }

        $obstructions = array_filter(array_count_values($steps), fn ($position) => $position > 1);

        return count($obstructions);
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

    public function turnRightAndGetNewDirection(array $direction): array
    {
        if ($direction[0] < 0 && $direction[1] === 0) {
            return [0, 1];
        }

        if ($direction[0] === 0 && $direction[1] < 0) {
            return [-1, 0];
        }

        if ($direction[0] === 0 && $direction[1] > 0) {
            return [1, 0];
        }

        return [0, -1];
    }

    public function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if (in_array($data[$y][$x], [self::FREE, self::OBSTACLE])) {
                    continue;
                }

                return [$y, $x, $this->getDirection($data[$y][$x])];
            }
        }

        throw new \Exception("Invalid position");
    }

    public function isExit(array $position): bool
    {
        $nextY = $position[0] + $position[2][0];
        $nextX = $position[1] + $position[2][1];

        return !array_key_exists($nextY, $this->data) || !array_key_exists($nextX, $this->data[$nextY]);
    }

    public function goAheadAndReturnSteps(array $data, array $position): array
    {
        $y = $position[0];
        $x = $position[1];
        $direction = $position[2];
        $steps = [];

        while (array_key_exists($y, $data) && array_key_exists($x, $data[$y]) && $data[$y][$x] !== self::OBSTACLE) {
            $y += $direction[0];
            $x += $direction[1];
            $steps[] = $y . '-' . $x;
        }

        // make step back to continue, only if it's an obstacle
        // Test fails with this, but original is ok. WTF??????
        if (array_key_exists($y, $data) && array_key_exists($x, $data[$y]) && $data[$y][$x] === self::OBSTACLE) {
            $y -= $direction[0];
            $x -= $direction[1];
            array_pop($steps);
        }

        return [[$y, $x, $direction], $steps];
    }
}
