<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day03 extends DayBase
{
    protected const int TEST_1 = 4361;
    protected const int TEST_2 = 467835;
    protected const string GEAR = self::ASTERISK;

    protected array $directions = [
        [-1, 1], // Right-top
        [0, 1], // Right
        [1, 1], // Right-bottom
        [1, 0], // Bottom
        [1, -1], // Left-bottom
        [0, -1], // Left
        [-1, -1], // Left-top
        [-1, 0], // Top
    ];
    protected array $leftDirection = [0, -1];
    protected array $rightDirection = [0, 1];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        return [$this->getSumOfPartNumbers(), $this->getSumOfRatios()];
    }

    private function getSumOfPartNumbers(): int
    {
        $parts = [];
        $nonParts = [];

        for ($y = 0; $y < count($this->data); $y++) {
            $number = '';
            $canBeSum = false;

            for ($x = 0; $x < count($this->data[$y]); $x++) {
                if (!is_numeric($this->data[$y][$x])) {
                    if (!empty($number)) {
                        $position = $y . '-' . ($x - 1);

                        if ($canBeSum) {
                            $parts[$position] = (int)$number;
                        } else {
                            $nonParts[$position] = (int)$number;
                        }
                    }

                    $number = '';
                    $canBeSum = false;

                    continue;
                }

                $number .= $this->data[$y][$x];

                if ($this->numberIsPart($y, $x)) {
                    $canBeSum = true;
                }
            }

            if (!empty($number)) {
                $position = $y . '-' . ($x - 1);

                if ($canBeSum) {
                    $parts[$position] = (int)$number;
                } else {
                    $nonParts[$position] = (int)$number;
                }
            }
        }

//        var_dump($parts);
//        var_dump($nonParts);
//        var_dump('Parts = ' . count($parts));
//        var_dump('Non Parts = ' . count($nonParts));
//        var_dump('Total Parts = ' . (count($parts) + count($nonParts)));

       return array_sum($parts);
    }

    private function getSumOfRatios(): int
    {
        $possibleGears = [];

        for ($y = 0; $y < count($this->data); $y++) {
            for ($x = 0; $x < count($this->data[$y]); $x++) {
                if ($this->data[$y][$x] === self::GEAR) {
                    $position = $y . '-' . $x;
                    $possibleGears[$position] = $this->getGearNumbers($y, $x);
                }
            }
        }

        $mapRow = array_fill(0, count($this->data[0]), self::FREE);
        $resultMap = array_fill(0, count($this->data), $mapRow);
        $noResultMap = array_fill(0, count($this->data), $mapRow);
        $sum = 0;

        foreach ($possibleGears as $position => $gears) {
            $isResult = count($gears) === 2;

            $pos = explode('-', $position);
            $posY = $pos[0];
            $posX = $pos[1];

            if ($isResult) {
                $resultMap[$posY][$posX] = self::GEAR;
            } else {
                $noResultMap[$posY][$posX] = self::GEAR;
            }

            $numbers = [];

            foreach ($gears as $gear) {
                $number = '';

                foreach ($gear as $gearPos => $gearNumber) {
                    $pos = explode('-', $gearPos);
                    $posY = $pos[0];
                    $posX = $pos[1];

                    if ($isResult) {
                        $resultMap[$posY][$posX] = $gearNumber;

                        $number .= $gearNumber;
                    } else {
                        $noResultMap[$posY][$posX] = $gearNumber;
                    }
                }

                $numbers[] = (int)$number;
            }

            $sum += array_product($numbers);
        }

        $this->printMap($resultMap);
        echo PHP_EOL;
        $this->printMap($noResultMap);
        echo PHP_EOL;

        return $sum;
    }

    private function numberIsPart(int $y, int $x): bool
    {
        foreach ($this->directions as $direction) {
            $newY = $y + $direction[0];
            $newX = $x + $direction[1];

            if (!array_key_exists($newY, $this->data) || !array_key_exists($newX, $this->data[$newY])) {
                continue;
            }

            if (is_numeric($this->data[$newY][$newX]) || $this->data[$newY][$newX] === static::FREE) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function getGearNumbers(int $y, int $x): array
    {
        $numbers = [];
        $cachedPositions = [];

        foreach ($this->directions as $direction) {
            $newY = $y + $direction[0];
            $newX = $x + $direction[1];

            if (!array_key_exists($newY, $this->data) || !array_key_exists($newX, $this->data[$newY])) {
                continue;
            }

            if (!is_numeric($this->data[$newY][$newX])) {
                continue;
            }

            $position = $newY . '-' . $newX;
            if (in_array($position, $cachedPositions)) {
                continue;
            }

            $number = [];
            $this->getCompleteNumber($number, $newY, $newX, []);

            ksort($number);

            $numbers[] = $number;
            $cachedPositions = [...$cachedPositions, ...array_keys($number)];
        }

        return $numbers;
    }

    private function getCompleteNumber(array &$number, int $y, int $x, array $direction = []): void
    {
        if (!array_key_exists($y, $this->data) || !array_key_exists($x, $this->data[$y])) {
            return;
        }

        if (!is_numeric($this->data[$y][$x])) {
            return;
        }

        $position = $y . '-' . $x;
        $number[$position] = $this->data[$y][$x];

        if (empty($direction)) {
            $newY = $y + $this->leftDirection[0];
            $newX = $x + $this->leftDirection[1];

            $this->getCompleteNumber($number, $newY, $newX, $this->leftDirection);

            $newY = $y + $this->rightDirection[0];
            $newX = $x + $this->rightDirection[1];

            $this->getCompleteNumber($number, $newY, $newX, $this->rightDirection);
        } else {
            $newY = $y + $direction[0];
            $newX = $x + $direction[1];

            $this->getCompleteNumber($number, $newY, $newX, $direction);
        }
    }
}
