<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day23 extends DayBase
{
    protected const int TEST_1 = 7;
    protected const string TEST_2 = 'co,de,ka,ta';

    public function loadData(string $filePath): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode("\r\n", $data);

        foreach ($data as $line) {
            $this->data[] = explode('-', $line);
        }
    }

    public function getResult(): array
    {
        $connections = $this->getConnections();

        return [count($connections), $this->getPassword($connections)];
    }

    private function getConnections(): array
    {
        $connections = [];

        foreach ($this->data as $key => $datum) {
            $a = $this->getConnectionCombinations($key, $datum[0]);
            $b = $this->getConnectionCombinations($key, $datum[1]);
            $combos = array_intersect($a, $b);

            if (empty($combos)) {
                continue;
            }

            foreach ($combos as $combo) {
                $connection = array_merge($datum, [$combo]);
                sort($connection);

                if (in_array($connection, $connections, true)) {
                    continue;
                }

                $connections[] = $connection;
            }
        }

        return array_filter($connections, function ($connection) {
            foreach ($connection as $conn) {
                if (str_starts_with($conn, 't')) {
                    return true;
                }
            }

            return false;
        });
    }

    private function getConnectionCombinations(int $itemKey, string $computer): array
    {
        $combinations = [];

        foreach ($this->data as $key => $datum) {
            if ($key === $itemKey) {
                continue;
            }

            if (!in_array($computer, $datum)) {
                continue;
            }

            foreach ($datum as $item) {
                if ($item === $computer) {
                    continue;
                }

                $combinations[] = $item;
            }
        }

        return $combinations;
    }

    private function getPassword(array $connections): string
    {
        $computers = array_unique(array_merge(...$connections));
        sort($computers);

        $passwords = [];

        foreach ($computers as $computer) {
            $availableConnections = array_filter($connections, function ($connection) use ($computer) {
                return in_array($computer, $connection, true);
            });
            $availableComputers = array_unique(array_merge(...$availableConnections));
            sort($availableComputers);

            $password = implode(',', $availableComputers);

            if (!array_key_exists($password, $passwords)) {
                $passwords[$password] = ['itemsCount' => count($availableComputers), 'count' => 0];
            }

            $passwords[$password]['count']++;
        }

        $passwords = array_filter($passwords, function ($password) {
            return $password['itemsCount'] === $password['count'];
        });

        uasort($passwords, function ($a, $b) {
           return $a['count'] <=> $b['count'];
        });

        return array_key_last($passwords);
    }
}
