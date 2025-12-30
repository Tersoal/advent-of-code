<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day10 extends DayBase
{
    protected const int TEST_1 = 7;
    protected const int TEST_2 = 33;

    private array $machines = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $line) {
            $this->machines[] = $this->parseLine($line);
        }

        if ($this->test) {
            print_r($this->data);
            print_r($this->machines);
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
        $sum = 0;

        foreach ($this->machines as $machine) {
            $sum += $this->solveMachine($machine);
        }

        return $sum;
    }

    private function getPart2(): int
    {
//        $sum = 0;
//
//        foreach ($this->machines as $machine) {
////            $sum += $this->solveMachine2($machine);
////            $sum += $this->solveMachine3($machine);
//            $sum += $this->solveMachine4($machine);
//        }
//
//        return $sum;

        return $this->solve();
    }

    // -----------------------------
    // Core BFS
    // -----------------------------
    private function solveMachine(array $machine): int
    {
        $start = $machine['initial'];
        $buttons = $machine['buttons'];
        $target = str_repeat('.', strlen($start));

        $queue = new \SplQueue();
        $visited = [];

        $queue->enqueue([$start, 0]);
        $visited[$start] = true;

        while (!$queue->isEmpty()) {
            [$state, $steps] = $queue->dequeue();

            if ($state === $target) {
                return $steps;
            }

            foreach ($buttons as $button) {
                $next = $this->pressButton($state, $button);

                if (!isset($visited[$next])) {
                    $visited[$next] = true;
                    $queue->enqueue([$next, $steps + 1]);
                }
            }
        }

        throw new \RuntimeException("No solution found");
    }

    // -----------------------------
    // State transition
    // -----------------------------
    private function pressButton(string $state, array $positions): string
    {
        $chars = str_split($state);

        foreach ($positions as $pos) {
            $chars[$pos] = ($chars[$pos] === '#') ? '.' : '#';
        }

        return implode('', $chars);
    }









    // -------------------------------------------------
    // Core BFS in bounded integer space
    // -------------------------------------------------
    private function solveMachine2(array $machine): int
    {
        $target = $machine['requirements'];
        $buttons = $machine['buttons'];

        $dim = count($target);
        $start = array_fill(0, $dim, 0);
        $targetKey = $this->stateKey($target);

        $queue = new \SplQueue();
        $visited = [];

        $queue->enqueue([$start, 0]);
        $visited[$this->stateKey($start)] = true;

        while (!$queue->isEmpty()) {
            [$state, $steps] = $queue->dequeue();

            if ($this->stateKey($state) === $targetKey) {
                return $steps;
            }

            foreach ($buttons as $button) {
                $next = $state;

                foreach ($button as $idx) {
                    $next[$idx]++;
                    if ($next[$idx] > $target[$idx]) {
                        continue 2; // poda fuerte
                    }
                }

                $key = $this->stateKey($next);
                if (!isset($visited[$key])) {
                    $visited[$key] = true;
                    $queue->enqueue([$next, $steps + 1]);
                }
            }
        }

        throw new \RuntimeException("No solution found");
    }

    // -------------------------------------------------
    // DP tipo Knapsack
    // -------------------------------------------------
    private function solveMachine3(array $machine): int
    {
        $buttons = $machine['buttons'];
        $target = $machine['requirements'];
        $dim = count($target);

        $dp = [];
        $startKey = $this->stateKey(array_fill(0, $dim, 0));
        $dp[$startKey] = 0;

        foreach ($dp as $stateKey => $presses) {
            $state = $this->keyToState($stateKey, $dim);

            foreach ($buttons as $button) {
                $next = $state;
                $valid = true;

                foreach ($button as $i) {
                    $next[$i]++;
                    if ($next[$i] > $target[$i]) {
                        $valid = false;
                        break;
                    }
                }

                if (!$valid) continue;

                $nextKey = $this->stateKey($next);
                $nextPresses = $presses + 1;

                if (!isset($dp[$nextKey]) || $dp[$nextKey] > $nextPresses) {
                    $dp[$nextKey] = $nextPresses;
                }
            }
        }

        $targetKey = $this->stateKey($target);
        return $dp[$targetKey] ?? throw new \RuntimeException("No solution found");
    }


    private function solveMachine4(array $machine): int
    {
        $buttons = $machine['buttons'];
        $target = $machine['requirements'];
        $dim = count($target);

        $start = array_fill(0, $dim, 0);
        $queue = new \SplQueue();
        $visited = [];

        $startKey = $this->stateKey($start);
        $queue->enqueue([$start, 0]);
        $visited[$startKey] = 0;

        while (!$queue->isEmpty()) {
            [$state, $presses] = $queue->dequeue();
            $stateKey = $this->stateKey($state);

            if ($state === $target) {
                return $presses;
            }

            foreach ($buttons as $button) {
                $next = $state;
                $valid = true;

                foreach ($button as $i) {
                    $next[$i]++;
                    if ($next[$i] > $target[$i]) {
                        $valid = false;
                        break;
                    }
                }

                if (!$valid) continue;

                $nextKey = $this->stateKey($next);
                $nextPresses = $presses + 1;

                // Si nunca se visitó o encontramos menos pulsaciones
                if (!isset($visited[$nextKey]) || $visited[$nextKey] > $nextPresses) {
                    $visited[$nextKey] = $nextPresses;
                    $queue->enqueue([$next, $nextPresses]);
                }
            }
        }

        throw new \RuntimeException("No solution found");
    }

    // -------------------------------------------------
    // Helpers
    // -------------------------------------------------
    private function stateKey(array $state): string
    {
        return implode(',', $state);
    }

    private function keyToState(string $key, int $dim): array
    {
        $parts = explode(',', $key);
        return array_map('intval', $parts);
    }








    private int $globalMin;


    public function solve(): int
    {
        $sum = 0;
        foreach ($this->machines as $machine) {
            $this->globalMin = PHP_INT_MAX;
            $this->backtrack($machine['buttons'], $machine['requirements'], array_fill(0, count($machine['requirements']), 0), 0, 0);
            $sum += $this->globalMin;
        }
        return $sum;
    }

    // -------------------------
    // Backtracking con poda
    // -------------------------
    private function backtrack(array $buttons, array $target, array $state, int $buttonIndex, int $pressesSoFar): void
    {
        $dim = count($target);

        // Poda: si ya superamos el mínimo global encontrado
        if ($pressesSoFar >= $this->globalMin) return;

        // Chequeo de éxito
        $success = true;
        for ($i = 0; $i < $dim; $i++) {
            if ($state[$i] != $target[$i]) {
                $success = false;
                break;
            }
        }
        if ($success) {
            $this->globalMin = min($this->globalMin, $pressesSoFar);
            return;
        }

        // Si ya procesamos todos los botones
        if ($buttonIndex >= count($buttons)) return;

        $button = $buttons[$buttonIndex];

        // Estimación máxima de pulsaciones para este botón:
        // no hace falta pulsarlo más veces de la distancia al target máximo
        $maxPresses = $this->estimateMaxPresses($button, $state, $target);

        for ($count = 0; $count <= $maxPresses; $count++) {
            $nextState = $state;
            foreach ($button as $idx) {
                $nextState[$idx] += $count;
                if ($nextState[$idx] > $target[$idx]) continue 2; // poda inmediata
            }

            $this->backtrack($buttons, $target, $nextState, $buttonIndex + 1, $pressesSoFar + $count);
        }
    }

    // -------------------------
    // Estimación de máximo pulsaciones útiles para este botón
    // -------------------------
    private function estimateMaxPresses(array $button, array $state, array $target): int
    {
        $max = PHP_INT_MAX;
        foreach ($button as $i) {
            $max = min($max, $target[$i] - $state[$i]);
        }
        return max(0, $max);
    }


















    // -----------------------------
    // Parsing
    // -----------------------------
    private function parseLine(string $line): array
    {
        // inicial pattern
        preg_match('/\[(.*?)]/', $line, $m);
        $initial = $m[1];

        // requirements
        preg_match('/\{(.*?)}/', $line, $m);
        $requirements = array_map('intval', explode(',', $m[1]));

        // buttons
        preg_match_all('/\((.*?)\)/', $line, $m);
        $buttons = [];

        foreach ($m[1] as $button) {
            if ($button === '') {
                continue;
            }

            $buttons[] = array_map('intval', explode(',', $button));
        }

        return [
            'initial' => $initial,
            'buttons' => $buttons,
            'requirements' => $requirements,
        ];
    }
}
