<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day03 extends DayBase
{
    private const string DONT = "don't()";
    private const string DO = "do()";
    protected const int TEST_1 = 161;
    protected const int TEST_2 = 48;

    public string $inputData = '';

    public function loadData(string $filePath): void
    {
        $this->inputData = file_get_contents($filePath);
        $this->inputData = str_replace(self::BOM,'', $this->inputData);
    }

    public function getResult(): array
    {
        return [$this->getSumResult(), $this->getSumResultOnlyEnabled()];
    }

    public function getSumResult(): int
    {
        return $this->getSum($this->inputData);
    }

    public function getSumResultOnlyEnabled(): int
    {
        $texts = $this->getEnabledTexts($this->inputData);
        $count = 0;

        foreach ($texts as $text) {
            $count += $this->getSum($text);
        }

        return $count;
    }

    public function getSum(string $data): int
    {
        $count = 0;

        preg_match_all('/(mul)\(\d+,\d+\)/', $data, $matches);
        //var_dump($matches);
        $results = $matches[0];
        //var_dump($results);

        foreach ($results as $result) {
            $operands = explode(',', str_replace(['mul(', ')'], '', $result));
            //var_dump($operands);
            //var_dump(array_product($operands));
            $count += array_product($operands);
        }

        return $count;
    }

    public function getEnabledTexts(string $data): array
    {
        $texts = explode(self::DONT, $data);
        //var_dump($texts);

        foreach ($texts as $key => $text) {
            // First is always without don't
            if ($key === 0 || strlen($text) === 0) {
                continue;
            }

            if (($pos = strpos($text, self::DO)) === false) {
                unset($texts[$key]);
            } else {
                $texts[$key] = substr($text, $pos + strlen(self::DO));
            }
        }

        array_filter($texts);
        //var_dump($texts);

        return $texts;
    }
}
