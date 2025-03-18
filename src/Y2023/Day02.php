<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day02 extends DayBase
{
    protected const int TEST_1 = 8;
    protected const int TEST_2 = 2286;

    protected array $maxCubes = ['red' => 12, 'green' => 13, 'blue' => 14];
    protected array $games = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        foreach ($this->data as $line) {
            $data = explode(":", $line);
            $gameId = (int)str_replace('Game ', "", $data[0]);
            $sets = explode(";", $data[1]);

            foreach ($sets as $setId => $set) {
                $cubes = explode(",", $set);

                foreach ($cubes as $cube) {
                    $cubeData = explode(" ", trim($cube));
                    $cubeCount = (int)$cubeData[0];
                    $cubeType = $cubeData[1];

                    $this->games[$gameId][$setId][$cubeType] = $cubeCount;
                }
            }
        }
    }

    public function getResult(): array
    {
        return [$this->getSumOfIds(), $this->getSumOfPowers()];
    }

    private function getSumOfIds(): int
    {
        $sum = 0;

        foreach ($this->games as $id => $game) {
            if ($this->gameIsPossible($game)) {
                $sum += $id;
            }
        }

       return $sum;
    }

    private function getSumOfPowers(): int
    {
        $sum = 0;

        foreach ($this->games as $game) {
            $minCubes = $this->getMinCubes($game);
            $sum += array_product($minCubes);
        }

       return $sum;
    }

    private function gameIsPossible(array $game): bool
    {
        foreach ($game as $set) {
            foreach ($set as $cubeType => $cubeCount) {
                if (!array_key_exists($cubeType, $this->maxCubes)) {
                    return false;
                }

                if ($cubeCount > $this->maxCubes[$cubeType]) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getMinCubes(array $game): array
    {
        $minCubes = [];

        foreach ($game as $set) {
            foreach ($set as $cubeType => $cubeCount) {
                if (!array_key_exists($cubeType, $minCubes)) {
                    $minCubes[$cubeType] = $cubeCount;
                } elseif ($cubeCount > $minCubes[$cubeType]) {
                    $minCubes[$cubeType] = $cubeCount;
                }
            }
        }

        return $minCubes;
    }
}
