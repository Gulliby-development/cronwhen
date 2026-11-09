<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SetsRowsTest extends TestCase
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
            [["fields"], "1,2 0 * * *\n", 0, "line 1 minute 1,2 hour 0 day * month * weekday *\n", ""],
            [["fields"], "1,2,3 0 * * *\n", 0, "line 1 minute 1-3 hour 0 day * month * weekday *\n", ""],
            [["fields"], "1,2,3,4 0 * * *\n", 0, "line 1 minute 1-4 hour 0 day * month * weekday *\n", ""],
            [["fields"], "1-2 0 * * *\n", 0, "line 1 minute 1,2 hour 0 day * month * weekday *\n", ""],
            [["fields"], "1,2,4,5,6 0 * * *\n", 0, "line 1 minute 1,2,4-6 hour 0 day * month * weekday *\n", ""],
            [["fields"], "1,3,5,7 0 * * *\n", 0, "line 1 minute 1,3,5,7 hour 0 day * month * weekday *\n", ""],
            [["fields"], "0-58 0 * * *\n", 0, "line 1 minute 0-58 hour 0 day * month * weekday *\n", ""],
            [["fields"], "0-59/1 0 * * *\n", 0, "line 1 minute * hour 0 day * month * weekday *\n", ""],
            [["fields"], "0 0 2-31 * *\n", 0, "line 1 minute 0 hour 0 day 2-31 month * weekday *\n", ""],
            [["fields"], "0 0 * * 1-6\n", 0, "line 1 minute 0 hour 0 day * month * weekday 1-6\n", ""],
            [["fields"], "0 0 * * 1,2,3,4,5,6,0\n", 0, "line 1 minute 0 hour 0 day * month * weekday *\n", ""],
            [["fields"], "0 0 * * 6,0,1\n", 0, "line 1 minute 0 hour 0 day * month * weekday 0,1,6\n", ""],
            [["fields"], "10-12,20,30-33 0 * * *\n", 0, "line 1 minute 10-12,20,30-33 hour 0 day * month * weekday *\n", ""],
            [["fields"], "*/20 */8 */16 */5 */4\n", 0, "line 1 minute 0,20,40 hour 0,8,16 day 1,17 month 1,6,11 weekday 0,4\n", ""],
        ];
    }
}
