<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EdgesRowsTest extends TestCase
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
            [["next", "--from", "2099-12-31T23:58", "--count", "3"], "59 23 * * *\n", 1, "2099-12-31 23:59 #1\nonly 1 before 2100\n", ""],
            [["next", "--from", "2099-12-31T00:00", "--count", "5"], "0 0 * * *\n59 23 * * *\n", 1, "2099-12-31 23:59 #2\nonly 1 before 2100\n", ""],
            [["next", "--from", "2099-12-31T23:00", "--count", "3"], "* * * * *\n", 0, "2099-12-31 23:01 #1\n2099-12-31 23:02 #1\n2099-12-31 23:03 #1\n", ""],
            [["next", "--from", "2099-12-31T23:57", "--count", "5"], "* * * * *\n", 1, "2099-12-31 23:58 #1\n2099-12-31 23:59 #1\nonly 2 before 2100\n", ""],
            [["next", "--from", "2096-01-01T00:00", "--count", "3"], "0 0 29 2 *\n", 1, "2096-02-29 00:00 #1\nonly 1 before 2100\n", ""],
            [["next", "--from", "2099-02-01T00:00", "--count", "3"], "0 0 29 2 *\n", 1, "only 0 before 2100\n", ""],
            [["next", "--from", "1970-01-01T00:00", "--count", "1"], "30 2 31 4 *\n", 1, "only 0 before 2100\n", ""],
            [["count", "--from", "2099-12-31T23:58", "--to", "2099-12-31T23:59"], "* * * * *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
            [["count", "--from", "2099-12-31T00:00", "--to", "2099-12-31T23:59"], "59 23 * * *\n", 0, "#1 0 fires\ntotal 0 fires\n", ""],
            [["count", "--from", "1970-01-01T00:00", "--to", "2099-12-31T23:59"], "0 0 29 2 *\n", 0, "#1 32 fires\ntotal 32 fires\n", ""],
            [["clash", "--from", "1970-01-01T00:00"], "0 0 29 2 *\n0 0 29 2 *\n", 0, "1972-02-29 00:00 #1,2\n", ""],
            [["clash", "--from", "2099-12-31T23:58"], "* * * * *\n58-59 23 * * *\n", 0, "2099-12-31 23:58 #1,2\n", ""],
        ];
    }
}
