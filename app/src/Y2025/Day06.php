<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day06 extends DayBase
{
    protected const int TEST_1 = 4277556;
    protected const int TEST_2 = 3263827;

    protected const string MULTIPLY = '*';
    protected const string SUM = '+';

    protected array $problems = [];
    protected array $problems2 = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $callback = fn(string $row): array => array_values(array_filter(explode(" ", $row)));
        $data = array_map($callback, $this->data);

        foreach ($data as $row) {
            foreach ($row as $column => $operand) {
                if (is_numeric($operand)) {
                    $this->problems[$column]['operands'][] = (int)$operand;
                } else {
                    $this->problems[$column]['operator'] = $operand;
                }
            }
        }

        // We must add space at the end of the test data in rows o and 1 because we lost them, I don't know why
        if ($this->test) {
            $this->data[0] = $this->data[0] . ' ';
            $this->data[1] = $this->data[1] . ' ';
        }

//        print_r($this->data);
//        exit();

        $callback = fn(string $row): array => str_split(strrev($row));
        $data = array_map($callback, $this->data);

//        print_r($data);

        $rowsCount = count($data) - 1; // to ignore operands in the last row
        $colsCount = count($data[0]);
        $problemCount = 0;

        for ($x = 0; $x < $colsCount; $x++) {
            $digits = [];

            for ($y = 0; $y < $rowsCount; $y++) {
                $digits[] = $data[$y][$x];
            }

            if (empty(array_filter(explode(" ", implode("", $digits))))) {
                $problemCount++;
            } else {
                $this->problems2[$problemCount]['operands'][] = (int) implode("", array_filter($digits));
            }
        }

//        print_r($data[$rowsCount]);

        $operators = implode("", $data[$rowsCount]);
        $operators = array_values(array_filter(explode(" ", $operators)));

//        print_r($operators);

        foreach ($operators as $column => $operator) {
            $this->problems2[$column]['operator'] = $operator;
        }

//        print_r($this->data);
//        print_r($this->problems);
//        print_r($this->problems2);
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
        return $this->getProblemsResult($this->problems);
    }

    private function getPart2(): int
    {
        return $this->getProblemsResult($this->problems2);
    }

    private function getProblemsResult(array $problems): int
    {
        $result = 0;

        foreach ($problems as $problem) {
            $result += $this->getOperationResult($problem['operands'], $problem['operator']);
        }

        return $result;
    }

    private function getOperationResult(array $operands, string $operator): int
    {
        if ($operator === self::MULTIPLY) {
            return array_product($operands);
        }

        if ($operator === self::SUM) {
            return array_sum($operands);
        }

        return 0;
    }
}
