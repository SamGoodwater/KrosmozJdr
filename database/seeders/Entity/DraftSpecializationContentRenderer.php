<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;

/**
 * Transforme les tableaux de spécialisations en sections HTML.
 *
 * Deux modes :
 * - enveloppe 2.4.6 (`envelope` / `progression`) : ambiance → tableau → niveaux ;
 * - gabarit legacy 2.4.2.6 : garanties / options fixes (autres fiches).
 */
final class DraftSpecializationContentRenderer
{
    public const DRAFT_BANNER = '<p><strong>Brouillon</strong> — proposition à retravailler (chiffres, coûts, liens vers les fiches capacités). Pas encore jouable.</p>';

    private bool $playable = false;

    private bool $envelope = false;

    /**
     * @param  array<string, mixed>  $spec
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function sections(array $spec): array
    {
        $this->playable = (bool) ($spec['playable'] ?? false);
        $this->envelope = $this->isEnvelope($spec);

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
                'content' => $this->levelHtml($level, $levelData, $spec),
                'capabilities' => $this->namedEntryNames($levelData['capacities'] ?? []),
                'aptitudes' => $this->namedEntryNames($levelData['aptitudes'] ?? []),
            ];
        }

        return $sections;
    }

    /**
     * @param  array<string, mixed>  $spec
     */
    private function isEnvelope(array $spec): bool
    {
        if (($spec['envelope'] ?? '') === '2.4.6') {
            return true;
        }

        $progression = $spec['progression'] ?? null;

        return is_array($progression) && $progression !== [];
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

        $description = trim((string) ($spec['description'] ?? ''));
        if ($description !== '' && $description !== $identity) {
            $parts[] = '<p>'.$this->richText($description).'</p>';
        }

        if ($this->envelope) {
            $parts[] = $this->progressionTableHtml($spec);

            $signature = $spec['signature'] ?? null;
            if (is_array($signature)) {
                $sigName = trim((string) ($signature['name'] ?? ''));
                $sigSummary = trim((string) ($signature['summary'] ?? ''));
                if ($sigName !== '' && $sigSummary !== '') {
                    $parts[] = '<h2>'.$this->capabilityKref($sigName).'</h2><p>'.$this->richText($sigSummary).'</p>';
                }
            }

            $styles = $this->stylesOf($spec);
            if ($styles !== []) {
                $parts[] = $this->stylesIntroHtml($spec, $styles);
            }

            $parts[] = '<h2>Comment lire un niveau</h2><p>'
                .'Le tableau ci-dessus dit ce que tu gagnes. En dessous, chaque niveau détaille les listes. '
                .'Quand c’est écrit <strong>ou</strong>, tu prends un seul côté. '
                .'Ça ne donne <strong>jamais</strong> de points de caractéristique.'
                .'</p>';
        } else {
            $parts[] = '<h2>Repères</h2><ul>'
                .$this->li('Focus', (string) ($spec['focus'] ?? ''))
                .$this->li('Caractéristiques', (string) ($spec['characteristics'] ?? ''))
                .$this->li('Idéal pour', (string) ($spec['idealFor'] ?? ''))
                .'</ul>';

            $parts[] = '<h2>Comment lire un niveau</h2><p>'
                .'À chaque niveau, tu lis <strong>choix entre X et Y</strong> : tu prends une capacité garantie, '
                .'puis une option. Ça ne donne jamais de points de caractéristique. '
                .'Les aptitudes arrivent aux niveaux 3, 9 et 15.'
                .'</p>';

            $styles = $this->stylesOf($spec);
            if ($styles !== []) {
                $parts[] = $this->stylesIntroHtml($spec, $styles);
            }
        }

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
     * @param  array<string, mixed>  $spec
     */
    private function progressionTableHtml(array $spec): string
    {
        $progression = $spec['progression'] ?? [];
        if (! is_array($progression) || $progression === []) {
            return '';
        }

        $rows = '';
        foreach ($progression as $level => $gains) {
            $cells = is_array($gains)
                ? array_map(static fn (mixed $cell): string => (string) $cell, $gains)
                : array_values(array_filter(array_map('trim', preg_split('/\s*·\s*/u', (string) $gains) ?: [])));
            if ($cells === []) {
                continue;
            }
            $rows .= '<tr><th scope="row">'.e((string) $level).'</th>';
            foreach ($cells as $cell) {
                $rows .= '<td>'.$this->richText($cell).'</td>';
            }
            $rows .= '</tr>';
        }

        if ($rows === '') {
            return '';
        }

        return '<h2>Progression</h2>'
            .'<table><thead><tr><th>Niveau</th><th colspan="10"></th></tr></thead><tbody>'
            .$rows
            .'</tbody></table>';
    }

    /**
     * @param  array<string, mixed>  $spec
     * @return array<string, mixed>
     */
    private function stylesOf(array $spec): array
    {
        $styles = $spec['styles'] ?? $spec['archetypes'] ?? [];

        return is_array($styles) ? $styles : [];
    }

    /**
     * Bloc « Styles » : une pastille par style (icône sans fond, hydratée côté front).
     *
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $styles
     */
    private function stylesIntroHtml(array $spec, array $styles): string
    {
        $stylesAt = (int) ($spec['styles_at'] ?? 3);

        return '<h2>Styles (niveau '.$stylesAt.')</h2>'
            .'<p>Au niveau '.$stylesAt.', tu choisis <strong>un</strong> style. Il fixe ta 2ᵉ sauvegarde et colore certaines capacités. Le niveau 1 reste commun.</p>'
            .$this->stylesSummaryHtml($styles);
    }

    /**
     * @param  array<string, mixed>  $styles
     */
    private function stylesSummaryHtml(array $styles): string
    {
        $blocks = [];
        foreach ($styles as $styleKey => $style) {
            if (! is_array($style)) {
                continue;
            }
            $name = trim((string) ($style['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $key = is_string($styleKey) && ! is_numeric($styleKey)
                ? $styleKey
                : strtolower($this->asciiSlug($name));
            $blocks[] = '<h3>'.$this->styleLabelHtml($key, $name).'</h3>';
            $blurb = trim((string) ($style['blurb'] ?? ''));
            if ($blurb !== '') {
                $blocks[] = '<p>'.$this->richText($blurb).'</p>';
            }
            $save = trim((string) ($style['save'] ?? ''));
            if ($save !== '') {
                $blocks[] = '<p>Tu maîtrises les jets de sauvegarde liés à '.$this->richText($save).'.</p>';
            }
            $applause = trim((string) ($style['applaudissement'] ?? ''));
            if ($applause !== '') {
                $blocks[] = '<p>'.$this->richText($applause).'</p>';
            }
        }

        return implode('', $blocks);
    }

    /**
     * Pastille HTML pour un style / variante (hydratée côté front via .spe-style).
     */
    private function styleLabelHtml(string $styleKey, string $label): string
    {
        $key = trim($styleKey);
        $text = trim($label) !== '' ? trim($label) : $key;

        return '<span class="spe-style" data-spe-style="'.e($key).'">'.e($text).'</span>';
    }

    private function asciiSlug(string $value): string
    {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $ascii = is_string($ascii) ? $ascii : $value;
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $ascii) ?? '');

        return trim($slug, '-') ?: 'style';
    }

    /**
     * @param  array<string, mixed>  $levelData
     * @param  array<string, mixed>  $spec
     */
    private function levelHtml(int $level, array $levelData, array $spec = []): string
    {
        $parts = [];
        if (! $this->playable) {
            $parts[] = self::DRAFT_BANNER;
        }

        $flavor = trim((string) ($levelData['flavor'] ?? ''));
        if ($flavor !== '') {
            $parts[] = '<p>'.$this->richText($flavor).'</p>';
        }

        $gains = trim((string) ($levelData['gains'] ?? ''));
        if ($gains !== '' && ! $this->envelope) {
            $parts[] = '<p><strong>À ce niveau</strong> : '.$this->richText($gains).'</p>';
        } elseif (! $this->envelope) {
            $choice = $this->buildChoiceLine($level, $levelData);
            if ($choice !== '') {
                $parts[] = '<p><strong>À ce palier</strong> : '.$this->richText($choice).'</p>';
            }
        }

        if (! empty($levelData['unlock_style'])) {
            $styles = $this->stylesOf($spec);
            if ($styles !== []) {
                $parts[] = '<h2>Choix de style</h2>'.$this->stylesSummaryHtml($styles);
            }
        }

        $masteries = $levelData['masteries'] ?? [];
        if (is_array($masteries) && $masteries !== []) {
            $items = '';
            foreach ($masteries as $label => $text) {
                if ($this->isEmptyMastery((string) $text)) {
                    continue;
                }
                $items .= $this->li((string) $label, (string) $text);
            }
            if ($items !== '') {
                $parts[] = '<h2>Maîtrises</h2><ul>'.$items.'</ul>';
            }
        }

        $ritual = trim((string) ($levelData['ritual'] ?? ''));
        if ($ritual !== '') {
            $parts[] = '<h2>Rituel</h2><p>'.$this->richText($ritual).'</p>';
        }

        $aptitudes = $levelData['aptitudes'] ?? [];
        if (is_array($aptitudes) && $aptitudes !== []) {
            $pick = (int) ($levelData['aptitude_pick'] ?? 0);
            $title = $pick > 0
                ? 'Aptitudes — choisis-en '.$pick
                : ($this->envelope ? 'Aptitudes' : 'Aptitude (automatique)');
            $parts[] = '<h2>'.$title.'</h2>'.$this->namedEffectsList($aptitudes, 'aptitudes');
        }

        $capacities = $levelData['capacities'] ?? [];
        if (is_array($capacities) && $capacities !== []) {
            $pick = (int) ($levelData['capacity_pick'] ?? 0);
            $title = $pick > 0
                ? 'Capacités — choisis-en '.$pick
                : ($this->playable ? 'Capacités' : 'Capacités (pistes)');
            $parts[] = '<h2>'.$title.'</h2>'.$this->namedEffectsList($capacities, 'capabilities');
        }

        if ($level === 1 && $flavor === '' && $masteries === []) {
            $parts[] = '<p>Palier à rédiger.</p>';
        }

        return implode('', $parts);
    }

    /**
     * @param  list<array<string, mixed>>|array<int|string, mixed>  $entries
     * @param  'capabilities'|'aptitudes'  $entityType
     */
    private function namedEffectsList(array $entries, string $entityType = 'capabilities'): string
    {
        $items = '';
        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $name = trim((string) ($entry['name'] ?? ''));
            $effect = trim((string) ($entry['effect'] ?? ''));
            $type = trim((string) ($entry['type'] ?? ''));
            $variants = $entry['archetype_variants'] ?? null;
            if ($name === '') {
                continue;
            }

            $label = $this->entityKref($entityType, $name);
            if ($type !== '' && ! $this->envelope) {
                $label .= ' <em>('.e($this->capacityTypeLabel($type)).')</em>';
            }

            $items .= '<li><strong>'.$label.'</strong>';
            if (is_array($variants) && $variants !== []) {
                $items .= ' — selon ton style :<ul>';
                foreach ($variants as $styleKey => $variant) {
                    if (! is_array($variant)) {
                        continue;
                    }
                    $vName = trim((string) ($variant['name'] ?? ''));
                    $vEffect = trim((string) ($variant['effect'] ?? ''));
                    if ($vName === '') {
                        continue;
                    }
                    $key = is_string($styleKey) && ! is_numeric($styleKey)
                        ? $styleKey
                        : strtolower($this->asciiSlug($vName));
                    $items .= '<li data-spe-style="'.e($key).'"><strong>'.$this->entityKref($entityType, $vName).'</strong>';
                    if ($vEffect !== '') {
                        $items .= ' — '.$this->richText($vEffect);
                    }
                    $items .= '</li>';
                }
                $items .= '</ul>';
            } elseif ($effect !== '') {
                $items .= ' — '.$this->richText($effect);
            }
            $items .= '</li>';
        }

        return $items === '' ? '<p>Aucune piste pour l’instant.</p>' : '<ul>'.$items.'</ul>';
    }

    /**
     * @param  list<array<string, mixed>>|array<int|string, mixed>  $entries
     * @return list<string>
     */
    private function namedEntryNames(array $entries): array
    {
        $names = [];
        if (! is_array($entries)) {
            return $names;
        }

        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }
            $name = trim((string) ($entry['name'] ?? ''));
            if ($name !== '') {
                $names[] = $name;
            }
            $variants = $entry['archetype_variants'] ?? null;
            if (! is_array($variants)) {
                continue;
            }
            foreach ($variants as $variant) {
                if (! is_array($variant)) {
                    continue;
                }
                $vName = trim((string) ($variant['name'] ?? ''));
                if ($vName !== '') {
                    $names[] = $vName;
                }
            }
        }

        return array_values(array_unique($names));
    }

    private function entityKref(string $entityType, string $name): string
    {
        $type = $entityType === 'aptitudes' ? 'aptitudes' : 'capabilities';

        return '[[kref:entity:'.$type.':'.$name.'|'.$name.']]';
    }

    private function capabilityKref(string $name): string
    {
        return $this->entityKref('capabilities', $name);
    }

    /**
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

        return '<li><strong>'.e($label).'</strong> '.$this->richText($text).'</li>';
    }

    /**
     * Une ligne « Aucune » ou « Aucun pour l’instant » ne s’affiche pas.
     */
    private function isEmptyMastery(string $text): bool
    {
        $text = trim($text);

        return $text === '' || preg_match('/^(aucune?s?|aucun(?: pour l[’\']instant)?)\.?$/iu', $text) === 1;
    }
}
