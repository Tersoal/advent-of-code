<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day11 extends DayBase
{
    protected const int TEST_1 = 5;
    protected const int TEST_2 = 2;

    protected const string YOU = 'you';
    protected const string SVR = 'svr';
    protected const string OUT = 'out';
    protected const string DAC = 'dac';
    protected const string FFT = 'fft';

    private array $links = [];
    private array $links2 = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $isPart2 = false;

        foreach ($this->data as $row) {
            if ($row[0] === self::OBSTACLE) {
                $isPart2 = true;
                continue;
            }

            $parts = explode(": ", $row);

            if ($isPart2) {
                $this->links2[$parts[0]] = explode(" ", $parts[1]);
            } else {
                $this->links[$parts[0]] = explode(" ", $parts[1]);
            }
        }

        if (empty($this->links2)) {
            $this->links2 = $this->links;
        }

        if ($this->test) {
            print_r($this->data);
            print_r($this->links);
            print_r($this->links2);
            echo PHP_EOL;
        }
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
        $paths = [];

        $this->getPaths($this->links, self::YOU, [], $paths);

        if ($this->test) {
            print_r($paths);
        }

        return count($paths);
    }

    private function getPaths(array $links, string $link, array $path, array &$paths): void
    {
        $path[] = $link;

        if ($link === self::OUT) {
            $paths[] = $path;

            return;
        }

        foreach ($links[$link] as $newLink) {
            $this->getPaths($links, $newLink, $path, $paths);
        }
    }

    private function getPart2(): int
    {
        $cache = [];

        $svrFftPaths = $this->getPathsCount($this->links2, self::SVR, self::FFT, $cache);
        $fftDacPaths = $this->getPathsCount($this->links2, self::FFT, self::DAC, $cache);
        $dacOutPaths = $this->getPathsCount($this->links2, self::DAC, self::OUT, $cache);
        $svrDacPaths = $this->getPathsCount($this->links2, self::SVR, self::DAC, $cache);
        $dacFftPaths = $this->getPathsCount($this->links2, self::DAC, self::FFT, $cache);
        $fftOutPaths = $this->getPathsCount($this->links2, self::FFT, self::OUT, $cache);

        return ($svrFftPaths * $fftDacPaths * $dacOutPaths) + ($svrDacPaths * $dacFftPaths * $fftOutPaths);
    }

    private function getPathsCount(array &$links, string $start, string $end, array &$cache): int
    {
        $key = $start . '|' . $end;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        if ($start === $end) {
            return 1;
        }

        if (!isset($links[$start])) {
            return 0;
        }

        $cache[$key] = 0;

        foreach ($links[$start] as $newLink) {
            $cache[$key] += $this->getPathsCount($links, $newLink, $end, $cache);
        }

        return $cache[$key];
    }
}
