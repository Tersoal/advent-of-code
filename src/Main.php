<?php

namespace App;

use App\Model\DayInterface;
use Exception;

class Main
{
    protected bool $test = true;
    protected int $year;
    protected int $day;
    protected string $dataFilePath;
    protected string $scriptName;
    protected string $scriptPath;

    /**
     * @throws Exception
     */
    public function __construct(int $year, int $day, string $test)
    {
        $this->year = $year;
        $this->day = $day;
        $this->test = $test === 'true' || $test === '1';

        $dayName = strlen($day) === 1 ? "0" . $day : (string)$day;
        $this->scriptName = "Day$dayName";
        $this->dataFilePath = __DIR__ . "/../data/$year/day$dayName/" . ($test ? "day$dayName-test.txt" : "day$dayName.txt");

        if (!file_exists($this->dataFilePath)) {
            throw new Exception("Data File $this->dataFilePath does not exist");
        }

        $this->scriptName = "Day$dayName";
        $this->scriptPath = __DIR__ . "/$year/$this->scriptName.php";

        if (!file_exists($this->scriptPath)) {
            throw new Exception("Script $this->scriptPath does not exist");
        }

        require_once $this->scriptPath;
    }

    public function execute(): void
    {
        $classNamespace = 'App\Y' . $this->year;
        $className = $classNamespace . "\\" . $this->scriptName;
        /** @var DayInterface $app */
        $app = new $className($this->test, $this->dataFilePath);
        $app->execute();
    }
}
