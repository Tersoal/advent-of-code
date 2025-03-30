<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day04 extends DayBase
{
    protected const int TEST_1 = 13;
    protected const int TEST_2 = 30;

    protected array $cards = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        foreach ($this->data as $line) {
            $data = explode(":", $line);
            $cardId = (int)str_replace('Card ', "", $data[0]);
            $sets = explode("|", $data[1]);

            $this->cards[$cardId]['winning'] = array_map(fn($n) => (int)$n, array_filter(explode(" ", trim($sets[0]))));
            $this->cards[$cardId]['my'] = array_map(fn($n) => (int)$n, array_filter(explode(" ", trim($sets[1]))));
            $this->cards[$cardId]['instances'] = 1;
        }

//        print_r($this->cards);
    }

    public function getResult(): array
    {
        return [$this->getSumOfPoints(), $this->getCardsTotal()];
    }

    private function getSumOfPoints(): int
    {
        $sum = 0;

        foreach ($this->cards as $card) {
            $numbers = array_filter($card['my'], fn($n) => in_array($n, $card['winning']));

            if (count($numbers) === 0) {
                continue;
            }

            $sum += pow(2, count($numbers) - 1);
        }

        return $sum;
    }

    private function getCardsTotal(): int
    {
        $instances = [];

        foreach ($this->cards as $id => $card) {
            if (!array_key_exists($id, $instances)) {
                $instances[$id] = 1;
            }

            $numbers = array_filter($card['my'], fn($n) => in_array($n, $card['winning']));

            if (count($numbers) === 0) {
                continue;
            }

            for ($i = 1; $i <= count($numbers); $i++) {
                if (!array_key_exists($id + $i, $this->cards)) {
                    continue;
                }

                if (!array_key_exists($id + $i, $instances)) {
                    $instances[$id + $i] = 1;
                }

                $instances[$id + $i] += $instances[$id];
            }
        }

//        print_r($instances);

        return array_sum($instances);
    }
}
