<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day17 extends DayBase
{
    protected const int TEST_1 = 102;
    protected const int TEST_2 = 94;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

        foreach ($this->data as $y => $row) {
            foreach ($row as $x => $value) {
                $this->data[$y][$x] = (int)$value;
            }
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [$this->getMinHeatLoss(), $this->getMinHeatLossPart2()];
    }

    public function getMinHeatLoss(): int
    {
        $maxY = count($this->data);
        $maxX = count($this->data[0]);

        $queue = new \SplPriorityQueue();
        $queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);

        // Estado inicial: y, x, dy, dx, steps en dirección actual, heatLoss
        $queue->insert([0, 0, 0, 0, 0, 0], 0); // Prioridad = -heatLoss (min-heap simulado)
        $queue->insert([0, 0, 0, 1, 0, 0], 0); // derecha
        $queue->insert([0, 0, 1, 0, 0, 0], 0); // abajo

        $visited = [];

        while (!$queue->isEmpty()) {
            [$y, $x, $dy, $dx, $steps, $loss] = $queue->extract();

            if ($y === $maxY - 1 && $x === $maxX - 1) {
                return $loss;
            }

            $key = "$y,$x,$dy,$dx,$steps";
            if (isset($visited[$key]) && $visited[$key] <= $loss) {
                continue;
            }
            $visited[$key] = $loss;

            foreach ([[0,1],[1,0],[0,-1],[-1,0]] as [$ndy, $ndx]) {
                if ($ndy === -$dy && $ndx === -$dx) {
                    continue; // no se puede dar la vuelta
                }

                $nsteps = ($dy === $ndy && $dx === $ndx) ? $steps + 1 : 1;
                if ($nsteps > 3) {
                    continue; // no más de 3 seguidos en misma dirección
                }

                $ny = $y + $ndy;
                $nx = $x + $ndx;

                if (!isset($this->data[$ny][$nx])) {
                    continue;
                }

                $nloss = $loss + $this->data[$ny][$nx];

                $queue->insert([$ny, $nx, $ndy, $ndx, $nsteps, $nloss], -$nloss); // prioridad negativa para simular min-heap
            }
        }

        return -1; // No se puede llegar (no debería pasar)
    }

    public function getMinHeatLossPart2(): int
    {
        $maxY = count($this->data);
        $maxX = count($this->data[0]);

        $queue = new \SplPriorityQueue();
        $queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);

        $queue->insert([0, 0, 0, 1, 0, 0], 0); // derecha
        $queue->insert([0, 0, 1, 0, 0, 0], 0); // abajo

        $visited = [];

        while (!$queue->isEmpty()) {
            [$y, $x, $dy, $dx, $steps, $loss] = $queue->extract();

            if ($y === $maxY - 1 && $x === $maxX - 1 && $steps >= 4) {
                return $loss;
            }

            $key = "$y,$x,$dy,$dx,$steps";
            if (isset($visited[$key]) && $visited[$key] <= $loss) {
                continue;
            }
            $visited[$key] = $loss;

            foreach ([[0,1],[1,0],[0,-1],[-1,0]] as [$ndy, $ndx]) {
                if ($ndy === -$dy && $ndx === -$dx) {
                    continue; // no girar 180°
                }

                // Misma dirección
                if ($ndy === $dy && $ndx === $dx) {
                    $nsteps = $steps + 1;
                    if ($nsteps > 10) continue;
                } else {
                    // Cambio de dirección: solo si llevamos al menos 4 en la anterior
                    if ($steps < 4) continue;
                    $nsteps = 1;
                }

                $ny = $y + $ndy;
                $nx = $x + $ndx;

                if (!isset($this->data[$ny][$nx])) continue;

                $nloss = $loss + $this->data[$ny][$nx];

                $queue->insert([$ny, $nx, $ndy, $ndx, $nsteps, $nloss], -$nloss);
            }
        }

        return -1; // No se puede llegar
    }
}
