<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day13 extends DayBase
{
    private const string BUTTON_A = 'Button A: ';
    private const string BUTTON_B = 'Button B: ';
    private const string PRIZE = 'Prize: ';
    private const int BUTTON_A_TOKEN_COST = 3;
    private const int BUTTON_B_TOKEN_COST = 1;
    protected const int TEST_1 = 480;
    protected const int TEST_2 = 0;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n\r\n", $data);

        foreach ($data as $prizeData) {
            $dataParts = explode("\r\n", $prizeData);
            $datum = ['A' => [], 'B' => [], 'P' => []];

            $buttonA = explode(', ', str_replace(self::BUTTON_A,'', $dataParts[0]));
            foreach ($buttonA as $button) {
                $positionData = explode('+', $button);
                $datum['A'][$positionData[0]] = (int)$positionData[1];
            }

            $buttonB = explode(', ', str_replace(self::BUTTON_B,'', $dataParts[1]));
            foreach ($buttonB as $button) {
                $positionData = explode('+', $button);
                $datum['B'][$positionData[0]] = (int)$positionData[1];
            }

            $prize = explode(', ', str_replace(self::PRIZE,'', $dataParts[2]));
            foreach ($prize as $button) {
                $positionData = explode('=', $button);
                $datum['P'][$positionData[0]] = (int)$positionData[1];
            }

            $this->data[] = $datum;
        }

        //var_dump($this->data);
    }

    public function getResult(): array
    {
        return [
            $this->getTokens(0),
            $this->getTokens(10000000000000)
        ];
    }

    // 94*N + 22*M = 8400
    // 34*N + 67*M = 5400

    // 94*N = 8400 - 22*M
    // N = (8400 - 22*M) / 94

    // 34*((8400 - 22*M) / 94) + 67*M = 5400
    // 34*8400/94 - 34*22*M/94 + 67*M = 5400
    // - 34*22*M/94 + 67*M = 5400 - 34*8400/94
    // (- 34*22*M + 94*67*M) / 94 = 5400 - 34*8400/94
    // (- 34*22 + 94*67)*M / 94 = 5400 - 34*8400/94
    // M / 94 = (5400 - 34*8400/94) / (- 34*22 + 94*67)
    // M = ((5400 - 34*8400/94) / (- 34*22 + 94*67)) * 94
    // N = (8400 - 22*M) / 94
    private function getTokens(int $prizeValueAdded): int
    {
        echo '=============================================' . PHP_EOL;
        echo 'TOKENS' . PHP_EOL;
        echo '=============================================' . PHP_EOL;

        $tokenCounters = [];

        foreach ($this->data as $key => $tokenData) {
            echo '=============================================' . PHP_EOL;
            echo 'Data # ' . $key . PHP_EOL;
            echo '=============================================' . PHP_EOL;

            $tokenData['P']['X'] += $prizeValueAdded;
            $tokenData['P']['Y'] += $prizeValueAdded;

            $BPulses = (($tokenData['P']['Y'] - ($tokenData['A']['Y'] * $tokenData['P']['X'] / $tokenData['A']['X'])) / (-1 * ($tokenData['A']['Y'] * $tokenData['B']['X']) + $tokenData['A']['X'] * $tokenData['B']['Y'])) * $tokenData['A']['X'];
            $BPulses = round($BPulses, 4);
            var_dump('$BPulses = ' . $BPulses);
//            var_dump('$BPulses rest = ' . fmod($BPulses, 1));

            if(fmod($BPulses, 1) !== 0.0) {
                continue;
            }

            $APulses = ($tokenData['P']['X'] - ($tokenData['B']['X'] * $BPulses)) / $tokenData['A']['X'];
            $APulses = round($APulses, 4);
            var_dump('$APulses = ' . $APulses);
//            var_dump('$APulses rest = ' . fmod($APulses, 1));

            if(fmod($APulses, 1) !== 0.0) {
                continue;
            }

            $tokenCounters[$key] = ($APulses * self::BUTTON_A_TOKEN_COST) + ($BPulses * self::BUTTON_B_TOKEN_COST);
        }

        var_dump($tokenCounters);

        return array_sum($tokenCounters);
    }
}
