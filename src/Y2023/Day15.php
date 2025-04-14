<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day15 extends DayBase
{
    protected const int TEST_1 = 1320;
    protected const int TEST_2 = 145;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, ',');

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getSumOfHashes(), $this->getTotalFocusingPower()];
    }

    public function getSumOfHashes(): int
    {
        $results = [];
        $cachedSequences = [];

        foreach ($this->data as $key => $sequence) {
            $results[$key . '||' . $sequence] = $this->getHashResult($sequence, $cachedSequences);
        }

//        print_r($results);
//        echo PHP_EOL;
        
        return array_sum($results);
    }

    public function getTotalFocusingPower(): int
    {
        $boxes = $this->getBoxes();

//        print_r($boxes);
//        echo PHP_EOL;

        $total = 0;

        foreach ($boxes as $box => $lens) {
            if (empty($lens)) {
                continue;
            }

            $labels = array_keys($lens);

            foreach ($labels as $index => $label) {
                $total += ($box + 1) * ($index + 1) * $lens[$label];
            }
        }

        return $total;
    }

    private function getBoxes(): array
    {
        $boxes = [];
        $cachedSequences = [];

        foreach ($this->data as $sequence) {
            if (str_ends_with($sequence, '-')) {
                $label = substr($sequence, 0, -1);
                $box = $this->getHashResult($label, $cachedSequences);

                unset($boxes[$box][$label]);
            } else {
                $parts = explode('=', $sequence);
                $label = $parts[0];
                $focalLength = $parts[1];
                $box = $this->getHashResult($label, $cachedSequences);
                $boxes[$box][$label] = (int)$focalLength;
            }
        }

        return $boxes;
    }

    private function getHashResult(string $sequence, array &$cachedSequences): int
    {
        if (isset($cachedSequences[$sequence])) {
            return $cachedSequences[$sequence];
        }

        $result = 0;

        for ($i = 0; $i < strlen($sequence); ++$i) {
            $result += ord($sequence[$i]);
            $result *= 17;
            $result %= 256;
        }

        $cachedSequences[$sequence] = $result;

        return $result;
    }
}
