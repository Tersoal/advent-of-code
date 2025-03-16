<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day05 extends DayBase
{
    protected const int TEST_1 = 143;
    protected const int TEST_2 = 123;

    public array $rules = [];

    public function loadData(string $filePath): void
    {
        $filename = __DIR__ . "/../../data/2024/day05/" . ($this->test ? "day05-rules-test.txt" : "day05-rules.txt");

        $rules = file_get_contents($filename);
        $rules = str_replace(self::BOM,'', $rules);
        $rules = explode("\r\n", $rules);

        $callback = fn(string $row): array => explode('|', $row);
        $this->rules = array_map($callback, $rules);

        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n", $data);

        $callback = fn(string $row): array => explode(',', $row);
        $this->data = array_map($callback, $data);
    }

    public function getResult(): array
    {
        return [$this->getMiddlePageNumbersSum(), $this->getFixedMiddlePageNumbersSum()];
    }

    public function getMiddlePageNumbersSum(): int
    {
        $updates = [];

        foreach ($this->data as $update) {
            if (!$this->isUpdateOk($update)) {
                continue;
            }

            $updates[] = $update;
        }

        $count = 0;

        foreach ($updates as $update) {
            $key = (int)((count($update) - 1)/2);
            $count += $update[$key];
        }

        return $count;
    }

    public function getFixedMiddlePageNumbersSum(): int
    {
        $updates = [];

        foreach ($this->data as $update) {
            if ($this->isUpdateOk($update)) {
                continue;
            }

            $updates[] = $this->getFixedUpdate($update);
        }

        $count = 0;

        foreach ($updates as $update) {
            $key = (int)((count($update) - 1)/2);
            $count += $update[$key];
        }

        return $count;
    }

    public function isUpdateOk(array $update): bool
    {
        for ($i = 1; $i < count($update); $i++) {
            for ($j = 0; $j < $i; $j++) {
                if (!in_array([$update[$j], $update[$i]], $this->rules)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getFixedUpdate(array $update): array
    {
        $fixed = $update;

        for ($i = 1; $i < count($update); $i++) {
            for ($j = 0; $j < $i; $j++) {
                if (in_array([$update[$j], $update[$i]], $this->rules)) {
                    continue;
                }

                $fixed[$j] = $update[$i];
                $fixed[$i] = $update[$j];

                return $this->getFixedUpdate($fixed);
            }
        }

        return $fixed;
    }
}
