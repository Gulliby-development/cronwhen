<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NamesRowsTest extends TestCase
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
            [["check"], "0 0 * jan *\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0 0 * JAN *\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0 0 * january *\n", 1, "", "cronwhen: line 1: bad month january\n"],
            [["check"], "0 0 * jan-mar *\n", 0, "ok 1 schedule\n", ""],
            [["check"], "0 0 * mar-jan *\n", 1, "", "cronwhen: line 1: bad month mar-jan\n"],
            [["check"], "0 0 * * sat-sun\n", 1, "", "cronwhen: line 1: bad weekday sat-sun\n"],
            [["check"], "0 0 * * mon/2\n", 0, "ok 1 schedule\n", ""],
        ];
    }
}
