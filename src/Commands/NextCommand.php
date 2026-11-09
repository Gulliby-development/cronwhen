<?php

declare(strict_types=1);

namespace Cronwhen\Commands;

/**
 * @phpstan-import-type Options from \Cronwhen\Cli
 * @phpstan-import-type Reply from \Cronwhen\Cli
 * @phpstan-import-type Schedule from \Cronwhen\Input
 */
final class NextCommand
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
        $count = $options['count'];
        if ($schedules === []) {
            return ['status' => 1, 'out' => "only 0 before 2100\n", 'err' => ''];
        }

        $end = \Cronwhen\Calendar::end();
        $endDay = intdiv($end, 1440);
        $minutesBySchedule = [];
        foreach ($schedules as $schedule) {
            $minutesBySchedule[] = \Cronwhen\Fires::minutesOfDay($schedule);
        }

        $output = '';
        $found = 0;
        $firstDay = intdiv($from, 1440);
        for ($day = $firstDay; $day < $endDay && $found < $count; $day++) {
            /** @var array<int, list<int>> $fires */
            $fires = [];
            foreach ($schedules as $index => $schedule) {
                if (!\Cronwhen\Fires::matchesDay($schedule, $day)) {
                    continue;
                }

                foreach ($minutesBySchedule[$index] as $minuteOfDay) {
                    $minute = ($day * 1440) + $minuteOfDay;
                    if ($minute <= $from || $minute >= $end) {
                        continue;
                    }
                    $fires[$minute][] = $index + 1;
                }
            }

            $timestamps = array_keys($fires);
            sort($timestamps, SORT_NUMERIC);
            foreach ($timestamps as $timestamp) {
                $scheduleNumbers = $fires[$timestamp];
                sort($scheduleNumbers, SORT_NUMERIC);
                $output .= \Cronwhen\Calendar::format($timestamp) . ' #'
                    . implode(',', $scheduleNumbers) . "\n";
                $found++;
                if ($found === $count) {
                    break;
                }
            }
        }

        if ($found < $count) {
            $output .= 'only ' . $found . " before 2100\n";

            return ['status' => 1, 'out' => $output, 'err' => ''];
        }

        return ['status' => 0, 'out' => $output, 'err' => ''];
    }
}
