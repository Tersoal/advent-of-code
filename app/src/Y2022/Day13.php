<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day13 extends DayBase
{
    protected const int TEST_1 = 13;
    protected const int TEST_2 = 140;

    private array $packets = [];
    private array $packets2 = [];
    private const string DIVIDER_PACKER_1 = '[[2]]';
    private const string DIVIDER_PACKER_2 = '[[6]]';

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $index = 0;

        foreach ($this->data as $line) {
            if (empty($line)) {
                $index++;
                continue;
            }

            $this->packets[$index][] = json_decode($line, true);
            $this->packets2[] = $line;
        }

        $this->packets2[] = self::DIVIDER_PACKER_1;
        $this->packets2[] = self::DIVIDER_PACKER_2;

        if ($this->test) {
            print_r($this->data);
            print_r($this->packets);
            print_r($this->packets2);
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
        $indices = [];

        foreach ($this->packets as $index => $packet) {
//            echo PHP_EOL;
//            echo '-------------------------------' . PHP_EOL;
//            echo "Index: " . ($index + 1) . PHP_EOL;
//            echo '-------------------------------' . PHP_EOL;
//            print_r($packet);
//            echo PHP_EOL;

            if ($this->comparePairs($packet[0], $packet[1]) !== false) {
                $indices[] = $index + 1;
            }
        }

        print_r($indices);
        echo PHP_EOL;

        return array_sum($indices);
    }

    /**
     * 18706 too low
     */
    private function getPart2(): int
    {
        $packets = $this->sortPackets($this->packets2);
        $packetsEncoded = array_map(fn ($packet) => json_encode($packet), $packets);

        print_r($packets);
        print_r($packetsEncoded);
        echo PHP_EOL;

        $divider1Index = array_search(self::DIVIDER_PACKER_1, $packetsEncoded);
        $divider2Index = array_search(self::DIVIDER_PACKER_2, $packetsEncoded);

        echo 'Divider 1 Index: ' . ($divider1Index + 1) . PHP_EOL;
        echo 'Divider 2 Index: ' . ($divider2Index + 1) . PHP_EOL;

        return ($divider1Index + 1) * ($divider2Index + 1);
    }

    private function comparePairs(array $pair1, array $pair2): ?bool
    {
//        echo 'Value 1: ' . json_encode($pair1) . PHP_EOL;
//        echo 'Value 2: ' . json_encode($pair2) . PHP_EOL;

        if (empty($pair1) && !empty($pair2)) {
            return true;
        }

        if (!empty($pair1) && empty($pair2)) {
            return false;
        }

        $maxCount = max(count($pair1), count($pair2));

        for ($i = 0; $i < $maxCount; $i++) {
            if (!isset($pair1[$i]) && isset($pair2[$i])) {
                return true;
            }

            if (isset($pair1[$i]) && !isset($pair2[$i])) {
                return false;
            }

            $value1 = $pair1[$i];
            $value2 = $pair2[$i];

            if (!is_array($value1) && !is_array($value2)) {
                if ($value1 < $value2) {
                    return true;
                }

                if ($value1 > $value2) {
                    return false;
                }

                continue;
            }

            if (!is_array($value1)) {
                $value1 = [$value1];
            }

            if (!is_array($value2)) {
                $value2 = [$value2];
            }

            $result = $this->comparePairs($value1, $value2);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    private function sortPackets(array $packets): array
    {
        $packets = array_map(fn ($packet) => json_decode($packet, true), $packets);

        usort($packets, function ($a, $b) {
            $cmp = $this->comparePairs($a, $b);

            if ($cmp === null) {
                return 0;
            }

            return $cmp ? -1 : 1;
        });

        return $packets;
    }
}
