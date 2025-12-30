<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day23 extends DayBase
{
    protected const int TEST_1 = 94;
    protected const int TEST_2 = 154;

    private array $visitedSteps = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

//        $this->printNewMap([], []);
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
        $initialPosition = $this->getEmptyPosition(array_key_first($this->data));
        $endPosition = $this->getEmptyPosition(array_key_last($this->data));
        $steps = null;
        $this->visitedSteps = [];

        $this->makePath($initialPosition, [], 0, $endPosition, $steps);

//        $this->printNewMap($initialPosition, $bestPath['moves']);

        return $steps;
    }

    private function getPart2(): int
    {
        $initialPosition = $this->getEmptyPosition(array_key_first($this->data));
        $endPosition = $this->getEmptyPosition(array_key_last($this->data));

        return $this->makePathOptimized($initialPosition, $endPosition);
    }

    public function makePath(array $position, array $prevPosition, int $stepsSoFar, array $endPosition, ?int &$bestSteps): void
    {
        $positionKey = $position[0] . ':' . $position[1];

        // Si llegamos al final
        if ($position === $endPosition) {
            if ($bestSteps === null || $stepsSoFar > $bestSteps) {
                $bestSteps = $stepsSoFar;
                echo "End reached with $stepsSoFar steps." . PHP_EOL;
            }

            return;
        }

        // Si ya hemos estado aquí con más pasos, salir (pruning)
        if (isset($this->visitedSteps[$positionKey]) && $this->visitedSteps[$positionKey] >= $stepsSoFar) {
            return;
        }

        $this->visitedSteps[$positionKey] = $stepsSoFar;

        // Posiciones adyacentes
        $right = [$position[0], $position[1] + 1];
        $bottom = [$position[0] + 1, $position[1]];
        $left = [$position[0], $position[1] - 1];
        $top = [$position[0] - 1, $position[1]];

        $tile = $this->data[$position[0]][$position[1]];

        $newPositions = match ($tile) {
            self::DIRECTION_ARROW_BOTTOM => ['B' => $bottom],
            self::DIRECTION_ARROW_LEFT => ['L' => $left],
            self::DIRECTION_ARROW_RIGHT => ['R' => $right],
            self::DIRECTION_ARROW_TOP => ['T' => $top],
            default => ['R' => $right, 'B' => $bottom, 'L' => $left, 'T' => $top],
        };

        foreach ($newPositions as $newPos) {
            if (!isset($this->data[$newPos[0]][$newPos[1]]) || $this->data[$newPos[0]][$newPos[1]] === self::WALL) {
                continue;
            }

            if ($newPos === $prevPosition) {
                continue;
            }

            $this->makePath($newPos, $position, $stepsSoFar + 1, $endPosition, $bestSteps);
        }
    }

    protected function posKey(array $pos): string
    {
        return $pos[0] . ':' . $pos[1];
    }

    protected function isWalkable(int $r, int $c): bool
    {
        return isset($this->data[$r][$c]) && $this->data[$r][$c] !== self::WALL;
    }

    protected function isNode(array $pos): bool
    {
        [$r, $c] = $pos;
        if (!$this->isWalkable($r, $c)) {
            return false;
        }

        $walkable = 0;
        foreach ([[0, 1], [1, 0], [0, -1], [-1, 0]] as [$dr, $dc]) {
            if ($this->isWalkable($r + $dr, $c + $dc)) {
                $walkable++;
            }
        }

        return $walkable !== 2;
    }

    protected function findEdgesFrom(array $start): array
    {
        $edges = [];
        $visited = [];
        $queue = [[$start[0], $start[1], 0]]; // fila, columna, pasos

        while (!empty($queue)) {
            [$r, $c, $steps] = array_shift($queue);
            $key = "$r:$c";

            if (isset($visited[$key])) {
                continue;
            }

            $visited[$key] = true;

            // Ignora el nodo inicial, pero si llegamos a otro nodo, guardamos el edge
            if ($steps > 0 && $this->isNode([$r, $c])) {
                $edges[$this->posKey([$r, $c])] = $steps;
                continue;
            }

            // Explorar todas las direcciones posibles
            foreach ([[0, 1], [0, -1], [1, 0], [-1, 0]] as [$dr, $dc]) {
                $nr = $r + $dr;
                $nc = $c + $dc;

                if (!$this->isWalkable($nr, $nc)) {
                    continue;
                }

                // Evita retrocesos innecesarios
                if (!isset($visited["$nr:$nc"])) {
                    $queue[] = [$nr, $nc, $steps + 1];
                }
            }
        }

        return $edges;
    }

    protected function buildGraph(array $start, array $end): array
    {
        $graph = [];
        $nodes = [];

        $rows = count($this->data);
        $cols = count($this->data[0]);

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                $pos = [$r, $c];
                if ($this->isNode($pos)) {
                    $nodes[] = $pos;
                }
            }
        }

        if (!in_array($start, $nodes)) {
            $nodes[] = $start;
        }

        if (!in_array($end, $nodes)) {
            $nodes[] = $end;
        }

        foreach ($nodes as $node) {
            $edges = $this->findEdgesFrom($node);
            $graph[$this->posKey($node)] = $edges;
        }

        return $graph;
    }

    public function makePathOptimized(array $start, array $end): int
    {
        $graph = $this->buildGraph($start, $end);
        $this->debugGraph($graph);

        $bestDistance = 0;

        $this->getBestPathFromNodes($graph, $this->posKey($start), $this->posKey($end), ['nodes' => [], 'distance' => 0], $bestDistance);

        return $bestDistance;
    }

    private function getBestPathFromNodes(array $graph, string $node, string $end, array $path, int &$bestDistance): void
    {
        if ($node === $end) {
            $bestDistance = max($bestDistance, $path['distance']);

            return;
        }

        foreach ($graph[$node] as $neighbor => $distance) {
            if (isset($path['nodes'][$neighbor])) {
                continue;
            }

            $newPath = $path;
            $newPath['nodes'][$neighbor] = $distance;
            $newPath['distance'] += $distance;

            $this->getBestPathFromNodes($graph, $neighbor, $end, $newPath, $bestDistance);
        }
    }

    private function getEmptyPosition(int $y): array
    {
        for ($x = 0; $x < count($this->data[$y]); $x++) {
            if ($this->data[$y][$x] === self::WALL) {
                continue;
            }

            return [$y, $x];
        }

        throw new \Exception("Invalid position");
    }

    private function printNewMap(array $initialPosition, array $steps): void
    {
        $map = $this->data;

        if (!empty($initialPosition)) {
            $map[$initialPosition[0]][$initialPosition[1]] = self::START_POSITION;
        }

        foreach ($steps as $step) {
            $map[$step[0]][$step[1]] = self::POSITION;
        }

        $this->printMap($map);
        echo PHP_EOL;
    }

    public function debugGraph(array $graph): void
    {
        print_r($graph);

        foreach ($graph as $from => $edges) {
            echo $from . " => ";

            foreach ($edges as $edge => $cost) {
                echo $edge . '(' . $cost . ') ';
            }

            echo PHP_EOL;
        }
    }
}
