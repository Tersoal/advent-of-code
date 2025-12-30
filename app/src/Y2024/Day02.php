<?php

namespace App\Y2024;

use App\Model\DayBase;

class Day02 extends DayBase
{
    protected const int TEST_1 = 2;
    protected const int TEST_2 = 8;

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\r\n");
    }

    public function getResult(): array
    {
        return [$this->getSafeReportsCount(0), $this->getSafeReportsCount(1)];
    }

    public function getSafeReportsCount(int $tolerance = 0): int
    {
        $count = 0;

        foreach ($this->data as $key => $report) {
            $report = explode(" ", $report);

            if ($this->isReportSafe($report, $tolerance)) {
                $count++;
                continue;
            }

            $reversed = array_reverse($report);

            if ($this->isReportSafe($reversed, $tolerance)) {
                $count++;
                continue;
            }

            echo "Error in line " . $key . "\n";
            var_dump($report);
        }

        return $count;
    }


//    private function loadData(bool $test): void
//    {
//        $filename = $test ? 'data/day02/day02-test.txt' : 'data/day02/day02.txt';
//        $file = fopen($filename, 'r');
//
//        // Progress file pointer and get first 3 characters to compare to the BOM string.
//        if (fgets($file, 4) !== self::BOM) {
//            // BOM not found - rewind pointer to start of file.
//            rewind($file);
//        }
//
//        while (($line = fgetcsv($file, null, ' ')) !== FALSE) {
//            $this->reports[] = $line;
//        }
//
//        fclose($file);
//    }

    public function getReportLevelStatuses(array $report): array
    {
        $safeDirection = 0;
        $levelStatuses = [true]; // first item is always valid

        for ($i = 0; $i < count($report) - 1; $i++) {
            $diff = (int)$report[$i] - (int)$report[$i + 1];

            if ($diff === 0) {
                $levelStatuses[$i + 1] = false;
                continue;
            }

            if (abs($diff) > 3) {
                $levelStatuses[$i + 1] = false;
                continue;
            }

            $direction = $diff < 0 ? 1 : -1;

            if ($i === 0) {
                $safeDirection = $direction;
            }

            if ($direction !== $safeDirection) {
                $levelStatuses[$i + 1] = false;
                continue;
            }

            $levelStatuses[$i + 1] = true;
        }

        return $levelStatuses;
    }

    public function isReportSafe(array $report, int $tolerance): bool
    {
        if (count($report) < 2) {
            return false;
        }

        $reportLevelStatuses = $this->getReportLevelStatuses($report);
        $reportLevelErrors = array_filter($reportLevelStatuses, function ($status) {
            return $status === false;
        });

        if (count($reportLevelErrors) === 0) {
            return true;
        }

        foreach (array_keys($reportLevelErrors) as $index => $key) {
            if ($index + 1 > $tolerance) {
                break;
            }

            unset($report[$key]);

            $reportLevelStatuses = $this->getReportLevelStatuses(array_values($report));
            $reportLevelErrors = array_filter($reportLevelStatuses, function ($status) {
                return $status === false;
            });

            if (count($reportLevelErrors) === 0) {
                return true;
            }
        }

        return false;
    }
}
