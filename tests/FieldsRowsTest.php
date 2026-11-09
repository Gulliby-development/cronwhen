<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FieldsRowsTest extends TestCase
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
            [["fields"], "* * * * *\n", 0, "line 1 minute * hour * day * month * weekday *\n", ""],
            [["fields"], "59 23 31 12 7\n", 0, "line 1 minute 59 hour 23 day 31 month 12 weekday 0\n", ""],
            [["fields"], "0,15,30,45 9-17 * * 1-5\n", 0, "line 1 minute 0,15,30,45 hour 9-17 day * month * weekday 1-5\n", ""],
            [["fields"], "*/15 */6 */10 */3 */2\n", 0, "line 1 minute 0,15,30,45 hour 0,6,12,18 day 1,11,21,31 month 1,4,7,10 weekday 0,2,4,6\n", ""],
            [["fields"], "10/20 5/9 25/3 11/1 3/2\n", 0, "line 1 minute 10,30,50 hour 5,14,23 day 25,28,31 month 11,12 weekday 0,3,5\n", ""],
            [["fields"], "10-20/5 2-10/4 1-31/10 1-12/4 1-5/2\n", 0, "line 1 minute 10,15,20 hour 2,6,10 day 1,11,21,31 month 1,5,9 weekday 1,3,5\n", ""],
            [["fields"], "5 4 * * sun,sat\n", 0, "line 1 minute 5 hour 4 day * month * weekday 0,6\n", ""],
            [["fields"], "0 0 1,2,3 jan,feb,mar mon,tue,wed\n", 0, "line 1 minute 0 hour 0 day 1-3 month 1-3 weekday 1-3\n", ""],
            [["fields"], "0 0 * * 0-7\n", 0, "line 1 minute 0 hour 0 day * month * weekday *\n", ""],
            [["fields"], "0 0 * * 7\n", 0, "line 1 minute 0 hour 0 day * month * weekday 0\n", ""],
            [["fields"], "0 0 * * */3\n", 0, "line 1 minute 0 hour 0 day * month * weekday 0,3,6\n", ""],
            [["fields"], "0 0 * * 1/3\n", 0, "line 1 minute 0 hour 0 day * month * weekday 0,1,4\n", ""],
            [["fields"], "0 0 * * mon/2\n", 0, "line 1 minute 0 hour 0 day * month * weekday 0,1,3,5\n", ""],
            [["fields"], "00 08 09 01 00\n", 0, "line 1 minute 0 hour 8 day 9 month 1 weekday 0\n", ""],
            [["fields"], "# c\n\n0 0 * * *\n\n5 5 5 5 5\n", 0, "line 3 minute 0 hour 0 day * month * weekday *\nline 5 minute 5 hour 5 day 5 month 5 weekday 5\n", ""],
        ];
    }
}
