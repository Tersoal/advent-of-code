<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day18 extends DayBase
{
    protected const int TEST_1 = 62;
    protected const int TEST_2 = 952408144115;

    protected array $directionsMap = ['R', 'D', 'L', 'U'];
    protected array $directions = ['R' => [0, 1], 'D' => [1, 0], 'L' => [0, -1], 'U' => [-1, 0]];
    protected array $data2 = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $key => $datum) {
            $parts = explode(" ", $datum);
            $color = trim($parts[2], "()");

            $this->data[$key] = [
                'direction' => $parts[0],
                'steps' => (int)$parts[1],
                'color' => $color,
            ];

            $colorDirection = substr($color, -1);
            $colorSteps = hexdec(substr($color, 0, -1));

            $this->data2[$key] = [
                'direction' => $this->directionsMap[$colorDirection],
                'steps' => $colorSteps,
            ];
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getCubes($this->data), $this->getCubes($this->data2)];
    }

    public function getCubes(array $data): int
    {
        $vertices = [];
        $y = $x = 0;
        $perimeter = 0;

        foreach ($data as $datum) {
            [$dy, $dx] = $this->directions[$datum['direction']];
            $steps = $datum['steps'];
            $perimeter += $steps;

            $y += $dy * $steps;
            $x += $dx * $steps;

            $vertices[] = [$y, $x];
        }

        $area = 0;
        $n = count($vertices);

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $area += ($vertices[$i][1] * $vertices[$j][0]) - ($vertices[$j][1] * $vertices[$i][0]);
        }

        $area = abs($area) / 2;

        // Aplicar Teorema de Pick: I = A - B/2 + 1
        $interior = $area - ($perimeter / 2) + 1;

        return (int)($interior + $perimeter);
    }
}
