<?php

declare(strict_types=1);

namespace Cronwhen\Commands;

/**
 * @phpstan-import-type Options from \Cronwhen\Cli
 * @phpstan-import-type Reply from \Cronwhen\Cli
 * @phpstan-import-type Schedule from \Cronwhen\Input
 */
final class CheckCommand
{
    /**
     * @param Options $options
     * @param list<Schedule> $schedules
     * @return Reply
     */
    public static function run(array $options, array $schedules): array
    {
        return [
            'status' => 0,
            'out' => 'ok ' . \Cronwhen\Report::plural(count($schedules), 'schedule') . "\n",
            'err' => '',
        ];
    }
}
