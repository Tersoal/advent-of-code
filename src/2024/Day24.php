<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day24 extends DayBase
{
    protected const int TEST_1 = 2024;
    protected const int TEST_2 = 0;

    private array $inputs = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n\r\n", $data);

        $parts = explode("\r\n", $data[0]);
        foreach ($parts as $part) {
            $input = explode(": ", $part);
            $this->inputs[$input[0]] = $input[1];
        }

        $parts = explode("\r\n", $data[1]);
        foreach ($parts as $part) {
            $input = explode(" -> ", $part);
            $operation = explode(" ", $input[0]);
            $this->data[$input[1]] = [
                'operands' => [$operation[0], $operation[2]],
                'operator' => $operation[1],
            ];
        }

//        var_dump($this->inputs);
//        var_dump($this->data);
    }

    public function getResult(): array
    {
        return [
            $this->getNumber($this->inputs, $this->data),
            0
        ];
    }

    private function getNumber(array $inputs, array $gates): int
    {
        while (!empty($gates)) {
            $availableGates = array_filter($gates, function ($gate) use ($inputs) {
                return in_array($gate['operands'][0], array_keys($inputs)) && in_array($gate['operands'][1], array_keys($inputs));
            });

            foreach ($availableGates as $gate => $gateData) {
                $inputs[$gate] = $this->getOutput($inputs, $gateData);
                unset($gates[$gate]);
            }
        }

        $finalGates = array_filter($inputs, function ($inputKey) {
            return str_starts_with($inputKey, 'z');
        }, ARRAY_FILTER_USE_KEY);

        krsort($finalGates);

        return bindec(implode("", $finalGates));
    }

    /**
     * @throws \Exception
     */
    private function getOutput(array $inputs, array $gateData): string
    {
        if (!array_key_exists($gateData['operands'][0], $inputs)) {
            throw new \Exception("Operand " . $gateData['operands'][0] . " does not exist.");
        }

        if (!array_key_exists($gateData['operands'][1], $inputs)) {
            throw new \Exception("Operand " . $gateData['operands'][0] . " does not exist.");
        }

        $value1 = $inputs[$gateData['operands'][0]];
        $value2 = $inputs[$gateData['operands'][1]];

        return match ($gateData['operator']) {
            'AND' => ($value1 === '1' && $value2 === '1') ? '1' : '0',
            'OR' => ($value1 === '1' || $value2 === '1') ? '1' : '0',
            'XOR' => ($value1 !== $value2) ? '1' : '0',
            default => throw new \Exception("Operator " . $gateData['operator'] . " does not exist."),
        };
    }
}
