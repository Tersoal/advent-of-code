<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day14 extends DayBase
{
    private const int SEC = 100;
    private const int MAX_X = 101;
    private const int MAX_Y = 103;
    private const int TEST_MAX_X = 11;
    private const int TEST_MAX_Y = 7;
    protected const int TEST_1 = 12;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n", $data);

        $callback = fn(string $coordinate): int => (int)$coordinate;

        foreach ($data as $robot) {
            $dataParts = explode(' ', $robot);
            $this->data[] = [
                'p' => array_map($callback, explode(',', substr($dataParts[0], 2))),
                'v' => array_map($callback, explode(',', substr($dataParts[1], 2))),
            ];
        }

        //var_dump($this->data);
    }

    public function getResult(): array
    {
        $maxX = $this->test ? self::TEST_MAX_X : self::MAX_X;
        $maxY = $this->test ? self::TEST_MAX_Y : self::MAX_Y;

        return [
            $this->getSafetyFactor($maxX, $maxY),
            0
        ];
    }

    private function getSafetyFactor(int $maxX, int $maxY): int
    {
        $robots = [];

        foreach ($this->data as $robot) {
            $robots[] = $this->getNewPosition($robot, $maxX, $maxY);
        }

        //var_dump($robots);

        $quadrants = $this->getQuadrants($robots, $maxX, $maxY);

        $quadrantsProduct = 1;
        foreach ($quadrants as $key => $quadrant) {
            $quadrantsProduct = $quadrantsProduct * count($quadrant);

            echo 'Quadrant ' . $key . ': ' . count($quadrant) . PHP_EOL;
        }

        return $quadrantsProduct;
    }

    private function getNewPosition(array $robot, int $maxX, int $maxY): array
    {
        for ($i = 0; $i < self::SEC; $i++) {
            $robot['p'][0] += $robot['v'][0];

            if ($robot['p'][0] < 0) {
                $robot['p'][0] = $maxX + $robot['p'][0];
            } elseif ($robot['p'][0] > $maxX - 1) {
                $robot['p'][0] = $robot['p'][0] - $maxX;
            }

            $robot['p'][1] += $robot['v'][1];

            if ($robot['p'][1] < 0) {
                $robot['p'][1] = $maxY + $robot['p'][1];
            } elseif ($robot['p'][1] > $maxY - 1) {
                $robot['p'][1] = $robot['p'][1] - $maxY;
            }

            //var_dump($robot);
        }

        return $robot;
    }

    private function getQuadrants(array $robots, int $maxX, int $maxY): array
    {
        $quadrants = ['1' => [], '2' => [], '3' => [], '4' => []];

        foreach ($robots as $robot) {
            $middleX = ($maxX - 1) / 2;
            $middleY = ($maxY - 1) / 2;

            if ($robot['p'][0] < $middleX) {
                if ($robot['p'][1] < $middleY) {
                    $quadrants['1'][] = $robot;
                } elseif ($robot['p'][1] > $middleY) {
                    $quadrants['3'][] = $robot;
                }
            } elseif ($robot['p'][0] > $middleX) {
                if ($robot['p'][1] < $middleY) {
                    $quadrants['2'][] = $robot;
                } elseif ($robot['p'][1] > $middleY) {
                    $quadrants['4'][] = $robot;
                }
            }
        }

        return $quadrants;
    }
}
