<?php

declare(strict_types=1);

namespace Cronwhen\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CountRowsTest extends TestCase
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
            [["count", "--from", "2026-11-04T12:00", "--to", "2026-11-04T12:00"], "0 12 * * *\n", 2, "", "cronwhen: --to is not after --from\n"],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-05T00:00"], "0 12 * * *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-05T00:00"], "0 9 * * mon-fri\n*/15 * * * *\n", 0, "#1 1 fire\n#2 96 fires\ntotal 97 fires\n", ""],
            [["count", "--from", "2026-11-04T12:00", "--to", "2026-11-04T12:01"], "0 12 * * *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
            [["count", "--from", "2026-11-04T11:59", "--to", "2026-11-04T12:00"], "0 12 * * *\n", 0, "#1 0 fires\ntotal 0 fires\n", ""],
            [["count", "--from", "2026-11-04T11:59", "--to", "2026-11-04T12:01"], "0 12 * * *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
            [["count", "--from", "2026-11-04T12:01", "--to", "2026-11-05T12:00"], "0 12 * * *\n", 0, "#1 0 fires\ntotal 0 fires\n", ""],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-04T01:00"], "* * * * *\n", 0, "#1 60 fires\ntotal 60 fires\n", ""],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-05T00:00"], "* * * * *\n*/5 * * * *\n", 0, "#1 1440 fires\n#2 288 fires\ntotal 1728 fires\n", ""],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-05T00:00"], "30 2 31 2 *\n0 0 * * *\n", 0, "#1 0 fires\n#2 1 fire\ntotal 1 fire\n", ""],
            [["count", "--from", "2026-11-02T00:00", "--to", "2026-11-09T00:00"], "0 9 * * mon-fri\n0 9 * * sat,sun\n", 0, "#1 5 fires\n#2 2 fires\ntotal 7 fires\n", ""],
            [["count", "--from", "2026-01-01T00:00", "--to", "2027-01-01T00:00"], "0 0 * * *\n", 0, "#1 365 fires\ntotal 365 fires\n", ""],
            [["count", "--from", "2026-01-01T00:00", "--to", "2027-01-01T00:00"], "* * * * *\n", 0, "#1 525600 fires\ntotal 525600 fires\n", ""],
            [["count", "--from", "1970-01-01T00:00", "--to", "2099-12-31T23:59"], "0 0 1 1 *\n", 0, "#1 130 fires\ntotal 130 fires\n", ""],
            [["count", "--from", "2026-11-04T00:00", "--to", "2026-11-05T00:00"], "0 0 4 11 *\n", 0, "#1 1 fire\ntotal 1 fire\n", ""],
        ];
    }
}
