<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day06 extends DayBase
{
    protected const string TEST_1 = '7,5,6,10,11';
    protected const string TEST_2 = '19,23,23,29,26';

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        if ($this->test) {
            print_r($this->data);
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

    private function getPart1(): string
    {
        $results = [];

        foreach ($this->data as $line) {
            $results[] = $this->getMarketFinalPosition($line, 4);
        }

        return implode(',', $results);
    }

    private function getPart2(): string
    {
        $results = [];

        foreach ($this->data as $line) {
            $results[] = $this->getMarketFinalPosition($line, 14);
        }

        return implode(',', $results);
    }

    private function getMarketFinalPosition(string $signal, int $markerLength): int
    {
        for ($i = 0; $i < strlen($signal); $i++) {
            $marker = str_split(substr($signal, $i, $markerLength));

            if (count(array_unique($marker)) === count($marker)) {
                return $i + $markerLength;
            }
        }

        return 0;
    }
}
