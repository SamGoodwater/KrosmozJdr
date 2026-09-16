<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Charge les fiches de bonne pratique (atelier Création) pour l’assembleur de prompt de conversion.
 *
 * Source unique : {@code resources/ia/creation-guides/}. Pas d’appel LLM.
 *
 * @example
 * $prompt = app(CreationGuideCatalog::class)->promptFor('spell');
 */
final class CreationGuideCatalog
{
    /** @var list<string> */
    public const CONVERSION_ENTITIES = [
        'spell',
        'monster',
        'item',
        'consumable',
        'capability',
        'trait',
        'resource',
    ];

    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $path = resource_path('ia/creation-guides/index.php');
        if (! is_file($path)) {
            return [];
        }

        $guides = require $path;

        return is_array($guides) ? $guides : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $entity): ?array
    {
        $guides = $this->all();

        return is_array($guides[$entity] ?? null) ? $guides[$entity] : null;
    }

    /**
     * Texte prêt à coller dans un prompt de conversion (krefs → libellés, HTML retiré).
     */
    public function promptFor(string $entity): ?string
    {
        $guide = $this->get($entity);
        if ($guide === null) {
            return null;
        }

        $title = is_string($guide['title'] ?? null) ? (string) $guide['title'] : $entity;
        $chunks = ['# '.$title];

        foreach ($guide['sections'] ?? [] as $section) {
            if (! is_array($section)) {
                continue;
            }
            $heading = is_string($section['title'] ?? null) ? (string) $section['title'] : '';
            $html = is_string($section['html'] ?? null) ? (string) $section['html'] : '';
            if ($heading === '' || $html === '') {
                continue;
            }
            $chunks[] = '## '.$heading."\n".$this->htmlToPrompt($html);
        }

        return trim(implode("\n\n", $chunks));
    }

    /**
     * @return array<string, string>
     */
    public function promptBundle(): array
    {
        $bundle = [];
        foreach (self::CONVERSION_ENTITIES as $entity) {
            $prompt = $this->promptFor($entity);
            if ($prompt !== null && $prompt !== '') {
                $bundle[$entity] = $prompt;
            }
        }

        return $bundle;
    }

    public function htmlToPrompt(string $html): string
    {
        $text = (string) preg_replace('/\[\[kref:[^\]|]+\|([^\]]+)\]\]/u', '$1', $html);
        $text = (string) preg_replace('/<\/(p|li|h2|h3|tr)>/i', "\n", $text);
        $text = (string) preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = (string) preg_replace('/<\/(td|th)>/i', ' | ', $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = (string) preg_replace("/[ \t]+\n/", "\n", $text);
        $text = (string) preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}
