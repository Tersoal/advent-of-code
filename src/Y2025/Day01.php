<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day01 extends DayBase
{
    protected const int TEST_1 = 3;
    protected const int TEST_2 = 6;

    protected const int INITIAL_POINT = 50;
    protected const string LEFT = 'L';
    protected const string RIGHT = 'R';

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

//        print_r($this->data);
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
            echo '=========================' . PHP_EOL;
            echo 'Test 1' . PHP_EOL;
            echo '=========================' . PHP_EOL;
        }

        return $this->getPassword(false);
    }

    private function getPart2(): int
    {
        if ($this->test) {
            echo '=========================' . PHP_EOL;
            echo 'Test 2' . PHP_EOL;
            echo '=========================' . PHP_EOL;
        }

        return $this->getPassword(true);
    }

    private function getPassword(bool $returnClicks = false): int
    {
        $point = self::INITIAL_POINT;
        $resultPoint = 0;
        $resultClicks = 0;

        foreach ($this->data as $value) {
            $direction = substr($value, 0, 1);
            $distance = (int)substr($value, 1);

            [$point, $clicks] = $this->turnDial($point, $direction, $distance);

            if ($point === 0) {
                $resultPoint++;
            }

            $resultClicks += $clicks;

            if ($this->test) {
                echo 'The dial is rotated ' . $direction . $distance . ' to point at ' . $point . ' and has ' . $clicks . ' clicks' . PHP_EOL;
            }
        }

        return $returnClicks ? $resultClicks : $resultPoint;
    }

    private function turnDial(int $currentPoint, string $direction, int $distance): array
    {
        $dirMultiplier = $direction === self::LEFT ? -1 : 1;
        $point = $currentPoint + ($distance * $dirMultiplier);

        $newPoint = $point % 100;

        if ($newPoint < 0) {
            $newPoint += 100;
        }

        $clicks = (int) abs($point / 100);

        if ($clicks === 0 && $point <= 0 && $currentPoint !== 0) {
            $clicks++;
        } elseif ($dirMultiplier < 0 && $point < 0 && $currentPoint !== 0) {
            $clicks++;
        }

        return [$newPoint, $clicks];
    }
}
