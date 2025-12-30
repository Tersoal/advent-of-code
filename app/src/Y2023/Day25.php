<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day25 extends DayBase
{
    protected const int TEST_1 = 0;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\n", $data);

        foreach ($data as $line) {
            $parts = explode(": ", $line);
            $this->data[$parts[0]] = explode(' ', trim($parts[1]));
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
        $nonConnected = [];

        foreach ($this->data as $component => $connections) {
            foreach ($connections as $connection) {
                foreach ($this->data[$component] as $sibling) {
                    if ($sibling === $connection) {
                        continue;
                    }

                    if (isset($nonConnected[$sibling . ':' . $connection]) || isset($nonConnected[$connection . ':' . $sibling])) {
                        continue;
                    }

                    if (!$this->componentsAreConnected($sibling, $connection, $component)) {
                        echo $connection . ' and ' . $sibling . ' are not connected' . PHP_EOL;

                        $nonConnected[$sibling . ':' . $connection] = true;
                    }
                }
            }
        }




        return 0;
    }

    private function componentsAreConnected(string $a, string $b, string $currentComponent): bool
    {
        if (
            (isset($this->data[$b]) && in_array($a, $this->data[$b])) ||
            (isset($this->data[$a]) && in_array($b, $this->data[$a]))
        ) {
            return true;
        }

        foreach ($this->data as $component => $connections) {
            if ($component === $currentComponent) {
                continue;
            }

            if (in_array($a, $connections) && in_array($b, $connections)) {
                return true;
            }
        }

        return false;
    }

    private function getPart2(): int
    {
        return 0;
    }
}
