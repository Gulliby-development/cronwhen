<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReadsRowsTest extends TestCase
{
    /**
     * @param list<string> $args
     */
    #[DataProvider('rows')]
    public function testRow(array $args, string $input, int $status, string $out, string $err): void
    {
        Harness::assertReads($args, $status);
    }

    /**
     * @return list<array{list<string>, string, int, string, string}>
     */
    public static function rows(): array
    {
        return [
            [["check"], "", 1, "", ""],
            [["next", "--from", "2026-11-04T09:00"], "", 1, "", ""],
            [["count", "--from", "2026-11-04T09:00", "--to", "2026-11-05T00:00"], "", 1, "", ""],
            [["check", "x"], "", 0, "", ""],
            [["next"], "", 0, "", ""],
            [["next", "--from", "2026-11-04T09:00", "--count", "50"], "", 1, "", ""],
            [["count", "--from", "2026-11-04T09:00"], "", 0, "", ""],
        ];
    }
}
