<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day04 extends DayBase
{
    private const string XMAS = 'XMAS';
    private const string MAS = 'MAS';
    protected const int TEST_1 = 18;
    protected const int TEST_2 = 9;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        return [$this->getXmasCount(), $this->getCrossedMasCount()];
    }

    public function getXmasCount(): int
    {
        $combinations = $this->getSoupCombinations($this->data);
        //var_dump($combinations);
        $count = 0;

        foreach ($combinations as $combination) {
            preg_match_all('/('.self::XMAS.')/', $combination, $matches);
            $count += count($matches[0]);

            preg_match_all('/('.self::XMAS.')/', strrev($combination), $matches);
            $count += count($matches[0]);
        }

        return $count;
    }

    public function getCrossedMasCount(): int
    {
        $count = 0;

        for ($y = 1; $y < count($this->data) - 1; $y++) {
            for ($x = 1; $x < count($this->data[$y]) - 1; $x++) {
                if ($this->data[$y][$x] !== 'A') {
                    continue;
                }

                $option = $this->data[$y - 1][$x - 1] . $this->data[$y][$x] . $this->data[$y + 1][$x + 1];
                if ($option !== self::MAS && strrev($option) !== self::MAS) {
                    continue;
                }

                $option = $this->data[$y - 1][$x + 1] . $this->data[$y][$x] . $this->data[$y + 1][$x - 1];
                if ($option !== self::MAS && strrev($option) !== self::MAS) {
                    continue;
                }

                $count++;
            }
        }

        return $count;
    }

    public function getSoupCombinations(array $data): array
    {
        $rows = array_map(function($row) {
            return implode('', $row);
        }, $data);

        $columns = array_map(function($column) use ($data) {
            $col = array_column($data, $column);
            return implode('', $col);
        }, array_keys($data[0]));

        $diagonals = [];
        $maxRows = count($data);
        $maxCols = count($data[0]);

        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                // to right, all columns for first row and only first column for rest of the rows
                if ($y === 0 || $x === 0) {
                    $diagonal = [];
                    for ($point = 0; $point < ($maxRows - $x); $point++) {
                        if (array_key_exists($y + $point, $data) && array_key_exists($x + $point, $data[$y + $point])) {
                            $diagonal[] = $data[$y + $point][$x + $point];
                        }
                    }
                    $diagonals[] = implode('', $diagonal);
                }

                // to left, all columns for first row and only last column for rest of the rows
                if ($y === 0 || $x === $maxCols - 1) {
                    $diagonal = [];
                    for ($point = 0; $point < ($x + 1); $point++) {
                        if (array_key_exists($y + $point, $data) && array_key_exists($x - $point, $data[$y + $point])) {
                            $diagonal[] = $data[$y + $point][$x - $point];
                        }
                    }
                    $diagonals[] = implode('', $diagonal);
                }
            }
        }

        return [...$rows, ...$columns, ...$diagonals];
    }
}
