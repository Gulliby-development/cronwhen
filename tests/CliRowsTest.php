<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CliRowsTest extends TestCase
{
    /**
     * @param list<string> $args
     */
    #[DataProvider('rows')]
    public function testRow(array $args, string $input, int $status, string $out, string $err): void
    {
        Harness::assertReply($args, $input, $status, $out, $err);
    }

    /**
     * @return list<array{list<string>, string, int, string, string}>
     */
    public static function rows(): array
    {
        return [
            [["check"], "0 9 * * mon-fri\n*/15 * * * *\n", 0, "ok 2 schedules\n", ""],
            [["check", "x"], "0 0 * * *\n", 2, "", "cronwhen: unexpected argument x\n"],
            [["next"], "0 0 * * *\n", 2, "", "cronwhen: missing --from\n"],
            [["check"], "frob\n", 1, "", "cronwhen: line 1: expected 5 fields\n"],
            [["next", "--from", "2026-11-04T09:00"], "0 0 * *\n", 1, "", "cronwhen: line 1: expected 5 fields\n"],
            [["fields"], "0 9 * * *\n", 0, "line 1 minute 0 hour 9 day * month * weekday *\n", ""],
            [["next", "--from", "2026-11-04T09:00", "--count", "2"], "0 12 * * *\n", 0, "2026-11-04 12:00 #1\n2026-11-05 12:00 #1\n", ""],
            [["count", "--from", "2026-11-01T00:00", "--to", "2026-11-02T00:00"], "0 12 * * *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "30 8 * * *\n30 8 * * 1-5\n", 0, "2026-11-05 08:30 #1,2\n", ""],
        ];
    }
}
