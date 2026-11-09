<?php

declare(strict_types=1);

namespace Cronwhen;

final class Cron
{
    /**
     * @param array{name: string, low: int, high: int, names: list<string>} $field
     *
     * @return list<int>|null
     */
    public static function field(string $word, array $field): ?array
    {
        $values = [];
        $items = explode(',', $word);

        foreach ($items as $item) {
            if ($item === '') {
                return null;
            }

            $parts = explode('/', $item);
            if (count($parts) > 2) {
                return null;
            }

            $base = $parts[0];
            $step = 1;
            $hasStep = count($parts) === 2;
            if ($hasStep) {
                if (!preg_match('/\\A[0-9]{1,2}\\z/', $parts[1])) {
                    return null;
                }
                $step = (int) $parts[1];
                if ($step < 1 || $step > $field['high']) {
                    return null;
                }
            }

            if ($base === '*') {
                $start = $field['low'];
                $end = $field['high'];
            } else {
                $range = explode('-', $base);
                if (count($range) > 2 || $range[0] === '') {
                    return null;
                }
                $start = self::value($range[0], $field);
                if ($start === null) {
                    return null;
                }

                if (count($range) === 2) {
                    $end = self::value($range[1], $field);
                    if ($end === null || $start > $end) {
                        return null;
                    }
                } elseif ($hasStep) {
                    $end = $field['high'];
                } else {
                    $end = $start;
                }
            }

            for ($value = $start; $value <= $end; $value += $step) {
                $values[] = $value;
            }
        }

        if ($field['name'] === 'weekday') {
            $values = array_map(static fn (int $value): int => $value % 7, $values);
        }

        $values = array_values(array_unique($values));
        sort($values, SORT_NUMERIC);

        return $values;
    }

    /**
     * @param array{name: string, low: int, high: int, names: list<string>} $field
     */
    private static function value(string $word, array $field): ?int
    {
        if (preg_match('/\\A[0-9]{1,2}\\z/', $word)) {
            $value = (int) $word;
        } else {
            if (!preg_match('/\\A[A-Za-z]{3}\\z/', $word)) {
                return null;
            }
            $name = strtolower($word);
            $index = array_search($name, $field['names'], true);
            if ($index === false) {
                return null;
            }
            $value = $field['low'] + $index;
        }

        if ($value < $field['low'] || $value > $field['high']) {
            return null;
        }

        return $value;
    }
}
