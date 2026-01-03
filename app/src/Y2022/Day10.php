<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day10 extends DayBase
{
    protected const int TEST_1 = 13140;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        if ($this->test) {
            print_r($this->data);
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
        $signalStrengthTotal = 0;

        foreach ($this->getSignals() as $cycle => $signal) {
            $signalStrengthTotal += $cycle * $signal;
        }

        return $signalStrengthTotal;
    }

    private function getPart2(): int
    {
        $this->createCRTImage();

        return 0;
    }

    private function getSignals(): array
    {
        $signals = [20 => 0, 60 => 0, 100 => 0, 140 => 0, 180 => 0, 220 => 0];
        $signalsPerCycle = [];
        $x = 1;
        $cycle = 1;

        foreach ($this->data as $instruction) {
            $addCycles = $instruction === 'noop' ? 1 : 2;
            $addX = $instruction === 'noop' ? 0 : (int)trim(str_replace('addx ', '', $instruction));

            for ($i = 0; $i < $addCycles; $i++) {
                $signalsPerCycle[$cycle] = $x . ' (' . $instruction . ')';

                if (isset($signals[$cycle])) {
                    $signals[$cycle] = $x;
                }

                if ($cycle >= array_key_last($signals)) {
                    break 2;
                }

                $cycle++;
            }

            $x += $addX;
        }

        if ($this->test) {
            print_r($signalsPerCycle);
            print_r($signals);
            echo PHP_EOL;
        }

        return $signals;
    }

    private function createCRTImage(): void
    {
        $crtRows = [];
        $spritePosition = array_fill(0, 40, '.');
        $spritePosition[0] = '#';
        $spritePosition[1] = '#';
        $spritePosition[2] = '#';
        $x = 1;
        $cycle = 0;

        foreach ($this->data as $instruction) {
            $addCycles = $instruction === 'noop' ? 1 : 2;
            $addX = $instruction === 'noop' ? 0 : (int)trim(str_replace('addx ', '', $instruction));

            for ($i = 0; $i < $addCycles; $i++) {
                $row = (int)floor($cycle / 40);
                $rowPosition = isset($crtRows[$row]) ? count($crtRows[$row]) : 0;
                $crtRows[$row][] = $spritePosition[$rowPosition];

                $cycle++;
            }

            $x += $addX;

            $spritePosition = array_fill(0, 40, '.');
            $spritePosition[$x - 1] = '#';
            $spritePosition[$x] = '#';
            $spritePosition[$x + 1] = '#';

            if ($this->test) {
                echo 'Cycle: ' .$cycle . PHP_EOL;
                echo 'Sprite position: ' . implode('', $spritePosition) . PHP_EOL;
                echo 'CRT rows:' . PHP_EOL;
                $this->printMap($crtRows);
                echo PHP_EOL;
            }
        }

        echo PHP_EOL;
        echo 'Final sprite position: ' . implode('', $spritePosition) . PHP_EOL;
        echo 'Final CRT rows:' . PHP_EOL;
        $this->printMap($crtRows);
        echo PHP_EOL;
    }
}
