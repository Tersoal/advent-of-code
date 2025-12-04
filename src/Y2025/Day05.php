<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day05 extends DayBase
{
    protected const int TEST_1 = 0;
    protected const int TEST_2 = 0;

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
        return 0;
    }

    private function getPart2(): int
    {
        return 0;
    }
}
