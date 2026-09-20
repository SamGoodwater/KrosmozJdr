<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;

/**
 * Transforme les tableaux de spécialisations (brouillons ou fiche jouable)
 * en sections HTML.
 *
 * Gabarit imposé par 2.4.2.6 : 7 paliers, 2 garanties au palier 1 puis 1,
 * 2 options à chaque palier (un trait dès le palier 12), 5 compétences
 * au palier 1 (en prendre 3), aptitudes uniquement aux paliers 3 / 9 / 15.
 * Pas de points de caractéristique. Les noms de capacités, jets et
 * compétences sont des shortcodes kref (cliquables une fois convertis).
 */
final class DraftSpecializationContentRenderer
{
    public const DRAFT_BANNER = '<p><strong>Brouillon</strong> — proposition à retravailler (chiffres, coûts, liens vers les fiches capacités). Pas encore jouable.</p>';

    private bool $playable = false;

    /**
     * @param  array<string, mixed>  $spec
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function sections(array $spec): array
    {
        $this->playable = (bool) ($spec['playable'] ?? false);

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
                'capabilities' => $this->capabilityNames($levelData),
            ];
        }

        return $sections;
    }

    /**
     * @param  array<string, mixed>  $spec
     */
    private function presentationHtml(array $spec): string
    {
        $parts = [];
        if (! $this->playable) {
            $parts[] = self::DRAFT_BANNER;
        }

        $identity = trim((string) ($spec['identity'] ?? ''));
        if ($identity !== '') {
            $parts[] = '<p>'.$this->richText($identity).'</p>';
        }

        $parts[] = '<h2>Repères</h2><ul>'
            .$this->li('Focus', (string) ($spec['focus'] ?? ''))
            .$this->li('Caractéristiques', (string) ($spec['characteristics'] ?? ''))
            .$this->li('Idéal pour', (string) ($spec['idealFor'] ?? ''))
            .'</ul>';

        $parts[] = '<h2>Comment lire un palier</h2><p>'
            .'À chaque palier, la fiche écrit <strong>choix entre X et Y</strong> : tu prends <strong>une</strong> des capacités garanties, '
            .'puis <strong>une</strong> des autres options. Ce n’est pas une case vide, et ça ne donne <strong>jamais</strong> de points de caractéristique. '
            .'Les options, ce sont des <strong>capacités</strong>, un <strong>trait</strong> à partir du niveau 12, plus les <strong>compétences</strong> '
            .'et les <strong>métiers</strong> du bloc Maîtrises. Les <strong>aptitudes</strong> tombent toutes seules aux niveaux 3, 9 et 15.'
            .'</p>';

        $difference = trim((string) ($spec['difference'] ?? ''));
        if ($difference !== '') {
            $parts[] = '<h2>Ce que cette spécialisation n’est pas</h2><p>'.$this->richText($difference).'</p>';
        }

        $synergies = $spec['synergies'] ?? [];
        if (is_array($synergies) && $synergies !== []) {
            $items = '';
            foreach ($synergies as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = ($this->playable ? '<h2>Synergies</h2><ul>' : '<h2>Synergies (pistes)</h2><ul>').$items.'</ul>';
        }

        $todo = $spec['todo'] ?? [];
        if (! $this->playable && is_array($todo) && $todo !== []) {
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
        $parts = [];
        if (! $this->playable) {
            $parts[] = self::DRAFT_BANNER;
        }

        $flavor = trim((string) ($levelData['flavor'] ?? ''));
        if ($flavor !== '') {
            $parts[] = '<p>'.$this->richText($flavor).'</p>';
        }

        $choice = $this->buildChoiceLine($level, $levelData);
        if ($choice !== '') {
            $parts[] = '<p><strong>À ce palier</strong> : '.$this->richText($choice).'</p>';
        }

        $masteries = $levelData['masteries'] ?? [];
        if (is_array($masteries) && $masteries !== []) {
            $items = '';
            foreach ($masteries as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = '<h2>Maîtrises</h2><ul>'.$items.'</ul>';
        }

        $capacities = $levelData['capacities'] ?? [];
        if (is_array($capacities) && $capacities !== []) {
            $parts[] = ($this->playable ? '<h2>Capacités</h2>' : '<h2>Capacités (pistes)</h2>').$this->namedEffectsList($capacities);
        }

        $aptitudes = $levelData['aptitudes'] ?? [];
        if (is_array($aptitudes) && $aptitudes !== []) {
            $parts[] = '<h2>Aptitude (automatique)</h2>'.$this->namedEffectsList($aptitudes);
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

            $label = $this->capabilityKref($name);
            if ($type !== '') {
                $label .= ' <em>('.e($this->capacityTypeLabel($type)).')</em>';
            }

            $items .= '<li><strong>'.$label.'</strong>';
            if ($effect !== '') {
                $items .= ' — '.$this->richText($effect);
            }
            $items .= '</li>';
        }

        return $items === '' ? '<p>Aucune piste pour l’instant.</p>' : '<ul>'.$items.'</ul>';
    }

    /**
     * @param  array<string, mixed>  $levelData
     * @return list<string>
     */
    private function capabilityNames(array $levelData): array
    {
        $names = [];
        foreach (['capacities', 'aptitudes'] as $key) {
            $entries = $levelData[$key] ?? [];
            if (! is_array($entries)) {
                continue;
            }
            foreach ($entries as $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                $name = trim((string) ($entry['name'] ?? ''));
                if ($name !== '') {
                    $names[] = $name;
                }
            }
        }

        return array_values(array_unique($names));
    }

    private function capabilityKref(string $name): string
    {
        return '[[kref:entity:capabilities:'.$name.'|'.$name.']]';
    }

    /**
     * Construit « Choix entre X et Y » à partir des noms du palier.
     *
     * @param  array<string, mixed>  $levelData
     */
    private function buildChoiceLine(int $level, array $levelData): string
    {
        $parts = [];

        if ($this->namedEntries($levelData['aptitudes'] ?? []) !== []) {
            $parts[] = '1 aptitude (automatique)';
        }

        $guaranteed = $this->capacityNamesByTypes($levelData, ['garantie']);
        $first = $this->entrePhrase($guaranteed);
        if ($first !== '') {
            $parts[] = $first;
        }

        $options = $this->capacityNamesByTypes($levelData, ['choix', 'emplacement libre']);
        if ($level >= 12) {
            $options[] = 'un trait';
        }
        $second = $this->entrePhrase($options);
        if ($second !== '') {
            $parts[] = $second;
        }

        $parts[] = $this->skillPhrase($level, $levelData);

        return implode(' · ', $parts);
    }

    /**
     * @param  list<string>  $names
     */
    private function entrePhrase(array $names): string
    {
        $labeled = [];
        foreach ($names as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $labeled[] = $name === 'un trait' ? 'un trait' : $this->capabilityKref($name);
        }

        $count = count($labeled);
        if ($count === 0) {
            return '';
        }
        if ($count === 1) {
            return $labeled[0];
        }

        $last = array_pop($labeled);

        return 'Choix entre '.implode(', ', $labeled).' et '.$last;
    }

    /**
     * @param  array<string, mixed>  $levelData
     * @param  list<string>  $types
     * @return list<string>
     */
    private function capacityNamesByTypes(array $levelData, array $types): array
    {
        $names = [];
        foreach ($this->namedEntries($levelData['capacities'] ?? []) as $entry) {
            $type = trim((string) ($entry['type'] ?? ''));
            if (in_array($type, $types, true)) {
                $names[] = (string) $entry['name'];
            }
        }

        return $names;
    }

    /**
     * @param  mixed  $entries
     * @return list<array<string, mixed>>
     */
    private function namedEntries(mixed $entries): array
    {
        if (! is_array($entries)) {
            return [];
        }

        $named = [];
        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }
            $name = trim((string) ($entry['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $named[] = $entry;
        }

        return $named;
    }

    /**
     * @param  array<string, mixed>  $levelData
     */
    private function skillPhrase(int $level, array $levelData): string
    {
        $count = $this->skillCount((string) ($levelData['choice'] ?? ''), $level);
        $phrase = match ($count) {
            3 => '3 compétences',
            2 => '2 compétences',
            default => '1 compétence',
        };

        if (in_array($level, [9, 15, 20], true)) {
            $phrase .= ' (expertise possible)';
        }

        return $phrase;
    }

    private function skillCount(string $choice, int $level): int
    {
        if (preg_match('/(\d+)\s+compétences?/u', $choice, $matches) === 1) {
            return (int) $matches[1];
        }

        return $level === 1 ? 3 : 1;
    }

    /**
     * Libellé joueur du type de capacité.
     */
    private function capacityTypeLabel(string $type): string
    {
        return in_array($type, ['emplacement libre', '2ᵉ capacité', 'choix'], true)
            ? 'choix'
            : $type;
    }

    private function richText(string $text): string
    {
        return RulesCharacteristicKrefReplacementCatalog::applyToMarkdown(e($text));
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

        return '<li><strong>'.e($label).'</strong> : '.$this->richText($text).'</li>';
    }
}
