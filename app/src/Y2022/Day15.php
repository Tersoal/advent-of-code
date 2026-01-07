<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day15 extends DayBase
{
    protected const int TEST_1 = 26;
    protected const int TEST_2 = 56000011;

    protected const int ROW_TO_CHECK_1 = 2000000;
    protected const int MIN_LIMIT = 0;
    protected const int MAX_LIMIT = 4000000;

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
        echo '****************************' . PHP_EOL;
        echo 'Part 1' . PHP_EOL;
        echo '****************************' . PHP_EOL;

        $rowToCheck = $this->test ? 10 : self::ROW_TO_CHECK_1;

        $ranges = $this->getSignalRanges($rowToCheck);

        return $this->getSignalPositionsCount($ranges, $rowToCheck);
    }

    private function getPart2(): int
    {
        echo '****************************' . PHP_EOL;
        echo 'Part 2' . PHP_EOL;
        echo '****************************' . PHP_EOL;

        $min = $this->test ? -2 : self::MIN_LIMIT;
        $max = $this->test ? 20 : self::MAX_LIMIT;

        $beacon = $this->getBeaconPosition($min, $max);

        return ($beacon[0] * self::MAX_LIMIT) + $beacon[1];
    }

    private function getBeaconPosition(int $min, int $max): array
    {
        $beaconX = 0;
        $beaconY = 0;

        for ($i = $min; $i <= $max; $i++) {
            if ($i % 100000 === 0) {
                echo 'Beacon Row: ' . $i . PHP_EOL;
            }

            $ranges = $this->getSignalRanges($i);
            $uniqueRanges = $this->getUniqueRanges($ranges);

            // Count total positions inside min and max
            $count = 0;

            foreach ($uniqueRanges as $range) {
                if ($range[1] < $min || $range[0] > $max) {
                    continue;
                }

                if ($range[0] <= $min && $range[1] >= $max) {
                    $count += $max - $min + 1;

                    continue;
                }

                if ($range[0] < $min && $range[1] <= $max) {
                    $count += $range[1] - $min + 1;

                    continue;
                }

                if ($range[0] >= $min && $range[0] < $max && $range[1] >= $max) {
                    $count += $max - $range[0] + 1;

                    continue;
                }

                $count += $range[1] - $range[0] + 1;
            }

            $outBeacons = $this->getRowBeacons($uniqueRanges, $i, false);
            foreach ($outBeacons as $outBeacon) {
                if ($outBeacon[0] >= $min && $outBeacon[0] <= $max) {
                    $count++;
                }
            }

            if ($count < ($max - $min + 1)) {
                $beaconY = $i;

                for ($u = 0; $u < count($uniqueRanges); $u++) {
                    if ($u === 0) {
                        continue;
                    }

                    if ($uniqueRanges[$u][0] - $uniqueRanges[$u - 1][1] > 1 && !in_array([$uniqueRanges[$u][0] - 1], array_column($outBeacons, 0))) {
                        $beaconX = $uniqueRanges[$u][0] - 1;
                    }
                }

                echo 'Beacon is in ' . $beaconX . ',' . $beaconY . PHP_EOL;
                echo 'Count: ' . $count . PHP_EOL;
                echo PHP_EOL;

                break;
            }
        }

        return [$beaconX, $beaconY];

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

        // Signal does not touch row to check
        if ($signalMaxY < $rowToCheck || $signalMinY > $rowToCheck) {
            return [];
        }

        // We get min distance between row to check and top and bottom.
        $distanceToRowToCheck = min(abs($signalMinY - $rowToCheck), abs($signalMaxY - $rowToCheck));

        // Sensor x + and - this distance are the row range covered by signal.
        return [($sensor[0] - $distanceToRowToCheck), ($sensor[0] + $distanceToRowToCheck)];
    }

    private function getSignalPositionsCount(array $ranges, int $rowToCheck): int
    {
        if (empty($ranges)) {
            return 0;
        }

        $uniqueRanges = $this->getUniqueRanges($ranges);

        // Count total positions
        $count = 0;
        foreach ($uniqueRanges as $range) {
            $count += $range[1] - $range[0] + 1;
        }

        // Remove each beacon inside ranges
        $deletedBeacons = $this->getRowBeacons($uniqueRanges, $rowToCheck, true);

        return $count - count($deletedBeacons);
    }

    private function getUniqueRanges(array $ranges): array
    {
        if (empty($ranges)) {
            return [];
        }

        usort($ranges, function ($a, $b) {
            $return = $a[0] <=> $b[0];
            return $return === 0 ? $a[1] <=> $b[1] : $return;
        });

        if ($this->test) {
            print_r($ranges);
            echo PHP_EOL;
        }

        $uniqueRanges = [];

        for ($i = 0; $i < count($ranges); $i++) {
            if (empty($uniqueRanges)) {
                $uniqueRanges[] = $ranges[$i];
                continue;
            }

            $newRanges = [$ranges[$i]];

            for ($u = 0; $u < count($uniqueRanges); $u++) {
                for ($j = 0; $j < count($newRanges); $j++) {
                    if ($newRanges[$j][1] < $uniqueRanges[$u][0] || $newRanges[$j][0] > $uniqueRanges[$u][1]) {
                        continue;
                    }

                    if ($newRanges[$j][0] >= $uniqueRanges[$u][0] && $newRanges[$j][1] <= $uniqueRanges[$u][1]) {
                        unset($newRanges[$j]);

                        continue;
                    }

                    if ($newRanges[$j][0] < $uniqueRanges[$u][0] && $newRanges[$j][1] > $uniqueRanges[$u][1]) {
                        $newRanges = [
                            [$newRanges[$j][0], $uniqueRanges[$u][0] - 1],
                            [$uniqueRanges[$u][1] + 1, $newRanges[$j][1]],
                        ];

                        continue;
                    }

                    if ($newRanges[$j][0] < $uniqueRanges[$u][0] && $newRanges[$j][1] <= $uniqueRanges[$u][1]) {
                        $newRanges = [[$newRanges[$j][0], $uniqueRanges[$u][0] - 1]];

                        continue;
                    }

                    if ($newRanges[$j][0] >= $uniqueRanges[$u][0] && $newRanges[$j][0] <= $uniqueRanges[$u][1] && $newRanges[$j][1] > $uniqueRanges[$u][1]) {
                        $newRanges = [[$uniqueRanges[$u][1] + 1, $newRanges[$j][1]]];

                        continue;
                    }
                }
            }

            $uniqueRanges = array_merge($uniqueRanges, $newRanges);
        }

        if ($this->test) {
            print_r($uniqueRanges);
            echo PHP_EOL;
        }

        return $uniqueRanges;
    }

    private function getRowBeacons(array $uniqueRanges, int $rowToCheck, bool $inside): array
    {
        $beacons = [];

        foreach ($this->data as $datum) {
            if ($datum['beacon'][1] !== $rowToCheck) {
                continue;
            }

            if (in_array($datum['beacon'], $beacons)) {
                continue;
            }

            $beaconIsInside = false;

            foreach ($uniqueRanges as $range) {
                if ($inside) {
                    if ($datum['beacon'][0] < $range[0] || $datum['beacon'][0] > $range[1]) {
                        continue;
                    }

                    $beacons[] = $datum['beacon'];

                    break;
                } else {
                    if ($datum['beacon'][0] >= $range[0] && $datum['beacon'][0] <= $range[1]) {
                        $beaconIsInside = true;

                        break;
                    }
                }
            }

            if (!$inside && !$beaconIsInside) {
                $beacons[] = $datum['beacon'];
            }
        }

        return $beacons;
    }
}
