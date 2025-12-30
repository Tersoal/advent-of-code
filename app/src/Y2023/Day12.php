<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day12 extends DayBase
{
    protected const int TEST_1 = 21;
    protected const int TEST_2 = 525152;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n", $data);

        foreach ($data as $line) {
            $parts = explode(' ', $line);
            $this->data[] = [
                'pattern' => $parts[0],
                'group' => array_map('intval', explode(',', $parts[1])),
            ];
        }

//        print_r($this->data);
    }

    public function getResult(): array
    {
        return [$this->getSumOfArrangements(0), $this->getSumOfArrangements(5)];
    }

    private function getSumOfArrangements(int $unfoldCount = 0): int
    {
        $total = 0;

        foreach ($this->data as $row) {
//            $results = $this->generateValidCombinations($row['pattern'], $row['group']);
//            $total += count($results);

            [$pattern, $groups] = $this->getUnfoldedInput($row['pattern'], $row['group'], $unfoldCount);

            $total += $this->countArrangements($pattern, $groups);
        }

        return $total;
    }

    private function countArrangements(string $pattern, array $groups, int $patternIndex = 0, int $groupIndex = 0, array &$cache = []): int
    {
        $cacheKey = "$patternIndex-$groupIndex";
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        if ($patternIndex >= strlen($pattern)) {
            return $groupIndex === count($groups) ? 1 : 0;
        }

        $char = $pattern[$patternIndex];
        $count = 0;

        // We try to set '.'
        if ($char === self::FREE || $char === self::UNKNOWN) {
            $count += $this->countArrangements($pattern, $groups, $patternIndex + 1, $groupIndex, $cache);
        }

        // We try to set a group of '#'
        if ($groupIndex < count($groups)) {
            $length = $groups[$groupIndex];

            // Verify if there is enough space
            if ($patternIndex + $length <= strlen($pattern)) {
                $valid = true;

                for ($i = 0; $i < $length; $i++) {
                    if ($pattern[$patternIndex + $i] === self::FREE) {
                        $valid = false;
                        break;
                    }
                }

                // El siguiente carácter debe ser '.' o fin de cadena
                if ($valid && ($patternIndex + $length === strlen($pattern) || $pattern[$patternIndex + $length] !== self::OBSTACLE)) {
                    $count += $this->countArrangements($pattern, $groups, $patternIndex + $length + 1, $groupIndex + 1, $cache);
                }
            }
        }

        $cache[$cacheKey] = $count;

        return $count;
    }

    private function getUnfoldedInput(string $pattern, array $groups, int $unfoldCount = 5): array
    {
        if ($unfoldCount <= 0) {
            return [$pattern, $groups];
        }

        $unfoldedPattern = implode(self::UNKNOWN, array_fill(0, $unfoldCount, $pattern));
        $unfoldedGroups = array_merge(...array_fill(0, $unfoldCount, $groups));

        return [$unfoldedPattern, $unfoldedGroups];
    }


    /**
     * This also works for part 1
     */
//    private function generateValidCombinations(string $pattern, array $groupPattern): array
//    {
//        $questionCount = substr_count($pattern, '?');
//        $totalCombos = 1 << $questionCount; // 2^n combinaciones
//        $validResults = [];
//
//        for ($i = 0; $i < $totalCombos; $i++) {
//            $binary = str_pad(decbin($i), $questionCount, '0', STR_PAD_LEFT);
//            $replaced = $pattern;
//            $index = 0;
//
//            // Sustituimos ? por # o . según el binario
//            foreach (str_split($binary) as $bit) {
//                $replaced = preg_replace('/\?/', $bit === '1' ? '#' : '.', $replaced, 1);
//            }
//
//            // Contamos los grupos de #
//            preg_match_all('/#+/', $replaced, $matches);
//            $groups = array_map('strlen', $matches[0]);
//
//            // Validamos
//            if ($groups === $groupPattern) {
//                $validResults[] = $replaced;
//            }
//        }
//
//        return $validResults;
//    }
}
