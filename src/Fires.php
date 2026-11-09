<?php

declare(strict_types=1);

namespace Cronwhen;

/**
 * Cron schedule matching helpers shared by commands.
 *
 * @phpstan-import-type Schedule from Input
 */
final class Fires
{
    /**
     * Determine whether a schedule matches a supported calendar day.
     *
     * @param Schedule $schedule
     */
    public static function matchesDay(array $schedule, int $day): bool
    {
        $date = Calendar::dateFromDay($day);
        $weekday = Calendar::weekday($day);
        $monthMatches = in_array($date['month'], $schedule['month'], true);
        $dayMatches = in_array($date['day'], $schedule['day'], true);
        $weekdayMatches = in_array($weekday, $schedule['weekday'], true);

        if (!$monthMatches) {
            return false;
        }

        if ($schedule['dayRestricted'] && $schedule['weekdayRestricted']) {
            return $dayMatches || $weekdayMatches;
        }

        return $dayMatches && $weekdayMatches;
    }

    /**
     * Return sorted unique minute offsets for one schedule day.
     *
     * @param Schedule $schedule
     * @return list<int>
     */
    public static function minutesOfDay(array $schedule): array
    {
        $minutes = [];
        foreach ($schedule['hour'] as $hour) {
            foreach ($schedule['minute'] as $minute) {
                $minutes[] = ($hour * 60) + $minute;
            }
        }

        sort($minutes, SORT_NUMERIC);

        return array_values(array_unique($minutes));
    }
}
