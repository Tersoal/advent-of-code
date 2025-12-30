<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day16 extends DayBase
{
    protected const int TEST_1 = 46;
    protected const int TEST_2 = 51;
    protected const string MIRROR_RIGHT = '/';
    protected const string MIRROR_LEFT = '\\';
    protected const string MIRROR_PIPE = '|';
    protected const string MIRROR_DASH = '-';

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

//        var_dump($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getEnergizedTiles(), $this->getMaxEnergizedTiles()];
    }

    public function getEnergizedTiles(): int
    {
        $tile = [0, 0];
        $direction = [0, 1];

        return $this->getEnergizedTilesFromOrigin($tile, $direction, []);
    }

    public function getMaxEnergizedTiles(): int
    {
        $origins = $this->getOrigins();
        $maxTiles = 0;

        foreach ($origins as $origin) {
            $count = $this->getEnergizedTilesFromOrigin($origin[0], $origin[1], []);

            if ($count > $maxTiles) {
                $maxTiles = $count;
            }
        }

        return $maxTiles;
    }

    public function getEnergizedTilesFromOrigin(array $tile, array $direction, array $tiles): int
    {
        $this->moveBeam($tile, $direction, $tiles);

//        print_r($tiles);
//        echo PHP_EOL;

//        $this->printNewMap($tiles);

        $uniqueTiles = array_map(fn ($tile) => 't' . $tile[0] . ':' . $tile[1], array_column($tiles, 0));
        $uniqueTiles = array_unique($uniqueTiles);

//        print_r($uniqueTiles);
//        echo PHP_EOL;

        return count($uniqueTiles);
    }

    public function moveBeam(array $tile, array $direction, array &$tiles): void
    {
        $tileMap = 't' . $tile[0] . ':' . $tile[1] . '||d' . $direction[0] . ':' . $direction[1];

        if (isset($tiles[$tileMap]) || !isset($this->data[$tile[0]][$tile[1]])) {
            return;
        }

        $tiles[$tileMap] = [$tile, $direction];

        $newDirections = $this->getDirections($tile, $direction);

        foreach ($newDirections as $newDirection) {
            $newY = $tile[0] + $newDirection[0];
            $newX = $tile[1] + $newDirection[1];
            $newTile = [$newY, $newX];

            $this->moveBeam($newTile, $newDirection, $tiles);
        }
    }

    public function getDirections(array $tile, array $direction): array
    {
        if (!isset($this->data[$tile[0]][$tile[1]])) {
            return [];
        }

        $value = $this->data[$tile[0]][$tile[1]];

        if ($value === self::FREE) {
            return [$direction];
        }

        if ($value === self::MIRROR_DASH && $direction[0] === 0) {
            return [$direction];
        }

        if ($value === self::MIRROR_DASH && $direction[0] !== 0) {
            return [[0, -1], [0, 1]];
        }

        if ($value === self::MIRROR_PIPE && $direction[0] !== 0) {
            return [$direction];
        }

        if ($value === self::MIRROR_PIPE && $direction[0] === 0) {
            return [[-1, 0], [1, 0]];
        }

        if ($value === self::MIRROR_RIGHT && $direction === [0, 1]) {
            return [[-1, 0]];
        }

        if ($value === self::MIRROR_RIGHT && $direction === [0, -1]) {
            return [[1, 0]];
        }

        if ($value === self::MIRROR_RIGHT && $direction === [1, 0]) {
            return [[0, -1]];
        }

        if ($value === self::MIRROR_RIGHT && $direction === [-1, 0]) {
            return [[0, 1]];
        }

        if ($value === self::MIRROR_LEFT && $direction === [0, 1]) {
            return [[1, 0]];
        }

        if ($value === self::MIRROR_LEFT && $direction === [0, -1]) {
            return [[-1, 0]];
        }

        if ($value === self::MIRROR_LEFT && $direction === [1, 0]) {
            return [[0, 1]];
        }

        if ($value === self::MIRROR_LEFT && $direction === [-1, 0]) {
            return [[0, -1]];
        }

        return [];
    }

    private function getOrigins(): array
    {
        $origins = [];

        for ($y = 0; $y < count($this->data); $y++) {
            for ($x = 0; $x < count($this->data[$y]); $x++) {
                $tile = [$y, $x];

                if ($y === 0) {
                    $origins[] = [$tile, [1, 0]];
                } elseif ($y === (count($this->data) - 1)) {
                    $origins[] = [$tile, [-1, 0]];
                } elseif ($x === 0) {
                    $origins[] = [$tile, [0, 1]];
                } elseif ($x === (count($this->data[0]) - 1)) {
                    $origins[] = [$tile, [0, -1]];
                }
            }
        }

        return $origins;
    }

    private function printNewMap(array $tiles): void
    {
        $map = $this->data;

        foreach ($tiles as [$tile, $direction]) {
            $map[$tile[0]][$tile[1]] = self::OBSTACLE;
        }

        $this->printMap($map);
        echo PHP_EOL;
    }
}
