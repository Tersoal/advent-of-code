<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day07 extends DayBase
{
    protected const int TEST_1 = 95437;
    protected const int TEST_2 = 24933642;

    protected const int FOLDER_SIZE_LIMIT1 = 100000;
    protected const int TOTAL_DISK_SPACE = 70000000;
    protected const int MIN_UNUSED_SPACE = 30000000;

    private array $directoryTree = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->directoryTree = $this->readDirectoryTree();

//        if ($this->test) {
            print_r($this->data);
            print_r($this->directoryTree);
            echo PHP_EOL;
//        }
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
        $folders = array_filter($this->getFoldersSizes(), fn($size) => $size <= self::FOLDER_SIZE_LIMIT1);

//        if ($this->test) {
        print_r($folders);
        echo PHP_EOL;
//        }

        return array_sum($folders);
    }

    private function getPart2(): int
    {
        $folders = $this->getFoldersSizes();
        $totalUsedSpace = $folders[''];
        $totalUnusedSpace = self::TOTAL_DISK_SPACE - $totalUsedSpace;

        $folders = array_filter($folders, fn($size) => $size >= self::MIN_UNUSED_SPACE - $totalUnusedSpace);

        asort($folders);

        echo 'Total Used Space: ' . $totalUsedSpace . PHP_EOL;
        echo 'Total Unused Space: ' . $totalUnusedSpace . PHP_EOL;

        print_r($folders);
        echo PHP_EOL;

        return array_first($folders);
    }

    private function readDirectoryTree(): array
    {
        $tree = [];
        $index = '';

        foreach ($this->data as $input) {
            if (str_starts_with($input, '$ ls')) {
                continue;
            }

            if (str_starts_with($input, '$ cd ')) {
                $newDir = trim(str_replace('$ cd ', '', $input));

                if ($newDir === '/') {
                    $index = '/';
                } elseif ($newDir === '..') {
                    $index = substr($index, 0, strrpos($index, '/'));
                } else {
                    if ($index !== '/') {
                        $index .= '/';
                    }

                    $index .= $newDir;
                }

                continue;
            }

            if (str_starts_with($input, 'dir')) {
                continue;
            }

            if (!isset($tree[$index])) {
                $tree[$index] = ['size' => 0, 'files' => []];
            }

            $file = explode(' ', $input);
            $fileName = $file[1];
            $fileSize = (int)$file[0];

            $tree[$index]['files'][] = [$fileName => $fileSize];
            $tree[$index]['size'] += $fileSize;
        }

        return $tree;
    }

    private function getFoldersSizes(): array
    {
        $folders = [];

        foreach ($this->directoryTree as $folder => $data) {
            $folderNames = explode('/', $folder);

            while (count($folderNames) > 0) {
                $newFolderName = implode('/', $folderNames);

                if (!isset($folders[$newFolderName])) {
                    $folders[$newFolderName] = 0;
                }

                $folders[$newFolderName] += $data['size'];

                array_pop($folderNames);
            }
        }

        unset($folders['/']);

//        if ($this->test) {
        print_r($folders);
        echo PHP_EOL;
//        }

        return $folders;
    }
}
