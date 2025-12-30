# Advent of Code

This is my PHP participation in [Advent of Code](https://adventofcode.com/).

It's incomplete, sometimes weird... but it's very funny :-)

## Installation

Simple mount docker:

```console
docker compose -f docker/docker-compose.yaml up -d
docker compose exec aoc bash
```

## Use

App is launched with some parameters to dynamically load and execute the day you need:

```console
php index.php <year> <day> <test>
```
