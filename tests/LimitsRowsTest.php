<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LimitsRowsTest extends TestCase
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
            [["check"], "0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n", 0, "ok 20 schedules\n", ""],
            [["check"], "0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n", 1, "", "cronwhen: input is too long (max 20 lines)\n"],
            [["check"], "# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n# c\n0 0 * * *\n", 1, "", "cronwhen: input is too long (max 20 lines)\n"],
            [["check"], "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", 1, "", "cronwhen: input is too long (max 20 lines)\n"],
            [["clash", "--from", "2026-11-04T09:00"], "0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n", 1, "", "cronwhen: input is too long (max 20 lines)\n"],
            [["count", "--from", "2026-11-04T09:00", "--to", "2026-11-05T00:00"], "0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n0 0 * * *\n", 0, "#1 0 fires\n#2 0 fires\n#3 0 fires\n#4 0 fires\n#5 0 fires\n#6 0 fires\n#7 0 fires\n#8 0 fires\n#9 0 fires\n#10 0 fires\n#11 0 fires\n#12 0 fires\n#13 0 fires\n#14 0 fires\n#15 0 fires\n#16 0 fires\n#17 0 fires\n#18 0 fires\n#19 0 fires\n#20 0 fires\ntotal 0 fires\n", ""],
        ];
    }
}
