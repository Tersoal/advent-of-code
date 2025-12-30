<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day20 extends DayBase
{
    protected const int TEST_1 = 11687500;
    protected const int TEST_2 = 0;
    protected const int PULSE_LOW = 0;
    protected const int PULSE_HIGH = 1;
    protected const int STATUS_OFF = 0;
    protected const int STATUS_ON = 1;
    protected const int PUSH_TIMES_1 = 1000;
    protected const int PUSH_TIMES_2 = 5000;
    protected const string MODULE_BROADCASTER = 'broadcaster';
    protected const string MODULE_RX = 'rx';
    protected const string MODULE_QT = 'qt';
    protected const string MODULE_FLIP_FLOP = '%';
    protected const string MODULE_CONJUNCTION = '&';

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $data = $this->data;
        $this->data = [];

        foreach ($data as $datum) {
            $parts = explode(' -> ', $datum);
            $module = $parts[0];
            $type = null;
            $destinations = explode(', ', $parts[1]);
            $status = [];

            if (in_array(substr($module, 0, 1), [self::MODULE_FLIP_FLOP, self::MODULE_CONJUNCTION], true)) {
                $type = substr($module, 0, 1);
                $module = substr($module, 1);

                if ($type === self::MODULE_FLIP_FLOP) {
                    $status = self::STATUS_OFF;
                }
            }

            $this->data[$module] = [
                'type' => $type,
                'destinations' => $destinations,
                'status' => $status,
            ];
        }

        // We save initial status of conjunction modules
        foreach ($this->data as $module => $datum) {
            if ($datum['type'] === self::MODULE_CONJUNCTION) {
                continue;
            }

            foreach ($datum['destinations'] as $destination) {
                if (!isset($this->data[$destination])) {
                    continue;
                }

                if ($this->data[$destination]['type'] !== self::MODULE_CONJUNCTION) {
                    continue;
                }

                $this->data[$destination]['status'][$module] = self::PULSE_LOW;
            }
        }

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [
            $this->getPulsesResult(self::PUSH_TIMES_1),
            $this->getPulseOnRxLow(self::PUSH_TIMES_2)
        ];
    }

    private function getPulsesResult(int $maxPushes): int
    {
        $currentStatus = $this->data;
        $lowPulses = 0;
        $highPulses = 0;
        $qtModules = $this->getQtModules(self::MODULE_QT);

        for ($push = 1; $push <= $maxPushes; ++$push) {
            $this->pushButton($push, $currentStatus, $lowPulses, $highPulses, $qtModules);
        }

        echo "Low pulses: " . $lowPulses . PHP_EOL;
        echo "High pulses: " . $highPulses . PHP_EOL;
        echo "Result: " . ($lowPulses * $highPulses) . PHP_EOL . PHP_EOL;

        return $lowPulses * $highPulses;
    }

    private function pushButton(int $push, array &$currentStatus, int &$lowPulses, int &$highPulses, array &$qtModules): void
    {
        $queue = [['from' => 'button', 'to' => self::MODULE_BROADCASTER, 'pulse' => self::PULSE_LOW]];

        while (!empty($queue)) {
            ['from' => $from, 'to' => $to, 'pulse' => $pulse] = array_shift($queue);

//            if ($push < 5) {
//                echo $from . ' - ' . ($pulse === 0 ? 'low' : 'high') . ' -> ' . $to . PHP_EOL;
//            }

            if ($pulse === self::PULSE_LOW) {
                $lowPulses++;
            } else {
                $highPulses++;
            }

            if (!isset($currentStatus[$to])) {
                continue;
            }

            $module = $currentStatus[$to];

            if ($module['type'] === self::MODULE_FLIP_FLOP) {
                if ($pulse === self::PULSE_HIGH) {
                    continue;
                }

                if ($module['status'] === self::STATUS_OFF) {
                    $module['status'] = self::STATUS_ON;
                    $pulse = self::PULSE_HIGH;
                } else {
                    $module['status'] = self::STATUS_OFF;
                    $pulse = self::PULSE_LOW;
                }
            } elseif ($module['type'] === self::MODULE_CONJUNCTION) {
                $module['status'][$from] = $pulse;

                if (array_all($module['status'], fn ($status) => $status === self::PULSE_HIGH)) {
                    $pulse = self::PULSE_LOW;
                } else {
                    $pulse = self::PULSE_HIGH;
                }

                if (in_array($to, array_keys($qtModules)) && $pulse === self::PULSE_HIGH) {
                    $qtModules[$to] = $push;

                    echo "Conjunction Module " . $to . " emits " . $pulse . " pulse on push " . $push . PHP_EOL;
                }
            }

            foreach ($module['destinations'] as $destination) {
                $queue[] = ['from' => $to, 'to' => $destination, 'pulse' => $pulse];
            }

            $currentStatus[$to] = $module;
        }

//        if ($push < 5) {
//            echo '*** Push ' . $push . ' status with accumulated low pulses of ' . $lowPulses . ' and high pulses of ' . $highPulses . '.' . PHP_EOL;
//            print_r($currentStatus);
//            echo PHP_EOL;
//        }
    }

    /**
     * Problem:
     *
     *  - "rx" has one only input ("qt").
     *  - "qt" is conjunction, so to send LOW, all it's inputs must send HIGH in the same push.
     *
     * Solution:
     *  - We need to save the push wich each input sends HIGH on.
     *  - Calculate LCM for them.
     */
    public function getPulseOnRxLow(int $maxPushes): int
    {
        $currentStatus = $this->data;
        $lowPulses = 0;
        $highPulses = 0;
        $qtModules = $this->getQtModules(self::MODULE_QT);

        for ($push = 1; $push <= $maxPushes; ++$push) {
            $this->pushButton($push, $currentStatus, $lowPulses, $highPulses, $qtModules);

            if (array_all($qtModules, fn ($status) => $status > 0)) {
                $lcm = $this->calculateLCM($qtModules);

                echo "LCM for inputs of 'rx' is: " . $lcm . PHP_EOL . PHP_EOL;
                print_r($qtModules);
                echo PHP_EOL;

                return $lcm;
            }
        }

        echo "There is no solution for 'rx' in " . $maxPushes . " pulses." . PHP_EOL;
        print_r($qtModules);
        echo PHP_EOL . PHP_EOL;

        return -1;
    }

    private function getQtModules(string $moduleName): array
    {
        $inputModules = [];

        foreach ($this->data as $module => $datum) {
            if (!in_array($moduleName, $datum['destinations'])) {
                continue;
            }

            $inputModules[$module] = 0;
        }

        return $inputModules;
    }

    private function calculateLCM(array $numbers): int
    {
        return array_reduce($numbers, fn($a, $b) => $this->lcm($a, $b), 1);
    }

    private function lcm(int $a, int $b): int
    {
        return intdiv($a * $b, $this->gcd($a, $b));
    }

    private function gcd(int $a, int $b): int
    {
        while ($b !== 0) {
            [$a, $b] = [$b, $a % $b];
        }

        return $a;
    }
}
