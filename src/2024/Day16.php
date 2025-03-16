<?php

namespace App\Y2024;

use App\Model\DayBase;
use Exception;

class Day16 extends DayBase
{
    private const int TURN_FACTOR = 1000;
    private const int MOVE_FACTOR = 1;
    protected const int TEST_1 = 11048;
    protected const int TEST_2 = 64;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        $initialPosition = $this->getInitialPosition($this->data);
        $score = null;
        $positionsVisited = [];
        $bestPaths = [];

        $this->getScore($initialPosition, [], 'R', ['moves' => [], 'turns' => [], 'score' => 0], $score, $positionsVisited, $bestPaths);

        $map = $this->data;
        $tiles = [];
        foreach ($bestPaths as $path) {
            foreach ($path['moves'] as $tile) {
                $map[$tile[0]][$tile[1]] = 'O';

                if (in_array($tile, $tiles)) {
                    continue;
                }

                $tiles[] = $tile;
            }
        }

        //var_dump($positionsVisited);

        $this->printMap($map);

        echo "# Paths = " . count($bestPaths) . "\n";
        echo "# Unique Moves Total = " . count($tiles) . "\n";

        return [$score, count($tiles) + 1]; // +1 because we must add E tile
    }

    public function getScore(array $position, array $prevPosition, $direction, array $path, ?int &$score, array &$positionsVisited, array &$bestPaths): void
    {
        if ($this->data[$position[0]][$position[1]] === 'E') {
//            echo "Path with Score = " . $path['score'] . PHP_EOL;
            if ($score === null) {
                $bestPaths = [$path];
                $score = $path['score'];
            } else {
                if ($path['score'] < $score) {
                    $bestPaths = [$path];
                    $score = $path['score'];
                } elseif ($path['score'] === $score) {
                    $bestPaths[] = $path;
                }
            }

//            var_dump($path);

            return;
        }

        if (
            array_key_exists($position[0], $positionsVisited) &&
            array_key_exists($position[1], $positionsVisited[$position[0]]) &&
            $positionsVisited[$position[0]][$position[1]] < $path['score']
        ) {
            return;
        }

        $path['moves'][] = $position;
        $path['score'] += self::MOVE_FACTOR;

        $positionsVisited[$position[0]][$position[1]] = $path['score'];

        $right = [$position[0], $position[1] + 1];
        $bottom = [$position[0] + 1, $position[1]];
        $left = [$position[0], $position[1] - 1];
        $top = [$position[0] - 1, $position[1]];
        $newPositions = ['R' => $right, 'B' => $bottom, 'L' => $left, 'T' => $top];

//        echo "Position = " . json_encode($position) . PHP_EOL;

        foreach ($newPositions as $key => $newPosition) {
//            echo "New Position in direction $key = " . json_encode($newPosition) . PHP_EOL;

            if ($this->data[$newPosition[0]][$newPosition[1]] === self::WALL) {
                continue;
            }

            if ($newPosition === $prevPosition) {
                continue;
            }

            if (in_array($newPosition, $path['moves'])) {
                continue;
            }

            if ($score !== null && $path['score'] > $score) {
                continue;
            }

            $newPath = $path;

            if ($key !== $direction) {
                $newPath['turns'][] = $position;
                $newPath['score'] += self::TURN_FACTOR;
            }

            $this->getScore($newPosition, $position, $key, $newPath, $score, $positionsVisited, $bestPaths);
        }
    }

    /**
     * @throws Exception
     */
    public function getInitialPosition(array $data): array
    {
        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ($data[$y][$x] !== 'S') {
                    continue;
                }

                return [$y, $x];
            }
        }

        throw new Exception("Invalid position");
    }
}
