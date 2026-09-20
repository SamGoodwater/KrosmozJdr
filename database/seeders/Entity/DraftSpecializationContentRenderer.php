<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;

/**
 * Transforme les tableaux de spécialisations (brouillons ou fiche jouable)
 * en sections HTML.
 *
 * Gabarit imposé par 2.4.2.6 : 7 paliers, 1 capacité garantie + 1 option,
 * 1 compétence (sauf palier 1 = 3), aptitudes uniquement aux paliers 3 / 9 / 15.
 * Les noms de capacités, jets et compétences sont des shortcodes kref
 * (cliquables une fois convertis à l’import).
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
            .'À chaque palier tu prends <strong>une capacité garantie</strong> (une seule, même s’il y en a plusieurs dans la liste). '
            .'Il te reste ensuite <strong>un emplacement</strong> : ce n’est pas un trou vide, c’est <strong>un choix</strong>. '
            .'Tu le dépenses en <strong>2ᵉ capacité</strong> (celles marquées ainsi), en <strong>+2 points</strong> de caractéristique '
            .'(trois fois dans toute ta carrière, pas plus), ou en <strong>trait</strong> à partir du niveau 12. '
            .'Tu ne perds jamais cet emplacement : s’il ne reste rien d’autre, il devient une compétence de plus.'
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

        $choice = trim((string) ($levelData['choice'] ?? ''));
        if ($choice !== '') {
            $parts[] = '<p><strong>À ce palier</strong> : '.$this->richText($this->choiceLabel($choice)).'</p>';
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
     * Libellé joueur du type de capacité (le gabarit stocke encore « emplacement libre »).
     */
    private function capacityTypeLabel(string $type): string
    {
        return $type === 'emplacement libre' ? '2ᵉ capacité' : $type;
    }

    /**
     * Remplace le jargon « emplacement libre » par le choix réel du palier.
     */
    private function choiceLabel(string $choice): string
    {
        $withTrait = '1 choix (2ᵉ capacité, +2 points, ou un trait)';
        $withoutTrait = '1 choix (2ᵉ capacité ou +2 points)';

        $choice = str_replace('1 emplacement libre (trait possible)', $withTrait, $choice);

        return str_replace('1 emplacement libre', $withoutTrait, $choice);
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
