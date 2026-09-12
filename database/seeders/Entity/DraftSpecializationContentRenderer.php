<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

/**
 * Transforme les tableaux de `database/seeders/data/draft-specializations.php`
 * en sections HTML de fiche spécialisation.
 */
final class DraftSpecializationContentRenderer
{
    public const DRAFT_BANNER = '<p><strong>Brouillon</strong> — proposition à retravailler (aptitudes, chiffres, liens vers les fiches capacités). Pas encore jouable.</p>';

    /**
     * @param  array<string, mixed>  $spec
     * @return list<array{title: string, level: int, content: string}>
     */
    public function sections(array $spec): array
    {
        $sections = [
            [
                'title' => 'Texte',
                'level' => 1,
                'content' => $this->presentationHtml($spec),
            ],
        ];

        $levels = $spec['levels'] ?? [];
        if (! is_array($levels)) {
            return $sections;
        }

        foreach ($levels as $level => $levelData) {
            if (! is_array($levelData)) {
                continue;
            }

            $level = (int) $level;
            $sections[] = [
                'title' => 'Niveau '.$level,
                'level' => max(1, $level),
                'content' => $this->levelHtml($level, $levelData),
            ];
        }

        return $sections;
    }

    /**
     * @param  array<string, mixed>  $spec
     */
    private function presentationHtml(array $spec): string
    {
        $parts = [self::DRAFT_BANNER];

        $identity = trim((string) ($spec['identity'] ?? ''));
        if ($identity !== '') {
            $parts[] = '<p>'.e($identity).'</p>';
        }

        $parts[] = '<h2>Repères</h2><ul>'
            .$this->li('Focus', (string) ($spec['focus'] ?? ''))
            .$this->li('Caractéristiques', (string) ($spec['characteristics'] ?? ''))
            .$this->li('Idéal pour', (string) ($spec['idealFor'] ?? ''))
            .'</ul>';

        $difference = trim((string) ($spec['difference'] ?? ''));
        if ($difference !== '') {
            $parts[] = '<h2>Ce que cette spécialisation n’est pas</h2><p>'.e($difference).'</p>';
        }

        $synergies = $spec['synergies'] ?? [];
        if (is_array($synergies) && $synergies !== []) {
            $items = '';
            foreach ($synergies as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = '<h2>Synergies (pistes)</h2><ul>'.$items.'</ul>';
        }

        $todo = $spec['todo'] ?? [];
        if (is_array($todo) && $todo !== []) {
            $items = '';
            foreach ($todo as $line) {
                $items .= '<li>'.e((string) $line).'</li>';
            }
            $parts[] = '<h2>À travailler avant le passage jouable</h2><ul>'.$items.'</ul>';
        }

        return implode('', $parts);
    }

    /**
     * @param  array<string, mixed>  $levelData
     */
    private function levelHtml(int $level, array $levelData): string
    {
        $parts = [self::DRAFT_BANNER];

        $flavor = trim((string) ($levelData['flavor'] ?? ''));
        if ($flavor !== '') {
            $parts[] = '<p>'.e($flavor).'</p>';
        }

        $choice = trim((string) ($levelData['choice'] ?? ''));
        if ($choice !== '') {
            $parts[] = '<p><strong>À ce palier</strong> : '.e($choice).'</p>';
        }

        $masteries = $levelData['masteries'] ?? [];
        if (is_array($masteries) && $masteries !== []) {
            $items = '';
            foreach ($masteries as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = '<h2>Maîtrises</h2><ul>'.$items.'</ul>';
        }

        $aptitudes = $levelData['aptitudes'] ?? [];
        if (is_array($aptitudes) && $aptitudes !== []) {
            $parts[] = '<h2>Aptitudes (pistes)</h2>'.$this->namedEffectsList($aptitudes);
        }

        $capacities = $levelData['capacities'] ?? [];
        if (is_array($capacities) && $capacities !== []) {
            $parts[] = '<h2>Capacités (pistes)</h2>'.$this->namedEffectsList($capacities);
        }

        if ($level === 1 && $flavor === '' && $masteries === []) {
            $parts[] = '<p>Palier à rédiger.</p>';
        }

        return implode('', $parts);
    }

    /**
     * @param  list<array<string, mixed>>|array<int|string, mixed>  $entries
     */
    private function namedEffectsList(array $entries): string
    {
        $items = '';
        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $name = trim((string) ($entry['name'] ?? ''));
            $effect = trim((string) ($entry['effect'] ?? ''));
            $type = trim((string) ($entry['type'] ?? ''));
            if ($name === '') {
                continue;
            }

            $label = e($name);
            if ($type !== '') {
                $label .= ' <em>('.e($type).')</em>';
            }

            $items .= '<li><strong>'.$label.'</strong>';
            if ($effect !== '') {
                $items .= ' — '.e($effect);
            }
            $items .= '</li>';
        }

        return $items === '' ? '<p>Aucune piste pour l’instant.</p>' : '<ul>'.$items.'</ul>';
    }

    private function li(string $label, string $text): string
    {
        $text = trim($text);
        if ($label === '' && $text === '') {
            return '';
        }

        if ($text === '') {
            return '<li>'.e($label).'</li>';
        }

        return '<li><strong>'.e($label).'</strong> : '.e($text).'</li>';
    }
}
