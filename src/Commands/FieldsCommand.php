<?php

declare(strict_types=1);

namespace Cronwhen\Commands;

/**
 * @phpstan-import-type Options from \Cronwhen\Cli
 * @phpstan-import-type Reply from \Cronwhen\Cli
 * @phpstan-import-type Schedule from \Cronwhen\Input
 */
final class FieldsCommand
{
    /**
     * @param Options $options
     * @param list<Schedule> $schedules
     * @return Reply
     */
    public static function run(array $options, array $schedules): array
    {
        $output = '';
        foreach ($schedules as $schedule) {
            $output .= 'line ' . $schedule['line']
                . ' minute ' . self::formatSet($schedule['minute'], 0, 59)
                . ' hour ' . self::formatSet($schedule['hour'], 0, 23)
                . ' day ' . self::formatSet($schedule['day'], 1, 31)
                . ' month ' . self::formatSet($schedule['month'], 1, 12)
                . ' weekday ' . self::formatSet($schedule['weekday'], 0, 6)
                . "\n";
        }

        return ['status' => 0, 'out' => $output, 'err' => ''];
    }

    /**
     * @param list<int> $values
     */
    private static function formatSet(array $values, int $low, int $high): string
    {
        if ($values === range($low, $high)) {
            return '*';
        }

        $parts = [];
        $start = $values[0];
        $previous = $start;
        foreach (array_slice($values, 1) as $value) {
            if ($value === $previous + 1) {
                $previous = $value;
                continue;
            }

            $parts[] = self::formatRun($start, $previous);
            $start = $value;
            $previous = $value;
        }
        $parts[] = self::formatRun($start, $previous);

        return implode(',', $parts);
    }

    private static function formatRun(int $start, int $end): string
    {
        if ($end - $start >= 2) {
            return $start . '-' . $end;
        }

        if ($start === $end) {
            return (string) $start;
        }

        return $start . ',' . $end;
    }
}
