<?php

declare(strict_types=1);

namespace App\Services\Entity;

/**
 * Réaligne les spécialisations legacy sur les paliers de 2.4.2.
 *
 * Les exports HTML utilisent la grille 1/3/5/8/10/13/15/18/20 et le vocabulaire
 * d'avant l'inversion (« Aptitudes » pour la liste de choix type sort,
 * « Capacités » pour les bonus nommés). Ce service produit des sections sur la
 * grille 1/3/6/9/12/15/20, renomme les blocs et ne garde que 3 aptitudes, aux
 * paliers 3, 9 et 15.
 */
class LegacySpecializationRealignService
{
    public function __construct(private readonly LegacyEntitySectionImportService $importer) {}

    /**
     * Ancienne grille de paliers vers la nouvelle. Deux fusions : 13+15 → 15 et
     * 18+20 → 20. Les niveaux 13, 15, 18 et 20 sont ceux qui proposent une
     * expertise ; les envoyer sur 15 et 20 respecte 2.4.5 (expertises aux
     * paliers 9, 15 et 20 uniquement).
     */
    public const PALIER_MAP = [
        1 => 1,
        3 => 3,
        5 => 6,
        8 => 9,
        10 => 12,
        13 => 15,
        15 => 15,
        18 => 20,
        20 => 20,
    ];

    /** Les seuls paliers qui portent une aptitude, acquise sans choix. */
    public const APTITUDE_PALIERS = [3, 9, 15];

    private const CHOICE_INTRO = 'Capacités proposées à ce palier :';

    /** Ce que chaque palier accorde, d'après 2.4.2. */
    public const PALIER_SUMMARY = [
        1 => '1 capacité garantie + 1 emplacement libre · 3 compétences',
        3 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre · 1 compétence',
        6 => '1 capacité garantie + 1 emplacement libre · 1 compétence',
        9 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre · 1 compétence (expertise possible)',
        12 => '1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence',
        15 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence (expertise possible)',
        20 => '1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence (expertise possible)',
    ];

    /**
     * @param  list<array{title: string, level: int, content: string, capabilities?: list<string>}>  $parsedSections
     * @param  array{aptitudes?: array<string, int>, authored?: array<int, array{name: string, html: string}>, replacements?: array<string, string>}  $plan
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function realign(array $parsedSections, array $plan): array
    {
        $aptitudePlan = $plan['aptitudes'] ?? [];
        $authored = $plan['authored'] ?? [];
        $replacements = $plan['replacements'] ?? [];

        $presentation = [];
        /** @var array<int, array{masteries: list<string>, choices: list<string>, capacities: list<string>, aptitudes: list<string>}> $paliers */
        $paliers = [];
        /** @var array<int, list<string>> $capabilityNames */
        $capabilityNames = [];

        foreach ($parsedSections as $section) {
            $title = (string) ($section['title'] ?? '');
            $isLevelSection = preg_match('/Niveau\s+\d+/iu', $title) === 1;

            if (isset($section['capabilities'])) {
                $level = $this->remapLevel((int) ($section['level'] ?? 1));
                $capabilityNames[$level] = array_values(array_unique(array_merge(
                    $capabilityNames[$level] ?? [],
                    array_map('strval', $section['capabilities']),
                )));

                continue;
            }

            if (! $isLevelSection) {
                $presentation[] = [
                    'title' => $title,
                    'level' => 1,
                    'content' => $this->swapVocabulary($this->stripEditorArtifacts((string) ($section['content'] ?? ''))),
                ];

                continue;
            }

            $level = $this->remapLevel((int) ($section['level'] ?? 1));
            $paliers[$level] ??= ['masteries' => [], 'choices' => [], 'capacities' => [], 'aptitudes' => []];

            $content = $this->remapLevelReferences($this->stripEditorArtifacts((string) ($section['content'] ?? '')));
            $content = strtr($content, $replacements);

            foreach ($this->splitH2Blocks($content) as $block) {
                $body = trim($block['body']);
                if ($body === '') {
                    continue;
                }

                $kind = $this->classifyHeading($block['heading']);

                if ($kind === 'masteries') {
                    $paliers[$level]['masteries'][] = $this->swapVocabulary($body);

                    continue;
                }

                if ($kind === 'choices') {
                    $paliers[$level]['choices'][] = $this->normalizeChoiceIntro($this->swapVocabulary($body));

                    continue;
                }

                $features = $this->splitFeatures($body);
                if ($features === []) {
                    $paliers[$level]['capacities'][] = $this->swapVocabulary($body);

                    continue;
                }

                foreach ($features as $feature) {
                    $target = $aptitudePlan[$feature['name']] ?? null;
                    $html = '<h3>'.e($this->swapVocabulary($feature['name'])).'</h3>'.$this->swapVocabulary($feature['body']);

                    if ($target !== null) {
                        $paliers[$target] ??= ['masteries' => [], 'choices' => [], 'capacities' => [], 'aptitudes' => []];
                        $paliers[$target]['aptitudes'][] = $html;

                        continue;
                    }

                    $paliers[$level]['capacities'][] = $html;
                }
            }
        }

        foreach ($authored as $level => $aptitude) {
            $level = (int) $level;
            $paliers[$level] ??= ['masteries' => [], 'choices' => [], 'capacities' => [], 'aptitudes' => []];
            $paliers[$level]['aptitudes'][] = '<h3>'.e((string) $aptitude['name']).'</h3>'.(string) $aptitude['html'];
        }

        ksort($paliers);
        $sections = $presentation;

        foreach ($paliers as $level => $blocks) {
            $content = $this->buildPalierHtml($level, $blocks);
            if ($content !== '') {
                $sections[] = [
                    'title' => 'Niveau '.$level,
                    'level' => $level,
                    'content' => $content,
                ];
            }

            $names = $capabilityNames[$level] ?? [];
            if ($names !== []) {
                $sections[] = [
                    'title' => "Capacités (niveau {$level})",
                    'level' => $level,
                    'content' => $this->importer->buildCapabilityKrefListHtml($names),
                    'capabilities' => $names,
                ];
            }
        }

        return $sections;
    }

    public function remapLevel(int $legacyLevel): int
    {
        return self::PALIER_MAP[$legacyLevel] ?? $legacyLevel;
    }

    /**
     * @param  array{masteries: list<string>, choices: list<string>, capacities: list<string>, aptitudes: list<string>}  $blocks
     */
    private function buildPalierHtml(int $level, array $blocks): string
    {
        $parts = [];

        $summary = self::PALIER_SUMMARY[$level] ?? null;
        if ($summary !== null) {
            $parts[] = '<p><strong>À ce palier</strong> : '.e($summary).'</p>';
        }

        if ($blocks['masteries'] !== []) {
            $parts[] = '<h2>Maîtrises</h2>'.implode('', $blocks['masteries']);
        }

        $choices = $blocks['choices'];
        foreach ($choices as $index => $choice) {
            if ($index > 0) {
                $choices[$index] = str_replace(self::CHOICE_INTRO, 'Autres capacités proposées :', $choice);
            }
        }

        $capacities = array_merge($choices, $blocks['capacities']);
        if ($capacities !== []) {
            $parts[] = '<h2>Capacités</h2>'.implode('', $capacities);
        }

        if ($blocks['aptitudes'] !== []) {
            $parts[] = '<h2>Aptitude (automatique)</h2>'.implode('', $blocks['aptitudes']);
        }

        return count($parts) <= 1 ? '' : implode('', $parts);
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function splitH2Blocks(string $html): array
    {
        $parts = preg_split('/<h2\b[^>]*>(.*?)<\/h2>/usi', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (! is_array($parts) || $parts === []) {
            return [];
        }

        $blocks = [];
        for ($i = 1; $i < count($parts); $i += 2) {
            $blocks[] = [
                'heading' => (string) $parts[$i],
                'body' => (string) ($parts[$i + 1] ?? ''),
            ];
        }

        return $blocks;
    }

    /**
     * Découpe un bloc de bonus nommés sur ses titres h3/h4.
     *
     * @return list<array{name: string, body: string}>
     */
    private function splitFeatures(string $html): array
    {
        $parts = preg_split('/<(h3|h4)\b[^>]*>(.*?)<\/\1>/usi', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (! is_array($parts) || count($parts) < 4) {
            return [];
        }

        $features = [];
        for ($i = 2; $i < count($parts); $i += 3) {
            $name = trim(html_entity_decode(strip_tags((string) $parts[$i])));
            $body = trim((string) ($parts[$i + 1] ?? ''));
            if ($name === '') {
                continue;
            }

            $features[] = ['name' => $name, 'body' => $body];
        }

        return $features;
    }

    /**
     * @return 'masteries'|'choices'|'features'
     */
    private function classifyHeading(string $heading): string
    {
        $normalized = mb_strtolower(trim(html_entity_decode(strip_tags($heading))));

        if (str_starts_with($normalized, 'mait') || str_starts_with($normalized, 'maît')) {
            return 'masteries';
        }

        return str_starts_with($normalized, 'aptitude') ? 'choices' : 'features';
    }

    /**
     * Le nombre de choix est désormais porté par le palier (1, ou 2 avec
     * l'emplacement libre) : les « une / deux au choix » legacy deviennent faux
     * dès qu'on fusionne deux anciens paliers.
     */
    private function normalizeChoiceIntro(string $html): string
    {
        return (string) preg_replace(
            [
                '/(?:Une|Deux|Trois)\s+(?:autre\s+)?capacités?\s+au\s+choix\s+(?:entre|parmi)[^<:]*:?/iu',
                '/Choisissez\s+(?:une|deux|trois)\s+capacités?\s+parmi[^<:]*:?/iu',
            ],
            self::CHOICE_INTRO,
            $html,
        );
    }

    /**
     * Remappe les niveaux cités dans le texte (paliers de montée en puissance
     * d'un bonus) sur la nouvelle grille.
     */
    private function remapLevelReferences(string $html): string
    {
        return (string) preg_replace_callback('/\b(niveau\s+)(\d+)\b/iu', function (array $match): string {
            $level = (int) $match[2];

            return $match[1].($this->remapLevel($level));
        }, $html);
    }

    /**
     * Dans le vocabulaire legacy, « aptitude » désignait ce qu'on appelle
     * désormais « capacité ». Le sens inverse n'existait pas dans ces exports.
     */
    private function swapVocabulary(string $html): string
    {
        $replacements = [
            '/\bd[\'’]aptitudes\b/u' => 'de capacités',
            '/\bd[\'’]aptitude\b/u' => 'de capacité',
            '/\bD[\'’]aptitudes\b/u' => 'De capacités',
            '/\bD[\'’]aptitude\b/u' => 'De capacité',
            '/\bAptitudes\b/u' => 'Capacités',
            '/\bAptitude\b/u' => 'Capacité',
            '/\baptitudes\b/u' => 'capacités',
            '/\baptitude\b/u' => 'capacité',
        ];

        return (string) preg_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Retire les résidus de l'éditeur legacy (bouton de sauvegarde, pense-bête).
     */
    private function stripEditorArtifacts(string $html): string
    {
        $html = (string) preg_replace('/<a\s+id="saveCkeditor".*$/us', '', $html);
        $html = (string) preg_replace('/\s*-\s*N[\'’]oublier pas d[\'’]enregistrer r[ée]guli[èe]rement\s*/u', '', $html);
        $html = (string) preg_replace('#(?:\s*</div>)+\s*$#u', '', $html);

        return trim($html);
    }
}
