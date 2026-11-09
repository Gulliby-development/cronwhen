<?php

declare(strict_types=1);

namespace Cronwhen;

/**
 * @phpstan-type Schedule array{line: int, minute: list<int>, hour: list<int>, day: list<int>, month: list<int>, weekday: list<int>, dayRestricted: bool, weekdayRestricted: bool}
 */
final class Input
{
    /**
     * @return list<Schedule>
     */
    public static function read(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $lines = explode("\n", $text);
        if (str_ends_with($text, "\n")) {
            array_pop($lines);
        }
        if (count($lines) > Registry::MAX_LINES) {
            throw new Failure(1, 'input is too long (max ' . Registry::MAX_LINES . ' lines)');
        }

        $schedules = [];
        foreach ($lines as $index => $line) {
            if (str_ends_with($line, "\r")) {
                $line = substr($line, 0, -1);
            }
            $trimmed = trim($line, " \t");
            if ($trimmed === '') {
                continue;
            }

            $words = preg_split('/[ \t]+/', $trimmed);
            if ($words === false) {
                continue;
            }
            if (str_starts_with($words[0], '#')) {
                continue;
            }
            if (count($words) !== count(Registry::FIELDS)) {
                throw new Failure(1, 'line ' . ($index + 1) . ': expected 5 fields');
            }

            $schedule = [
                'line' => $index + 1,
                'minute' => [],
                'hour' => [],
                'day' => [],
                'month' => [],
                'weekday' => [],
                'dayRestricted' => false,
                'weekdayRestricted' => false,
            ];
            foreach (Registry::FIELDS as $fieldIndex => $field) {
                $word = $words[$fieldIndex];
                $values = Cron::field($word, $field);
                if ($values === null) {
                    throw new Failure(1, 'line ' . ($index + 1) . ': bad ' . $field['name'] . ' ' . $word);
                }
                switch ($fieldIndex) {
                    case 0:
                        $schedule['minute'] = $values;
                        break;
                    case 1:
                        $schedule['hour'] = $values;
                        break;
                    case 2:
                        $schedule['day'] = $values;
                        $schedule['dayRestricted'] = $word[0] !== '*';
                        break;
                    case 3:
                        $schedule['month'] = $values;
                        break;
                    case 4:
                        $schedule['weekday'] = $values;
                        $schedule['weekdayRestricted'] = $word[0] !== '*';
                        break;
                }
            }
            $schedules[] = $schedule;
        }

        return $schedules;
    }
}
