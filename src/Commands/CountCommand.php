<?php

declare(strict_types=1);

namespace Cronwhen\Commands;

use Cronwhen\Fires;
use Cronwhen\Report;

/**
 * @phpstan-import-type Options from \Cronwhen\Cli
 * @phpstan-import-type Reply from \Cronwhen\Cli
 * @phpstan-import-type Schedule from \Cronwhen\Input
 */
final class CountCommand
{
    private const MINUTES_PER_DAY = 1440;

    /**
     * @param Options $options
     * @param list<Schedule> $schedules
     * @return Reply
     */
    public static function run(array $options, array $schedules): array
    {
        assert($options['from'] !== null);
        assert($options['to'] !== null);
        $from = $options['from'];
        $to = $options['to'];
        $output = '';
        $total = 0;
        $firstDay = intdiv($from, self::MINUTES_PER_DAY);
        $lastDay = intdiv($to - 1, self::MINUTES_PER_DAY);

        foreach ($schedules as $index => $schedule) {
            $minutesOfDay = Fires::minutesOfDay($schedule);
            $count = 0;
            for ($day = $firstDay; $day <= $lastDay; $day++) {
                if (!Fires::matchesDay($schedule, $day)) {
                    continue;
                }

                $dayStart = $day * self::MINUTES_PER_DAY;
                if ($dayStart >= $from && $dayStart + self::MINUTES_PER_DAY <= $to) {
                    $count += count($minutesOfDay);
                    continue;
                }

                foreach ($minutesOfDay as $minuteOfDay) {
                    $minute = $dayStart + $minuteOfDay;
                    if ($minute >= $from && $minute < $to) {
                        $count++;
                    }
                }
            }

            $output .= '#' . ($index + 1) . ' ' . Report::plural($count, 'fire') . "\n";
            $total += $count;
        }

        $output .= 'total ' . Report::plural($total, 'fire') . "\n";

        return ['status' => 0, 'out' => $output, 'err' => ''];
    }
}
