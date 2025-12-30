<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day06 extends DayBase
{
    protected const int TEST_1 = 288;
    protected const int TEST_2 = 71503;

    protected array $races = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        $times = array_filter(explode(' ', trim(str_replace('Time: ', "", $this->data[0]))));
        $times = array_map(fn ($t) => (int)$t, $times);
        $distances = array_filter(explode(' ', trim(str_replace('Distance: ', "", $this->data[1]))));
        $distances = array_map(fn ($d) => (int)$d, $distances);

        $this->races = array_combine($times, $distances);

//        print_r($this->races);
    }

    public function getResult(): array
    {
        $time = (int)implode('', array_keys($this->races));
        $distance = (int)implode('', array_values($this->races));

        return [$this->getProduct($this->races), $this->getProduct([$time => $distance])];
    }

    private function getProduct(array $races): int
    {
        $numbers = [];

        foreach ($races as $time => $distance) {
            $numbers[] = $this->getNumberOfBeats($time, $distance);
        }

//        print_r($numbers);

        return array_product($numbers);
    }

    private function getNumberOfBeats(int $time, int $distance): int
    {
        $min = 1;
        while (($min * ($time - $min)) <= $distance) {
            $min++;
        }

        $max = $time - 1;
        while (($max * ($time - $max)) <= $distance) {
            $max--;
        }

        return $max - $min + 1;
    }
}
