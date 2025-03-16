<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day22 extends DayBase
{
    private const int CYCLES = 2000;
    protected const int TEST_1 = 37327623;
    protected const int TEST_2 = 23;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");
    }

    public function getResult(): array
    {
        $sumOfFinalSecrets = 0;
        $prices = [];

        foreach ($this->data as $buyer => $secret) {
            $prices[$buyer] = [(int)substr($secret, -1)];

            $sumOfFinalSecrets += $this->createSecret($prices, $buyer, (int)$secret, self::CYCLES, 1);
        }

        return [$sumOfFinalSecrets, $this->getBananas($prices)];
    }

    private function getBananas(array $prices): int
    {
        $sequencesOfPrices = [];

        foreach ($prices as $buyer => $priceUnits) {
            for ($i = 0; $i < count($priceUnits); $i++) {
                if ($i < 4) {
                    continue;
                }

                $sequence = ($priceUnits[$i - 3] - $priceUnits[$i - 4]) . '|' .
                    ($priceUnits[$i - 2] - $priceUnits[$i - 3]) . '|' .
                    ($priceUnits[$i - 1] - $priceUnits[$i - 2]) . '|' .
                    ($priceUnits[$i] - $priceUnits[$i - 1]);

                if (!array_key_exists($sequence, $sequencesOfPrices) || !array_key_exists($buyer, $sequencesOfPrices[$sequence])) {
                    $sequencesOfPrices[$sequence][$buyer] = $priceUnits[$i];
                }
            }
        }

        $sequencesOfPricesTotals = [];

        foreach ($sequencesOfPrices as $sequence => $prices) {
            $sequencesOfPricesTotals[$sequence] = array_sum($prices);
        }

        arsort($sequencesOfPricesTotals);

        return $sequencesOfPricesTotals[array_key_first($sequencesOfPricesTotals)];
    }

    private function createSecret(array &$prices, int $buyer, int $secret, int $cycles, int $cycle): int
    {
        // Calculate the result of multiplying the secret number by 64.
        $newSecret = $secret * 64;

        // Then, mix this result into the secret number.
        // To mix a value into the secret number, calculate the bitwise XOR of the given value and the secret number.
        // Then, the secret number becomes the result of that operation. (If the secret number is 42 and you were
        // to mix 15 into the secret number, the secret number would become 37.)
        $newSecret = $secret ^ $newSecret;

        // Finally, prune the secret number.
        // To prune the secret number, calculate the value of the secret number modulo 16777216.
        // Then, the secret number becomes the result of that operation. (If the secret number is 100000000 and
        // you were to prune the secret number, the secret number would become 16113920.)
        $newSecret = $newSecret % 16777216;

        // Calculate the result of dividing the secret number by 32. Round the result down to the nearest integer.
        // Then, mix this result into the secret number. Finally, prune the secret number.
        $newSecret = ($newSecret ^ (int)($newSecret / 32)) % 16777216;

        // Calculate the result of multiplying the secret number by 2048.
        // Then, mix this result into the secret number. Finally, prune the secret number.
        $newSecret = ($newSecret ^ ($newSecret * 2048)) % 16777216;

        $prices[$buyer][] = (int)substr($newSecret, -1);

        if ($cycle === $cycles) {
            return $newSecret;
        }

        return $this->createSecret($prices, $buyer, $newSecret, $cycles, $cycle + 1);
    }
}
