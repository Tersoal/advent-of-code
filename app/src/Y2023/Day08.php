<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day08 extends DayBase
{
    protected const int TEST_1 = 2;
    protected const int TEST_2 = 6;
    protected const string START = 'AAA';
    protected const string START2 = 'A';
    protected const string END = 'ZZZ';
    protected const string END2 = 'Z';
    protected const string RIGHT = 'R';
    protected const string LEFT = 'L';

    protected array $instructions = [];
    protected array $nodes = [];
    protected array $data2 = [];
    protected array $instructions2 = [];
    protected array $nodes2 = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        $this->instructions = str_split(trim($this->data[0]));

        foreach ($this->data as $key => $line) {
            if ($key < 2) {
                continue;
            }

            $parts = explode(" = ", $line);
            $this->nodes[$parts[0]] = explode(", ", trim($parts[1], '()'));
        }

        $this->instructions2 = $this->instructions;
        $this->nodes2 = $this->nodes;

        if ($this->test) {
            $filename = __DIR__ . "/../../data/2023/day08/day08-test2.txt";

            $data = file_get_contents($filename);
            $data = str_replace(self::BOM,'', $data);
            $this->data2 = explode("\r\n", $data);

            $this->instructions2 = str_split(trim($this->data2[0]));
            $this->nodes2 = [];

            foreach ($this->data2 as $key => $line) {
                if ($key < 2) {
                    continue;
                }

                $parts = explode(" = ", $line);
                $this->nodes2[$parts[0]] = explode(", ", trim($parts[1], '()'));
            }
        }

//        print_r($this->instructions);
//        print_r($this->nodes);
//        print_r($this->instructions2);
//        print_r($this->nodes2);
    }

    public function getResult(): array
    {
        return [$this->getSteps(), $this->getSteps2()];
    }

    private function getSteps(): int
    {
        return $this->getNextStep(self::START, 0,0);
    }

    public function getNextStep(string $currentStep, int $currentInstruction, int $totalSteps): int
    {
//        print_r(['$currentStep' => $currentStep, '$currentInstruction' => $currentInstruction, '$totalSteps' => $totalSteps]);
//        if ($totalSteps > 20) {
//            return $totalSteps;
//        }

        $instructionIndex = $this->instructions[$currentInstruction] === self::RIGHT ? 1 : 0;
        $nextStep = $this->nodes[$currentStep][$instructionIndex];

        if ($nextStep === self::END) {
            return $totalSteps + 1;
        }

        $nextInstruction = $currentInstruction + 1;

        if (!array_key_exists($nextInstruction, $this->instructions)) {
            $nextInstruction = 0;
        }

        return $this->getNextStep($nextStep, $nextInstruction,$totalSteps + 1);
    }

    private function getSteps2(): int
    {
        $currentSteps = array_filter(array_keys($this->nodes2), fn (string $node) => str_ends_with($node, self::START2));
        $steps = [];

        foreach ($currentSteps as $step) {
            $steps[] = $this->getNextStep2($step, 0,0);
        }

        return $this->getLcm($steps, count($steps));
    }

    public function getNextStep2(string $currentStep, int $currentInstruction, int $totalSteps): int
    {
//        print_r(['$currentStep' => $currentStep, '$currentInstruction' => $currentInstruction, '$totalSteps' => $totalSteps]);
//        if ($totalSteps > 20) {
//            return $totalSteps;
//        }

        $instructionIndex = $this->instructions2[$currentInstruction] === self::RIGHT ? 1 : 0;
        $nextStep = $this->nodes2[$currentStep][$instructionIndex];

        if (str_ends_with($nextStep, self::END2)) {
            return $totalSteps + 1;
        }

        $nextInstruction = $currentInstruction + 1;

        if (!array_key_exists($nextInstruction, $this->instructions2)) {
            $nextInstruction = 0;
        }

        return $this->getNextStep2($nextStep, $nextInstruction,$totalSteps + 1);
    }

    /**
     * Ref: https://www.skillpundit.com/php/php-find-lcm-n-numbers.php
     */
    function getGcd(int $a, int $b): int
    {
        if ($b == 0) {
            return $a;
        }

        return $this->getGcd($b, $a % $b);
    }

    function getLcm(array $arr, int $n): int
    {
        // Initialize result
        $ans = $arr[0];

        // Ans contains LCM of arr[0], ..arr[i] after i'th iteration.
        for ($i = 1; $i < $n; $i++) {
            $ans = ((($arr[$i] * $ans)) / ($this->getGcd($arr[$i], $ans)));
        }

        return $ans;
    }





    /**
     * Parallel does not work because infinite loop. LCM must be applied.
     */

//    private function getSteps2(): int
//    {
//        $currentSteps = array_filter(array_keys($this->nodes2), fn (string $node) => str_ends_with($node, self::START2));
//
//        return $this->getNextStep2($currentSteps, 0,0);
//    }
//
//    public function getNextStep2(array &$currentSteps, int $currentInstruction, int $totalSteps): int
//    {
////        print_r(['$currentSteps' => $currentSteps, '$currentInstruction' => $currentInstruction, '$totalSteps' => $totalSteps]);
////        if ($totalSteps > 20) {
////            return $totalSteps;
////        }
////        if ($totalSteps === 100000) {
////            print_r(['$currentSteps' => $currentSteps, '$currentInstruction' => $currentInstruction, '$totalSteps' => $totalSteps]);
////        }
//
//        $instructionIndex = $this->instructions2[$currentInstruction] === self::RIGHT ? 1 : 0;
//        $currentSteps = array_map(fn(string $step) => $this->nodes2[$step][$instructionIndex], $currentSteps);
//
//        if (array_all($currentSteps, fn(string $step) => str_ends_with($step, self::END2))) {
//            return $totalSteps + 1;
//        }
//
//        $nextInstruction = $currentInstruction + 1;
//
//        if (!array_key_exists($nextInstruction, $this->instructions2)) {
//            $nextInstruction = 0;
//        }
//
//        return $this->getNextStep2($currentSteps, $nextInstruction,$totalSteps + 1);
//    }
}
