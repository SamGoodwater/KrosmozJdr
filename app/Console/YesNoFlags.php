<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Command;

/**
 * Flags `-y` / `--yes` et `--no` pour les confirmations Artisan du projet.
 *
 * Symfony réserve déjà `-n` pour `--no-interaction` : le refus explicite est `--no`.
 *
 * @example YesNoFlags::ideHelperModelsArguments(true, false, false)
 */
final class YesNoFlags
{
    public const SIGNATURE = '{--y|yes : Accepter les confirmations (écraser les modèles IDE Helper, apt, etc.)} {--no : Refuser les confirmations}';

    public const CONFLICT_MESSAGE = 'Incompatible : -y / --yes et --no.';

    /**
     * Arguments transmis à `ide-helper:models`.
     *
     * @return array<string, bool>
     */
    public static function ideHelperModelsArguments(bool $yes, bool $no, bool $non_interactive): array
    {
        if ($yes) {
            return ['--write' => true];
        }

        if ($no || $non_interactive) {
            return ['--nowrite' => true];
        }

        return [];
    }

    public static function wantsYes(Command $command): bool
    {
        return self::boolOption($command, 'yes');
    }

    public static function wantsNo(Command $command): bool
    {
        return self::boolOption($command, 'no');
    }

    public static function isConflicting(Command $command): bool
    {
        return self::wantsYes($command) && self::wantsNo($command);
    }

    /**
     * Options à transmettre à une commande enfant.
     *
     * @return array<string, bool>
     */
    public static function callOptions(Command $command): array
    {
        $options = [];

        if (self::wantsYes($command)) {
            $options['--yes'] = true;
        }

        if (self::wantsNo($command)) {
            $options['--no'] = true;
        }

        if ((bool) $command->option('no-interaction')) {
            $options['--no-interaction'] = true;
        }

        return $options;
    }

    private static function boolOption(Command $command, string $name): bool
    {
        return $command->getDefinition()->hasOption($name)
            && (bool) $command->option($name);
    }
}
