<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day13Part1Original extends DayBase
{
    protected const int TEST_1 = 405;
    protected const int TEST_2 = 0;
    protected const int ROW_FACTOR = 100;
    protected const int COL_FACTOR = 1;

    protected array $maps = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $this->data = explode("\r\n\r\n", $data);

        foreach ($this->data as $map) {
            $map = explode("\r\n", $map);

            $callback = fn(string $row): array => str_split($row);
            $this->maps[] = array_map($callback, $map);
        }

//        foreach ($this->maps as $map) {
//            $this->printMap($map);
//            echo PHP_EOL;
//        }
    }

    public function getResult(): array
    {
        return [$this->getSummarize(false), $this->getSummarize(true)];
    }

    private function getSummarize(bool $searchSmudge = false): int
    {
        $total = 0;

        foreach ($this->maps as $map) {
            $total += $this->getMapSum($map, $searchSmudge);
        }

        return $total;
    }

    private function getMapSum(array $map, bool $searchSmudge = false): int
    {
        $sum = self::ROW_FACTOR * $this->getMapRowsSum($map, $searchSmudge);

        if ($sum === 0) {
            $sum += self::COL_FACTOR * $this->getMapColsSum($map, $searchSmudge);
        }

        return $sum;
    }

    private function getMapRowsSum(array $map, bool $searchSmudge = false): int
    {
        [$map, $rowsWithReflection] = $this->getMapRowsWithRefection($map, $searchSmudge);

//        echo 'getMapRowsSum ' . PHP_EOL;
//        print_r($rowsWithReflection);
//        echo PHP_EOL;

        $rowsCount = 0;

        foreach ($rowsWithReflection as [$rowPrev, $rowNext]) {
            if (!$this->mapHasRowRefection($map, $rowPrev, $rowNext)) {
                continue;
            }

//            echo 'Has reflection in row '. $rowPrev . PHP_EOL;
//            echo '******************************************' . PHP_EOL;

            $rowsCount = $rowPrev + 1;
        }

        return $rowsCount;
    }

    private function getMapRowsWithRefection(array $map, bool $searchSmudge = false): array
    {
        $rowsWithReflection = [];

        for ($i = 0; $i < count($map); $i++) {
            $nextIndex = $i + 1;

            if (!array_key_exists($nextIndex, $map)) {
                continue;
            }

            if ($map[$i] !== $map[$nextIndex]) {
                continue;
            }

            $rowsWithReflection[] = [$i, $nextIndex];
        }

        return [$map, $rowsWithReflection];
    }

    private function mapHasRowRefection(array $map, int $rowPrev, int $rowNext): bool
    {
        if ($rowPrev === 0 || $rowNext === (count($map) - 1)) {
            return true;
        }

        while (array_key_exists($rowPrev - 1, $map) && array_key_exists($rowNext + 1, $map)) {
            if ($map[$rowPrev - 1] !== $map[$rowNext + 1]) {
                return false;
            }

            $rowPrev--;
            $rowNext++;
        }

        return true;
    }

    private function getMapColsSum(array $map, bool $searchSmudge = false): int
    {
        $colsWithReflection = $this->getMapColsWithRefection($map, $searchSmudge);

//        echo 'getMapColsSum ' . PHP_EOL;
//        print_r($colsWithReflection);
//        echo PHP_EOL;

        $colsCount = 0;

        foreach ($colsWithReflection as [$colPrev, $colNext]) {
            if (!$this->mapHasColRefection($map, $colPrev, $colNext)) {
                continue;
            }

//            echo 'Has reflection in col '. $colPrev . PHP_EOL;
//            echo '******************************************' . PHP_EOL;

            $colsCount = $colPrev + 1;
        }

        return $colsCount;
    }

    private function getMapColsWithRefection(array $map, bool $searchSmudge = false): array
    {
        $colsWithReflection = [];

        for ($i = 0; $i < count($map[0]); $i++) {
            $nextIndex = $i + 1;

            if (!array_key_exists($nextIndex, $map[0])) {
                continue;
            }

            if (array_column($map, $i) !== array_column($map, $nextIndex)) {
                continue;
            }

            $colsWithReflection[] = [$i, $nextIndex];
        }

        return $colsWithReflection;
    }

    private function mapHasColRefection(array $map, int $colPrev, int $colNext): bool
    {
        if ($colPrev === 0 || $colNext === (count($map[0]) - 1)) {
            return true;
        }

        while (array_key_exists($colPrev - 1, $map[0]) && array_key_exists($colNext + 1, $map[0])) {
            if (array_column($map, $colPrev - 1) !== array_column($map, $colNext + 1)) {
                return false;
            }

            $colPrev--;
            $colNext++;
        }

        return true;
    }

    private function differByOne(array $a, array $b): bool
    {
        $diff = 0;

        foreach ($a as $i => $char) {
            if ($char !== $b[$i]) {
                $diff++;
                
                if ($diff > 1) {
                    return false;
                }
            }
        }

        return $diff === 1;
    }
}
