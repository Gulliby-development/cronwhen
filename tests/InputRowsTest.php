<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InputRowsTest extends TestCase
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
            [["check"], "", 0, "ok 0 schedules\n", ""],
            [["check"], "\n", 0, "ok 0 schedules\n", ""],
            [["check"], "# note\n", 0, "ok 0 schedules\n", ""],
            [["check"], "0 0 * * *", 0, "ok 1 schedule\n", ""],
            [["check"], "0 0 * * *\r\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0\t0\t*\t*\t*\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0 0 * *\n", 1, "", "cronwhen: line 1: expected 5 fields\n"],
            [["check"], "0 0 * * *\n0 0 * *\n", 1, "", "cronwhen: line 2: expected 5 fields\n"],
            [["check"], "# c\n\n0 0 * * *\n61 0 * * *\n", 1, "", "cronwhen: line 4: bad minute 61\n"],
            [["check"], "0 0 * * *#x\n", 1, "", "cronwhen: line 1: bad weekday *#x\n"],
        ];
    }
}
