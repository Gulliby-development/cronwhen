<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OptionsRowsTest extends TestCase
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
            [["check", "--from", "2026-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: option --from is not valid for check\n"],
            [["fields", "--count", "3"], "0 0 * * *\n", 2, "", "cronwhen: option --count is not valid for fields\n"],
            [["clash", "--count", "2", "--from", "2026-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: option --count is not valid for clash\n"],
            [["next", "--nope", "x"], "0 0 * * *\n", 2, "", "cronwhen: unknown option --nope\n"],
            [["next", "-"], "0 0 * * *\n", 2, "", "cronwhen: unexpected argument -\n"],
            [["next", ""], "0 0 * * *\n", 2, "", "cronwhen: unexpected argument \n"],
            [["next", "--from", "2026-11-04T09:00", "--from", "2026-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: given twice: --from\n"],
            [["next", "--from"], "0 0 * * *\n", 2, "", "cronwhen: missing value for --from\n"],
            [["count", "--from", "2026-11-04T09:00", "--to", "2026-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: --to is not after --from\n"],
            [["count", "--from", "2026-11-05T00:00", "--to", "2026-11-04T09:00"], "0 0 * * *\n", 2, "", "cronwhen: --to is not after --from\n"],
        ];
    }
}
