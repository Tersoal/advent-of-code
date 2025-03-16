<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day21 extends DayBase
{
    protected const int TEST_1 = 126384;
    protected const int TEST_2 = 0;

    private array $numericKeyPad = [];
    private array $directionalKeyPad = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");
        $this->numericKeyPad = [
            '9' => [-3, 0],
            '8' => [-3, -1],
            '7' => [-3, -2],
            '6' => [-2, 0],
            '5' => [-2, -1],
            '4' => [-2, -2],
            '3' => [-1, 0],
            '2' => [-1, -1],
            '1' => [-1, -2],
            '0' => [0, -1],
            'A' => [0, 0],
        ];
        $this->directionalKeyPad = [
            '<' => [1, -2],
            'v' => [1, -1],
            '>' => [1, 0],
            '^' => [0, -1],
            'A' => [0, 0],
        ];
    }

    public function getResult(): array
    {
        return [$this->getComplexity(), 0];
    }

    private function getComplexity(): int
    {
        $complexity = 0;

        foreach ($this->data as $code) {
            $sequence = $this->getSequence($code);
            $numberCode = (int)substr($code, 0, -1);
            $complexity += count($sequence[3]) * $numberCode;

            echo "Complexity = " . count($sequence[3]) . " * $numberCode" . PHP_EOL;
        }

        return $complexity;
    }

    private function getSequence(string $code): array
    {
        $padPosition[0] = $this->numericKeyPad['A'];
        $padPosition[1] = [0, 0];
        $padPosition[2] = [0, 0];
        $padPosition[3] = [0, 0];
        $sequence[0] = str_split($code);

        echo "=======================" . PHP_EOL;
        echo "Sequence for code $code" . PHP_EOL;
        echo "=======================" . PHP_EOL;

        $this->getPadSequence($sequence, $padPosition, 1);

        return $sequence;
    }

    private function getPadSequence(array &$sequence, array $padPosition, int $level): void
    {
        if ($level > 3) {
            return;
        }

        foreach ($sequence[$level - 1] as $code) {
            $nextPosition = $level === 1 ? $this->numericKeyPad[$code] : $this->directionalKeyPad[$code];

            $y = $nextPosition[0] - $padPosition[$level - 1][0];
            $yMove = $y < 0 ? -1 : ($y > 0 ? 1 : 0);
            $x = $nextPosition[1] - $padPosition[$level - 1][1];
            $xMove = $x < 0 ? -1 : ($x > 0 ? 1 : 0);

            if ($padPosition[$level - 1][0] === 0) {
                for ($i = 0; $i < abs($y); $i++) {
                    $sequence[$level][] = $this->getDirectionChar($yMove, 0);
                }
                for ($i = 0; $i < abs($x); $i++) {
                    $sequence[$level][] = $this->getDirectionChar(0, $xMove);
                }
            } else {
                for ($i = 0; $i < abs($x); $i++) {
                    $sequence[$level][] = $this->getDirectionChar(0, $xMove);
                }
                for ($i = 0; $i < abs($y); $i++) {
                    $sequence[$level][] = $this->getDirectionChar($yMove, 0);
                }
            }

            $sequence[$level][] = $this->getDirectionChar(0, 0);

            $padPosition[$level - 1] = $nextPosition;
        }

        echo "Sequence level $level = " . implode('', $sequence[$level]) . PHP_EOL;

        $this->getPadSequence($sequence, $padPosition, $level + 1);
    }

    private function getDirectionChar(int $y, int $x): string
    {
        if ($y === -1 && $x === 0) {
            return '^';
        }
        if ($y === 0 && $x === -1) {
            return '<';
        }
        if ($y === 0 && $x === 1) {
            return '>';
        }
        if ($y === 1 && $x === 0) {
            return 'v';
        }

        return 'A';
    }
}
