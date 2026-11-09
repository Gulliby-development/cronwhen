<?php

declare(strict_types=1);

namespace Cronwhen\Commands;

/**
 * Placeholder: the count and clash task replaces this file with the clash command.
 *
 * @phpstan-import-type Options from \Cronwhen\Cli
 * @phpstan-import-type Reply from \Cronwhen\Cli
 * @phpstan-import-type Schedule from \Cronwhen\Input
 */
final class ClashCommand
{
    /**
     * @param Options $options
     * @param list<Schedule> $schedules
     * @return Reply
     */
    public static function run(array $options, array $schedules): array
    {
        assert($options['from'] !== null);
        $from = $options['from'];
        if (count($schedules) < 2) {
            return ['status' => 1, 'out' => "no clash before 2100\n", 'err' => ''];
        }

        $end = \Cronwhen\Calendar::end();
        $endDay = intdiv($end, 1440);
        $minutesBySchedule = [];
        foreach ($schedules as $schedule) {
            $minutesBySchedule[] = \Cronwhen\Fires::minutesOfDay($schedule);
        }

        $firstDay = intdiv($from, 1440);
        for ($day = $firstDay; $day < $endDay; $day++) {
            /** @var array<int, list<int>> $fires */
            $fires = [];
            foreach ($schedules as $index => $schedule) {
                if (!\Cronwhen\Fires::matchesDay($schedule, $day)) {
                    continue;
                }

                foreach ($minutesBySchedule[$index] as $minuteOfDay) {
                    $minute = ($day * 1440) + $minuteOfDay;
                    if ($minute < $from || $minute >= $end) {
                        continue;
                    }
                    $fires[$minute][] = $index + 1;
                }
            }

            $timestamps = array_keys($fires);
            sort($timestamps, SORT_NUMERIC);
            foreach ($timestamps as $timestamp) {
                $scheduleNumbers = $fires[$timestamp];
                if (count($scheduleNumbers) < 2) {
                    continue;
                }
                sort($scheduleNumbers, SORT_NUMERIC);

                return [
                    'status' => 0,
                    'out' => \Cronwhen\Calendar::format($timestamp) . ' #'
                        . implode(',', $scheduleNumbers) . "\n",
                    'err' => '',
                ];
            }
        }

        return ['status' => 1, 'out' => "no clash before 2100\n", 'err' => ''];
    }
}
