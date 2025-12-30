<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day13 extends DayBase
{
    protected const int TEST_1 = 405;
    protected const int TEST_2 = 400;
    protected const int ROW_FACTOR = 100;
    protected const int COL_FACTOR = 1;

    protected array $maps = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $this->data = explode("\r\n\r\n", $data);

        foreach ($this->data as $map) {
            $map = explode("\r\n", $map);

            $callback = fn(string $row): array => str_split($row);
            $this->maps[] = array_map($callback, $map);
        }

//        foreach ($this->maps as $map) {
//            $this->printMap($map);
//            echo PHP_EOL;
//        }
    }

    /**
     * Code from ChatGPT because part2 was difficult. My original Code is in Day13Part1Original.
     * @return array
     */
    public function getResult(): array
    {
        return [$this->part1(), $this->part2()];
    }

    public function part1(): int
    {
        $total = 0;

        foreach ($this->maps as $map) {
            $map = array_map(function ($row) {
                return implode('', $row);
            }, $map);

            $total += $this->findReflection($map);
        }

        return $total;
    }

    public function part2(): int
    {
        $total = 0;

        foreach ($this->maps as $map) {
            $map = array_map(function ($row) {
                return implode('', $row);
            }, $map);

            $originalReflection = $this->findReflection($map);

            // Buscar nueva reflexión con smudge en filas
            $found = false;
            for ($row = 1; $row < count($map); $row++) {
                if ($row * 100 === $originalReflection) {
                    continue; // misma reflexión que parte 1
                }

                if ($this->mapHasRowReflection($map, $row, true)) {
                    $total += $row * 100;
                    $found = true;
                    break;
                }
            }

            if ($found) {
                continue;
            }

            // Buscar nueva reflexión con smudge en columnas
            $width = strlen($map[0]);
            for ($col = 1; $col < $width; $col++) {
                if ($col === $originalReflection) {
                    continue; // misma reflexión que parte 1
                }

                if ($this->mapHasColReflection($map, $col, true)) {
                    $total += $col;
                    break;
                }
            }
        }

        return $total;
    }

    private function findReflection(array $map): int
    {
        for ($row = 1; $row < count($map); $row++) {
            if ($this->mapHasRowReflection($map, $row)) {
                return $row * 100;
            }
        }

        $width = strlen($map[0]);
        for ($col = 1; $col < $width; $col++) {
            if ($this->mapHasColReflection($map, $col)) {
                return $col;
            }
        }

        throw new \RuntimeException("No reflection found");
    }

    private function mapHasRowReflection(array $map, int $row, bool $allowSmudge = false): bool
    {
        $smudgeUsed = false;
        for ($offset = 0; $row - $offset - 1 >= 0 && $row + $offset < count($map); $offset++) {
            $top = $map[$row - $offset - 1];
            $bottom = $map[$row + $offset];

            if ($top !== $bottom) {
                if (!$allowSmudge) {
                    return false;
                }

                $diffs = 0;
                for ($i = 0; $i < strlen($top); $i++) {
                    if ($top[$i] !== $bottom[$i]) {
                        $diffs++;
                        if ($diffs > 1 || $smudgeUsed) {
                            return false;
                        }
                    }
                }

                if ($diffs === 1) {
                    $smudgeUsed = true;
                } elseif ($diffs > 1) {
                    return false;
                }
            }
        }

        return !$allowSmudge || $smudgeUsed;
    }

    private function mapHasColReflection(array $map, int $col, bool $allowSmudge = false): bool
    {
        $smudgeUsed = false;
        $height = count($map);

        for ($offset = 0; $col - $offset - 1 >= 0 && $col + $offset < strlen($map[0]); $offset++) {
            for ($row = 0; $row < $height; $row++) {
                $left = $map[$row][$col - $offset - 1];
                $right = $map[$row][$col + $offset];

                if ($left !== $right) {
                    if (!$allowSmudge) {
                        return false;
                    }

                    if ($smudgeUsed) {
                        return false;
                    }

                    $smudgeUsed = true;
                }
            }
        }

        return !$allowSmudge || $smudgeUsed;
    }
}
