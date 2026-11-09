<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SyntaxRowsTest extends TestCase
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
            [["check"], "* * * * *\n", 0, "ok 1 schedule\n", ""],
            [["check"], "59 23 31 12 7\n", 0, "ok 1 schedule\n", ""],
            [["check"], "60 0 * * *\n", 1, "", "cronwhen: line 1: bad minute 60\n"],
            [["check"], "0 0 0 * *\n", 1, "", "cronwhen: line 1: bad day 0\n"],
            [["check"], "00 00 01 01 00\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0,,15 0 * * *\n", 1, "", "cronwhen: line 1: bad minute 0,,15\n"],
            [["check"], "5-1 0 * * *\n", 1, "", "cronwhen: line 1: bad minute 5-1\n"],
            [["check"], "*-5 0 * * *\n", 1, "", "cronwhen: line 1: bad minute *-5\n"],
            [["check"], "*/60 0 * * *\n", 1, "", "cronwhen: line 1: bad minute */60\n"],
            [["check"], "10/5 0 * * *\n", 0, "ok 1 schedule\n", ""],
            [["check"], "5/0 0 * * *\n", 1, "", "cronwhen: line 1: bad minute 5/0\n"],
            [["check"], "x 0 * * *\n", 1, "", "cronwhen: line 1: bad minute x\n"],
            [["check"], "0 0 31-1 * *\n", 1, "", "cronwhen: line 1: bad day 31-1\n"],
            [["check"], "0 0 * * 6-0\n", 1, "", "cronwhen: line 1: bad weekday 6-0\n"],
        ];
    }
}
