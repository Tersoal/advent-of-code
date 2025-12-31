<?php

namespace App\Y2022;

use App\Model\DayBase;

class Day02 extends DayBase
{
    protected const int TEST_1 = 15;
    protected const int TEST_2 = 12;

    private const string OP_ROCK = 'A';
    private const string OP_PAPER = 'B';
    private const string OP_SCISSORS = 'C';
    private const string ME_ROCK = 'X';
    private const string ME_LOSE = 'X';
    private const string ME_PAPER = 'Y';
    private const string ME_DRAW = 'Y';
    private const string ME_SCISSORS = 'Z';
    private const string ME_WON= 'Z';
    private const int LOST = 0;
    private const int DRAW = 3;
    private const int WON = 6;

    private array $mePoints = [
        self::ME_ROCK => 1,
        self::ME_PAPER => 2,
        self::ME_SCISSORS => 3,
    ];
    private array $mePoints2 = [
        self::OP_ROCK . '|' . self::ME_LOSE => self::ME_SCISSORS,
        self::OP_ROCK . '|' . self::ME_DRAW => self::ME_ROCK,
        self::OP_ROCK . '|' . self::ME_WON => self::ME_PAPER,
        self::OP_PAPER . '|' . self::ME_LOSE => self::ME_ROCK,
        self::OP_PAPER . '|' . self::ME_DRAW => self::ME_PAPER,
        self::OP_PAPER . '|' . self::ME_WON => self::ME_SCISSORS,
        self::OP_SCISSORS . '|' . self::ME_LOSE => self::ME_PAPER,
        self::OP_SCISSORS . '|' . self::ME_DRAW => self::ME_SCISSORS,
        self::OP_SCISSORS . '|' . self::ME_WON => self::ME_ROCK,
    ];

    private array $roundPoints = [
        self::OP_ROCK . '|' . self::ME_ROCK => self::DRAW,
        self::OP_ROCK . '|' . self::ME_PAPER => self::WON,
        self::OP_ROCK . '|' . self::ME_SCISSORS => self::LOST,
        self::OP_PAPER . '|' . self::ME_ROCK => self::LOST,
        self::OP_PAPER . '|' . self::ME_PAPER => self::DRAW,
        self::OP_PAPER . '|' . self::ME_SCISSORS => self::WON,
        self::OP_SCISSORS . '|' . self::ME_ROCK => self::WON,
        self::OP_SCISSORS . '|' . self::ME_PAPER => self::LOST,
        self::OP_SCISSORS . '|' . self::ME_SCISSORS => self::DRAW,
    ];
    private array $roundPoints2 = [
        self::ME_ROCK => self::LOST,
        self::ME_PAPER => self::DRAW,
        self::ME_SCISSORS => self::WON,
    ];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

        $this->data = array_map(fn($line) => explode(" ", $line), $this->data);

        if ($this->test) {
            print_r($this->data);
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
        return $this->getScore();
    }

    private function getScore(): int
    {
        $score = 0;

        foreach ($this->data as $round) {
            $score += $this->mePoints[$round[1]];
            $score += $this->roundPoints[$round[0] . '|' . $round[1]];
        }

        return $score;
    }

    private function getPart2(): int
    {
        return $this->getScore2();
    }

    private function getScore2(): int
    {
        $score = 0;

        foreach ($this->data as $round) {
            $result = $this->mePoints2[$round[0] . '|' . $round[1]];
            $score += $this->mePoints[$result];
            $score += $this->roundPoints2[$round[1]];
        }

        return $score;
    }
}
