<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day24 extends DayBase
{
    protected const int TEST_1 = 2;
    protected const int TEST_2 = 47;

    private int $fromLimitTest = 7;
    private int $toLimitTest = 27;
    private int $fromLimit = 200000000000000;
    private int $toLimit = 400000000000000;

    private int $limitFrom = 0;
    private int $limitTo = 0;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\n", $data);

        foreach ($data as $line) {
            $parts = explode("@", $line);
            $position = array_map('intval', explode(',', trim($parts[0])));
            $velocity = array_map('intval', explode(',', trim($parts[1])));
            $this->data[] = ['position' => $position, 'velocity' => $velocity];
        }

        $this->limitFrom = $this->test ? $this->fromLimitTest : $this->fromLimit;
        $this->limitTo = $this->test ? $this->toLimitTest : $this->toLimit;

//        print_r($this->data);
//        echo PHP_EOL;
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
        $results = [];
        $processed = [];

        foreach ($this->data as $i => $value1) {
            foreach ($this->data as $j => $value2) {
                if ($i === $j) {
                    continue;
                }

                $key = $i . ':' . $j;
                $key2 = $j . ':' . $i;

                if (isset($processed[$key])) {
                    continue;
                }

                $results[$key] = $this->calculateIntersection($value1, $value2);
                $processed[$key] = true;
                $processed[$key2] = true;
            }
        }

//        print_r($results);
        $inside = 0;

        foreach ($results as $result) {
            if ($result === null || $result[0] === null || $result[1] === null) {
                continue;
            }

            if ($result[0] < $this->limitFrom || $result[0] > $this->limitTo) {
                continue;
            }

            if ($result[1] < $this->limitFrom || $result[1] > $this->limitTo) {
                continue;
            }

            $inside++;
        }

        return $inside;
    }

    /**
     * @param array $value1
     * @param array $value2
     * @return array|null
     * @example
     *
     * Hailstone A: 18, 19, 22 @ -1, -1, -2
     * Hailstone B: 12, 31, 28 @ -1, -2, -1
     * Hailstones' paths will cross outside the test area (at x=-6, y=-5).
     */
    private function calculateIntersection(array $value1, array $value2): ?array
    {
        // Posiciones iniciales
        [$x1, $y1] = $value1['position'];
        [$x2, $y2] = $value2['position'];

        // Velocidades
        [$vx1, $vy1] = $value1['velocity'];
        [$vx2, $vy2] = $value2['velocity'];

        // Resolver como sistema: encontrar t1 tal que:
        // x1 + vx1*t = x2 + vx2*s
        // y1 + vy1*t = y2 + vy2*s

        $den = $vx1 * $vy2 - $vy1 * $vx2;
        if ($den == 0) {
            // Líneas paralelas
            return null;
        }

        $dx = $x2 - $x1;
        $dy = $y2 - $y1;

        $t = ($dx * $vy2 - $dy * $vx2) / $den;
        $s = ($dx * $vy1 - $dy * $vx1) / $den;

        if ($t < 0 || $s < 0) {
            // Intersección en el pasado para al menos una piedra
            return null;
        }

        $ix = $x1 + $vx1 * $t;
        $iy = $y1 + $vy1 * $t;

        return [$ix, $iy];
    }

//    private function getPart2(): int
//    {
//        $equations = $this->getEquations();
//
//        print_r($equations);
//
//        $solution = $this->solveLinearSystem($equations[0], $equations[1]);
//
//        print_r($solution);
//
//        return 0;
//    }
//
//    private function getEquations(): array
//    {
//        $A = [];
//        $b = [];
//
//        foreach ($this->data as $i => $value) {
//            [$xi, $yi, $zi] = $value['position'];
//            [$vxi, $vyi, $vzi] = $value['velocity'];
//
//            // Variables: xr, yr, zr, vxr, vyr, vzr (6 variables)
//            // Ecuaciones: (xr - xi) = t * (vxi - vxr) => xr - t * vxr = xi + t * -vxi
//            // Reorganizamos: xr - t * vxr - xi + t * vxi = 0
//            // Finalmente: xr - xi + t * (vxi - vxr) = 0 => xr - xi = t * (vxi - vxr)
//
//            // Vamos a construir 6 ecuaciones con coeficientes sobre 6 variables
//            // xr, yr, zr, vxr, vyr, vzr
//            // Usamos ti como variable interna que luego se elimina del sistema
//
//            $ti = 1; // Variable auxiliar que eliminaremos al montar ecuaciones entre pares
//
//            // Ecuaciones para cada dimensión
//            $A[] = [1, 0, 0, -$ti, 0, 0];
//            $b[] = $xi + $ti * $vxi;
//
//            $A[] = [0, 1, 0, 0, -$ti, 0];
//            $b[] = $yi + $ti * $vyi;
//
//            $A[] = [0, 0, 1, 0, 0, -$ti];
//            $b[] = $zi + $ti * $vzi;
//
//            // Solo 6 ecuaciones necesarias
//            if (count($A) >= 6) {
//                break;
//            }
//        }
//
//        return [$A, $b];
//    }
//
//    function solveLinearSystem($A, $b): array
//    {
//        $n = count($A);
//
//        // Augment matrix
//        for ($i = 0; $i < $n; $i++) {
//            $A[$i][] = $b[$i];
//        }
//
//        // Gaussian elimination
//        for ($i = 0; $i < $n; $i++) {
//            // Find pivot
//            $maxRow = $i;
//            for ($k = $i + 1; $k < $n; $k++) {
//                if (abs($A[$k][$i]) > abs($A[$maxRow][$i])) {
//                    $maxRow = $k;
//                }
//            }
//            // Swap rows
//            $temp = $A[$i];
//            $A[$i] = $A[$maxRow];
//            $A[$maxRow] = $temp;
//
//            // If pivot is zero (or casi cero), no se puede continuar
//            if (abs($A[$i][$i]) < 1e-12) {
//                throw new \Exception('Matriz singular o pivote cero encontrado en fila ' . $i);
//            }
//
//            // Make all rows below this have 0 in current column
//            for ($k = $i + 1; $k < $n; $k++) {
//                $factor = $A[$k][$i] / $A[$i][$i];
//                for ($j = $i; $j <= $n; $j++) {
//                    $A[$k][$j] -= $factor * $A[$i][$j];
//                }
//            }
//        }
//
//        // Back substitution
//        $x = array_fill(0, $n, 0);
//        for ($i = $n - 1; $i >= 0; $i--) {
//            $x[$i] = $A[$i][$n] / $A[$i][$i];
//            for ($k = $i - 1; $k >= 0; $k--) {
//                $A[$k][$n] -= $A[$k][$i] * $x[$i];
//            }
//        }
//
//        return $x;
//    }





    private function getPart2(): int
    {
        // px0 + t1*vx0 = px1 + t1*vx1
        // py0 + t1*vy0 = py1 + t1*vy1
        // pz0 + t1*vz0 = pz1 + t1*vz1
        //
        // px0 + t2*vx0 = px2 + t2*vx2
        // py0 + t2*vy0 = py2 + t2*vy2
        // pz0 + t2*vz0 = pz2 + t2*vz2
        //
        // px0 + t3*vx0 = px3 + t3*vx3
        // py0 + t3*vy0 = py3 + t3*vy3
        // pz0 + t3*vz0 = pz3 + t3*vz3
        //

        // Ejemplo de uso:
//        $hailstones = [
//            [19, 13, 30, -2,  1, -2],
//            [18, 19, 22, -1, -1, -2],
//            [20, 25, 34, -2, -2, -4]
//        ];

        $hailstones[0] = array_merge($this->data[0]['position'], $this->data[0]['velocity']);
        $hailstones[1] = array_merge($this->data[1]['position'], $this->data[1]['velocity']);
        $hailstones[2] = array_merge($this->data[2]['position'], $this->data[2]['velocity']);
//
//        list($A, $b) = $this->build_equation_system($hailstones);
//        try {
//            $solution = $this->solve_linear_system($A, $b);
//            list($px0, $py0, $pz0, $vx0, $vy0, $vz0) = $solution;
//            echo "Rock position: $px0, $py0, $pz0\n";
//            echo "Rock velocity: $vx0, $vy0, $vz0\n";
//
//            return $px0 + $py0 + $pz0;
//        } catch (\Exception $e) {
//            echo "Error: " . $e->getMessage();
//
//            return 0;
//        }

//        [$A, $b] = $this->build_linear_system($hailstones);
//
//        print_r($A);
//        print_r($b);
//
//        $solution = $this->solve_linear_system($A, $b);
//
//        echo "px0 = {$solution[0]}, py0 = {$solution[1]}, pz0 = {$solution[2]}\n";
//        echo "vx0 = {$solution[3]}, vy0 = {$solution[4]}, vz0 = {$solution[5]}\n";
//        echo "t1 = {$solution[6]}, t2 = {$solution[7]}, t3 = {$solution[8]}\n";
//
//        return $solution[0] + $solution[1] + $solution[2];


        list($A, $b) = $this->build_system();
        $solution = $this->solve_linear_system($A, $b);

        list($px, $py, $pz, $vx, $vy, $vz) = $solution;
        echo "Rock starts at (" . round($px) . ", " . round($py) . ", " . round($pz) . ")\n";
        echo "Rock velocity: (" . round($vx) . ", " . round($vy) . ", " . round($vz) . ")\n";

        return 0;
    }


    function solve_linear_system($A, $b) {
        $n = count($A);

        // Augment matrix
        for ($i = 0; $i < $n; $i++) {
            $A[$i][] = $b[$i];
        }

        // Gaussian elimination with partial pivoting
        for ($i = 0; $i < $n; $i++) {
            // Find the row with the maximum element in this column
            $maxRow = $i;
            $maxVal = abs($A[$i][$i]);
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($A[$k][$i]) > $maxVal) {
                    $maxVal = abs($A[$k][$i]);
                    $maxRow = $k;
                }
            }

            // If pivot is too small, consider it zero
            if ($maxVal < 1e-12) {
                throw new \Exception("Pivot too small or zero at row $i");
            }

            // Swap rows
            if ($maxRow != $i) {
                $temp = $A[$i];
                $A[$i] = $A[$maxRow];
                $A[$maxRow] = $temp;
            }

            // Eliminate rows below
            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $A[$k][$i] / $A[$i][$i];
                for ($j = $i; $j <= $n; $j++) {
                    $A[$k][$j] -= $factor * $A[$i][$j];
                }
            }
        }

        // Back substitution
        $x = array_fill(0, $n, 0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $sum = $A[$i][$n];
            for ($j = $i + 1; $j < $n; $j++) {
                $sum -= $A[$i][$j] * $x[$j];
            }
            if (abs($A[$i][$i]) < 1e-12) {
                throw new \Exception("Division by zero during back substitution at row $i");
            }
            $x[$i] = $sum / $A[$i][$i];
        }

        return $x;
    }

    function build_system() {
        // Datos de las 3 piedras
        $stones = [
            [19, 13, 30, -2,  1, -2],
            [18, 19, 22, -1, -1, -2],
            [20, 25, 34, -2, -2, -4],
        ];

        $A = [];
        $b = [];

        for ($i = 0; $i < 2; $i++) {
            for ($j = $i + 1; $j < 3; $j++) {
                $s1 = $stones[$i];
                $s2 = $stones[$j];

                // restamos las ecuaciones de la piedra j - i y eliminamos ti, tj
                // (px - sx1) + t1 (vx - vx1) = (px - sx2) + t2 (vx - vx2)
                // => (sx2 - sx1) + (vx2 - vx1)t = (vx - vx1)t1 - (vx - vx2)t2
                // pero para cada eje

                // Ecuación en X
                $A[] = [1, 0, 0, $s1[3] - $s2[3], 0, 0]; // px + t*(vx - vx1)
                $b[] = $s1[0] - $s2[0];

                // Ecuación en Y
                $A[] = [0, 1, 0, 0, $s1[4] - $s2[4], 0];
                $b[] = $s1[1] - $s2[1];

                // Ecuación en Z
                $A[] = [0, 0, 1, 0, 0, $s1[5] - $s2[5]];
                $b[] = $s1[2] - $s2[2];
            }
        }

        return [$A, $b];
    }

}