<?php

namespace App\Enums;

/**
 * Enum pour les états d'une page.
 * 
 * @method static self DRAFT()
 * @method static self PREVIEW()
 * @method static self PUBLISHED()
 * @method static self ARCHIVED()
 */
enum PageState: string
{
    case DRAFT = 'draft';
    case PREVIEW = 'preview';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    /**
     * Retourne le label traduit de l'état.
     * 
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PREVIEW => 'Prévisualisation',
            self::PUBLISHED => 'Publié',
            self::ARCHIVED => 'Archivé',
        };
    }

    /**
     * Retourne la couleur DaisyUI associée à l'état.
     * 
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'neutral',
            self::PREVIEW => 'warning',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'error',
        };
    }

    /**
     * Retourne tous les états possibles.
     * 
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Retourne tous les états avec leurs labels.
     * 
     * @return array<string, string>
     */
    public static function toArrayWithLabels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}

