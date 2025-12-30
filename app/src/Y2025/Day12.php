<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day12 extends DayBase
{
    protected const int TEST_1 = 2;
    protected const int TEST_2 = 0;

    private array $shapes = [];
    private array $regions = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $index = 0;

        foreach ($this->data as $row) {
            if (empty($row)) {
                continue;
            }

            if (str_contains($row, "x")) {
                $parts = explode(": ", $row);
                $dimensions = explode("x", $parts[0]);
                $presentsToFit = explode(" ", $parts[1]);
                $presentsToFit = array_map('intval', $presentsToFit);

                $this->regions[] = [
                    'dimensions' => $dimensions,
                    'presentsToFit' => $presentsToFit,
                ];

                continue;
            }

            if (str_contains($row, ":")) {
                $index = intval(str_replace(":", "", $row));
                $this->shapes[$index] = [
                    'shape' => [],
                    'area' => 0,
                ];

                continue;
            }

            $this->shapes[$index]['shape'][] = str_split($row);
            $this->shapes[$index]['area'] += substr_count($row, self::WALL);
        }

        if ($this->test) {
            print_r($this->shapes);
            print_r($this->regions);
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
        $validRegionsCount = 0;

        foreach ($this->regions as $region) {
            $validRegionsCount += $this->presentsFitInRegion($region);
        }

        return $validRegionsCount;
    }

    /**
     * Fit all shapes is crazy, I needed to read Reddit and solution is even crazier.
     * Full size solution does not work
     * Reduced size by 3x3 units works on actual input, but not in test input!!!!!!!!!!!!!!!
     * WHY???????
     */
    private function presentsFitInRegion(array $region): int
    {
        $presentsArea = 0;

        foreach ($region['presentsToFit'] as $index => $count) {
            $presentsArea += $this->shapes[$index]['area'] * $count;
        }

        $presentsCount = array_sum($region['presentsToFit']);

//        $regionArea = array_product($region['dimensions']);
        $regionArea = intval(floor($region['dimensions'][0] / 3) * floor($region['dimensions'][1] / 3));

//        echo 'Presents total area is ' . $presentsArea . ' and dimensions area is ' . $regionArea . PHP_EOL;
        echo 'Presents total count is ' . $presentsCount . ' and dimensions area is ' . $regionArea . PHP_EOL;

//        return $presentsArea <= $regionArea ? 1 : 0;
        return $presentsCount <= $regionArea ? 1 : 0;
    }

    private function getPart2(): int
    {
        return 0;
    }
}
