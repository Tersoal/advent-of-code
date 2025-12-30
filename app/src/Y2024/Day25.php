<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day25 extends DayBase
{
    protected const int TEST_1 = 3;
    protected const int TEST_2 = 0;

    private array $locks = [];
    private array $keys = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n\r\n", $data);

        $callback = fn(string $row): array => str_split($row);

        foreach ($data as $line) {
            $line = explode("\r\n", $line);
            $this->data[] = array_map($callback, $line);
        }

        foreach ($this->data as $data) {
            $item = [];

            for ($i = 0; $i < count($data[0]); $i++) {
                $values = array_column($data, $i);
                $values = array_filter($values, fn ($value) => $value === self::WALL);
                $item[] = count($values) - 1;
            }

            if ($data[0][0] === self::WALL) {
                $this->locks[] = $item;
            } else {
                $this->keys[] = $item;
            }
        }

//        var_dump($this->locks);
//        var_dump($this->keys);
    }

    public function getResult(): array
    {
        return [count($this->getPairs()), 0];
    }

    private function getPairs(): array
    {
        $pins = count($this->locks[0]);
        $pairs = [];

        foreach ($this->locks as $l => $lock) {
            foreach ($this->keys as $k => $key) {
                $fit = true;

                for ($i = 0; $i < $pins; $i++) {
                    if (($lock[$i] + $key[$i]) > 5) {
                        $fit = false;
                        break;
                    }
                }

                if ($fit) {
                    $pairs[] = [$l, $k];
                }
            }
        }

//        var_dump($pairs);

        return $pairs;
    }
}
