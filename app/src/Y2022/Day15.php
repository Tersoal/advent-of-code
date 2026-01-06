<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day15 extends DayBase
{
    protected const int TEST_1 = 26;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        foreach ($this->data as $key => $value) {
            $parts = explode(':', $value);

            $sensorParts = explode(',', $parts[0]);
            $sensorX = (int)explode('=', $sensorParts[0])[1];
            $sensorY = (int)explode('=', $sensorParts[1])[1];

            $beaconParts = explode(',', $parts[1]);
            $beaconX = (int)explode('=', $beaconParts[0])[1];
            $beaconY = (int)explode('=', $beaconParts[1])[1];

            $this->data[$key] = [
                'sensor' => [$sensorX, $sensorY],
                'beacon' => [$beaconX, $beaconY],
            ];
        }

        if ($this->test) {
            print_r($this->data);
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
        $rowToCheck = $this->test ? 10 : 2000000;
        $ranges = $this->getSignalRanges($rowToCheck);

        return $this->getSignalPositionsCount($ranges);
    }

    private function getPart2(): int
    {
        return 0;
    }

    private function getSignalRanges(int $rowToCheck): array
    {
        $ranges = [];

        foreach($this->data as $datum) {
            $range = $this->getSignalRange($rowToCheck, $datum['sensor'], $datum['beacon']);

            if (!empty($range)) {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }

    private function getSignalRange(int $rowToCheck, array $sensor, array $beacon): array
    {
        // Manhattan distance
        $distance = abs($sensor[0] - $beacon[0]) + abs($sensor[1] - $beacon[1]);

        $signalMinY = $sensor[1] - $distance;
        $signalMaxY = $sensor[1] + $distance;

        // Signal do not touch row to check
        if ($signalMaxY < $rowToCheck || $signalMinY > $rowToCheck) {
            return [];
        }

        // We get min distance between row to check and top and bottom.
        // Sensor x + and - this distance are the row range covered by signal.
        $distanceToRowToCheck = min(abs($signalMinY - $rowToCheck), abs($signalMaxY - $rowToCheck));

        return [($sensor[0] - $distanceToRowToCheck), ($sensor[0] + $distanceToRowToCheck)];
    }

    private function getSignalPositionsCount(array $ranges): int
    {
        $count = 0;

        usort($ranges, fn ($a, $b) => $a[0] <=> $b[0]);

        foreach($ranges as $i => $range) {
            if ($i === 0) {
                $count += $range[1] - $range[0];

                continue;
            }

            if ($range[0] > $ranges[$i - 1][1]) {
                $count += $range[1] - $range[0];

                continue;
            }

            $count += $range[1] - $ranges[$i - 1][1];
        }

        return $count;
    }
}
