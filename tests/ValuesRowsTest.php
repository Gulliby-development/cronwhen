<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValuesRowsTest extends TestCase
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
            [["next", "--from", "2026-11-04T09:00:00"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", "2026-11-04t09:00"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", "26-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", "2026-13-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", "2026-02-29T09:00"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", ""], "0 0 * * *\n", 2, "", "cronwhen: bad value for --from\n"],
            [["next", "--from", "2026-11-04T09:00", "--count", "51"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --count\n"],
            [["next", "--from", "2026-11-04T09:00", "--count", "-1"], "0 0 * * *\n", 2, "", "cronwhen: bad value for --count\n"],
            [["next", "--from", "2028-02-29T09:00", "--count", "1"], "0 0 * * *\n", 0, "2028-03-01 00:00 #1\n", ""],
            [["next", "--from", "2026-11-04T09:00", "--count", "50"], "0 0 * * *\n", 0, "2026-11-05 00:00 #1\n2026-11-06 00:00 #1\n2026-11-07 00:00 #1\n2026-11-08 00:00 #1\n2026-11-09 00:00 #1\n2026-11-10 00:00 #1\n2026-11-11 00:00 #1\n2026-11-12 00:00 #1\n2026-11-13 00:00 #1\n2026-11-14 00:00 #1\n2026-11-15 00:00 #1\n2026-11-16 00:00 #1\n2026-11-17 00:00 #1\n2026-11-18 00:00 #1\n2026-11-19 00:00 #1\n2026-11-20 00:00 #1\n2026-11-21 00:00 #1\n2026-11-22 00:00 #1\n2026-11-23 00:00 #1\n2026-11-24 00:00 #1\n2026-11-25 00:00 #1\n2026-11-26 00:00 #1\n2026-11-27 00:00 #1\n2026-11-28 00:00 #1\n2026-11-29 00:00 #1\n2026-11-30 00:00 #1\n2026-12-01 00:00 #1\n2026-12-02 00:00 #1\n2026-12-03 00:00 #1\n2026-12-04 00:00 #1\n2026-12-05 00:00 #1\n2026-12-06 00:00 #1\n2026-12-07 00:00 #1\n2026-12-08 00:00 #1\n2026-12-09 00:00 #1\n2026-12-10 00:00 #1\n2026-12-11 00:00 #1\n2026-12-12 00:00 #1\n2026-12-13 00:00 #1\n2026-12-14 00:00 #1\n2026-12-15 00:00 #1\n2026-12-16 00:00 #1\n2026-12-17 00:00 #1\n2026-12-18 00:00 #1\n2026-12-19 00:00 #1\n2026-12-20 00:00 #1\n2026-12-21 00:00 #1\n2026-12-22 00:00 #1\n2026-12-23 00:00 #1\n2026-12-24 00:00 #1\n", ""],
            [["count", "--from", "2026-11-04T09:00", "--to", "2026-11-04T09:01"], "0 0 * * *\n", 0, "#1 0 fires\ntotal 0 fires\n", ""],
        ];
    }
}
