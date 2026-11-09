<?php

declare(strict_types=1);

namespace Cronwhen;

final class Registry
{
    public const VERSION = '0.1.0';
    public const MAX_LINES = 20;
    public const DEFAULT_COUNT = 5;
    public const MAX_COUNT = 50;
    public const FIRST_YEAR = 1970;
    public const LAST_YEAR = 2099;

    public const USAGE = "usage: cronwhen check\n"
        . "       cronwhen fields\n"
        . "       cronwhen next --from TIME [--count N]\n"
        . "       cronwhen count --from TIME --to TIME\n"
        . "       cronwhen clash --from TIME\n";

    /**
     * One entry per command: its word and the options it accepts.
     *
     * @var list<array{name: string, options: list<string>}>
     */
    public const COMMANDS = [
        ['name' => 'check', 'options' => []],
        ['name' => 'fields', 'options' => []],
        ['name' => 'next', 'options' => ['--from', '--count']],
        ['name' => 'count', 'options' => ['--from', '--to']],
        ['name' => 'clash', 'options' => ['--from']],
    ];

    /**
     * The five fields of a schedule line, in order: the word in messages, the lowest and the highest value and the names (the first name is the lowest value).
     * The weekday runs to 7 (7 is Sunday, like 0).
     *
     * @var list<array{name: string, low: int, high: int, names: list<string>}>
     */
    public const FIELDS = [
        ['name' => 'minute', 'low' => 0, 'high' => 59, 'names' => []],
        ['name' => 'hour', 'low' => 0, 'high' => 23, 'names' => []],
        ['name' => 'day', 'low' => 1, 'high' => 31, 'names' => []],
        ['name' => 'month', 'low' => 1, 'high' => 12, 'names' => ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec']],
        ['name' => 'weekday', 'low' => 0, 'high' => 7, 'names' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']],
    ];
}
