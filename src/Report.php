<?php

declare(strict_types=1);

namespace Cronwhen;

final class Report
{
    public static function plural(int $count, string $noun): string
    {
        return $count . ' ' . $noun . ($count === 1 ? '' : 's');
    }
}
