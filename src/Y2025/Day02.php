<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day02 extends DayBase
{
    protected const int TEST_1 = 1227775554;
    protected const int TEST_2 = 4174379265;

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode(',', $data);

        foreach ($data as $row) {
            $values = explode('-', $row);
            $this->data[] = ['from'  => (int)$values[0], 'to' => (int)$values[1]];
        }

//        print_r($this->data);
//        echo PHP_EOL;
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
        return array_sum($this->getInvalidIds());
    }

    private function getPart2(): int
    {
        return array_sum($this->getInvalidIds(true));
    }

    private function getInvalidIds(bool $withAnySequence = false): array
    {
        $invalidIds = [];

        foreach ($this->data as $row) {
            if ($withAnySequence) {
                $invalidIds = array_merge($invalidIds, $this->findInvalidRepeatedPatternIdsFastPure((string)$row['from'], (string)$row['to']));
            } else {
                $invalidIds = array_merge($invalidIds, $this->getInvalidIdsFromRange($row['from'], $row['to']));
            }
        }

        return $invalidIds;
    }

    private function getInvalidIdsFromRange(int $from, int $to): array
    {
        if (strlen($from) === strlen($to) && strlen($from) % 2 !== 0) {
            return [];
        }

        while (strlen($from) % 2 !== 0) {
            $from++;
        }

        $invalidIds = [];
        $sequence = substr($from, 0, strlen($from) / 2);

        while (true) {
            $id = intval($sequence . $sequence);

            if ($id < $from) {
                $sequence++;

                continue;
            }

            if ($id > $to) {
                break;
            }

            $invalidIds[] = $id;
            $sequence++;
        }

        return $invalidIds;
    }

    /**
     *
     *
     *
     * PARTE 2 CREADA CON CHATGPT - NO SABRÍA HACERLO POR MI CUENTA.
     *
     *
     *
     */

    /** --------- UTILIDADES DE ENTEROS GRANDES EN STRINGS --------- **/

    function cmp_str(string $a, string $b): int
    {
        $a = ltrim($a, '0');
        if ($a === '') $a = '0';
        $b = ltrim($b, '0');
        if ($b === '') $b = '0';
        if (strlen($a) !== strlen($b)) return (strlen($a) < strlen($b)) ? -1 : 1;
        if ($a === $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    function add_str(string $a, string $b): string
    {
        $a = strrev($a);
        $b = strrev($b);
        $carry = 0;
        $out = '';
        $n = max(strlen($a), strlen($b));
        for ($i = 0; $i < $n; $i++) {
            $da = $i < strlen($a) ? intval($a[$i]) : 0;
            $db = $i < strlen($b) ? intval($b[$i]) : 0;
            $s = $da + $db + $carry;
            $out .= chr(($s % 10) + 48);
            $carry = intdiv($s, 10);
        }
        if ($carry) $out .= chr($carry + 48);
        $out = strrev($out);
        return ltrim($out, '0') ?: '0';
    }

    function sub_str(string $a, string $b): string
    {
        // asumimos a >= b
        $a = strrev($a);
        $b = strrev($b);
        $carry = 0;
        $out = '';
        for ($i = 0; $i < strlen($a); $i++) {
            $da = intval($a[$i]);
            $db = $i < strlen($b) ? intval($b[$i]) : 0;
            $d = $da - $db - $carry;
            if ($d < 0) {
                $d += 10;
                $carry = 1;
            } else {
                $carry = 0;
            }
            $out .= chr($d + 48);
        }
        $out = strrev($out);
        return ltrim($out, '0') ?: '0';
    }

    function mul_str_small(string $a, int $m): string
    {
        if ($m === 0 || $a === "0") return "0";
        $a = strrev($a);
        $carry = 0;
        $out = '';
        for ($i = 0; $i < strlen($a); $i++) {
            $p = intval($a[$i]) * $m + $carry;
            $out .= chr(($p % 10) + 48);
            $carry = intdiv($p, 10);
        }
        while ($carry > 0) {
            $out .= chr(($carry % 10) + 48);
            $carry = intdiv($carry, 10);
        }
        return strrev($out);
    }

    function div_str_floor(string $a, int $b): string
    {
        // divisor pequeño (<= 2^63)
        $carry = 0;
        $out = '';
        for ($i = 0; $i < strlen($a); $i++) {
            $carry = $carry * 10 + intval($a[$i]);
            $digit = intdiv($carry, $b);
            $out .= chr($digit + 48);
            $carry -= $digit * $b;
        }
        return ltrim($out, '0') ?: '0';
    }

    function div_str_ceil(string $a, int $b): string
    {
        $f = $this->div_str_floor($a, $b);
        // if a % b == 0 then floor == ceil
        $prod = $this->mul_str_small($f, $b);
        if ($this->cmp_str($prod, $a) === 0) return $f;
        return $this->add_str($f, "1");
    }

    /** ------------------------------------------------------------- **/

    function pow10_str(int $n): string
    {
        return "1" . str_repeat("0", $n);
    }


    /** ========= FUNCIÓN PRINCIPAL SIN BCMATH =========== **/

    function findInvalidRepeatedPatternIdsFastPure(string $minStr, string $maxStr): array
    {
        $minStr = ltrim($minStr, '0');
        if ($minStr === '') $minStr = '0';
        $maxStr = ltrim($maxStr, '0');
        if ($maxStr === '') $maxStr = '0';

        if ($this->cmp_str($minStr, $maxStr) > 0) {
            $t = $minStr;
            $minStr = $maxStr;
            $maxStr = $t;
        }

        $minLen = strlen($minStr);
        $maxLen = strlen($maxStr);

        $candidates = [];
        $sum = "0";

        for ($L = $minLen; $L <= $maxLen; $L++) {

            $lowBound = ($L == 1) ? "0" : $this->pow10_str($L - 1);
            $highBound = $this->sub_str($this->pow10_str($L), "1");

            if ($this->cmp_str($highBound, $minStr) < 0) continue;
            if ($this->cmp_str($lowBound, $maxStr) > 0) continue;

            $rangeMin = ($this->cmp_str($minStr, $lowBound) >= 0) ? $minStr : $lowBound;
            $rangeMax = ($this->cmp_str($maxStr, $highBound) <= 0) ? $maxStr : $highBound;

            for ($k = 2; $k <= $L; $k++) {
                if ($L % $k !== 0) continue;
                $m = intdiv($L, $k);

                // multiplier = (10^(m*k)-1)/(10^m -1)
                $pow_mk = $this->pow10_str($m * $k);
                $pow_m = $this->pow10_str($m);
                $numer = $this->sub_str($pow_mk, "1");
                $denom = intval($this->sub_str($pow_m, "1")); // cabe en 64 bits
                $mult = $this->div_str_floor($numer, $denom); // string

                $mult_int = intval($mult); // Esto SIEMPRE cabe (≤ 111111...)

                $pMin = $this->div_str_ceil($rangeMin, $mult_int);
                $pMax = $this->div_str_floor($rangeMax, $mult_int);

                $absMin = $this->pow10_str($m - 1);
                $absMax = $this->sub_str($this->pow10_str($m), "1");

                if ($this->cmp_str($pMin, $absMin) < 0) $pMin = $absMin;
                if ($this->cmp_str($pMax, $absMax) > 0) $pMax = $absMax;

                if ($this->cmp_str($pMin, $pMax) > 0) continue;

                // iteramos p dentro del rango (siempre pocos valores)
                for ($p = $pMin; $this->cmp_str($p, $pMax) <= 0; $p = $this->add_str($p, "1")) {

                    if ($p[0] === '0') continue;

                    $candidate = str_repeat($p, $k);

                    if ($this->cmp_str($candidate, $rangeMin) >= 0 &&
                        $this->cmp_str($candidate, $rangeMax) <= 0) {

                        if (!isset($candidates[$candidate])) {
                            $candidates[$candidate] = true;
                            $sum = $this->add_str($sum, $candidate);
                        }
                    }
                }
            }
        }

        $keys = array_keys($candidates);
        usort($keys, function ($a, $b) {
            return $this->cmp_str($a, $b);
        });

//        return ['candidates' => $keys, 'sum' => $sum];
        return $keys;
    }
}
