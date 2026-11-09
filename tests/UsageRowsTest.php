<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UsageRowsTest extends TestCase
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
            [[], "", 2, "", "usage: cronwhen check\n       cronwhen fields\n       cronwhen next --from TIME [--count N]\n       cronwhen count --from TIME --to TIME\n       cronwhen clash --from TIME\n"],
            [["help"], "", 0, "usage: cronwhen check\n       cronwhen fields\n       cronwhen next --from TIME [--count N]\n       cronwhen count --from TIME --to TIME\n       cronwhen clash --from TIME\n", ""],
            [["-h"], "", 0, "usage: cronwhen check\n       cronwhen fields\n       cronwhen next --from TIME [--count N]\n       cronwhen count --from TIME --to TIME\n       cronwhen clash --from TIME\n", ""],
            [["--help", "frob"], "ignored\n", 0, "usage: cronwhen check\n       cronwhen fields\n       cronwhen next --from TIME [--count N]\n       cronwhen count --from TIME --to TIME\n       cronwhen clash --from TIME\n", ""],
            [["--version"], "", 0, "cronwhen 0.1.0\n", ""],
            [["frob"], "", 2, "", "cronwhen: unknown command frob\n"],
            [[""], "", 2, "", "cronwhen: unknown command \n"],
        ];
    }
}
