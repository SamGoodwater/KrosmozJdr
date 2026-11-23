<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;

/**
 * Transforme une fiche classe rédigée en sections CMS.
 *
 * Mode enveloppe (`class_progression`) : Texte + tableau récap + une section par niveau.
 * Ancien gabarit : une seule section Texte.
 */
final class ClassSheetContentRenderer
{
    /**
     * @param  array<string, mixed>  $sheet
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function sections(array $sheet): array
    {
        $passive = trim((string) ($sheet['passive'] ?? ''));
        $progression = $sheet['class_progression'] ?? null;

        if (! is_array($progression) || $progression === []) {
            return [
                [
                    'title' => 'Texte',
                    'level' => 1,
                    'content' => $this->legacyPresentationHtml($sheet),
                    'capabilities' => $passive !== '' ? [$passive] : [],
                ],
            ];
        }

        $sections = [
            [
                'title' => 'Texte',
                'level' => 1,
                'content' => $this->introHtml($sheet, $progression),
                'capabilities' => $passive !== '' ? [$passive] : [],
            ],
        ];

        $levels = $progression['levels'] ?? [];
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
                'content' => $this->levelHtml($level, $levelData, $progression),
            ];
        }

        return $sections;
    }

    /**
     * @param  array<string, mixed>  $sheet
     * @param  array<string, mixed>  $progression
     */
    private function introHtml(array $sheet, array $progression): string
    {
        $parts = [];

        $presentation = $sheet['presentation'] ?? null;
        if (is_array($presentation)) {
            foreach ($presentation as $text) {
                $text = trim((string) $text);
                if ($text !== '') {
                    $parts[] = '<p>'.$this->richText($text).'</p>';
                }
            }
        }

        // Le tableau de progression est rendu en Vue (`BreedProgressionRecap`) sur la fiche classe.
        $parts[] = '<p><em>N’oublie pas les gains de ta spécialisation aux paliers 1, 3, 6, 9, 12, 15 et 20 — ils sont sur sa fiche, pas ici.</em></p>';

        return implode('', $parts);
    }

    /**
     * @param  array<string, mixed>  $levelData
     * @param  array<string, mixed>  $progression
     */
    private function levelHtml(int $level, array $levelData, array $progression): string
    {
        $parts = [];
        $hitDie = trim((string) ($progression['hit_die'] ?? 'd8'));

        $flavor = trim((string) ($levelData['flavor'] ?? ''));
        if ($flavor !== '') {
            $parts[] = '<p>'.$this->richText($flavor).'</p>';
        }

        $parts[] = '<h2>Points de vie</h2>';
        if ($level === 1) {
            $parts[] = '<p>Tu poses tes PV de départ : <strong>maximum du '.$this->richText($hitDie).'</strong> + ton modificateur de Vitalité.</p>';
            $random = trim((string) ($progression['hp_random'] ?? ''));
            $stable = trim((string) ($progression['hp_stable'] ?? ''));
            if ($random !== '' || $stable !== '') {
                $parts[] = '<p>Pour les niveaux suivants, la table choisit une méthode pour tout le monde :</p><ul>'
                    .($random !== '' ? '<li><strong>Aléatoire</strong> : '.$this->richText($random).' (un 1 se relance).</li>' : '')
                    .($stable !== '' ? '<li><strong>Stable</strong> : '.$this->richText($stable).'.</li>' : '')
                    .'</ul>';
            }
        } else {
            $parts[] = '<p>PV actuels + <strong>1'.$this->richText($hitDie).'</strong> (méthode de la table) + ton modificateur de Vitalité.</p>'
                .'<p>Si ton modificateur de Vitalité vient d’augmenter, on fait comme s’il avait toujours eu cette valeur : '
                .'tu ajoutes aussi <strong>niveau × (nouveau mod. − ancien mod.)</strong> à tes PV.</p>';
        }

        $spells = $levelData['spells'] ?? null;
        if (is_array($spells) && $spells !== []) {
            $choices = array_values(array_filter(
                $spells,
                static fn (mixed $choice): bool => is_array($choice) && $choice !== [],
            ));
            $variantCounts = array_map(
                static fn (array $choice): int => count($choice),
                $choices,
            );
            $parts[] = '<h2>Sorts</h2>';
            $parts[] = count($choices) === 1 && ($variantCounts[0] ?? 0) === 1
                ? '<p>Tu gagnes le sort ci-dessous.</p>'
                : '<p>Tu as le choix entre '.e($this->variantPhrase($variantCounts))
                    .' — les cartes ci-dessous.</p>';
            // Ancre pour BreedLevelSpellChoices (rendu Vue principal).
            $parts[] = '<p data-breed-level-spells="'.e((string) $level).'" class="breed-level-spells-anchor" hidden></p>';
            // Krefs TipTap view=minimal pour réutilisation CMS / édition.
            $parts[] = $this->levelSpellChoicesHtml($level, $choices);
        }

        if (! empty($levelData['carac_or_trait'])) {
            $parts[] = '<h2>Caractéristiques ou trait</h2>'
                .'<p>Tu choisis <strong>une</strong> option :</p><ul>'
                .'<li><strong>2 points</strong> : +2 sur une caractéristique, ou +1 sur deux caractéristiques différentes '
                .'(Intelligence, Chance, Force, Agilité, Sagesse, Vitalité) ;</li>'
                .'<li><strong>1 trait</strong> parmi ceux marqués pour les personnages (niveau min. respecté) — '
                .'[[kref:page:bibliotheque-creature-trait|Bibliothèque → Traits]].</li>'
                .'</ul>';
        }

        $classCap = trim((string) ($levelData['class_capability'] ?? ''));
        if ($classCap !== '') {
            $parts[] = '<h2>Capacité de classe</h2><p>Tu gagnes [[kref:entity:capabilities:'.$classCap.'|'.$classCap.']].</p>';
        }

        $extra = trim((string) ($levelData['extra'] ?? ''));
        if ($extra !== '') {
            $parts[] = '<p>'.$this->richText($extra).'</p>';
        }

        return implode('', $parts);
    }

    /**
     * @param  list<int>  $variantCounts
     */
    private function variantPhrase(array $variantCounts): string
    {
        $unique = array_values(array_unique(array_filter($variantCounts, static fn (int $n): bool => $n > 0)));
        sort($unique);
        if ($unique === []) {
            return 'une ou plusieurs variantes';
        }
        if (count($unique) === 1) {
            $n = $unique[0];

            return $n === 1 ? '1 variante' : $n.' variantes';
        }

        $last = array_pop($unique);

        return implode(', ', $unique).' ou '.$last.' variantes';
    }

    private function spellKref(string $name, string $view = 'text'): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }
        $base = '[[kref:entity:spells:'.$name.'|'.$name;
        if ($view !== '' && $view !== 'text') {
            return $base.'|view='.$view.']]';
        }

        return $base.']]';
    }

    /**
     * Blocs sorts d’un niveau en krefs {@code view=minimal} (libellés N1 explicites).
     *
     * @param  list<list<mixed>>  $choices
     */
    private function levelSpellChoicesHtml(int $level, array $choices): string
    {
        if ($choices === []) {
            return '';
        }

        $chunks = [];
        foreach ($choices as $idx => $choice) {
            if (! is_array($choice) || $choice === []) {
                continue;
            }
            $slotIndex = $idx + 1;
            $heading = $level === 1
                ? $this->level1SpellChoiceLabel($slotIndex)
                : (count($choices) > 1 ? 'Choix '.$slotIndex : '');
            if ($heading !== '') {
                $chunks[] = '<h3>'.e($heading).'</h3>';
            }
            $krefs = [];
            foreach ($choice as $spellName) {
                $spellName = trim((string) $spellName);
                if ($spellName === '') {
                    continue;
                }
                $krefs[] = '<p>'.$this->spellKref($spellName, 'minimal').'</p>';
            }
            $chunks = array_merge($chunks, $krefs);
        }

        return implode('', $chunks);
    }

    private function level1SpellChoiceLabel(int $slotIndex): string
    {
        return match ($slotIndex) {
            1 => 'Sort 1 (élémentaire)',
            2 => 'Sort 2',
            3 => 'Sort 3',
            default => 'Sort '.$slotIndex,
        };
    }

    /**
     * @param  array<string, mixed>  $sheet
     */
    private function legacyPresentationHtml(array $sheet): string
    {
        $presentation = $sheet['presentation'] ?? null;
        if (is_array($presentation) && $presentation !== []) {
            $paragraphs = array_filter(array_map(
                static fn (mixed $text): string => trim((string) $text),
                $presentation,
            ));

            return implode('', array_map(
                fn (string $text): string => '<p>'.$this->richText($text).'</p>',
                $paragraphs,
            ));
        }

        $parts = [];
        $identity = trim((string) ($sheet['identity'] ?? ''));
        if ($identity !== '') {
            $parts[] = '<p>'.$this->richText($identity).'</p>';
        }

        $passive = trim((string) ($sheet['passive'] ?? ''));
        $passiveLine = $passive !== ''
            ? 'Passif : [[kref:entity:capabilities:'.$passive.'|'.$passive.']]'
            : '';

        $parts[] = '<h2>Repères</h2><ul>'
            .$this->li('Focus', (string) ($sheet['focus'] ?? ''))
            .$this->li('Caractéristiques', (string) ($sheet['characteristics'] ?? ''))
            .$this->li('Idéal pour', (string) ($sheet['idealFor'] ?? ''))
            .($passiveLine !== '' ? '<li>'.$this->richText($passiveLine).'</li>' : '')
            .'</ul>';

        $difference = trim((string) ($sheet['difference'] ?? ''));
        if ($difference !== '') {
            $parts[] = '<h2>Ce que cette classe n’est pas</h2><p>'.$this->richText($difference).'</p>';
        }

        $synergies = $sheet['synergies'] ?? [];
        if (is_array($synergies) && $synergies !== []) {
            $items = '';
            foreach ($synergies as $label => $text) {
                $items .= $this->li((string) $label, (string) $text);
            }
            $parts[] = '<h2>Synergies</h2><ul>'.$items.'</ul>';
        }

        return implode('', $parts);
    }

    private function richText(string $text): string
    {
        return RulesCharacteristicKrefReplacementCatalog::applyToMarkdown(e($text));
    }

    private function li(string $label, string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        return '<li><strong>'.e($label).'</strong> — '.$this->richText($text).'</li>';
    }
}
