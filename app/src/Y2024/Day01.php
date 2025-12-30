<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day01 extends DayBase
{
    protected const int TEST_1 = 11;
    protected const int TEST_2 = 31;

    public array $dataLeft = [];
    public array $dataRight = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        foreach ($this->data as $line) {
            $line = explode(";", $line);

            $this->dataLeft[] = (int)$line[0];
            $this->dataRight[] = (int)$line[1];
        }
    }

    public function getResult(): array
    {
        return [$this->getDistance(), $this->getSimilarityScore()];
    }

    public function getDistance(): int
    {
        sort($this->dataLeft);
        sort($this->dataRight);

        $distance = 0;

        for ($i = 0; $i < count($this->dataLeft); $i++) {
            $distance += abs($this->dataLeft[$i] - $this->dataRight[$i]);
        }

       return $distance;
    }

    public function getSimilarityScore(): int
    {
        $score = 0;
        $valuesCounter = array_count_values($this->dataRight);

        foreach ($this->dataLeft as $value) {
            if (array_key_exists($value, $valuesCounter)) {
                $score += $value * $valuesCounter[$value];
            }
        }

        return $score;
    }
}
