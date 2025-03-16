<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day08 extends DayBase
{
    protected const int TEST_1 = 14;
    protected const int TEST_2 = 9;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        return [$this->getAntinodeCount(), $this->getCrossedMasCount()];
    }

    public function getAntinodeCount(): int
    {
        $antennas = $this->getAntennas($this->data);
        $antinodes = [];

        foreach ($antennas as $index => $antenna) {
            $antinodes = array_merge($antinodes, $this->getAntinodes($antennas, $antenna, $index));
        }

//        var_dump(array_unique($antinodes));

        return count(array_unique($antinodes));
    }

    public function getCrossedMasCount(): int
    {
        $count = 0;



        return $count;
    }

    public function getAntennaType(string $antenna): ?int
    {
        if (ctype_upper($antenna)) {
            return 1;
        }

        if (ctype_lower($antenna)) {
            return 2;
        }

        if (ctype_digit($antenna) && $antenna !== self::FREE) {
            return 3;
        }

        return null;
    }

    public function getAntennas(array $data): array
    {
        $antennas = [];

        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] === self::FREE) {
                    continue;
                }

                $antennas[] = [$y, $x, $data[$y][$x], $this->getAntennaType($data[$y][$x])];
            }
        }

        return $antennas;
    }

    public function getAntinodes(array $antennas, array $currentAntenna, int $currentIndex): array
    {
        $antinodes = [];

        foreach ($antennas as $index => $antenna) {
            if ($index === $currentIndex || $antenna[3] !== $currentAntenna[3]) {
                continue;
            }

            $diffY = $antenna[0] - $currentAntenna[0];
            $diffX = $antenna[1] - $currentAntenna[1];

            $antinode = [$antenna[0] + $diffY, $antenna[1] + $diffX];
            if (!array_key_exists($antinode[0], $this->data) || !array_key_exists($antinode[1], $this->data[$antenna[0]])) {
                continue;
            }

            if ($this->getAntennaType($this->data[$antinode[0]][$antinode[1]]) === $antenna[3]) {
                continue;
            }

            $antinodes[] = implode('-', $antinode);

            if ($currentIndex === 5) {
                echo 'Antenna #' . $currentIndex . "\n";
                echo "============================\n";
                var_dump($currentAntenna);
                var_dump($antenna);
                var_dump($antinode);
            }
        }

        return $antinodes;
    }
}
