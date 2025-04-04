<?php

namespace App\Y2023;

use App\Model\DayBase;

class Day07 extends DayBase
{
    protected const int TEST_1 = 6440;
    protected const int TEST_2 = 5905;

    protected array $hands = [];
    protected array $cardLabels = ['A', 'K', 'Q', 'J', 'T', '9', '8', '7', '6', '5', '4', '3', '2'];
    protected array $cardLabels2 = ['A', 'K', 'Q', 'T', '9', '8', '7', '6', '5', '4', '3', '2', 'J'];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");

        foreach ($this->data as $line) {
            $parts = explode(" ", $line);
            $cards = str_split($parts[0]);
            $this->hands[] = [
                'cards' => $cards,
                'bid' => (int)$parts[1],
                'type' => null,
            ];
        }

//        print_r($this->hands);
    }

    public function getResult(): array
    {
        $hands = [];
        foreach ($this->hands as $hand) {
            $hand['type'] = $this->getHandType($hand['cards']);
            $hands[] = $hand;
        }

        $hands2 = [];
        foreach ($this->hands as $hand2) {
            $hand2['type'] = $this->getHandType2($hand2['cards']);
            $hands2[] = $hand2;
        }

//        print_r($hands2);

        return [$this->getWinnings($hands, $this->cardLabels), $this->getWinnings($hands2, $this->cardLabels2)];
    }

    private function getWinnings(array $hands, array $cardLabels): int
    {
        usort($hands, function ($a, $b) use ($cardLabels) {
            if ($a['type'] === $b['type']) {
                for ($i = 0; $i < count($a['cards']); $i++) {
                    $aLevel = array_search($a['cards'][$i], $cardLabels);
                    $bLevel = array_search($b['cards'][$i], $cardLabels);

                    if ($aLevel === $bLevel) {
                        continue;
                    }

                    return ($aLevel < $bLevel) ? -1 : 1;
                }

                return 0;
            }

            return ($a['type'] < $b['type']) ? -1 : 1;
        });

        $sum = 0;

        foreach (array_reverse($hands) as $pos => $hand) {
            $sum += $hand['bid'] * ($pos + 1);
        }

//        print_r(array_reverse($hands));

        return $sum;
    }

    public function getHandType(array $cards): int
    {
        $repeated = array_count_values($cards);
        $keys = array_keys($repeated);

        switch (count($repeated)) {
            case 1:
                return 1;
            case 2:
                if ($repeated[$keys[0]] === 4 || $repeated[$keys[1]] === 4) {
                    return 2;
                }

                return 3;
            case 3:
                if ($repeated[$keys[0]] === 3 || $repeated[$keys[1]] === 3 || $repeated[$keys[2]] === 3) {
                    return 4;
                }

                return 5;
            case 4:
                return 6;
            default:
                return 7;
        }
    }

    public function getHandType2(array $cards): int
    {
        $repeated = array_count_values($cards);
        $keys = array_keys($repeated);

        switch (count($repeated)) {
            case 1:
                return 1;
            case 2:
                if (in_array('J', $keys)) {
                    return 1;
                }

                if (in_array(4, $repeated)) {
                    return 2;
                }

                return 3;
            case 3:
                if (in_array('J', $keys)) {
                    if ($repeated['J'] === 2) {
                        return 2;
                    }

                    if (in_array(3, $repeated)) {
                        return 2;
                    }

                    return 3;
                }

                if (in_array(3, $repeated)) {
                    return 4;
                }

                return 5;
            case 4:
                if (in_array('J', $keys)) {
                    return 4;
                }

                return 6;
            default:
                if (in_array('J', $keys)) {
                    return 6;
                }

                return 7;
        }
    }
}
