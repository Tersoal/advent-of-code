<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day11 extends DayBase
{
    protected const int TEST_1 = 10605;
    protected const int TEST_2 = 2713310158;

    protected const int MAX_ROUNDS_1 = 20;
    protected const int MAX_ROUNDS_2 = 10000;
    protected const int WORRY_LEVEL_DIVISOR_1 = 3;

    private array $monkeys = [];
    private int $modConstant = 1; // We need to module all items by teh same number, that is the product of all test numbers (common divider)

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->parseInput();

        print_r($this->data);
        print_r($this->monkeys);
        print_r($this->modConstant);
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
        return $this->getMonkeyBusinessLevel($this->monkeys, self::MAX_ROUNDS_1, self::WORRY_LEVEL_DIVISOR_1, true);
    }

    private function getPart2(): int
    {
        return $this->getMonkeyBusinessLevel($this->monkeys, self::MAX_ROUNDS_2, 0, true);
    }

    private function getMonkeyBusinessLevel(array $monkeys, int $maxRounds, int $worryLevelDivisor, bool $printStatus): int
    {
        $monkeys = $this->calculateMonkeysActivity($monkeys, $maxRounds, $worryLevelDivisor, $printStatus);

        uasort($monkeys, fn($a, $b) => $a['inspected_times'] <=> $b['inspected_times']);

        $mostActiveMonkeys = array_slice($monkeys, -2);

        return array_product(array_column($mostActiveMonkeys, 'inspected_times'));
    }

    private function calculateMonkeysActivity(array $monkeys, int $maxRounds, int $worryLevelDivisor, bool $printStatus): array
    {
        for ($round = 1; $round <= $maxRounds; $round++) {
            for ($monkeyIndex = 0; $monkeyIndex < count($monkeys); $monkeyIndex++) {
                $monkey = $monkeys[$monkeyIndex];

                foreach ($monkey['items'] as $itemIndex => $item) {
                    $monkeys[$monkeyIndex]['inspected_times']++;

                    $operand = array_first($monkey['operation']);
                    if ($operand === null) {
                        $operand = $item;
                    }

                    if (array_key_first($monkey['operation']) === '*') {
                        $worryLevel = $item * $operand;
                    } else {
                        $worryLevel = $item + $operand;
                    }

                    if ($worryLevelDivisor) {
                        $worryLevel = (int)floor($worryLevel / $worryLevelDivisor);
                    } else {
                        $worryLevel = $worryLevel % $this->modConstant;
                    }

                    $isDivisible = ($worryLevel % $monkey['divisible_test']['by']) === 0 ? 1 : 0;
                    $newMonkeyIndex = $monkey['divisible_test']['throw'][$isDivisible];

                    $monkeys[$newMonkeyIndex]['items'][] = $worryLevel;

                    unset($monkeys[$monkeyIndex]['items'][$itemIndex]);
                }
            }

            if ($printStatus && in_array($round, [1, 20, 1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000])) {
                echo 'Monkeys status after round: ' . $round . PHP_EOL;
                print_r($monkeys);
                echo PHP_EOL;
            }
        }

        return $monkeys;
    }

    private function parseInput(): void
    {
        $monkeyIndex = 0;

        foreach ($this->data as $line) {
            if (str_starts_with($line, 'Monkey')) {
                $monkeyIndex = (int)str_replace(['Monkey ', ':'], '', trim($line));

                $this->monkeys[$monkeyIndex]['inspected_times'] = 0;

                continue;
            }

            if (str_contains($line, 'Starting items: ')) {
                $this->monkeys[$monkeyIndex]['items'] = array_map('intval', explode(', ', trim(str_replace('Starting items: ', '', $line))));

                continue;
            }

            if (str_contains($line, 'Operation: new = old ')) {
                $operation = explode(' ', trim(str_replace('Operation: new = old ', '', $line)));
                $this->monkeys[$monkeyIndex]['operation'][$operation[0]] = $operation[1] === 'old' ? null : (int)$operation[1];

                continue;
            }

            if (str_contains($line, 'Test: divisible by ')) {
                $test = (int)trim(str_replace('Test: divisible by ', '', $line));
                $this->monkeys[$monkeyIndex]['divisible_test']['by'] = $test;
                $this->modConstant *= $test;

                continue;
            }

            if (str_contains($line, 'If true: throw to monkey ')) {
                $result = (int)trim(str_replace('If true: throw to monkey ', '', $line));
                $this->monkeys[$monkeyIndex]['divisible_test']['throw'][1] = $result;

                continue;
            }

            if (str_contains($line, 'If false: throw to monkey ')) {
                $result = (int)trim(str_replace('If false: throw to monkey ', '', $line));
                $this->monkeys[$monkeyIndex]['divisible_test']['throw'][0] = $result;

                continue;
            }
        }
    }
}
