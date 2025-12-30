<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day07 extends DayBase
{
    protected const int TEST_1 = 3749;
    protected const int TEST_2 = 11387;

    public array $operators = ['+', '*'];
    public array $operatorsWithConcat = ['+', '*', '||'];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        $data = $this->data;
        $this->data = [];

        foreach ($data as $line) {
            $parts = explode(':', $line);
            $this->data[] = ['result' => (int)$parts[0], 'values' => explode(' ', trim($parts[1]))];
        }
    }

    public function getResult(): array
    {
        return [$this->getCalibration(false), $this->getCalibration(true)];
    }

    /**
     * @throws \Exception
     */
    public function getCalibration(bool $withConcatenation = false): int
    {
//        echo "CALIBRATION\n";
//        echo "============================\n";

        $calibration = 0;

        foreach ($this->data as $equation) {
            if (!$this->equationIsOk($equation, $withConcatenation)) {
                continue;
            }

            $calibration += $equation['result'];
        }

        return $calibration;
    }

    function combinar($array, $operadores, &$combinaciones, $index = 0, $expr = '') {
        if ($index == count($array)) {
            // Cuando hemos recorrido todos los números, agregamos la combinación
            $combinaciones[] = $expr;
            return;
        }

        // Si no estamos en el primer número, agregar un operador antes del número
        if ($index > 0) {
            foreach ($operadores as $operador) {
                $this->combinar($array, $operadores, $combinaciones, $index + 1, '(' . $expr . $operador . $array[$index] . ')');
            }
        } else {
            // Si es el primer número, no agregamos operador antes de él
            $this->combinar($array, $operadores, $combinaciones, $index + 1, $expr . $array[$index]);
        }
    }

    // Función para generar todas las combinaciones con las operaciones
    function generarCombinaciones($array, $withConcatenation) {
        $combinaciones = [];
        $operators = $withConcatenation ? $this->operatorsWithConcat : $this->operators;

        $this->combinar($array, $operators, $combinaciones);

        return $combinaciones;
    }

    /**
     * @throws \Exception
     */
    public function equationIsOk(array $equation, bool $withConcatenation): bool
    {
        $combinations = $this->generarCombinaciones($equation['values'], $withConcatenation);

        foreach ($combinations as $combination) {
            if (str_contains($combination, '||')) {
                $parts = explode('||', $combination);
                $combination = '';

                foreach ($parts as $part) {
                    $numberOfOpen = substr_count($part, '(');
                    $numberOfClose = substr_count($part, ')');

                    // We remove leading open parenthesis
                    if ($numberOfOpen > $numberOfClose) {
                        for ($i = 0; $i < ($numberOfOpen - $numberOfClose); $i++) {
                            $part = substr($part, 1);
                        }
                    }

                    $combination .= $part;

                    // We add needed parenthesis again
                    $numberOfOpen = substr_count($combination, '(');
                    $numberOfClose = substr_count($combination, ')');

                    if ($numberOfOpen > $numberOfClose) {
                        for ($i = 0; $i < ($numberOfOpen - $numberOfClose); $i++) {
                            $combination .= ')';
                        }
                    } elseif ($numberOfOpen < $numberOfClose) {
                        for ($i = 0; $i < ($numberOfClose - $numberOfOpen); $i++) {
                            $combination = '(' . $combination;
                        }
                    }

                    $combination = eval("return $combination;");
                }
            }

//            var_dump($combination);
            $result = eval("return $combination;");
//            var_dump($combination . ' = ' . $result);

            if ($result === $equation['result']) {
                return true;
            }
        }

        return false;
    }
}
