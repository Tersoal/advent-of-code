<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day07 extends DayBase
{
    protected const int TEST_1 = 21;
    protected const int TEST_2 = 40;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

        $this->printMap($this->data);
        echo PHP_EOL;
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
        [$splitCounter, $timelinesCounter] = $this->getBeamsSplitData(false);

        return $splitCounter;
    }

    private function getPart2(): int
    {
        [$splitCounter, $timelinesCounter] = $this->getBeamsSplitData(true);

        return $timelinesCounter;
    }

    private function getBeamsSplitData(bool $sumAllInRow): array
    {
        $initialPosition = $this->getInitialPosition($this->data);
        $initialPositionKey = $initialPosition[0] . '-' . $initialPosition[1];

        $beams[$initialPositionKey] = 1;
        $splitCounter = 0;

        foreach ($this->data as $row => $columns) {
            $beams = $this->getBeamsInRow($row, $beams, $sumAllInRow, $splitCounter);

            if ($this->test) {
                echo 'Beams in row ' . $row . ': ' . implode(',', array_keys($beams)) . PHP_EOL;

                $this->printNewMap($this->data, $beams);
            }
        }

        return [$splitCounter, array_sum($beams)];
    }

    private function getBeamsInRow(int $row, array $beams, bool $sumAllInRow, int &$splitCounter): array
    {
        if ($row === 0) {
            return $beams;
        }

        if (!array_key_exists($row, $this->data)) {
            return $beams;
        }

        $newBeams = [];

        foreach ($beams as $beam => $counter) {
            [$y, $x] = explode('-', $beam);
            $x = intval($x);

            if ($this->data[$row][$x] !== self::DIRECTION_ARROW_TOP) {
                $newPositionKey = $row . '-' . $x;

                if (array_key_exists($newPositionKey, $newBeams)) {
                    $newBeams[$newPositionKey] += $counter;
                } else {
                    $newBeams[$newPositionKey] = $counter;
                }

                continue;
            }

            $splitCounter++;

            $newPositionKey = $row . '-' . ($x + 1);

            if (array_key_exists($newPositionKey, $newBeams)) {
                $newBeams[$newPositionKey] += $counter;
            } else {
                $newBeams[$newPositionKey] = $counter;
            }

            if ($sumAllInRow || !isset($this->data[$row][$x - 2]) || $this->data[$row][$x - 2] !== self::DIRECTION_ARROW_TOP) {
                $newPositionKey = $row . '-' . ($x - 1);

                if (array_key_exists($newPositionKey, $newBeams)) {
                    $newBeams[$newPositionKey] += $counter;
                } else {
                    $newBeams[$newPositionKey] = $counter;
                }
            }
        }

        return $newBeams;
    }

    private function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] !== self::START_POSITION) {
                    continue;
                }

                return [$y, $x];
            }
        }

        throw new \Exception("Invalid position");
    }

    private function printNewMap(array $map, array $beams): void
    {
        foreach ($beams as $beam => $counter) {
            [$y, $x] = explode('-', $beam);
            $map[$y][$x] = $counter;
        }

        $this->printMap($map);
    }
}
