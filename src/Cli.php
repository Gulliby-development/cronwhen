<?php

declare(strict_types=1);

namespace Cronwhen;

use Cronwhen\Commands\CheckCommand;
use Cronwhen\Commands\ClashCommand;
use Cronwhen\Commands\CountCommand;
use Cronwhen\Commands\FieldsCommand;
use Cronwhen\Commands\NextCommand;

/**
 * @phpstan-type Reply array{status: int, out: string, err: string}
 * @phpstan-type Options array{from: int|null, to: int|null, count: int}
 * @phpstan-type Validated array{command: string, options: Options}
 */
final class Cli
{
    /**
     * @param list<string> $args
     * @return array{status: int, out: string, err: string}
     */
    public static function run(array $args, string $input): array
    {
        if ($args === []) {
            return ['status' => 2, 'out' => '', 'err' => Registry::USAGE];
        }
        if (in_array($args[0], ['help', '-h', '--help'], true)) {
            return ['status' => 0, 'out' => Registry::USAGE, 'err' => ''];
        }
        if ($args[0] === '--version') {
            return ['status' => 0, 'out' => 'cronwhen ' . Registry::VERSION . "\n", 'err' => ''];
        }

        try {
            $validated = self::validate($args);
            $schedules = Input::read($input);
            $command = $validated['command'];
            $options = $validated['options'];

            return match ($command) {
                'check' => CheckCommand::run($options, $schedules),
                'fields' => FieldsCommand::run($options, $schedules),
                'next' => NextCommand::run($options, $schedules),
                'count' => CountCommand::run($options, $schedules),
                'clash' => ClashCommand::run($options, $schedules),
                default => throw new Failure(2, 'unknown command ' . $command),
            };
        } catch (Failure $failure) {
            return [
                'status' => $failure->status(),
                'out' => '',
                'err' => 'cronwhen: ' . $failure->getMessage() . "\n",
            ];
        }
    }

    /**
     * @param list<string> $args
     */
    public static function readsStdin(array $args): bool
    {
        return !(self::validationResult($args) instanceof Failure);
    }

    /**
     * @param list<string> $args
     * @return Validated|Failure
     */
    private static function validationResult(array $args): array|Failure
    {
        try {
            return self::validate($args);
        } catch (Failure $failure) {
            return $failure;
        }
    }

    /**
     * @param list<string> $args
     * @return Validated
     */
    private static function validate(array $args): array
    {
        if ($args === []) {
            throw new Failure(2, Registry::USAGE);
        }

        $command = $args[0];
        $commandEntry = null;
        foreach (Registry::COMMANDS as $entry) {
            if ($entry['name'] === $command) {
                $commandEntry = $entry;
                break;
            }
        }
        if ($commandEntry === null) {
            throw new Failure(2, 'unknown command ' . $command);
        }

        $options = ['from' => null, 'to' => null, 'count' => Registry::DEFAULT_COUNT];
        $seen = [];
        $length = count($args);
        for ($index = 1; $index < $length; $index++) {
            $word = $args[$index];
            if ($word === '' || $word === '-' || !str_starts_with($word, '-')) {
                throw new Failure(2, 'unexpected argument ' . $word);
            }
            if (!in_array($word, ['--from', '--to', '--count'], true)) {
                throw new Failure(2, 'unknown option ' . $word);
            }
            if (!in_array($word, $commandEntry['options'], true)) {
                throw new Failure(2, 'option ' . $word . ' is not valid for ' . $command);
            }
            if (isset($seen[$word])) {
                throw new Failure(2, 'given twice: ' . $word);
            }
            $seen[$word] = true;
            if ($index + 1 >= $length) {
                throw new Failure(2, 'missing value for ' . $word);
            }
            $value = $args[++$index];
            if ($word === '--from' || $word === '--to') {
                $time = Calendar::parse($value);
                if ($time === null) {
                    throw new Failure(2, 'bad value for ' . $word);
                }
                if ($word === '--from') {
                    $options['from'] = $time;
                } else {
                    $options['to'] = $time;
                }
            } else {
                if (preg_match('/^(?:[1-9][0-9]*)$/D', $value) !== 1) {
                    throw new Failure(2, 'bad value for ' . $word);
                }
                $count = (int) $value;
                if ($count < 1 || $count > Registry::MAX_COUNT) {
                    throw new Failure(2, 'bad value for ' . $word);
                }
                $options['count'] = $count;
            }
        }

        if (in_array($command, ['next', 'count', 'clash'], true) && $options['from'] === null) {
            throw new Failure(2, 'missing --from');
        }
        if ($command === 'count' && $options['to'] === null) {
            throw new Failure(2, 'missing --to');
        }
        if ($command === 'count' && $options['to'] <= $options['from']) {
            throw new Failure(2, '--to is not after --from');
        }

        return ['command' => $command, 'options' => $options];
    }
}
