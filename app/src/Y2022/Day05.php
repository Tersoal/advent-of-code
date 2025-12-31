<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day05 extends DayBase
{
    protected const string TEST_1 = 'CMZ';
    protected const string TEST_2 = 'MCD';

    private array $crates = [];
    private array $steps = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $line) {
            if (str_contains($line, '[')) {
                $this->crates[] = $line;
            }

            if (str_starts_with($line, 'move')) {
                preg_match_all('/\d+/', $line, $matches);
                $this->steps[] = array_map('intval', $matches[0]);
            }
        }

        $cratesTxt = implode("\n", $this->crates);
        $this->crates = $this->parseCrates($cratesTxt);

        if ($this->test) {
            print_r($this->data);
            print_r($this->crates);
            print_r($this->steps);
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

    private function getPart1(): string
    {
        return $this->getResultWord(true);
    }

    private function getPart2(): string
    {
        return $this->getResultWord(false);
    }

    private function getResultWord(bool $moveOneEachTime): string
    {
        $result = '';
        $crates = $this->moveCrates($moveOneEachTime);

        foreach ($crates as $stack) {
            $result .= array_last($stack);
        }

        return $result;
    }

    private function moveCrates(bool $moveOneEachTime): array
    {
        $crates = $this->crates;

        foreach ($this->steps as $step) {
            if ($moveOneEachTime) {
                for ($i = 0; $i < $step[0]; $i++) {
                    $crates[$step[2] - 1][] = array_pop($crates[$step[1] - 1]);
                }
            } else {
                $pick = array_splice($crates[$step[1] - 1], -$step[0]);
                $crates[$step[2] - 1] = array_merge($crates[$step[2] - 1], $pick);
            }
        }

        return $crates;
    }

    /**
     * 1. Separar el texto en líneas
     * 2. Recorrer las líneas de abajo a arriba
     * 3. Para cada línea:
     *      - Leer posiciones 1, 5, 9, 13, ...
     *      - Si hay una letra [A-Z], añadirla a su columna
     *
     * Cada columna será un array ordenado de abajo → arriba
     */
    private function parseCrates(string $text): array
    {
        $lines = explode("\n", $text);
        $lines = array_reverse($lines); // de abajo a arriba

        $stacks = [];

        foreach ($lines as $line) {
            for ($i = 0; $i < strlen($line); $i += 4) {
                $char = $line[$i + 1] ?? null;

                if ($char !== null && ctype_alpha($char)) {
                    $column = intdiv($i, 4);
                    $stacks[$column][] = $char;
                }
            }
        }

        // Opcional: reindexar columnas
        ksort($stacks);

        return array_values($stacks);
    }
}
