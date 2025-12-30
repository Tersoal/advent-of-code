<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day11 extends DayBase
{
    protected const int TEST_1 = 55312;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath);
    }

    public function getResult(): array
    {
        return [
            $this->getStonesCount(25),
            $this->getStonesCount(75)
        ];
    }

    private function getStonesCount(int $blinks): int
    {
        $counter = 0;
        $stones = [];

        //var_dump($this->data);

        foreach ($this->data as $stone) {
            $counter += $this->getStonesAfterBlink($stones, (int)$stone, $blinks, 0);
        }

        //var_dump($stones);

        return $counter;
    }

    private function getStonesAfterBlink(array &$stones, int $stone, int $blinks, int $blink): int
    {
        if ($blink === $blinks) {
            return 1;
        }

        if (array_key_exists($stone, $stones) && array_key_exists($blink, $stones[$stone])) {
            return $stones[$stone][$blink];
        }

        if (strlen($stone) % 2 === 0) {
            $parts = str_split($stone, strlen($stone) / 2);
            $stones[$stone][$blink] = 0;

            foreach ($parts as $part) {
                $stones[$stone][$blink] += $this->getStonesAfterBlink($stones, (int)$part, $blinks, $blink + 1);
            }

            return $stones[$stone][$blink];
        }

        if ($stone === 0) {
            $stones[$stone][$blink] = $this->getStonesAfterBlink($stones, 1, $blinks, $blink + 1);

            return $stones[$stone][$blink];
        }

        $stones[$stone][$blink] = $this->getStonesAfterBlink($stones, $stone * 2024, $blinks, $blink + 1);

        return $stones[$stone][$blink];
    }

//    public function getStonesAfterBlink(array $stones): array
//    {
//        $newStones = [];
//
//        foreach ($stones as $stone) {
//            if ((int)$stone === 0) {
//                $newStones[] = 1;
//
//                continue;
//            }
//
//            if (strlen($stone) % 2 === 0) {
//                $parts = str_split($stone, strlen($stone) / 2);
//
//                $newStones[] = (int)$parts[0];
//                $newStones[] = (int)$parts[1];
//
//                continue;
//            }
//
//            $newStones[] = (int)$stone * 2024;
//        }
//
//        return $newStones;
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $stones = $this->data;
//
//        for ($i = 0; $i < $blinks; $i++) {
//            $stones = $this->getStonesAfterBlink($stones);
//        }
//
//        return count($stones);
//    }




//    public function getStonesAfterBlink(array &$stones, int $blinks, int $blink): void
//    {
//        if ($blink === $blinks) {
//            return;
//        }
//
//        $count = count($stones);
//
//        for ($i = 0; $i < $count; $i++) {
//            $stoneArray = $stones[$i];
//            $stones[$i] = [];
//
//            if (!is_array($stoneArray)) {
//                $stoneArray = [$stoneArray];
//            }
//
//            $c = count($stoneArray);
//
//            for ($j = 0; $j < $c; $j++) {
//                if ((int)$stoneArray[$j] === 0) {
//                    $stones[$i][] = 1;
//
//                    continue;
//                }
//
//                if (strlen($stoneArray[$j]) % 2 === 0) {
//                    $parts = str_split($stoneArray[$j], strlen($stoneArray[$j]) / 2);
//
//                    $stones[$i][] = (int)$parts[0];
//                    $stones[$i][] = (int)$parts[1];
//
//                    continue;
//                }
//
//                $stones[$i][] = (int)$stoneArray[$j] * 2024;
//            }
//        }
//
//        echo 'Blink ' . $blink . ' Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//
//        $this->getStonesAfterBlink($stones, $blinks, $blink + 1);
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $stones = $this->data;
//
//        $this->getStonesAfterBlink($stones, $blinks, 0);
//
//        return count(array_merge(...$stones));
//    }



//    public function getStonesAfterBlink(array &$stones, int $blinks, int $blink): void
//    {
//        if ($blink === $blinks) {
//            return;
//        }
//
//        $c = count($stones);
//        $st = $stones;
//        $stones = [];
//
//        for ($i = 0; $i < $c; $i++) {
//            if ((int)$st[$i] === 0) {
//                $stones[] = 1;
//
//                continue;
//            }
//
//            if (strlen($st[$i]) % 2 === 0) {
//                $parts = str_split($st[$i], strlen($st[$i]) / 2);
//
//                $stones[] = (int)$parts[0];
//                $stones[] = (int)$parts[1];
//
//                continue;
//            }
//
//            $stones[] = (int)$st[$i] * 2024;
//        }
//
//        echo 'Blink ' . $blink . ' Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//
//        $this->getStonesAfterBlink($stones, $blinks, $blink + 1);
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $stones = $this->data;
//
//        $this->getStonesAfterBlink($stones, $blinks, 0);
//
//        return count($stones);
//    }



//    public function getStonesAfterBlink(array &$stones, int &$count, int $blinks, int $blink): void
//    {
//        if ($blink === $blinks) {
//            $count += count($stones);
//            $stones = [];
//
//            echo "Count = " . $count . "\n";
//
//            return;
//        }
//
//        $c = count($stones);
//        $st = $stones;
//        $stones = [];
//
//        for ($i = 0; $i < $c; $i++) {
//            if ((int)$st[$i] === 0) {
//                $stones[] = 1;
//
//                continue;
//            }
//
//            if (strlen($st[$i]) % 2 === 0) {
//                $parts = str_split($st[$i], strlen($st[$i]) / 2);
//
//                $stones[] = (int)$parts[0];
//                $stones[] = (int)$parts[1];
//
//                continue;
//            }
//
//            $stones[] = (int)$st[$i] * 2024;
//        }
//
//        echo 'Blink ' . $blink . ' Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//
//        $chunks = array_chunk($stones, 3000000);
//        $cc = count($chunks);
//
//        for ($i = 0; $i < $cc; $i++) {
//            $this->getStonesAfterBlink($chunks[$i], $count, $blinks, $blink + 1);
//        }
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $stones = $this->data;
//        $count = 0;
//
//        $this->getStonesAfterBlink($stones, $count, $blinks, 0);
//
//        return $count;
//    }




//    public function getStonesAfterBlink(array &$stones, int &$counter, int $blinks, int $blink): void
//    {
//        if ($blink === $blinks - 1) {
//            $counter = count($stones) + count(array_filter($stones, function ($stone) {
//                return (strlen($stone) % 2 === 0);
//            }));
//
//            return;
//        }
//
//        $c = count($stones);
//        $newStones = [];
//
//        for ($i = 0; $i < $c; $i++) {
//            if ((int)$stones[$i] === 0) {
//                $newStones[] = 1;
//
//                continue;
//            }
//
//            if (strlen($stones[$i]) % 2 === 0) {
//                $parts = str_split($stones[$i], strlen($stones[$i]) / 2);
//
//                $newStones[] = (int)$parts[0];
//                $newStones[] = (int)$parts[1];
//
//                continue;
//            }
//
//            $newStones[] = (int)$stones[$i] * 2024;
//        }
//
//        echo 'Blink ' . $blink . ' Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//
//        $newStones = array_chunk($newStones, 1000000);
//        $cc = count($newStones);
//
//        for ($i = 0; $i < $cc; $i++) {
//            $this->getStonesAfterBlink($newStones[$i], $counter, $blinks, $blink + 1);
//        }
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $stones = $this->data;
//        $counter = 0;
//
//        $this->getStonesAfterBlink($stones, $counter, $blinks, 0);
//
//        return $counter;
//    }




//    public function getStonesAfterBlink(int $stone, int &$counter, int $blinks, int $blink): void
//    {
//        //echo 'Blink ' . $blink . ' Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//
//        if ($blink === $blinks - 1) {
//            $counter++;
//
//            if (strlen($stone) % 2 === 0) {
//                $counter++;
//            }
//
//            return;
//        }
//
//        if (strlen($stone) % 2 === 0) {
//            $parts = str_split($stone, strlen($stone) / 2);
//
//            foreach ($parts as $part) {
//                $this->getStonesAfterBlink((int)$part, $counter, $blinks, $blink + 1);
//            }
//
//            return;
//        }
//
//        if ($stone === 0) {
//            $this->getStonesAfterBlink(1, $counter, $blinks, $blink + 1);
//
//            return;
//        }
//
//        $this->getStonesAfterBlink($stone * 2024, $counter, $blinks, $blink + 1);
//    }
//
//    public function getStonesCount(int $blinks): int
//    {
//        $counter = 0;
//
//        foreach ($this->data as $stone) {
//            $this->getStonesAfterBlink((int)$stone, $counter, $blinks, 0);
//            echo 'Stone ' . $stone . '; Counter = ' . $counter . ' ; Memory consumed: ' . round(memory_get_usage() / 1024 / 1024) . "MB\n";
//        }
//
//        return $counter;
//    }
}
