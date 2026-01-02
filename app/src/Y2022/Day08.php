<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day08 extends DayBase
{
    protected const int TEST_1 = 21;
    protected const int TEST_2 = 8;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArrayMap($filePath, "\n");

        if ($this->test) {
            $this->printMap($this->data);
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
        $treesMap = $this->getTreesStatusVisibleFromEdges();
        $visibleTrees = 0;

        foreach ($treesMap as $treeRow) {
            $visibleTrees += array_sum($treeRow);
        }

        return $visibleTrees;
    }

    private function getPart2(): int
    {
        $treesMap = $this->getTreesVisibleFromEachTreeToEdge();
        $maxVisibleTrees = 0;

        foreach ($treesMap as $treeRow) {
            $maxVisibleTrees = max($maxVisibleTrees, max($treeRow));
        }

        return $maxVisibleTrees;
    }

    private function getTreesStatusVisibleFromEdges(): array
    {
        $treeRow = array_fill(0, count($this->data[0]), 0);
        $trees = array_fill(0, count($this->data), $treeRow);

        // LEFT SIDE
        for ($y = 0; $y < count($this->data); $y++) {
            $treeMin = -1;

            for ($x = 0; $x < count($this->data[$y]); $x++) {
                $tree = (int)$this->data[$y][$x];

                if ($tree > $treeMin) {
                    $trees[$y][$x] = 1;
                    $treeMin = $tree;
                }
            }
        }

        // RIGHT SIDE
        for ($y = 0; $y < count($this->data); $y++) {
            $treeMin = -1;

            for ($x = count($this->data[$y]) - 1; $x >= 0; $x--) {
                $tree = (int)$this->data[$y][$x];

                if ($tree > $treeMin) {
                    $trees[$y][$x] = 1;
                    $treeMin = $tree;
                }
            }
        }

        // UPSIDE
        for ($x = 0; $x < count($this->data[0]); $x++) {
            $treeMin = -1;

            for ($y = 0; $y < count($this->data); $y++) {
                $tree = (int)$this->data[$y][$x];

                if ($tree > $treeMin) {
                    $trees[$y][$x] = 1;
                    $treeMin = $tree;
                }
            }
        }

        // DOWNSIDE
        for ($x = 0; $x < count($this->data[0]); $x++) {
            $treeMin = -1;

            for ($y = count($this->data) - 1; $y >= 0; $y--) {
                $tree = (int)$this->data[$y][$x];

                if ($tree > $treeMin) {
                    $trees[$y][$x] = 1;
                    $treeMin = $tree;
                }
            }
        }

        return $trees;
    }

    private function getTreesVisibleFromEachTreeToEdge(): array
    {
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
        $trees = [];

        for ($y = 1; $y < count($this->data) - 1; $y++) {
            for ($x = 1; $x < count($this->data[$y]) - 1; $x++) {
                $visibleTrees = [];

                foreach ($directions as $direction) {
                    $dirCount = 0;
                    $newY = $y + $direction[0];
                    $newX = $x + $direction[1];

                    while (isset($this->data[$newY][$newX])) {
                        $dirCount++;

                        if ((int)$this->data[$newY][$newX] >= (int)$this->data[$y][$x]) {
                            break;
                        }

                        $newY += $direction[0];
                        $newX += $direction[1];
                    }

                    $visibleTrees[] = $dirCount;
                }

                $trees[$y][$x] = array_product($visibleTrees);
            }
        }

        if ($this->test) {
            print_r($trees);
            echo PHP_EOL;
        }

        return $trees;
    }
}
