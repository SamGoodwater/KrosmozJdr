<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * États de publication des entités JDR, types, pages et sections.
 *
 * @example
 * 'state' => ['nullable', 'string', EntityState::rule()]
 */
enum EntityState: string
{
    case Raw = 'raw';
    case Draft = 'draft';
    case Auto = 'auto';
    case Playable = 'playable';
    case Archived = 'archived';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Raw => 'Brut',
            self::Draft => 'Brouillon',
            self::Auto => 'Auto',
            self::Playable => 'Jouable',
            self::Archived => 'Archivé',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $case): array => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }

    /**
     * États précochés dans un catalogue qui masque Brut par défaut.
     *
     * @return list<string>
     *
     * @example
     * EntityState::catalogDefaultValues();
     * // ['playable']
     */
    public static function catalogDefaultValues(): array
    {
        return array_values(array_filter(
            self::values(),
            static fn (string $value): bool => $value === self::Playable->value
        ));
    }

    public static function rule(): In
    {
        return Rule::in(self::values());
    }

    public function daisyColor(): string
    {
        return match ($this) {
            self::Raw => 'error',
            self::Draft => 'warning',
            self::Auto => 'secondary',
            self::Playable => 'success',
            self::Archived => 'info',
        };
    }

    public function chartColor(): string
    {
        return match ($this) {
            self::Raw => '#94a3b8',
            self::Draft => '#fbbf24',
            self::Auto => '#a78bfa',
            self::Playable => '#34d399',
            self::Archived => '#f87171',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function chartColors(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->chartColor();
        }

        return $out;
    }
}
