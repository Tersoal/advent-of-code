<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day09 extends DayBase
{
    protected const int TEST_1 = 50;
    protected const int TEST_2 = 24;

    protected const string RED_TILE = self::OBSTACLE;
    protected const string GREEN_TILE = 'X';

    protected array $map;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->data = array_flip($this->data);

        foreach ($this->data as $key => $row) {
            $this->data[$key] = explode(',', $key);
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [
            $this->getPart1(),
            $this->getPart2()
//            $this->getPart22()
        ];
    }

    private function getPart1(): int
    {
        $area = 0;

        foreach ($this->data as $key => [$x, $y]) {
            foreach ($this->data as $key2 => [$x2, $y2]) {
                if ($key === $key2) {
                    continue;
                }

                $area = max($area, array_product([(abs($x2 - $x) + 1), (abs($y2 - $y) + 1)]));
            }
        }

        return $area;
    }

    /**
     * 4771232025 too high
     * 3001445088 too high
     */
    private function getPart2(): int
    {
        $areas = [];

        foreach ($this->data as $key => [$x, $y]) {
            foreach ($this->data as $key2 => [$x2, $y2]) {
                if ($key === $key2) {
                    continue;
                }

                $areas[$key.'|'.$key2] = array_product([(abs($x2 - $x) + 1), (abs($y2 - $y) + 1)]);
            }
        }

        arsort($areas);

        if ($this->test) {
            print_r($areas);
        }

//        $largestArea = 0;
//
//        foreach ($areas as $key => $area) {
//            $points = explode('|', $key);
//            [$x1, $y1] = explode(',', $points[0]);
//            [$x2, $y2] = explode(',', $points[1]);
//
//            if (!$this->pointsInArea($x1, $y1, $x2, $y2)) {
//                $largestArea = $area;
//                break;
//            }
//        }
//
//        return $largestArea;





        $points = array_values($this->data);
        $rects = [];

        foreach ($points as $key => [$x1, $y1]) {
            if (isset($points[$key + 1])) {
                $x2 = $points[$key + 1][0];
                $y2 = $points[$key + 1][1];
            } else {
                $x2 = $points[0][0];
                $y2 = $points[0][1];
            }

//            $rects[] = $x1.','.$y1.'|'.$x2.','.$y2;
            $rects[] = [
                [min($x1, $x2), min($y1, $y2)],
                [max($x1, $x2), max($y1, $y2)],
                ($x1 === $x2 ? 'V' : 'H')
            ];
        }

        $largestArea = 0;

        foreach ($areas as $key => $area) {
            $points = explode('|', $key);
            [$x1, $y1] = explode(',', $points[0]);
            [$x2, $y2] = explode(',', $points[1]);

            if (!$this->areaIsIntersectByRect($x1, $y1, $x2, $y2, $rects)) {
                $largestArea = $area;
                break;
            }
        }

        return $largestArea;










//        $minX = min(array_column($this->data, 0));
//        $maxX = max(array_column($this->data, 0));
//        $minY = min(array_column($this->data, 1));
//        $maxY = max(array_column($this->data, 1));
//
//        $row = array_fill($minX, $maxX, self::GROUND);
//
//        $this->map = array_fill($minY, $maxY, $row);
//
//        $points = array_values($this->data);
//
//        foreach ($points as $key => [$x, $y]) {
//            $this->map[$y][$x] = self::RED_TILE;
//
//            if (isset($points[$key + 1])) {
//                $x2 = $points[$key + 1][0];
//                $y2 = $points[$key + 1][1];
//            } else {
//                $x2 = $points[0][0];
//                $y2 = $points[0][1];
//            }
//
//            if ($x === $x2) {
//                for ($i = $y + 1; $i < $y2; $i++) {
//                    $this->map[$i][$x] = self::GREEN_TILE;
//                }
//            } else {
//                for ($i = $x + 1; $i < $x2; $i++) {
//                    $this->map[$y][$i] = self::GREEN_TILE;
//                }
//            }
//        }
//
//        if ($this->test) {
//            $this->printMap($this->map);
//            echo PHP_EOL;
//        }

//        return 0;
    }



    private function pointsInArea(int $x1, int $y1, int $x2, int $y2): bool
    {
        foreach ($this->data as [$x, $y]) {
//            if ($x === $x1 && $y === $y1) {
//                continue;
//            }
//
//            if ($x === $x2 && $y === $y2) {
//                continue;
//            }
//
//            if ($x >= min($x1, $x2) && $x <= max($x1, $x2) && $y >= min($y1, $y2) && $y <= max($y1, $y2)) {
//                return true;
//            }

            if ($x > min($x1, $x2) && $x < max($x1, $x2) && $y > min($y1, $y2) && $y < max($y1, $y2)) {
                return true;
            }
        }

        return false;
    }



    private function areaIsIntersectByRect(int $x1, int $y1, int $x2, int $y2, array $rects): bool
    {
        $sides = [
            [[min($x1, $x2), min($y1, $y2)], [max($x1, $x2), min($y1, $y2)], 'H'],
            [[max($x1, $x2), min($y1, $y2)], [max($x1, $x2), max($y1, $y2)], 'V'],
            [[min($x1, $x2), max($y1, $y2)], [max($x1, $x2), max($y1, $y2)], 'H'],
            [[min($x1, $x2), min($y1, $y2)], [min($x1, $x2), max($y1, $y2)], 'V'],
        ];

        print_r($sides);

        foreach ($sides as $side) {
            foreach ($rects as $rect) {
                if ($this->rectsIntersect($side, $rect)) {
                    return true;
                }
            }
        }

        print_r($sides);
        print_r($rects);

        return false;
    }

    private function rectsIntersect(array $side, array $rect): bool
    {
        if ($side[2] === 'V') {
            if ($rect[2] === 'H') {
                if ($rect[1][0] < $side[0][0]) {
                    return false;
                }

                if ($rect[0][0] > $side[0][0]) {
                    return false;
                }

                if ($rect[1][1] < $side[0][1]) {
                    return false;
                }

                if ($rect[0][1] > $side[1][1]) {
                    return false;
                }
            } else {
                if ($rect[0][0] !== $side[0][0]) {
                    return false;
                }

                if ($rect[1][1] <= $side[0][1]) {
                    return false;
                }

                if ($rect[0][1] >= $side[1][1]) {
                    return false;
                }

                if ($rect[0][1] <= $side[0][1] && $rect[1][1] >= $side[1][1]) {
                    return false;
                }
            }

            return true;
        }

        if ($side[2] === 'H') {
            if ($rect[2] === 'V') {
                if ($rect[1][1] < $side[0][1]) {
                    return false;
                }

                if ($rect[0][1] > $side[1][1]) {
                    return false;
                }

                if ($rect[0][0] < $side[0][0]) {
                    return false;
                }

                if ($rect[0][0] > $side[1][0]) {
                    return false;
                }
            } else {
                if ($rect[0][1] !== $side[0][1]) {
                    return false;
                }

                if ($rect[1][0] <= $side[0][0]) {
                    return false;
                }

                if ($rect[0][0] >= $side[1][0]) {
                    return false;
                }

                if ($rect[0][0] <= $side[0][0] && $rect[1][0] >= $side[1][0]) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }









//    private function getPart2(): int
//    {
////        $area = 0;
////
////        foreach ($this->data as $key => [$x, $y]) {
////            foreach ($this->data as $key2 => [$x2, $y2]) {
////                if ($key === $key2) {
////                    continue;
////                }
////
//////                if ($x === $x2 || $y === $y2) {
//////                    continue;
//////                }
////
//////                if ($x <= $x2 || $y <= $y2) {
//////                    continue;
//////                }
////
////                if ($this->areaHasTileInMiddle($x, $y,  $x2, $y2)) {
////                    continue;
////                }
////
////                echo ' x-y  = ' . $x . '-' . $y . PHP_EOL;
////                echo 'x2-y2 = ' . $x2 . '-' . $y2 . PHP_EOL;
////                echo 'prod  = ' . array_product([($x2 - $x + 1), ($y2 - $y + 1)]) . PHP_EOL . PHP_EOL;
////
////                $area = max($area, array_product([($x2 - $x + 1), ($y2 - $y + 1)]));
////            }
////        }
//
//
//
//
////        $contour = $this->expandRedPointsToFullContour(array_values($this->data));
////        $contour = $this->sortPolygonPoints($contour);
////
////        $area = $this->findLargestRectangle($contour);
//
//
//
//
////        $area = $this->findLargestRectangle(array_values($this->data));
//
//
//
////        $result = $this->run(array_values($this->data));
////
////        print_r($result['bestRect']);
////        echo "Area: " . $result['bestRect']['area'] . PHP_EOL;
////
////        print_r($result['contour']);
////        echo "tiles filled: " . count($result['filledMap']) . PHP_EOL;
////        print_r($result['bestRect']);
//
////        $this->redPoints = array_values(array_values($this->data));
////        $this->buildLines();
////        $result = $this->run();
//
//
////        $this->construct(array_values($this->data));
////        $result = $this->maxRectangle();
////
////        print_r($result);
////
////        return $result ? $result['area'] : 0;
//
//
//
//
//        $rect = $this->other(array_values($this->data));
//
//        print_r($rect);
//
//        return $rect ? $rect['area'] : 0;
//    }

    private function areaHasTileInMiddle(int $x, int $y, int $x2, int $y2): bool
    {
//        for ($i = $x + 1; $i <= $x2; $i++) {
//            for ($j = $y + 1; $j <= $y2; $j++) {
//                $newKey = $i . ',' . $j;
//
//                if (isset($this->data[$newKey])) {
//                    return true;
//                }
//            }
//        }
//
//        return false;

//        $tilesInMiddle = array_find($this->data, function (array $tile) use ($x, $y, $x2, $y2) {
//            $tileX = $tile[0];
//            $tileY = $tile[1];
//
////            if ($tileX === $x || $tileX === $x2 || $tileY === $y || $tileY === $y2) {
////                return false;
////            }
//
////            if (($tileX === $x && $tileY === $y) || ($tileX === $x2 && $tileY === $y2)) {
////                return false;
////            }
//
//
//
//
////            if ($x < $x2 && $y < $y2) {
////                return $tileX > $x && $tileX < $x2 && $tileY > $y && $tileY < $y2;
////            }
////
////            if ($x > $x2 && $y > $y2) {
////                return $tileX < $x && $tileX > $x2 && $tileY < $y && $tileY > $y2;
////            }
////
////            if ($x > $x2 && $y < $y2) {
////                return $tileX < $x && $tileX > $x2 && $tileY > $y && $tileY < $y2;
////            }
////
////            return $tileX > $x && $tileX < $x2 && $tileY < $y && $tileY > $y2;
//
//
//
//
//
//            if ($x < $x2 && $y < $y2) {
//                return $tileX > $x && $tileX < $x2 && $tileY > $y && $tileY < $y2;
//            }
//
//            if ($x < $x2 && $y > $y2) {
//                return $tileX > $x && $tileX < $x2 && $tileY < $y && $tileY > $y2;
//            }
//
//            return false;
//
//
//
//
//
////            if ($x > $x2 || $y > $y2) {
////                return false;
////            }
//
//
////            return $tileX > $x && $tileX < $x2 && $tileY > $y && $tileY < $y2;
//        });



//        return !empty($tilesInMiddle);


        foreach ($this->data as $key => [$tileX, $tileY]) {
            if ($tileX === $x && $tileY === $y) {
                continue;
            }

            if ($tileX === $x2 && $tileY === $y2) {
                continue;
            }

            if ($tileX === $x && $tileY === $y2) {
                continue;
            }

            if ($tileX === $x2 && $tileY === $y) {
                continue;
            }

            if ($x < $x2 && $y < $y2) {
                if ($tileX > $x && $tileX < $x2 && $tileY > $y && $tileY < $y2) {
                    return true;
                }
            }

            if ($x > $x2 && $y > $y2) {
                if ($tileX < $x && $tileX > $x2 && $tileY < $y && $tileY > $y2) {
                    return true;
                }
            }

            if ($x < $x2 && $y > $y2) {
                if ($tileX > $x && $tileX < $x2 && $tileY < $y && $tileY > $y2) {
                    return true;
                }
            }

            if ($x > $x2 && $y < $y2) {
                if ($tileX < $x && $tileX > $x2 && $tileY > $y && $tileY < $y2) {
                    return true;
                }
            }

//            if ($x <> $x2 && $y === $y2) {
//                return true;
//            }
//
//            if ($x === $x2 && $y <> $y2) {
//                return true;
//            }
        }

        return false;
    }





//    // Comprueba si un punto está dentro del polígono
//    private function pointInPolygon($x, $y, $poly): bool
//    {
//        $inside = false;
//        $n = count($poly);
//
//        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
//            $xi = $poly[$i][0];
//            $yi = $poly[$i][1];
//            $xj = $poly[$j][0];
//            $yj = $poly[$j][1];
//
//            $intersect = (($yi > $y) !== ($yj > $y)) &&
//                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi + 0.0000001) + $xi);
//
//            if ($intersect) {
//                $inside = !$inside;
//            }
//        }
//        return $inside;
//    }
//
//    // Comprueba si un rectángulo está completamente dentro del polígono
//    private function rectangleInsidePolygon($x1, $y1, $x2, $y2, $poly): bool
//    {
//        // Normalizamos coordenadas
//        $minX = min($x1, $x2);
//        $maxX = max($x1, $x2);
//        $minY = min($y1, $y2);
//        $maxY = max($y1, $y2);
//
//        // Comprobar que las 4 esquinas están dentro
//        $corners = [
//            [$minX, $minY],
//            [$minX, $maxY],
//            [$maxX, $minY],
//            [$maxX, $maxY]
//        ];
//
//        foreach ($corners as [$cx, $cy]) {
//            if (!$this->pointInPolygon($cx, $cy, $poly)) {
//                return false;
//            }
//        }
//
//        return true;
//    }
//
//    // Método principal: devuelve el mayor rectángulo
//    public function findLargestRectangle(array $points): ?array
//    {
//        $maxArea = 0;
//        $bestRect = null;
//
//        $n = count($points);
//
//        // Probar todas las parejas de puntos como esquinas opuestas
//        for ($i = 0; $i < $n; $i++) {
//            for ($j = $i + 1; $j < $n; $j++) {
//                [$x1, $y1] = $points[$i];
//                [$x2, $y2] = $points[$j];
//
//                // Solo rectángulos axis-aligned
//                if ($x1 == $x2 || $y1 == $y2) continue;
//
//                if ($this->rectangleInsidePolygon($x1, $y1, $x2, $y2, $points)) {
//                    $area = abs(($x2 - $x1) * ($y2 - $y1));
//
//                    if ($area > $maxArea) {
//                        $maxArea = $area;
//                        $bestRect = [
//                            'x1' => $x1, 'y1' => $y1,
//                            'x2' => $x2, 'y2' => $y2,
//                            'area' => $area
//                        ];
//                    }
//                }
//            }
//        }
//
//        return $bestRect;
//    }







//    // Tolerancia para comparaciones con floats
//    private $eps = 1e-9;
//
//    /**
//     * Ordena puntos para formar el contorno real (clockwise)
//     */
//    function sortPolygonPoints(array $points): array
//    {
//        // Calcular centroide
//        $cx = 0; $cy = 0;
//        foreach ($points as $p) {
//            $cx += $p[0];
//            $cy += $p[1];
//        }
//        $cx /= count($points);
//        $cy /= count($points);
//
//        // Ordenar por ángulo desde el centroide
//        usort($points, function($a, $b) use ($cx, $cy) {
//            $angA = atan2($a[1] - $cy, $a[0] - $cx);
//            $angB = atan2($b[1] - $cy, $b[0] - $cx);
//            return $angA <=> $angB;
//        });
//
//        return $points;
//    }
//
//    function expandRedPointsToFullContour(array $redPoints): array
//    {
//        $contour = [];
//
//        $n = count($redPoints);
//        if ($n < 2) return $contour;
//
//        for ($i = 0; $i < $n; $i++) {
//            $p1 = $redPoints[$i];
//            $p2 = $redPoints[($i + 1) % $n]; // el siguiente (y al final vuelve al primero)
//
//            $x1 = $p1[0];  $y1 = $p1[1];
//            $x2 = $p2[0];  $y2 = $p2[1];
//
//            // Añadir el punto actual si no estaba
//            if (empty($contour) || end($contour) !== $p1) {
//                $contour[] = $p1;
//            }
//
//            // Mismo X → movimiento vertical
//            if ($x1 == $x2) {
//                if ($y1 < $y2) {
//                    for ($y = $y1 + 1; $y <= $y2; $y++) {
//                        $contour[] = [$x1, $y];
//                    }
//                } else {
//                    for ($y = $y1 - 1; $y >= $y2; $y--) {
//                        $contour[] = [$x1, $y];
//                    }
//                }
//            }
//            // Mismo Y → movimiento horizontal
//            elseif ($y1 == $y2) {
//                if ($x1 < $x2) {
//                    for ($x = $x1 + 1; $x <= $x2; $x++) {
//                        $contour[] = [$x, $y1];
//                    }
//                } else {
//                    for ($x = $x1 - 1; $x >= $x2; $x--) {
//                        $contour[] = [$x, $y1];
//                    }
//                }
//            }
//            else {
//                throw new \Exception("Los puntos rojos deben estar alineados en eje X o Y, nunca diagonales");
//            }
//        }
//
//        // Opcional: eliminar el último punto repetido si coincide con el primero
//        if ($contour[0] === end($contour)) {
//            array_pop($contour);
//        }
//
//        return $contour;
//    }
//
//    // Comprueba si p está sobre el segmento ab
//    private function pointOnSegment($px, $py, $ax, $ay, $bx, $by)
//    {
//        // Vector cross product == 0  (colineal)
//        $cross = ($py - $ay) * ($bx - $ax) - ($px - $ax) * ($by - $ay);
//        if (abs($cross) > $this->eps) return false;
//
//        // Ahora comprobar que px,py está entre ax,bx y ay,by
//        $dot = ($px - $ax) * ($px - $bx) + ($py - $ay) * ($py - $by);
//        return $dot <= $this->eps; // <= 0 significa entre los extremos
//    }
//
//    // Test punto en polígono: true si dentro O en el borde
//    public function pointInPolygon($px, $py, $poly)
//    {
//        $n = count($poly);
//        if ($n < 3) return false;
//
//        // 1) comprobar borde: si está sobre cualquier arista, devolvemos true
//        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
//            [$xi, $yi] = $poly[$i];
//            [$xj, $yj] = $poly[$j];
//            if ($this->pointOnSegment($px, $py, $xi, $yi, $xj, $yj)) {
//                return true;
//            }
//        }
//
//        // 2) ray casting (algoritmo estándar). Contar intersecciones con rayo horizontal a la derecha.
//        $inside = false;
//        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
//            [$xi, $yi] = $poly[$i];
//            [$xj, $yj] = $poly[$j];
//
//            // ¿El rayo cruza el segmento (yi > py) != (yj > py) ?
//            $intersect = (($yi > $py) !== ($yj > $py));
//            if ($intersect) {
//                // calcular x de la intersección del segmento con la línea y = py
//                $xInter = $xi + ($py - $yi) * ($xj - $xi) / ($yj - $yi);
//                if ($xInter > $px) {
//                    $inside = !$inside;
//                }
//            }
//        }
//        return $inside;
//    }
//
//    // Normaliza coordenadas y comprueba que las cuatro esquinas están dentro o en borde
//    private function rectangleInsidePolygon($x1, $y1, $x2, $y2, $poly)
//    {
//        $minX = min($x1, $x2);
//        $maxX = max($x1, $x2);
//        $minY = min($y1, $y2);
//        $maxY = max($y1, $y2);
//
//        $corners = [
//            [$minX, $minY],
//            [$minX, $maxY],
//            [$maxX, $minY],
//            [$maxX, $maxY]
//        ];
//
//        foreach ($corners as [$cx, $cy]) {
//            if (!$this->pointInPolygon($cx, $cy, $poly)) {
//                return false;
//            }
//        }
//
//        // Opcional: comprobar que las aristas del rectángulo no cruzan el polígono en sitios no permitidos.
//        // Para muchos polígonos rectilíneos esta comprobación de esquinas + borde es suficiente.
//
//        return true;
//    }
//
//    // Encuentra el mayor rectángulo usando pares de puntos (esquinas opuestas)
//    public function findLargestRectangle(array $points)
//    {
//        $maxArea = 0;
//        $bestRect = null;
//        $n = count($points);
//
//        for ($i = 0; $i < $n; $i++) {
//            for ($j = $i + 1; $j < $n; $j++) {
//                [$x1, $y1] = $points[$i];
//                [$x2, $y2] = $points[$j];
//
//                // saltar si comparte x o y (no es rectángulo de área)
//                if ($x1 == $x2 || $y1 == $y2) continue;
//
//                if ($this->rectangleInsidePolygon($x1, $y1, $x2, $y2, $points)) {
//                    $area = abs(($x2 - $x1) * ($y2 - $y1));
//                    if ($area > $maxArea) {
//                        $maxArea = $area;
//                        $bestRect = [
//                            'x1' => $x1, 'y1' => $y1,
//                            'x2' => $x2, 'y2' => $y2,
//                            'area' => $area
//                        ];
//                    }
//                }
//            }
//        }
//
//        return $bestRect;
//    }









//    function largestInclusiveRectangle(array $points): int
//    {
//        $n = count($points);
//        $maxArea = 0;
//
//        // Generamos todos los pares de puntos opuestos del rectángulo
//        for ($i = 0; $i < $n; $i++) {
//            for ($j = $i + 1; $j < $n; $j++) {
//
//                $x1 = min($points[$i][0], $points[$j][0]);
//                $x2 = max($points[$i][0], $points[$j][0]);
//                $y1 = min($points[$i][1], $points[$j][1]);
//                $y2 = max($points[$i][1], $points[$j][1]);
//
//                // Comprobamos todos los puntos ENTEROS dentro del rectángulo
//                for ($x = $x1; $x <= $x2; $x++) {
//                    for ($y = $y1; $y <= $y2; $y++) {
//
//                        if (!$this->pointInPolygon($x, $y, $points)) {
//                            continue 3; // este rectángulo NO sirve
//                        }
//                    }
//                }
//
//                // Área INCLUSIVA
//                $area = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
//
//                if ($area > $maxArea) {
//                    $maxArea = $area;
//                }
//            }
//        }
//
//        return $maxArea;
//    }







//    // Entrada: array de puntos [[x,y],...]
//    public function run(array $redPoints)
//    {
//        // 1) normalizar puntos únicos
//        $pts = $this->uniquePoints($redPoints);
//
//        // 2) construir conexiones ortogonales (vecinos más cercanos en cada eje)
//        $edges = $this->buildOrthogonalEdges($pts);
//
//        // 3) extraer ciclo exterior ordenado (si existe)
//        $contour = $this->traceContourFromEdges($edges);
//        if (empty($contour)) {
//            throw new \Exception("No se pudo reconstruir un contorno cerrado desde los puntos dados.");
//        }
//
//        // 4) expandir aristas a puntos unitarios de borde
//        $borderPoints = $this->expandContourToUnitPoints($contour);
//
//        // 5) obtener relleno mediante scanline -> conjunto de tiles interiores (incluye borde)
//        $filledTiles = $this->scanlineFill($borderPoints);
//
//        // 6) encontrar mayor rectángulo cuyos tiles estén todos en $filledTiles
//        $best = $this->largestRectangleInTileSet($filledTiles);
//
//        // devolver resultados
//        return [
//            'contour' => $contour,
//            'border' => $borderPoints,
//            'filled' => $filledTiles,
//            'best' => $best
//        ];
//    }
//
//    private function uniquePoints(array $pts)
//    {
//        $map = [];
//        $out = [];
//        foreach ($pts as $p) {
//            $k = $p[0] . ',' . $p[1];
//            if (!isset($map[$k])) {
//                $map[$k] = true;
//                $out[] = [$p[0], $p[1]];
//            }
//        }
//        return $out;
//    }
//
//    private function buildOrthogonalEdges(array $pts)
//    {
//        // Indexar por fila y columna
//        $byY = [];
//        $byX = [];
//        foreach ($pts as $p) {
//            [$x, $y] = $p;
//            $byY[$y][] = $x;
//            $byX[$x][] = $y;
//        }
//        foreach ($byY as $y => &$arr) sort($arr);
//        foreach ($byX as $x => &$arr) sort($arr);
//
//        $edges = []; // cada arista: [x1,y1,x2,y2]
//        // para cada punto, buscar vecino inmediato a izquierda/derecha en misma fila
//        foreach ($pts as $p) {
//            [$x, $y] = $p;
//            $row = $byY[$y];
//            // buscar index de x en row
//            $i = array_search($x, $row, true);
//            if ($i !== false) {
//                // vecino a la derecha
//                if (isset($row[$i + 1])) {
//                    $x2 = $row[$i + 1];
//                    $edges[$this->edgeKey($x, $y, $x2, $y)] = [$x, $y, $x2, $y];
//                }
//                // vecino a la izquierda
//                if (isset($row[$i - 1])) {
//                    $x2 = $row[$i - 1];
//                    $edges[$this->edgeKey($x2, $y, $x, $y)] = [$x2, $y, $x, $y];
//                }
//            }
//            // columna: vecino arriba/abajo
//            $col = $byX[$x];
//            $j = array_search($y, $col, true);
//            if ($j !== false) {
//                if (isset($col[$j + 1])) {
//                    $y2 = $col[$j + 1];
//                    $edges[$this->edgeKey($x, $y, $x, $y2)] = [$x, $y, $x, $y2];
//                }
//                if (isset($col[$j - 1])) {
//                    $y2 = $col[$j - 1];
//                    $edges[$this->edgeKey($x, $y2, $x, $y)] = [$x, $y2, $x, $y];
//                }
//            }
//        }
//
//        return array_values($edges);
//    }
//
//    private function edgeKey($x1, $y1, $x2, $y2)
//    {
//        return $x1 . ',' . $y1 . '-' . $x2 . ',' . $y2;
//    }
//
//    private function traceContourFromEdges(array $edges)
//    {
//        if (empty($edges)) return [];
//
//        // construir mapa de adyacencia direccional
//        $adj = [];
//        foreach ($edges as $e) {
//            [$x1, $y1, $x2, $y2] = $e;
//            $k1 = $x1 . ',' . $y1;
//            $k2 = $x2 . ',' . $y2;
//            $adj[$k1][] = [$x2, $y2];
//            // también dirección inversa (grafo no dirigido)
//            $adj[$k2][] = [$x1, $y1];
//        }
//
//        // elegir punto inicial: el de menor y, luego menor x (top-left)
//        $allPoints = [];
//        foreach ($adj as $k => $_) {
//            [$xx, $yy] = explode(',', $k);
//            $allPoints[] = [(int)$xx, (int)$yy];
//        }
//        usort($allPoints, function ($a, $b) {
//            if ($a[1] === $b[1]) return $a[0] <=> $b[0];
//            return $a[1] <=> $b[1];
//        });
//        $start = $allPoints[0];
//        $startKey = $start[0] . ',' . $start[1];
//
//        // recorrer ciclo con regla de giro a la derecha preferida
//        $current = $start;
//        $prev = null;
//        $contour = [$current];
//        $maxSteps = count($adj) * 10; // safety
//        $steps = 0;
//
//        // definir función para obtener vecinos ordenados por preferencia de dirección (clockwise)
//        while (true) {
//            $ck = $current[0] . ',' . $current[1];
//            $neighbors = $adj[$ck] ?? [];
//            if (empty($neighbors)) break;
//
//            // si venimos de prev, intentar seguir con prioridad giro a la derecha
//            $next = null;
//            if ($prev === null) {
//                // escoger vecino con menor angulo (preferir derecha hacia abajo?) simple: escoger vecino con menor (dx,dy) manhattan?
//                $next = $neighbors[0];
//            } else {
//                // ordenar vecinos por ángulo relativo a vector (prev->current)
//                $vx = $current[0] - $prev[0];
//                $vy = $current[1] - $prev[1];
//                // compute angle of candidate and pick the one that makes smallest right-hand turn
//                $bestScore = null;
//                foreach ($neighbors as $cand) {
//                    if ($cand[0] === $prev[0] && $cand[1] === $prev[1]) continue; // don't go back unless no choice
//                    $cx = $cand[0] - $current[0];
//                    $cy = $cand[1] - $current[1];
//                    // cross and dot to get turn; we want prefer right turn (negative cross) then straight then left
//                    $cross = $vx * $cy - $vy * $cx;
//                    $dot = $vx * $cx + $vy * $cy;
//                    // score tuple: (isBack, cross sign, -dot) -> lower better
//                    $isBack = ($cx === -$vx && $cy === -$vy) ? 1 : 0;
//                    $score = [$isBack, ($cross < 0 ? 0 : ($cross == 0 ? 1 : 2)), -$dot];
//                    if ($bestScore === null || $this->cmpScore($score, $bestScore) < 0) {
//                        $bestScore = $score;
//                        $next = $cand;
//                    }
//                }
//                if ($next === null) {
//                    // solo queda volver al previo
//                    $next = [$prev[0], $prev[1]];
//                }
//            }
//
//            // avanzar
//            $prev = $current;
//            $current = $next;
//            // si volvemos al inicio y tenemos al menos 2 pasos, cerramos
//            if ($current[0] === $start[0] && $current[1] === $start[1]) {
//                break;
//            }
//            $contour[] = $current;
//
//            if (++$steps > $maxSteps) break;
//        }
//
//        // cerrar
//        if (end($contour)[0] !== $start[0] || end($contour)[1] !== $start[1]) {
//            // intentar forzar cierre si no cerró
//            $contour[] = $start;
//        }
//
//        // eliminar colineal redundantes (simplificar)
//        $contour = $this->simplifyColinear($contour);
//
//        return $contour;
//    }
//
//    private function cmpScore($a, $b)
//    {
//        for ($i = 0; $i < count($a); $i++) {
//            if ($a[$i] < $b[$i]) return -1;
//            if ($a[$i] > $b[$i]) return 1;
//        }
//        return 0;
//    }
//
//    private function simplifyColinear(array $pts)
//    {
//        if (count($pts) < 3) return $pts;
//        $out = [];
//        $n = count($pts);
//        for ($i = 0; $i < $n; $i++) {
//            $p = $pts[$i];
//            $prev = $pts[($i - 1 + $n) % $n];
//            $next = $pts[($i + 1) % $n];
//            // si prev->p y p->next son colineales, saltar p
//            if (($prev[0] === $p[0] && $p[0] === $next[0]) || ($prev[1] === $p[1] && $p[1] === $next[1])) {
//                // es colineal -> saltarlo
//                continue;
//            }
//            $out[] = $p;
//        }
//        // asegurar cierre
//        if (count($out) > 0) {
//            $first = $out[0];
//            $last = end($out);
//            if ($first[0] !== $last[0] || $first[1] !== $last[1]) {
//                $out[] = $first;
//            }
//        }
//        return $out;
//    }
//
//    private function expandContourToUnitPoints(array $contour)
//    {
//        $unit = [];
//        $n = count($contour);
//        for ($i = 0; $i < $n - 1; $i++) {
//            [$x1, $y1] = $contour[$i];
//            [$x2, $y2] = $contour[$i + 1];
//            if ($x1 == $x2) {
//                if ($y1 <= $y2) {
//                    for ($y = $y1; $y <= $y2; $y++) $unit[$x1 . ',' . $y] = [$x1, $y];
//                } else {
//                    for ($y = $y1; $y >= $y2; $y--) $unit[$x1 . ',' . $y] = [$x1, $y];
//                }
//            } else if ($y1 == $y2) {
//                if ($x1 <= $x2) {
//                    for ($x = $x1; $x <= $x2; $x++) $unit[$x . ',' . $y1] = [$x, $y1];
//                } else {
//                    for ($x = $x1; $x >= $x2; $x--) $unit[$x . ',' . $y1] = [$x, $y1];
//                }
//            } else {
//                // diagonales no esperadas
//            }
//        }
//        // devolver valores como array de puntos
//        return array_values($unit);
//    }
//
//    private function scanlineFill(array $borderPoints)
//    {
//        if (empty($borderPoints)) return [];
//
//        // índice por y
//        $byY = [];
//        $minX = PHP_INT_MAX;
//        $maxX = PHP_INT_MIN;
//        $minY = PHP_INT_MAX;
//        $maxY = PHP_INT_MIN;
//        foreach ($borderPoints as $p) {
//            [$x, $y] = $p;
//            $byY[$y][$x] = true;
//            $minX = min($minX, $x);
//            $maxX = max($maxX, $x);
//            $minY = min($minY, $y);
//            $maxY = max($maxY, $y);
//        }
//
//        $filled = [];
//        // para cada y entre minY y maxY, obtener intersecciones (xs) y rellenar entre pares
//        for ($y = $minY; $y <= $maxY; $y++) {
//            if (!isset($byY[$y])) continue;
//            $xs = array_keys($byY[$y]);
//            sort($xs, SORT_NUMERIC);
//            // si hay pares (x1,x2,x3,x4...), rellenar [x1..x2], [x3..x4], ...
//            // si número impar, intentar emparejar secuencialmente (esto ocurre si borde incluye extremos)
//            $m = count($xs);
//            if ($m == 1) {
//                // solo un borde en esa fila -> marcarlo
//                $filled[$xs[0] . ',' . $y] = [$xs[0], $y];
//                continue;
//            }
//            for ($i = 0; $i < $m; $i += 2) {
//                if (!isset($xs[$i + 1])) break;
//                $x1 = $xs[$i];
//                $x2 = $xs[$i + 1];
//                if ($x1 > $x2) {
//                    $tmp = $x1;
//                    $x1 = $x2;
//                    $x2 = $tmp;
//                }
//                for ($x = $x1; $x <= $x2; $x++) {
//                    $filled[$x . ',' . $y] = [$x, $y];
//                }
//            }
//        }
//
//        // Devolver array de puntos
//        return array_values($filled);
//    }
//
//    private function largestRectangleInTileSet(array $filledTiles)
//    {
//        // transformar a mapa por coordenadas para acceso O(1)
//        $map = [];
//        $minX = PHP_INT_MAX;
//        $maxX = PHP_INT_MIN;
//        $minY = PHP_INT_MAX;
//        $maxY = PHP_INT_MIN;
//        foreach ($filledTiles as $p) {
//            [$x, $y] = $p;
//            $map[$y][$x] = true;
//            $minX = min($minX, $x);
//            $maxX = max($maxX, $x);
//            $minY = min($minY, $y);
//            $maxY = max($maxY, $y);
//        }
//
//        // lista de tiles ordenada por y luego x para iterar esquinas
//        $coords = [];
//        foreach ($map as $y => $row) {
//            foreach ($row as $x => $_) {
//                $coords[] = [$x, (int)$y];
//            }
//        }
//
//        $n = count($coords);
//        $best = ['area' => 0];
//        // probar pares como esquinas opuestas (inclusivo)
//        for ($i = 0; $i < $n; $i++) {
//            for ($j = $i + 1; $j < $n; $j++) {
//                $x1 = min($coords[$i][0], $coords[$j][0]);
//                $x2 = max($coords[$i][0], $coords[$j][0]);
//                $y1 = min($coords[$i][1], $coords[$j][1]);
//                $y2 = max($coords[$i][1], $coords[$j][1]);
//
//                // comprobar que todos los tiles en el rect están en map
//                $ok = true;
//                for ($y = $y1; $y <= $y2 && $ok; $y++) {
//                    for ($x = $x1; $x <= $x2; $x++) {
//                        if (!isset($map[$y][$x])) {
//                            $ok = false;
//                            break;
//                        }
//                    }
//                }
//                if ($ok) {
//                    $area = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
//                    if ($area > $best['area']) {
//                        $best = [
//                            'area' => $area,
//                            'x1' => $x1, 'y1' => $y1,
//                            'x2' => $x2, 'y2' => $y2
//                        ];
//                    }
//                }
//            }
//        }
//
//        return $best;
//    }







//    private $polygon;
//    private $map;
//    private $width;
//    private $height;
//
//    public function construct(array $polygon)
//    {
//        $this->polygon = $polygon;
//
//        // Determinar límites
//        $xs = array_column($polygon, 0);
//        $ys = array_column($polygon, 1);
//        $this->width = max($xs) + 2;
//        $this->height = max($ys) + 2;
//
//        // Inicializar mapa
//        $this->map = array_fill(0, $this->height, array_fill(0, $this->width, 0));
//
//        // Rellenar polígono
//        $this->fillPolygon();
//    }
//
//    private function pointInPolygon($x, $y)
//    {
//        $inside = false;
//        $n = count($this->polygon);
//        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
//            $xi = $this->polygon[$i][0];
//            $yi = $this->polygon[$i][1];
//            $xj = $this->polygon[$j][0];
//            $yj = $this->polygon[$j][1];
//
//            if ((($yi > $y) != ($yj > $y)) &&
//                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi + 0.0) + $xi)) {
//                $inside = !$inside;
//            }
//        }
//        return $inside;
//    }
//
//    private function fillPolygon()
//    {
//        for ($y = 0; $y < $this->height; $y++) {
//            for ($x = 0; $x < $this->width; $x++) {
//                if ($this->pointInPolygon($x, $y)) {
//                    $this->map[$y][$x] = 1;
//                }
//            }
//        }
//    }
//
//    public function maxRectangle()
//    {
//        $heights = array_fill(0, $this->width, 0);
//        $maxArea = 0;
//        $bestRect = null;
//
//        for ($y = 0; $y < $this->height; $y++) {
//            for ($x = 0; $x < $this->width; $x++) {
//                $heights[$x] = $this->map[$y][$x] ? $heights[$x] + 1 : 0;
//            }
//
//            $stack = [];
//            $x = 0;
//            while ($x <= $this->width) {
//                $h = ($x < $this->width) ? $heights[$x] : 0;
//                if (empty($stack) || $h >= $heights[end($stack)]) {
//                    $stack[] = $x++;
//                } else {
//                    $top = array_pop($stack);
//                    $width = empty($stack) ? $x : $x - end($stack) - 1;
//                    $area = $heights[$top] * $width;
//                    if ($area > $maxArea) {
//                        $maxArea = $area;
//                        $bestRect = [
//                            'x1' => $x - $width,
//                            'y1' => $y - $heights[$top] + 1,
//                            'x2' => $x - 1,
//                            'y2' => $y,
//                            'area' => $area
//                        ];
//                    }
//                }
//            }
//        }
//
//        return $bestRect;
//    }






//    private function other(array $points) {
//        // 1. Calcular rectángulos posibles
//        $rects = [];
//        foreach ($points as $i => $p1) {
//            foreach ($points as $j => $p2) {
//                if ($i >= $j) continue;
//                $x1 = min($p1[0], $p2[0]);
//                $x2 = max($p1[0], $p2[0]);
//                $y1 = min($p1[1], $p2[1]);
//                $y2 = max($p1[1], $p2[1]);
//                $area = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
//                $rects[] = ['x1'=>$x1,'y1'=>$y1,'x2'=>$x2,'y2'=>$y2,'area'=>$area,'corners'=>[$i,$j]];
//            }
//        }
//
//// 2. Ordenar rectángulos por área descendente
//        usort($rects, fn($a,$b) => $b['area'] <=> $a['area']);
//
//// 3. Comprobar validez
//        foreach ($rects as $rect) {
//            $valid = true;
//            foreach ($points as $k => $pt) {
//                if (in_array($k, $rect['corners'])) continue;
//                if ($pt[0] >= $rect['x1'] && $pt[0] <= $rect['x2'] &&
//                    $pt[1] >= $rect['y1'] && $pt[1] <= $rect['y2']) {
//                    $valid = false;
//                    break;
//                }
//            }
//
//            // Chequear puntos medios de todos los pares consecutivos
//            for ($i=0; $i<count($points); $i++) {
//                $p1 = $points[$i];
//                $p2 = $points[($i+1)%count($points)];
//                $mx = ($p1[0]+$p2[0])/2;
//                $my = ($p1[1]+$p2[1])/2;
//                if ($mx >= $rect['x1'] && $mx <= $rect['x2'] &&
//                    $my >= $rect['y1'] && $my <= $rect['y2']) {
//                    $valid = false;
//                    break;
//                }
//            }
//
//            if ($valid) {
//                return $rect; // primer rectángulo válido = máximo
//            }
//        }
//
//        return null;
//    }






    /**
     * 4771232025 too high
     * 3001445088 too high
     */
    private function getPart22(): int
    {
        $points = array_values($this->data);

        $this->points = $points;
        $this->buildEdges();

        $best = $this->getMaxArea();

        return $best['area'];
    }


    private array $points; // puntos del perímetro
    private array $edges = [];

    // Construye las aristas del polígono
    private function buildEdges(): void
    {
        $n = count($this->points);
        for ($i = 0; $i < $n; $i++) {
            $this->edges[] = [
                $this->points[$i],
                $this->points[($i + 1) % $n]
            ];
        }
    }

    // Punto dentro del polígono (ray casting)
    private function pointInPolygon(float $x, float $y): bool
    {
        $inside = false;
        $n = count($this->points);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            [$xi, $yi] = $this->points[$i];
            [$xj, $yj] = $this->points[$j];

            if ((($yi > $y) !== ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
        }
        return $inside;
    }

    // Comprueba que TODO el rectángulo está dentro del polígono
    private function rectangleIsInside(int $x1, int $y1, int $x2, int $y2): bool
    {
        if ($x1 > $x2) [$x1, $x2] = [$x2, $x1];
        if ($y1 > $y2) [$y1, $y2] = [$y2, $y1];

        // Comprobamos solo los puntos extremos de cada fila
        for ($y = $y1; $y <= $y2; $y++) {
            if (!$this->pointInPolygon($x1 + 0.5, $y + 0.5) || !$this->pointInPolygon($x2 + 0.5, $y + 0.5)) {
                return false;
            }
        }

        // Comprobamos los extremos verticales (opcional, pero más seguro)
        for ($x = $x1; $x <= $x2; $x++) {
            if (!$this->pointInPolygon($x + 0.5, $y1 + 0.5) || !$this->pointInPolygon($x + 0.5, $y2 + 0.5)) {
                return false;
            }
        }

        return true;
    }

    // Encuentra el área máxima
    public function getMaxArea(): array
    {
        $best = ['area' => 0];

        $n = count($this->points);
        for ($i = 0; $i < $n; $i++) {
            [$x1, $y1] = $this->points[$i];
            for ($j = $i + 1; $j < $n; $j++) {
                [$x2, $y2] = $this->points[$j];

                if ($x1 == $x2 || $y1 == $y2) continue; // mismo eje, no sirve

                $rx1 = min($x1, $x2);
                $rx2 = max($x1, $x2);
                $ry1 = min($y1, $y2);
                $ry2 = max($y1, $y2);

                if ($this->rectangleIsInside($rx1, $ry1, $rx2, $ry2)) {
                    $area = ($rx2 - $rx1) * ($ry2 - $ry1);
                    if ($area > $best['area']) {
                        $best = compact('rx1','ry1','rx2','ry2','area');
                    }
                }
            }
        }

        return $best;
    }
}


