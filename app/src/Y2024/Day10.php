<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day10 extends DayBase
{
    protected const int TEST_1 = 36;
    protected const int TEST_2 = 81;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath);
    }

    public function getResult(): array
    {
        return [$this->getScore(), $this->getRating()];
    }

    public function getScore(): int
    {
        $trailheads = $this->getTrailheads($this->data);
        $score = 0;

        foreach ($trailheads as $trailhead) {
            $score += $this->getTrailheadScore($trailhead);
        }

        return $score;
    }

    public function getRating(): int
    {
        $trailheads = $this->getTrailheads($this->data);
        $rating = 0;

        foreach ($trailheads as $trailhead) {
            $rating += $this->getTrailheadRating($trailhead);
        }

        return $rating;
    }

    public function getTrailheads(array $data): array
    {
        $trailheads = [];

        for ($y = 0; $y < count($data); $y++) {
            for ($x = 0; $x < count($data[$y]); $x++) {
                if ((int)$data[$y][$x] !== 0) {
                    continue;
                }

                $trailheads[] = [$y, $x];
            }
        }

        return $trailheads;
    }

    public function getTrails(array $trailStep, int $height, array $trail, array &$trails): void
    {
        if ($height === 9) {
            $trail[] = $trailStep;
            $trails[] = $trail;

            return;
        }

        $right = [$trailStep[0], $trailStep[1] + 1];
        $bottom = [$trailStep[0] + 1, $trailStep[1]];
        $left = [$trailStep[0], $trailStep[1] - 1];
        $top = [$trailStep[0] - 1, $trailStep[1]];
        $positions = [$right, $bottom, $left, $top];

        foreach ($positions as $position) {
            if (!array_key_exists($position[0], $this->data) ||
                !array_key_exists($position[1], $this->data[$position[0]]) ||
                (int)$this->data[$position[0]][$position[1]] !== $height + 1
            ) {
                continue;
            }

            $newTrail = $trail;
            $newTrail[] = $trailStep;
            $this->getTrails($position, $height + 1, $newTrail, $trails);
        }
    }

    public function getTrailheadScore(array $trailhead): int
    {
        $height = 0;
        $trails = [];

        $this->getTrails($trailhead, $height, [], $trails);

        //var_dump($trails);

//        $uniqueTrails =
//            array_unique(
//                array_map(
//                    fn($trail) => implode('|', array_map(fn($trailStep) => implode('-', $trailStep), $trail)
//                ), $trails)
//            );
//
//        var_dump($uniqueTrails);
        // Multiple trails that score same top should be considered as 1
        $trailsScores = [];
        foreach ($trails as $trail) {
            $trailScore = implode('-', $trail[9]);
            if (!in_array($trailScore, $trailsScores)) {
                $trailsScores[] = $trailScore;
            }
        }

        return count($trailsScores);
    }

    public function getTrailheadRating(array $trailhead): int
    {
        $height = 0;
        $trails = [];

        $this->getTrails($trailhead, $height, [], $trails);

        return count($trails);
    }
}
