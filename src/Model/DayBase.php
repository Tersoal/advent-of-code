<?php

namespace App\Model;

Abstract class DayBase implements DayInterface
{
    protected bool $test = true;
    protected const string BOM = "\xef\xbb\xbf"; // BOM as a string for comparison.
    protected const string WALL = '#';
    protected const string OBSTACLE = '#';
    protected const string FREE = '.';
    protected const string GROUND = '.';
    protected const string ASTERISK = '*';
    protected const string UNKNOWN = '?';
    protected const string START_POSITION = 'S';
    protected const string POSITION = 'O';
    protected const string DIRECTION_ARROW_TOP = '^';
    protected const string DIRECTION_ARROW_BOTTOM = 'v';
    protected const string DIRECTION_ARROW_RIGHT = '>';
    protected const string DIRECTION_ARROW_LEFT = '<';

    protected array $data = [];

    /**
     */
    public function __construct(bool $test = true, ?string $filePath = null)
    {
        $this->test = $test;
        $this->loadData($filePath);
    }

    abstract public function loadData(string $filePath): void;
    abstract public function getResult(): array;

    public function execute(): void
    {
        $class = new \ReflectionClass($this);

        echo "====================================================" . PHP_EOL;
        echo "Executing " . $class->getShortName() . " in " . ($this->test ? "test" : "prod") . " mode" . PHP_EOL;
        echo "====================================================" . PHP_EOL . PHP_EOL;

        memory_reset_peak_usage();
        $start = microtime(true);

        [$test1, $test2] = $this->getResult();

        $end = microtime(true);

        echo "Test 1: " . $test1 . PHP_EOL;
        echo "Test 2: " . $test2 . PHP_EOL;

        if ($this->test) {
            echo "Test is: " . ($this->testResult($test1, $test2) ? 'OK' : 'FAIL') . PHP_EOL . PHP_EOL;
        }

        echo 'Peak usage: ' . round(memory_get_peak_usage() / 1024 / 1024) . " MB of memory." . PHP_EOL;
        echo 'Execution time: ' . round($end - $start, 4) . " seconds." . PHP_EOL;
    }

    public function testResult($test1, $test2): bool
    {
        return $test1 === static::TEST_1 && $test2 === static::TEST_2;
    }

    public function loadDataAsArrayMap(string $filePath, string $separator = "\r\n"): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $data = explode($separator, $data);

        $callback = fn(string $row): array => str_split($row);
        $this->data = array_map($callback, $data);
    }

    public function loadDataAsArray(string $filePath, string $separator = ' '): void
    {
        $data = file_get_contents($filePath);
        $data = str_replace(self::BOM,'', $data);
        $this->data = explode($separator, $data);
    }

    public function printMap(array $map): void
    {
        foreach ($map as $row) {
            echo implode('', $row) . PHP_EOL;
        }
    }
}
