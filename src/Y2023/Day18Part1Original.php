<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day18Part1Original extends DayBase
{
    protected const int TEST_1 = 62;
    protected const int TEST_2 = 0;

    protected array $directions = ['R' => [0, 1], 'D' => [1, 0], 'L' => [0, -1], 'U' => [-1, 0]];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $key => $datum) {
            $parts = explode(" ", $datum);
            $this->data[$key] = [
                'direction' => $parts[0],
                'steps' => (int)$parts[1],
                'color' => trim($parts[2], "()"),
            ];
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getCubes(), 0];
    }

    public function getCubes(): int
    {
        $perimeterCubes = $this->getPerimeterPoints();
        $interiorCubes = $this->getInteriorPoints($perimeterCubes);

        $this->printNewMap($perimeterCubes, $interiorCubes);

        return count($perimeterCubes) + count($interiorCubes);
    }

    private function getPerimeterPoints(): array
    {
        $perimeterCubes = [];
        $position = [0, 0];

        foreach ($this->data as $datum) {
            [$addY, $addX] = $this->directions[$datum['direction']];

            for ($i = 0; $i < $datum['steps']; $i++) {
                $newY = $position[0] + $addY;
                $newX = $position[1] + $addX;
                $map = $newY . ':' . $newX;

                $perimeterCubes[$map] = [
                    'y' => $newY,
                    'x' => $newX,
                    'color' => $datum['color'],
                ];

                $position[0] = $newY;
                $position[1] = $newX;
            }
        }

        return $perimeterCubes;
    }

    private function getInteriorPoints(array $perimeter): array
    {
        // Determinar bounding box
        $xs = array_column($perimeter, 'x');
        $ys = array_column($perimeter, 'y');
        $minX = min($xs);
        $maxX = max($xs);
        $minY = min($ys);
        $maxY = max($ys);

        $interior = [];

        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (!$this->pointOnPerimeter([$y, $x], array_values($perimeter)) && $this->pointInPolygon([$y, $x], array_values($perimeter))) {
                    $interior[] = ['y' => $y, 'x' => $x];
                }
            }
        }

        return $interior;
    }

    private function pointOnPerimeter(array $point, array $perimeter): bool
    {
        for ($i = 0; $i < count($perimeter) - 1; $i++) {
            $y1 = $perimeter[$i]['y'];
            $x1 = $perimeter[$i]['x'];
            $y2 = $perimeter[$i + 1]['y'];
            $x2 = $perimeter[$i + 1]['x'];

            if ($this->isPointOnSegment($point, [$y1, $x1], [$y2, $x2])) {
                return true;
            }
        }

        return false;
    }

    private function isPointOnSegment(array $p, array $a, array $b): bool
    {
        [$py, $px] = $p;
        [$y1, $x1] = $a;
        [$y2, $x2] = $b;

        // Chequear si está dentro del rango del segmento
        if ($px < min($x1, $x2) || $px > max($x1, $x2) || $py < min($y1, $y2) || $py > max($y1, $y2)) {
            return false;
        }

        // Chequear si es colineal
        return ($x2 - $x1) * ($py - $y1) === ($px - $x1) * ($y2 - $y1);
    }

    private function pointInPolygon(array $point, array $polygon): bool
    {
        [$py, $px] = $point;
        $inside = false;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $yi = $polygon[$i]['y'];
            $xi = $polygon[$i]['x'];
            $yj = $polygon[$j]['y'];
            $xj = $polygon[$j]['x'];

            $intersects = (($yi > $py) !== ($yj > $py)) && ($px < ($xj - $xi) * ($py - $yi) / (($yj - $yi) ?: 1e-10) + $xi);
            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    private function printNewMap(array $pathCubes, array $interiorCubes): void
    {
        $maxY = max(array_column($pathCubes, 'y'));
        $minY = min(array_column($pathCubes, 'y'));
        $maxX = max(array_column($pathCubes, 'x'));
        $minX = min(array_column($pathCubes, 'x'));
        $map = [];

        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                $map[$y][$x] = self::FREE;
            }
        }

        foreach ($pathCubes as $pathCube) {
            $map[$pathCube['y']][$pathCube['x']] = self::OBSTACLE;
        }

        foreach ($interiorCubes as $pathCube) {
            $map[$pathCube['y']][$pathCube['x']] = self::ASTERISK;
        }

        $this->printMap($map);
        echo PHP_EOL;
    }
}
