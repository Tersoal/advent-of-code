<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day19 extends DayBase
{
    protected const int TEST_1 = 19114;
    protected const int TEST_2 = 167409079868000;

    protected array $workflows = [];
    protected array $parts = [];

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        [$workflows, $parts] = explode("\n\n", $data);
        $workflows = explode("\n", $workflows);
        $parts = explode("\n", $parts);

        foreach ($workflows as $workflow) {
            $split = explode('{', $workflow);
            $instructions = explode(',', trim($split[1], '{}'));

            foreach ($instructions as $instruction) {
                $instructionParts = explode(',', $instruction);

                foreach ($instructionParts as $instructionPart) {
                    if (!str_contains($instructionPart, ':')) {
                        $this->workflows[$split[0]][] = [
                            'rate' => null,
                            'operator' => null,
                            'condition' => null,
                            'destination' => $instructionPart,
                        ];

                        continue;
                    }

                    $definition = explode(':', $instructionPart);

                    $this->workflows[$split[0]][] = [
                        'rate' => substr($definition[0], 0, 1),
                        'operator' => substr($definition[0], 1, 1),
                        'condition' => (int)substr($definition[0], 2),
                        'destination' => $definition[1],
                    ];
                }
            }
        }

        foreach ($parts as $key => $part) {
            $split = explode(',', trim($part, '{}'));

            foreach ($split as $step) {
                $steps = explode('=', $step);
                $this->parts[$key][$steps[0]] = (int)$steps[1];
            }
        }

//        print_r($this->workflows);
//        print_r($this->parts);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
//        return [$this->getTotalRating(), $this->getTotalCombinations()];
        return [$this->getTotalRating(), $this->countAcceptedCombinations()];
    }

    private function getTotalRating(): int
    {
        $total = 0;

        foreach ($this->parts as $part) {
            if (!$this->isRatingAccepted($part, $this->workflows['in'])) {
                continue;
            }

            foreach ($part as $rating) {
                $total += $rating;
            }
        }

        return $total;
    }

    private function isRatingAccepted(array $part, array $workflow): bool
    {
        foreach ($workflow as $step) {
            $result = true;

            if ($step['rate'] !== null) {
                $value = $part[$step['rate']];
                $operation = $value . $step['operator'] . $step['condition'];
                $result = eval("return $operation;");
            }

            if ($result) {
                if ($step['destination'] === 'A') {
                    return true;
                }

                if ($step['destination'] === 'R') {
                    return false;
                }

                return $this->isRatingAccepted($part, $this->workflows[$step['destination']]);
            }
        }

        return false;
    }

    private function countAcceptedCombinations(): int
    {
        return $this->evaluateRanges($this->workflows, 'in', [
            'x' => [1, 4000],
            'm' => [1, 4000],
            'a' => [1, 4000],
            's' => [1, 4000],
        ]);
    }

    private function evaluateRanges(array $workflows, string $workflowName, array $ranges): int
    {
        if ($workflowName === 'A') {
            return $this->countCombinations($ranges);
        }

        if ($workflowName === 'R') {
            return 0;
        }

        $rules = $workflows[$workflowName];
        $count = 0;

        foreach ($rules as $rule) {
            if ($rule['rate'] === null) {
                // No condición, solo destino por defecto
                $count += $this->evaluateRanges($workflows, $rule['destination'], $ranges);

                break;
            }

            $var = $rule['rate'];
            $op = $rule['operator'];
            $val = $rule['condition'];
            $dest = $rule['destination'];
            [$low, $high] = $ranges[$var];

            if ($op === '<') {
                if ($val <= $low) {
                    // No se cumple, sigue al siguiente
                    continue;
                } elseif ($val > $high) {
                    // Todo el rango cumple
                    $count += $this->evaluateRanges($workflows, $dest, $ranges);

                    break;
                } else {
                    // Divide el rango
                    $newRange = $ranges;
                    $newRange[$var] = [$low, $val - 1];
                    $count += $this->evaluateRanges($workflows, $dest, $newRange);

                    $ranges[$var] = [$val, $high];
                }
            } elseif ($op === '>') {
                if ($val >= $high) {
                    continue;
                } elseif ($val < $low) {
                    $count += $this->evaluateRanges($workflows, $dest, $ranges);

                    break;
                } else {
                    $newRange = $ranges;
                    $newRange[$var] = [$val + 1, $high];
                    $count += $this->evaluateRanges($workflows, $dest, $newRange);

                    $ranges[$var] = [$low, $val];
                }
            }
        }

        return $count;
    }

    private function countCombinations(array $ranges): int
    {
        return array_reduce($ranges, function ($carry, $range) {
            return $carry * ($range[1] - $range[0] + 1);
        }, 1);
    }



    /**
     * My poor attempt at solving it
     */
//    private function getTotalCombinations(): int
//    {
//        $partRanges = [];
//        $acceptedPaths = [];
//
//        $this->getCombinationsOfParts($this->workflows['in'], [], $acceptedPaths, $partRanges);
//
////        print_r($partRanges);
////        echo PHP_EOL;
//
//        foreach ($partRanges as $name => $step) {
//            sort($values);
//
//            $partRanges[$name] = [...[1], ...$values, ...[4000]];
//        }
//
//        $rangeCombinations = [];
//
//        foreach ($partRanges as $name => $step) {
//
//
//
//
//        }
//
//
//
//
//
//
//        return 0;
//    }
//
//    /**
//     * paths in test, manually checked:
//     *
//     *      [0]  => [in => [s => [1, 1350]],    px  => [a => [1, 2005]],                         qkq => [x => [1, 1415]]] ---------------------------------------------------> A
//     *      [1]  => [in => [s => [1, 1350]],    px  => [a => [1, 2005]],                         qkq => [x => [1416, 4000]],                   crn => [x => [1, 2662]]] -----> R
//     *      [2]  => [in => [s => [1, 1350]],    px  => [a => [1, 2005]],                         qkq => [x => [1416, 4000]],                   crn => [x => [2663, 4000]]] --> A
//     *      [3]  => [in => [s => [1, 1350]],    px  => [a => [2006, 4000], m => [1, 2090]],      rfg => [s => [1, 536]],                       gd  => [a => [1, 3333]]] -----> R
//     *      [4]  => [in => [s => [1, 1350]],    px  => [a => [2006, 4000], m => [1, 2090]],      rfg => [s => [1, 536]],                       gd  => [a => [3334, 4000]]] --> R
//     *      [5]  => [in => [s => [1, 1350]],    px  => [a => [2006, 4000], m => [1, 2090]],      rfg => [s => [537, 4000], x => [1, 2440]]] ---------------------------------> A
//     *      [6]  => [in => [s => [1, 1350]],    px  => [a => [2006, 4000], m => [1, 2090]],      rfg => [s => [537, 4000], x => [2441, 4000]]] ------------------------------> R
//     *      [7]  => [in => [s => [1, 1350]],    px  => [a => [2006, 4000], m => [2091, 4000]]] ------------------------------------------------------------------------------> A
//     *      [8]  => [in => [s => [1351, 4000]], qqz => [s => [1, 2770], m => [1, 1800]],         hdj => [m => [1, 838]],                       pv => [a => [1, 1716]]] ------> A
//     *      [9]  => [in => [s => [1351, 4000]], qqz => [s => [1, 2770], m => [1, 1800]],         hdj => [m => [1, 838]],                       pv => [a => [1717, 4000]]] ---> R
//     *      [10] => [in => [s => [1351, 4000]], qqz => [s => [1, 2770], m => [1, 1800]],         hdj => [m => [839, 4000]]] -------------------------------------------------> A
//     *      [11] => [in => [s => [1351, 4000]], qqz => [s => [1, 2770], m => [1801, 4000]]] ---------------------------------------------------------------------------------> R
//     *      [12] => [in => [s => [1351, 4000]], qqz => [s => [2771, 4000]],                      qs  => [s => [1, 3448]],                      lnx => [m => [1, 1548]] ------> A
//     *      [13] => [in => [s => [1351, 4000]], qqz => [s => [2771, 4000]],                      qs  => [s => [1, 3448]],                      lnx => [m => [1549, 4000]] ---> A
//     *      [14] => [in => [s => [1351, 4000]], qqz => [s => [2771, 4000]],                      qs  => [s => [3449, 4000]]] ------------------------------------------------> A
//     *
//     *
//     * @param array $workflow
//     * @param array $currentPath
//     * @param array $acceptedPaths
//     * @param array $partRanges
//     * @return void
//     */
//    private function getCombinationsOfParts(array $workflow, array $currentPath, array &$acceptedPaths, array &$partRanges): void
//    {
//        $minValue = 1;
//        $maxValue = 4000;
//
//        foreach ($workflow as $name => $step) {
//            if ($step['destination'] === 'R') {
//                continue;
//            }
//
//            $newPath = $currentPath;
//
//            if ($step['rate'] !== null) {
//                $value = $step['condition'];
////                if ($step['operator'] === '<') {
////                    $value--;
////                }
//
//                $partRanges[$step['rate']][] = $value;
//
//                $newPath[$name][$step['rate']] = [$minValue, $value];
//
//                $minValue = $value;
//            }
//
//            if ($step['destination'] === 'A') {
//                $acceptedPaths[] = $newPath;
//
//                continue;
//            }
//
//            $this->getCombinationsOfParts($this->workflows[$step['destination']], $partRanges);
//        }
//    }
}
