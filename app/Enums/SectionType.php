<?php

namespace App\Enums;

/**
 * Enum pour les types de sections disponibles.
 * 
 * Chaque type correspond à un template Vue dans le frontend.
 * 
 * @method static self TEXT()
 * @method static self IMAGE()
 * @method static self GALLERY()
 * @method static self VIDEO()
 * @method static self ENTITY_TABLE()
 */
enum SectionType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case GALLERY = 'gallery';
    case VIDEO = 'video';
    case ENTITY_TABLE = 'entity_table';

    /**
     * Retourne le label traduit du type.
     * 
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Texte',
            self::IMAGE => 'Image',
            self::GALLERY => 'Galerie',
            self::VIDEO => 'Vidéo',
            self::ENTITY_TABLE => 'Tableau d\'entités',
        };
    }

    /**
     * Retourne l'icône FontAwesome associée au type.
     * 
     * @return string
     */
    public function icon(): string
    {
        return match($this) {
            self::TEXT => 'fa-file-lines',
            self::IMAGE => 'fa-image',
            self::GALLERY => 'fa-images',
            self::VIDEO => 'fa-video',
            self::ENTITY_TABLE => 'fa-table',
        };
    }

    /**
     * Retourne la structure attendue des params pour ce type.
     * 
     * @return array<string, mixed>
     */
    public function expectedParams(): array
    {
        return match($this) {
            self::TEXT => [
                'content' => 'string (required)',
                'align' => 'string (optional: left|center|right)',
                'size' => 'string (optional: sm|md|lg|xl)',
            ],
            self::IMAGE => [
                'src' => 'string (required)',
                'alt' => 'string (required)',
                'caption' => 'string (optional)',
                'align' => 'string (optional: left|center|right)',
                'size' => 'string (optional: sm|md|lg|xl|full)',
            ],
            self::GALLERY => [
                'images' => 'array (required)',
                'columns' => 'integer (optional: 2|3|4)',
                'gap' => 'string (optional: sm|md|lg)',
            ],
            self::VIDEO => [
                'src' => 'string (required)',
                'type' => 'string (required: youtube|vimeo|direct)',
                'autoplay' => 'boolean (optional)',
                'controls' => 'boolean (optional)',
            ],
            self::ENTITY_TABLE => [
                'entity' => 'string (required)',
                'filters' => 'array (optional)',
                'columns' => 'array (optional)',
            ],
        };
    }

    /**
     * Retourne tous les types possibles.
     * 
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Retourne tous les types avec leurs labels.
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

