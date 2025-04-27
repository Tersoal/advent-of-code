<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day21 extends DayBase
{
    protected const int TEST_1 = 16;
    protected const int TEST_2 = 50;
    protected const int STEPS_TEST = 6;
    protected const int STEPS_TEST2 = 10;
    protected const int STEPS = 64;
    protected const int STEPS2 = 26501365;

    protected array $directions = [[-1, 0], [0, -1], [1, 0], [0, 1]];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [
            $this->getTotalPlots($this->test ? self::STEPS_TEST : self::STEPS),
            $this->getTotalPlots2($this->test ? self::STEPS_TEST2 : self::STEPS2)
        ];
    }

    private function getTotalPlots(int $steps): int
    {
        $initialPlot = $this->getInitialPosition($this->data);
        $plots = [$initialPlot];

        for ($i = 0; $i < $steps; $i++) {
            $this->getPlotsOnStep($i, $plots);
        }

//        $this->printNewMap($plots);

        return count($plots);
    }

    private function getPlotsOnStep(int $step, array &$plots): void
    {
        $newPlots = [];
        $sizeY = count($this->data);
        $sizeX = count($this->data[0]);

        foreach ($plots as $plot) {
            foreach ($this->directions as $direction) {
                $newY = $plot[0] + $direction[0];
                $newX = $plot[1] + $direction[1];
                $positionMap = $newY . ':' . $newX;

                if (isset($newPlots[$positionMap])) {
                    continue;
                }

                // Coordenadas relativas al mapa original
                $mapY = ($newY % $sizeY + $sizeY) % $sizeY;
                $mapX = ($newX % $sizeX + $sizeX) % $sizeX;

                if ($this->data[$mapY][$mapX] === self::OBSTACLE) {
                    continue;
                }

                $newPlots[$positionMap] = [$newY, $newX];
            }
        }

        $plots = $newPlots;
    }

    /**
     * Oh my God, very crazy for me.
     *
     * Ref: https://www.reddit.com/r/adventofcode/comments/18nevo3/2023_day_21_solutions/
     * Ref: https://advent-of-code.xavd.id/writeups/2023/day/21/
     *
     * @param int $steps
     * @return int
     * @throws \Exception
     */
    private function getTotalPlots2(int $steps): int
    {
        $gridSize = count($this->data); // Suponemos cuadrado n x n
        $extra = $steps % $gridSize;

        // Obtenemos f(0), f(1), f(2) → pasos: extra, extra+n, extra+2n
        $s0 = $extra;
        $s1 = $extra + $gridSize;
        $s2 = $extra + 2 * $gridSize;

        $f0 = $this->getTotalPlots($s0);
        $f1 = $this->getTotalPlots($s1);
        $f2 = $this->getTotalPlots($s2);

        // Interpolación cuadrática:
        // f(n) = a·n² + b·n + c
        // Usamos n = 0, 1, 2

        $a = ($f2 - 2 * $f1 + $f0) / 2;
        $b = $f1 - $f0 - $a;
        $c = $f0;

        $n = intdiv($steps - $extra, $gridSize);

        return (int)($a * $n * $n + $b * $n + $c);
    }

    private function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] !== self::START_POSITION) {
                    continue;
                }

                return [$y, $x];
            }
        }

        throw new \Exception("Invalid position");
    }

    private function printNewMap(array $plots): void
    {
        $map = $this->data;

        foreach ($plots as $plot) {
            $map[$plot[0]][$plot[1]] = self::POSITION;
        }

        $this->printMap($map);
        echo PHP_EOL;
    }
}
