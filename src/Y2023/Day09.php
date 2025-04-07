<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day09 extends DayBase
{
    protected const int TEST_1 = 114;
    protected const int TEST_2 = 2;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        foreach ($this->data as $key => $row) {
            $datum = explode(' ', $row);
            $callback = fn(string $number): int => $number;
            $this->data[$key] = array_map($callback, $datum);
        }

//        print_r($this->data);
    }

    public function getResult(): array
    {
        return [$this->getNextExtrapolatedSum(), $this->getPrevExtrapolatedSum()];
    }

    private function getNextExtrapolatedSum(): int
    {
        $extrapolated = [];

        foreach ($this->data as $sequence) {
            $placeholders = [];

            $this->getNextExtrapolated($sequence, $placeholders);

            $extrapolated[] = array_sum($placeholders);
        }

        return array_sum($extrapolated);
    }

    private function getNextExtrapolated(array $sequence, array &$placeholders): void
    {
        $placeholders[] = $sequence[count($sequence) - 1];

        $nextSequence = $this->getNextSequence($sequence);

        if (array_all($nextSequence, fn(int $number): bool => $number === 0)) {
            return;
        }

        $this->getNextExtrapolated($nextSequence, $placeholders);
    }

    private function getPrevExtrapolatedSum(): int
    {
        $extrapolated = [];

        foreach ($this->data as $sequence) {
            $placeholders = [];

            $this->getPrevExtrapolated($sequence, $placeholders);

//            print_r($placeholders);

            $finalExtrapolated = 0;

            foreach (array_reverse($placeholders) as $placeholder) {
                $finalExtrapolated = $placeholder - $finalExtrapolated;
            }

            $extrapolated[] = $finalExtrapolated;
        }

        return array_sum($extrapolated);
    }

    private function getPrevExtrapolated(array $sequence, array &$placeholders): void
    {
        $placeholders[] = $sequence[0];

        $nextSequence = $this->getNextSequence($sequence);

        if (array_all($nextSequence, fn(int $number): bool => $number === 0)) {
            return;
        }

        $this->getPrevExtrapolated($nextSequence, $placeholders);
    }

    private function getNextSequence(array $sequence): array
    {
        $result = array_map(function ($v, $y) {
            return $y - $v;
        }, $sequence, array_slice($sequence, 1));

        array_pop($result); // to remove (kinda-fix) for the last difference

        return $result;
    }
}
