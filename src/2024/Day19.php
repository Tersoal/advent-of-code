<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day19 extends DayBase
{
    protected const int TEST_1 = 6;
    protected const int TEST_2 = 0;

    private array $patterns = [];
    private int $patternsMinLength = 0;
    private int $patternsMaxLength = 0;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $this->data = explode("\r\n", $data);

        $this->patterns = explode(", ", $this->data[0]);
        usort($this->patterns, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        $this->patternsMaxLength = strlen($this->patterns[0]);
        $this->patternsMinLength = strlen($this->patterns[count($this->patterns) - 1]);

        array_shift($this->data);
        array_shift($this->data);

//        echo "Patterns = " . json_encode($this->patterns) . "\n";
//        echo "Towels = " . "\n";
//        foreach ($this->data as $row) {
//            echo $row . PHP_EOL;
//        }
    }

    public function getResult(): array
    {
        $designs = [];

        foreach ($this->data as $key => $design) {
            echo "Current Design # " . $key + 1 . "/" . count($this->data) . ": " . $design . "\n";

            if ($this->designIsPossible($design)) {
                $designs[] = $design;
            }
        }

        return [count($designs), 0];
    }

//    private function designIsPossible(string $design): bool
//    {
//        //echo "Current Design: " . $design . "\n";
//
//        $patterns = array_filter($this->patterns, function ($pattern) use ($design) {
//            return str_starts_with($design, $pattern);
//        });
//
//        //var_dump($patterns);
//
//        foreach ($patterns as $pattern) {
//            $newDesign = substr($design, strlen($pattern));
//
//            //echo "New Design: " . $newDesign . "\n";
//
//            if (strlen($newDesign) === 0) {
//                return true;
//            }
//
//            if (!$this->designIsPossible($newDesign)) {
//               continue;
//            }
//
//            return true;
//        }
//
//        return false;
//    }

    private function designIsPossible(string $design): bool
    {
        while (strlen($design) > 0) {
            $lengthCounter = $this->patternsMaxLength;

            if (strlen($design) < $lengthCounter) {
                $partOfDesignToCheck = $design;
                $lengthCounter = strlen($design);
            } else {
                $partOfDesignToCheck = substr($design, 0, $lengthCounter);
            }

            $stop = false;

            while (!$stop) {
                //echo "partOfDesignToCheck: " . $partOfDesignToCheck . "\n";

                if (in_array($partOfDesignToCheck, $this->patterns)) {
                    $stop = true;
                    continue;
                }

                $lengthCounter--;

                if ($lengthCounter < $this->patternsMinLength) {
                    echo "Fails because length is lower than minimum and " . $partOfDesignToCheck . " is not found in patterns. \n";

                    $stop = true;
                    return false;
                }

                $partOfDesignToCheck = substr($partOfDesignToCheck, 0, $lengthCounter);
            }

            $design = substr($design, $lengthCounter);
        }

        return true;
    }
}
