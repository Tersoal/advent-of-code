<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day01 extends DayBase
{
    protected const int TEST_1 = 142;
    protected const int TEST_2 = 281;

    protected array $numberMap = ["zero" => "0", "one" => "1", "two" => "2", "three" => "3", "four" => "4", "five" => "5", "six" => "6", "seven" => "7", "eight" => "8", "nine" => "9"];
    protected array $reversedNumberMap = [];
    protected array $data2 = [];

    public function loadData(string $filePath): void
    {
        if ($this->test) {
            $filename = __DIR__ . "/../../data/2023/day01/day01-test2.txt";

            $data = file_get_contents($filename);
            $data = str_replace(self::BOM,'', $data);
            $this->data2 = explode("\r\n", $data);
        }

        $this->loadDataAsArray($filePath, "\r\n");

        // Number map in reverse order
        foreach ($this->numberMap as $word => $digit) {
            $this->reversedNumberMap[strrev($word)] = $digit;
        }
    }

    public function getResult(): array
    {
        $data2 = $this->test ? $this->data2 : $this->data;

        return [$this->getCalibration($this->data, false), $this->getCalibration($data2, true)];
    }

    private function getCalibration(array $data, bool $applyNames = false): int
    {
        $sum = 0;

        foreach ($data as $datum) {
            $number = $this->extractFirstAndLastNumber($datum, $applyNames);

            if (empty($number)) {
                continue;
            }

            $sum += (int)$number;
        }

       return $sum;
    }

    private function extractFirstAndLastNumber(string $text, bool $applyNames = false): string
    {
        if (!$applyNames) {
            $datum = preg_replace('/[^0-9]/', '', $text);
            $datum = str_split($datum);

            return $datum[array_key_first($datum)] . $datum[array_key_last($datum)];
        }

        // Regular expression to find first number
        $pattern = '/' . implode('|', array_keys($this->numberMap)) . '|\d/';
        // Regular expression to find first number in reverse mode (i.e. last number)
        $reversedPattern = '/' . implode('|', array_keys($this->reversedNumberMap)) . '|\d/';

        // Find first
        preg_match($pattern, $text, $firstMatch);
        // Find first in reversed text
        preg_match($reversedPattern, strrev($text), $lastMatch);

        if (empty($firstMatch) || empty($lastMatch)) {
            return ''; // No numbers
        }

        // Convert to number if necessary
        $first = $this->numberMap[$firstMatch[0]] ?? $firstMatch[0];

        // Undo reverse to find number
        $reversedLast = strrev($lastMatch[0]);
        $last = $this->numberMap[$reversedLast] ?? $reversedLast;

        return $first . $last;
    }
}
