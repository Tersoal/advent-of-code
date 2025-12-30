<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day22 extends DayBase
{
    protected const int TEST_1 = 5;
    protected const int TEST_2 = 7;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\n", $data);

        foreach ($data as $line) {
            $parts = explode("~", $line);
            $from = array_map('intval', explode(',', $parts[0]));
            $to = array_map('intval', explode(',', $parts[1]));
            $this->data[] = ['from' => $from, 'to' => $to, 'supportedBy' => [], 'supportTo' => []];
        }

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
        $this->simulateFall();
        $this->calculateSupports();

//        print_r($this->data);
//        echo PHP_EOL;

        return $this->countRemovableBricks();
    }

    private function getPart2(): int
    {
        $this->simulateFall();
        $this->calculateSupports();

//        print_r($this->data);
//        echo PHP_EOL;

        return $this->countTotalFallingBricks();
    }

    public function simulateFall(): void
    {
        // Crear un mapa de ocupación tipo "x,y,z" => index del ladrillo
        $occupied = [];

        // Obtener los índices de los ladrillos, ordenados por z ascendente
        $indices = array_keys($this->data);
        usort($indices, function ($a, $b) {
            return $this->data[$a]['from'][2] <=> $this->data[$b]['from'][2];
        });

        foreach ($indices as $i) {
            $brick = &$this->data[$i];

            // Normalizar coordenadas: de menor a mayor
            $x1 = min($brick['from'][0], $brick['to'][0]);
            $x2 = max($brick['from'][0], $brick['to'][0]);
            $y1 = min($brick['from'][1], $brick['to'][1]);
            $y2 = max($brick['from'][1], $brick['to'][1]);
            $z1 = min($brick['from'][2], $brick['to'][2]);
            $z2 = max($brick['from'][2], $brick['to'][2]);

            // Simular caída
            while ($z1 > 1) {
                $canFall = true;

                for ($x = $x1; $x <= $x2; $x++) {
                    for ($y = $y1; $y <= $y2; $y++) {
                        $below = $z1 - 1;
                        if (isset($occupied["$x,$y,$below"])) {
                            $canFall = false;
                            break 2;
                        }
                    }
                }

                if (!$canFall) break;

                $z1--; $z2--;
            }

            // Actualizar coordenadas
            $brick['from'][2] = $z1;
            $brick['to'][2] = $z2;

            // Marcar ocupación
            for ($x = $x1; $x <= $x2; $x++) {
                for ($y = $y1; $y <= $y2; $y++) {
                    for ($z = $z1; $z <= $z2; $z++) {
                        $occupied["$x,$y,$z"] = $i;
                    }
                }
            }
        }
    }

    public function calculateSupports(): void
    {
        // Crear un mapa de ocupación: "x,y,z" => índice de ladrillo
        $occupied = [];

        foreach ($this->data as $i => $brick) {
            $x1 = min($brick['from'][0], $brick['to'][0]);
            $x2 = max($brick['from'][0], $brick['to'][0]);
            $y1 = min($brick['from'][1], $brick['to'][1]);
            $y2 = max($brick['from'][1], $brick['to'][1]);
            $z1 = min($brick['from'][2], $brick['to'][2]);
            $z2 = max($brick['from'][2], $brick['to'][2]);

            for ($x = $x1; $x <= $x2; $x++) {
                for ($y = $y1; $y <= $y2; $y++) {
                    for ($z = $z1; $z <= $z2; $z++) {
                        $occupied["$x,$y,$z"] = $i;
                    }
                }
            }
        }

        // Calcular relaciones de soporte
        foreach ($this->data as $i => &$brick) {
            $x1 = min($brick['from'][0], $brick['to'][0]);
            $x2 = max($brick['from'][0], $brick['to'][0]);
            $y1 = min($brick['from'][1], $brick['to'][1]);
            $y2 = max($brick['from'][1], $brick['to'][1]);
            $z1 = min($brick['from'][2], $brick['to'][2]);

            $supports = [];

            for ($x = $x1; $x <= $x2; $x++) {
                for ($y = $y1; $y <= $y2; $y++) {
                    $belowKey = "$x,$y," . ($z1 - 1);
                    if (isset($occupied[$belowKey])) {
                        $supports[] = $occupied[$belowKey];
                    }
                }
            }

            // Quitar duplicados
            $supports = array_unique($supports);

            // Añadir a supportedBy
            $brick['supportedBy'] = $supports;

            // También actualizamos el campo supportTo de los ladrillos que lo sostienen
            foreach ($supports as $supportIndex) {
                $this->data[$supportIndex]['supportTo'][] = $i;
            }
        }
    }

    public function countRemovableBricks(): int
    {
        $removable = 0;

        foreach ($this->data as $i => $brick) {
            $canRemove = true;

            foreach ($brick['supportTo'] as $supportedIndex) {
                $supportedBrick = $this->data[$supportedIndex];

                // Si el ladrillo que sostenemos solo tiene 1 soporte (nosotros), no se puede quitar
                if (count($supportedBrick['supportedBy']) === 1) {
                    $canRemove = false;
                    break;
                }
            }

            if ($canRemove) {
                $removable++;
            }
        }

        return $removable;
    }

    public function countTotalFallingBricks(): int
    {
        $total = 0;
        $n = count($this->data);

        for ($removed = 0; $removed < $n; $removed++) {
            $fallen = [$removed => true]; // Marcamos como caído el inicial
            $queue = [$removed]; // Cola de los que han caído

            while (!empty($queue)) {
                $current = array_shift($queue);

                foreach ($this->data[$current]['supportTo'] as $supportedIndex) {
                    if (isset($fallen[$supportedIndex])) {
                        continue; // Ya cayó
                    }

                    // Si TODOS los soportes del ladrillo ya han caído...
                    $allSupportsGone = true;
                    foreach ($this->data[$supportedIndex]['supportedBy'] as $supportIndex) {
                        if (!isset($fallen[$supportIndex])) {
                            $allSupportsGone = false;
                            break;
                        }
                    }

                    if ($allSupportsGone) {
                        $fallen[$supportedIndex] = true;
                        $queue[] = $supportedIndex;
                    }
                }
            }

            // No contamos el original, solo los que cayeron como efecto
            $total += count($fallen) - 1;
        }

        return $total;
    }
}
