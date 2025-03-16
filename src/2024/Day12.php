<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day12 extends DayBase
{
    protected const int TEST_1 = 1930;
    protected const int TEST_2 = 1206;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        $regions = $this->getRegions();

        return [
            $this->getPrice($regions),
            $this->getPriceForSides($regions)
        ];
    }

    private function getPrice(array $regions): int
    {
        return array_reduce($regions, function ($price, $region) {
            return $price + (count($region['plots']) * $region['perimeter']);
        }, 0);
    }

    private function getPriceForSides(array $regions): int
    {
        return array_reduce($regions, function ($price, $region) {
            return $price + (count($region['plots']) * $region['sides']);
        }, 0);
    }

    private function getRegions(): array
    {
        $regions = [];
        $loadedPlots = [];

        for ($y = 0; $y < count($this->data); $y++) {
            for ($x = 0; $x < count($this->data[$y]); $x++) {
                if (in_array([$y, $x], $loadedPlots)) {
                    continue;
                }

                $region = ['plots' => [], 'perimeter' => 0, 'sides' => 0];

                $this->followPlot($loadedPlots, $region, [$y, $x], $this->data[$y][$x]);
                $this->followRegionSides($region, [$y, $x], $this->data[$y][$x]);

                $regions[] = $region;
            }
        }

        var_dump($regions);

        return $regions;
    }

    private function followPlot(array &$loadedPlots, array &$region, array $plot, string $plant): array
    {
        if (!array_key_exists($plot[0], $this->data) || !array_key_exists($plot[1], $this->data[$plot[0]]) || $this->data[$plot[0]][$plot[1]] !== $plant) {
            $region['perimeter']++;

            return $region;
        }

        if (in_array($plot, $loadedPlots)) {
            return $region;
        }

        $loadedPlots[] = $plot;
        $region['plots'][] = $plot;

        $directions = [
            [0, 1], // Right
            [1, 0], // Bottom
            [0, -1], // Left
            [-1, 0], // Top
        ];

        foreach ($directions as $direction) {
            $contiguousPlot = [$plot[0] + $direction[0], $plot[1] + $direction[1]];

            $this->followPlot($loadedPlots, $region, [$contiguousPlot[0], $contiguousPlot[1]], $plant);
        }

        return $region;
    }

    private function followRegionSides(array &$region, array $plot, string $plant): void
    {
        $exit = false;
        $position = [$plot[0], $plot[1], [0, 1]]; // First Right

        while (!$exit) {
            $position = $this->goAheadUntilTurn($position, $plant);
            $region['sides']++;
            $exit = $plot === [$position[0], $position[1]];
        }
    }

    public function goAheadUntilTurn(array $position, string $plant): array
    {
        $y = $position[0];
        $x = $position[1];
        $direction = $position[2];

        while (array_key_exists($y, $this->data) && array_key_exists($x, $this->data[$y]) && $this->data[$y][$x] === $plant) {
            $y += $direction[0];
            $x += $direction[1];
        }

        $y -= $direction[0];
        $x -= $direction[1];

        $newDirection = [];
        $directions = [
            [0, 1], // Right
            [1, 0], // Bottom
            [0, -1], // Left
            [-1, 0], // Top
        ];

        foreach ($directions as $maybeDirection) {
            if ($maybeDirection === $direction) {
                continue;
            }








        }

        return [$y, $x, $direction];
    }

    public function turnRightAndGetNewDirection(array $direction): array
    {
        if ($direction[0] < 0 && $direction[1] === 0) {
            return [0, 1];
        }

        if ($direction[0] === 0 && $direction[1] < 0) {
            return [-1, 0];
        }

        if ($direction[0] === 0 && $direction[1] > 0) {
            return [1, 0];
        }

        return [0, -1];
    }
}
