<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day03 extends DayBase
{
    protected const int TEST_1 = 157;
    protected const int TEST_2 = 70;

    private array $groups = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->data = array_map(function ($line) {
            $parts = str_split($line, strlen($line) / 2);
            return array_map('str_split', $parts);
        }, $this->data);

        $this->groups = array_chunk($this->data, 3);

        if ($this->test) {
            print_r($this->data);
            print_r($this->groups);
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
        return $this->getTotalPriority($this->data);
    }

    private function getPart2(): int
    {
        $groupData = [];

        foreach ($this->groups as $group) {
            $groupData[] = array_map(fn ($rucksack) => array_merge(...$rucksack), $group);
        }

        if ($this->test) {
            print_r($groupData);
            echo PHP_EOL;
        }

        return $this->getTotalPriority($groupData);
    }

    private function getTotalPriority(array $data): int
    {
        $totalPriority = 0;

        foreach ($data as $rucksack) {
            $different = array_intersect(...$rucksack);
            $common = reset($different);
            $totalPriority += $this->getCharPriority($common);
        }

        return $totalPriority;
    }

    private function getCharPriority(string $char): int
    {
        $ascii = ord($char);

        if ($ascii >= ord('a') && $ascii <= ord('z')) {
            return $ascii - ord('a') + 1;
        }

        if ($ascii >= ord('A') && $ascii <= ord('Z')) {
            return $ascii - ord('A') + 27;
        }

        return 0;
    }
}
