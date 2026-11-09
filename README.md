<p align="center"><img src="docs/logo.svg" width="96" alt="" /></p>

# cronwhen

[![CI](https://github.com/Gulliby-development/cronwhen/actions/workflows/ci.yml/badge.svg)](https://github.com/Gulliby-development/cronwhen/actions/workflows/ci.yml) [![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

cronwhen is a small, deterministic command-line inspector for five-field cron schedules. It helps operators review a schedule list before deploying it: validate it, see its expanded values, find upcoming firing minutes, count firings in a time window, and find the first simultaneous firing. It is suitable for terminal use, scripts, and CI. It uses PHP 8.4 and the standard library only; it needs neither network access nor the system clock.

## Run it

From the repository directory, run a command with PHP:

```text
php bin/cronwhen COMMAND
```

Each command reads schedules from standard input. On Windows, macOS, and Linux, use the PHP invocation above; no POSIX shell is required by the program. The examples below use a POSIX shell in the repository directory. In them, `cronwhen` means this shell function, not a globally installed executable:

```sh
cronwhen() { php bin/cronwhen "$@"; }
```

The supplied transcript assumes that function has been defined. The function forwards every argument to the repository program.

## Command synopsis

```text
usage: cronwhen check
       cronwhen fields
       cronwhen next --from TIME [--count N]
       cronwhen count --from TIME --to TIME
       cronwhen clash --from TIME
```

The first word selects a command. `help`, `-h`, and `--help` as the first word print the usage and exit successfully, ignoring later words. `--version` prints `cronwhen 0.1.0` and exits successfully. With no command, usage is written to standard error and the status is 2. Another first word reports `cronwhen: unknown command WORD`.

The program validates all command-line words before reading standard input. The entry point reads stdin only for a valid invocation of one of the five commands; help, version, and invalid command lines do not consume it.

## Schedule input

Provide one schedule per line. A schedule has exactly five fields in this order: minute, hour, day of month, month, weekday. Fields are separated by one or more spaces or tabs; leading and trailing spaces or tabs are ignored. LF separates lines, one trailing CR on a line is removed, and a final line without LF is still a line. Empty input has no lines.

A line containing only spaces and tabs is blank. A line whose first word begins with `#` is a comment. Both are skipped, but still count toward the maximum of 20 input lines. More than 20 lines is an error, even if the extra lines are blank or comments. Schedule numbers count only actual schedules in their input order. The line labels printed by `fields` instead retain original input line numbers, including skipped lines.

Each field is a comma-separated set of items. An item is a value, a range, a wildcard, or one of those bases followed by a step:

- A value is one or two decimal digits. Month and weekday fields also accept three-letter names in any case: months `jan` through `dec`, weekdays `sun` through `sat`.
- A range is `A-B`, inclusive and ascending; it cannot wrap around.
- `*` means the whole field range. `*/S` steps through that range from its lowest value. `A-B/S` steps from A through B, and `A/S` steps from A through the field maximum. A lone `A` without a step selects only A.
- Values from all items are combined, duplicates removed, then sorted. Weekday 7 is equivalent to Sunday (0); normalized weekday values are 0 through 6.

The valid ranges are minute 0–59, hour 0–23, day 1–31, month 1–12, and weekday 0–7. Numeric values may have a leading zero when at most two digits (for example, `05`); three-digit values, signs, empty list items, invalid names, out-of-range values, descending ranges, and steps outside 1 through that field's maximum are invalid.

### Day matching and calendar

All times are UTC and use the Gregorian calendar. A schedule matches its minute, hour, month, and day conditions. If both the day-of-month and weekday fields do not begin with `*`, either day condition may match (OR). Otherwise both must match (AND). This is based on the first character of each field: for example, `*/2` begins with `*`. Invalid calendar dates never fire, so a schedule such as `30 2 31 2 *` never fires.

TIME is exactly `YYYY-MM-DDTHH:MM`, with ASCII digits and the uppercase `T\). It must be a real Gregorian date and time from 1970-01-01 through 2099-12-31, with hours 00–23 and minutes 00–59. The exclusive search boundary is 2100-01-01T00:00. Output timestamps use `YYYY-MM-DD HH:MM` (a space instead of `T`). The `--count` value is decimal digits without a sign or leading zero, from 1 to 50; `next` defaults to 5.

## Commands and results

- **`check`** validates the complete input and prints `ok N schedule(s)`. Zero schedules prints `ok 0 schedules`.
- **`fields`** prints one line per schedule: `line N minute SET hour SET day SET month SET weekday SET`. Sets are ascending numeric values; full ranges print as `*`, runs of three or more consecutive values use `A-B`, and shorter runs list values separately. Weekdays print normalized as 0–6. Empty input prints nothing.
- **`next`** requires `--from TIME`; optional `--count N\) chooses how many firing minutes to list. FROM itself is excluded. Each result is `YYYY-MM-DD HH:MM #A,B`, with all firing schedule numbers merged and ascending. If fewer than requested are found before 2100, it prints the results found followed by `only K before 2100` and exits 1. Empty or never-firing schedules yield `only 0 before 2100` and status 1.
- **`count`** requires `--from TIME --to TIME`, with TO later than FROM. It counts the half-open interval [FROM, TO): FROM is included and TO excluded. It prints `#N K fire(s)` for every schedule, then `total K fire(s)`; it exits 0, including for an empty list.
- **`clash`** requires `--from TIME`. FROM is included. It prints the earliest minute before 2100 shared by at least two schedules, listing every schedule firing then in ascending order. If none exists (including with zero or one schedule), it prints `no clash before 2100` and exits 1.

For count nouns, exactly 1 uses the singular (`1 fire`, `1 schedule`); every other count uses the plural (`0 fires`, `2 schedules`).

## Errors and exit status

A successful command writes its result to standard output and nothing to standard error. The `next` and `clash` exhaustion messages are standard output, not errors. A failure writes no standard output and one newline-terminated `cronwhen: MESSAGE` line to standard error.

Exit status 0 means success, 1 means the schedule input is invalid or a search is exhausted/no clash exists, and 2 means the command line is invalid. Input errors include:

- `cronwhen: input is too long (max 20 lines)`
- `cronwhen: line N: expected 5 fields`
- `cronwhen: line N: bad FIELD WORD`

Here N is the one-based input line number, FIELD is `minute`, `hour`, `day`, `month`, or `weekday`, and WORD is the invalid field exactly as supplied.

Command-line errors include:

- `cronwhen: unknown command WORD`
- `cronwhen: unexpected argument WORD`
- `cronwhen: unknown option WORD`
- `cronwhen: option OPTION is not valid for COMMAND`
- `cronwhen: given twice: OPTION`
- `cronwhen: missing value for OPTION`
- `cronwhen: bad value for OPTION`
- `cronwhen: missing --from`
- `cronwhen: missing --to`
- `cronwhen: --to is not after --from`

Arguments are checked left to right; the first violation wins and stdin is not read. A non-option word (including an empty word or exactly `-`) is unexpected. An unrecognized option is reported as unknown; a recognized option not accepted by that command is reported as invalid for the command. For a recognized and accepted option, duplicate detection precedes missing-value and bad-value checks. An option consumes the next word as its value even when it resembles an option. After argument scanning, required options are checked in order: FROM, then TO, then whether TO is after FROM.

## Development

Run the repository checks from the repository directory:

```text
php vendor/bin/pint --test
php vendor/bin/phpstan analyse --no-progress
php vendor/bin/phpunit
```

The implementation separates command-line validation and dispatch (`Cli`), line and schedule parsing (`Input` and `Cron`), Gregorian time calculations (`Calendar`), and command handlers under `src/Commands/`. The supplied scaffold and its dependency/configuration files are retained; the application itself uses PHP's standard library only.

## Examples

The following transcript is from a POSIX shell in the repository directory, with the `cronwhen` function defined as shown above:

```text
$ printf '0 9 * * mon-fri\n*/15 * * * *\n' | cronwhen fields
line 1 minute 0 hour 9 day * month * weekday 1-5
line 2 minute 0,15,30,45 hour * day * month * weekday *
$ printf '0 9 * * mon-fri\n*/20 9 * * *\n' | cronwhen next --from 2026-11-04T08:50 --count 4
2026-11-04 09:00 #1,2
2026-11-04 09:20 #2
2026-11-04 09:40 #2
2026-11-05 09:00 #1,2
$ printf '0 0 1 * *\n0 0 * * mon\n' | cronwhen count --from 2026-11-01T00:00 --to 2026-12-01T00:00; printf '0 12 * * *\n30 12 * * *\n' | cronwhen clash --from 2026-11-04T00:00
#1 1 fire
#2 5 fires
total 6 fires
no clash before 2100
$ printf '0 9 * * *\n61 9 * * *\n' | cronwhen check 2>&1; printf '0 0 * * *\n' | cronwhen count --from 2026-11-04T00:00 --to 2026-11-04T00:00 2>&1
cronwhen: line 2: bad minute 61
cronwhen: --to is not after --from
```

---

Built with [BAKED](https://github.com/Gulliby-development/baked), an open-source coding agent (bench round R99).
