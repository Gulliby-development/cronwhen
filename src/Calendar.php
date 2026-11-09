<?php

declare(strict_types=1);

namespace Cronwhen;

/**
 * Gregorian calendar calculations using minutes since the Unix epoch.
 */
final class Calendar
{
    private const MINUTES_PER_DAY = 1440;

    public static function isLeap(int $year): bool
    {
        return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
    }

    public static function daysInMonth(int $year, int $month): int
    {
        return match ($month) {
            1, 3, 5, 7, 8, 10, 12 => 31,
            4, 6, 9, 11 => 30,
            2 => self::isLeap($year) ? 29 : 28,
            default => 0,
        };
    }

    public static function dayIndex(int $year, int $month, int $day): int
    {
        $index = 0;
        for ($currentYear = Registry::FIRST_YEAR; $currentYear < $year; $currentYear++) {
            $index += self::isLeap($currentYear) ? 366 : 365;
        }

        for ($currentMonth = 1; $currentMonth < $month; $currentMonth++) {
            $index += self::daysInMonth($year, $currentMonth);
        }

        return $index + $day - 1;
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public static function dateFromDay(int $day): array
    {
        $year = Registry::FIRST_YEAR;
        while ($day >= (self::isLeap($year) ? 366 : 365)) {
            $day -= self::isLeap($year) ? 366 : 365;
            $year++;
        }

        $month = 1;
        while ($day >= self::daysInMonth($year, $month)) {
            $day -= self::daysInMonth($year, $month);
            $month++;
        }

        return ['year' => $year, 'month' => $month, 'day' => $day + 1];
    }

    public static function weekday(int $day): int
    {
        return ($day + 4) % 7;
    }

    public static function parse(string $time): ?int
    {
        if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2})$/D', $time, $parts) !== 1) {
            return null;
        }

        $year = (int) $parts[1];
        $month = (int) $parts[2];
        $day = (int) $parts[3];
        $hour = (int) $parts[4];
        $minute = (int) $parts[5];

        if ($year < Registry::FIRST_YEAR || $year > Registry::LAST_YEAR
            || $month < 1 || $month > 12
            || $day < 1 || $day > self::daysInMonth($year, $month)
            || $hour > 23 || $minute > 59
        ) {
            return null;
        }

        return (self::dayIndex($year, $month, $day) * self::MINUTES_PER_DAY) + ($hour * 60) + $minute;
    }

    public static function format(int $minute): string
    {
        $day = intdiv($minute, self::MINUTES_PER_DAY);
        $minuteOfDay = $minute % self::MINUTES_PER_DAY;
        $date = self::dateFromDay($day);

        return sprintf(
            '%04d-%02d-%02d %02d:%02d',
            $date['year'],
            $date['month'],
            $date['day'],
            intdiv($minuteOfDay, 60),
            $minuteOfDay % 60,
        );
    }

    public static function end(): int
    {
        return self::dayIndex(2100, 1, 1) * self::MINUTES_PER_DAY;
    }
}
