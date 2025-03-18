<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day09 extends DayBase
{
    protected const int TEST_1 = 1928;
    protected const int TEST_2 = 2858;

    public string $definitions = '';

    public function loadData(string $filePath): void
    {
        $this->definitions = file_get_contents($filePath);
        $this->definitions = str_replace(self::BOM,'', $this->definitions);
    }

    public function getResult(): array
    {
        return [$this->getChecksum(), $this->getChecksumFragmented()];
    }

    public function getChecksum(): int
    {
        $filesWithSpaces = $this->getFilesWithSpaces();
        $filesCompacted = $this->compactFiles($filesWithSpaces);
        //var_dump($filesCompacted);

        $checksum = 0;
        foreach ($filesCompacted as $index => $block) {
            if (is_null($block)) {
                continue;
            }

            $checksum += $index * $block;
        }

        return $checksum;
    }

    public function getChecksumFragmented(): int
    {
        $filesWithSpaces = $this->getFilesWithSpaces();
        $filesFragmented = $this->defragmentFiles($filesWithSpaces);
        //var_dump($filesFragmented);

        $checksum = 0;
        foreach ($filesFragmented as $index => $block) {
            if (is_null($block)) {
                continue;
            }

            $checksum += $index * $block;
        }

        return $checksum;
    }

    public function getFilesWithSpaces(): array
    {
        $fileDefinitions = str_split($this->definitions, 2);
        $filesWithSpaces = [];

        foreach ($fileDefinitions as $index => $fileDefinition) {
            $fileLength = $fileDefinition[0];
            $spacesLength = 0;

            if (strlen($fileDefinition) === 2) {
                $spacesLength = $fileDefinition[1];
            }

            $fileWithSpaces = array_fill(0, $fileLength, $index);

            if ($spacesLength) {
                $fileWithSpaces = array_merge($fileWithSpaces, array_fill(0, $spacesLength, null));
            }

            $filesWithSpaces[] = $fileWithSpaces;
        }

        return $filesWithSpaces;
    }

    public function compactFiles(array $filesWithSpaces): array
    {
        $filesWithSpaces = array_merge(...$filesWithSpaces);
        //var_dump($filesWithSpaces);
        $spaceIndexes = array_keys(array_filter($filesWithSpaces, fn(?int $value): ?int => is_null($value)));
        //var_dump($spaceIndexes);

        for ($i = count($filesWithSpaces) - 1; $i >= 0; $i--) {
            if (is_null($filesWithSpaces[$i])) {
                continue;
            }

            $spaceIndex = array_shift($spaceIndexes);
            if ($spaceIndex > $i) {
                break;
            }

            $filesWithSpaces[$spaceIndex] = $filesWithSpaces[$i];
            $filesWithSpaces[$i] = null;
        }

        return $filesWithSpaces;
    }

    public function defragmentFiles(array $filesWithSpaces): array
    {
        $filesBlocks = $filesWithSpaces;
        //var_dump($filesBlocks);

        for ($i = count($filesWithSpaces) - 1; $i >= 0; $i--) {
            $file = array_filter($filesWithSpaces[$i], fn(?int $value): ?int => !is_null($value));

            if (count($file) === 0) {
                continue;
            }

            foreach ($filesBlocks as $blockIndex => $block) {
                if ($blockIndex >= $i) {
                    break;
                }

                $spaces = array_filter($block, fn(?int $value): ?int => is_null($value));

                if (count($file) > count($spaces)) {
                    continue;
                }

                $blockFile = array_filter($block, fn(?int $value): ?int => !is_null($value));
                $filesBlocks[$blockIndex] = [...$blockFile, ...$file];

                $diff = count($spaces) - count($file);
                if ($diff > 0) {
                    $filesBlocks[$blockIndex] = [...$filesBlocks[$blockIndex], ...array_fill(0, $diff, null)];
                }

                $filesBlocks[$i] = array_replace($filesBlocks[$i], array_fill(0, count($file), null));

                //$print = array_merge(...$filesBlocks);
                //var_dump(implode('', array_map(fn(?int $value): string => is_null($value) ? '.' : (string)$value, $print)));

                break;
            }
        }

        return array_merge(...$filesBlocks);
    }
}
