<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day05 extends DayBase
{
    protected const int TEST_1 = 35;
    protected const int TEST_2 = 46;

    protected array $seeds = [];
    protected array $maps = [];
    protected array $steps = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        $currentMap = null;

        foreach ($this->data as $key => $line) {
            if ($key === 0) {
                $seeds = explode(' ', trim(str_replace('seeds: ', "", $line)));
                $this->seeds = array_map(fn ($s) => (int)$s, $seeds);

                continue;
            }

            if (empty($line)) {
                $currentMap = null;

                continue;
            }

            if (str_ends_with($line, 'map:')) {
                $currentMap = trim(str_replace('map:', "", $line));
                $this->steps[] = $currentMap;
                $this->maps[$currentMap] = [];

                continue;
            }

            [$destination, $source, $length] = explode(' ', $line);
            $this->maps[$currentMap][] = [
                'sourceFrom' => (int)$source,
                'sourceTo' => (int)$source + (int)$length - 1,
                'destinationFrom' => (int)$destination,
                'destinationTo' => (int)$destination + (int)$length - 1,
            ];
        }

//        print_r($this->seeds);
//        print_r($this->maps);
//        print_r($this->steps);
    }

    public function getResult(): array
    {
        return [$this->getLowestLocation(), $this->getLowestLocation2()];
    }

    private function getLowestLocation(): int
    {
        $locations = [];

        foreach ($this->seeds as $seed) {
            $destination = $seed;

            foreach ($this->steps as $step) {
                $destination = $this->getStepDestination($destination, $step);
            }

            $locations[] = $destination;
        }

        return min($locations);
    }

    private function getStepDestination(int $location, string $step): int
    {
        foreach ($this->maps[$step] as $map) {
            if ($location >= $map['sourceFrom'] && $location <= $map['sourceTo']) {
                return $map['destinationFrom'] + $location - $map['sourceFrom'];
            }
        }

        return $location;
    }

    private function getLowestLocation2(): int
    {
        $locationRanges = [];
        $seedChunks = array_chunk($this->seeds, 2);

        foreach ($seedChunks as $seedChunk) {
            $from = $seedChunk[0];
            $to = $seedChunk[0] + $seedChunk[1] - 1;

            $locationRanges = array_merge($locationRanges, $this->getStepDestinationRange([[$from, $to]], 0));
        }

        //print_r($locationRanges);

        return min(array_column($locationRanges, 0));
    }

    private function getStepDestinationRange(array $ranges, int $step): array
    {
        if ($step === 0) {
            echo PHP_EOL;
            echo '======================================' . PHP_EOL;
            echo 'Step ' . $step . PHP_EOL;
            echo '======================================' . PHP_EOL;

            print_r($ranges);
        }

        if (!array_key_exists($step, $this->steps)) {
            return $ranges;
        }

        $newRanges = [];
        $stepName = $this->steps[$step];

        foreach ($ranges as $range) {
            $newRange = [];

            foreach ($this->maps[$stepName] as $map) {
                if ($step === 0) {
                    echo 'MAP *************' . PHP_EOL;

                    print_r($map);
                }

                if ($range[0] >= $map['sourceFrom'] && $range[1] <= $map['sourceTo']) {
                    $newRange[] = [
                        ($map['destinationFrom'] + $range[0] - $map['sourceFrom']),
                        ($map['destinationTo'] + $range[1] - $map['sourceTo'])
                    ];
                } elseif ($range[0] < $map['sourceFrom'] && $range[1] > $map['sourceTo']) {
                    $newRange[] = [$range[0], ($map['sourceFrom'] - 1)];
                    $newRange[] = [$map['destinationFrom'], $map['destinationTo']];
                    $newRange[] = [($map['sourceTo'] + 1), $range[1]];
                } elseif ($range[0] < $map['sourceFrom'] && $range[1] >= $map['sourceFrom'] && $range[1] <= $map['sourceTo']) {
                    $newRange[] = [$range[0], ($map['sourceFrom'] - 1)];
                    $newRange[] = [
                        $map['destinationFrom'],
                        ($map['destinationFrom'] + $range[1] - $map['sourceFrom'])
                    ];
                } elseif ($range[0] >= $map['sourceFrom'] && $range[0] <= $map['sourceTo'] && $range[1] > $map['sourceTo']) {
                    $newRange[] = [
                        ($map['destinationFrom'] + $range[0] - $map['sourceFrom']),
                        $map['destinationTo'],
                    ];
                    $newRange[] = [($map['sourceTo'] + 1), $range[1]];
                }
            }

            if (empty($newRange)) {
                $newRange[] = [$range[0], $range[1]];
            }

            if ($step === 0) {
                echo PHP_EOL;

                print_r($newRange);
            }

            $newRanges = array_merge($newRanges, $newRange);
        }

        return $this->getStepDestinationRange($newRanges, $step + 1);
    }
}
