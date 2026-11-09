<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ClashRowsTest extends TestCase
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
            [["clash", "--from", "2026-11-04T09:00"], "30 8 * * *\n30 8 * * 1-5\n", 0, "2026-11-05 08:30 #1,2\n", ""],
            [["clash", "--from", "2026-11-07T00:00"], "30 8 * * *\n30 8 * * 1-5\n", 0, "2026-11-09 08:30 #1,2\n", ""],
            [["clash", "--from", "2026-11-07T08:30"], "30 8 * * *\n30 8 * * 1-5\n", 0, "2026-11-09 08:30 #1,2\n", ""],
            [["clash", "--from", "2026-11-07T08:31"], "30 8 * * *\n30 8 * * 1-5\n", 0, "2026-11-09 08:30 #1,2\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "", 1, "no clash before 2100\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "0 0 * * *\n", 1, "no clash before 2100\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "0 0 * * *\n0 0 * * *\n", 0, "2026-11-05 00:00 #1,2\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "0 9 * * *\n0 10 * * *\n", 1, "no clash before 2100\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "*/15 * * * *\n*/20 * * * *\n", 0, "2026-11-04 09:00 #1,2\n", ""],
            [["clash", "--from", "2026-11-04T09:01"], "*/15 * * * *\n*/20 * * * *\n", 0, "2026-11-04 10:00 #1,2\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "0 0 * * *\n0 0 * * *\n0 0 * * *\n", 0, "2026-11-05 00:00 #1,2,3\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "30 2 31 2 *\n30 2 * * *\n", 1, "no clash before 2100\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "0 0 1 * *\n0 0 * * mon\n", 0, "2027-02-01 00:00 #1,2\n", ""],
            [["clash", "--from", "2099-12-31T23:59"], "59 23 * * *\n59 23 31 12 *\n", 0, "2099-12-31 23:59 #1,2\n", ""],
            [["clash", "--from", "2026-11-04T09:00"], "# x\n\n0 3 * * *\n\n0 3 * * *\n", 0, "2026-11-05 03:00 #1,2\n", ""],
        ];
    }
}
