<?php

namespace App\Model;

interface DayInterface
{
    public function loadData(string $filePath) :void;
    public function getResult(): array;
    public function testResult($test1, $test2): bool;
}
