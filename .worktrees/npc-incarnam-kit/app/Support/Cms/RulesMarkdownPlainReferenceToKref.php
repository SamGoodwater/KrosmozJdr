<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Convertit les renvois textuels internes (sections numérotées, bibliothèques) en shortcodes {@code [[kref:…]]}.
 *
 * @example
 * « consulte la section **2.3. Choisir sa classe** » → {@code [[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]]}
 */
final class RulesMarkdownPlainReferenceToKref
{
    /** @var array<string, string> Libellé (normalisé) → slug page bibliothèque */
    private const BIBLIOTHEQUE_SLUGS = [
        'les classes' => 'bibliotheque-breed',
        'classes' => 'bibliotheque-breed',
        'specialisations' => 'bibliotheque-specialization',
        'spécialisations' => 'bibliotheque-specialization',
        'sorts' => 'bibliotheque-spell',
        'capacites' => 'bibliotheque-capability',
        'capacités' => 'bibliotheque-capability',
        'monstres' => 'bibliotheque-monster',
        'equipements' => 'bibliotheque-item',
        'équipements' => 'bibliotheque-item',
        'equipement' => 'bibliotheque-item',
        'équipement' => 'bibliotheque-item',
        'panoplies' => 'bibliotheque-panoply',
        'consommables' => 'bibliotheque-consumable',
        'ressources' => 'bibliotheque-resource',
        'etats' => 'bibliotheque-condition',
        'états' => 'bibliotheque-condition',
        'traits' => 'bibliotheque-creature-trait',
    ];

    private const PLAIN_SECTION_LINE = '/Pour plus de détails\s*:\s*Section\s+(?<num>\d+(?:\.\d+)*)\s*[—–-]\s*(?<title>[^\.\n]+)/iu';

    private const SECTION_IN_TEXT = '/(?:'
        .'consulte la section|Consulte la section|'
        .'Pour les règles détaillées, consulte la section|'
        .'Pour les règles détaillées sur[^,]*, consulte la section|'
        .'Pour plus de détails, consulte la section|'
        .'la section|section|détaillé(?:e|s)? dans la section|détail(?:s)?\s*:\s*'
        .')\s*'
        .'\*\*(?<num>\d+(?:\.\d+)*)\.\s*(?:\[\[kref:[^|]+\|(?<kreflabel>[^\]]+)\]\]|(?<title>[^*]+?))\*\*/iu';

    private const BIBLIOTHEQUE_IN_TEXT = '/\*\*Bibliothèque\s*→\s*(?<label>[^*]+?)\*\*/iu';

    private const BIBLIOTHEQUE_INLINE = '/Bibliothèque\s*→\s*(?<label>[A-Za-zÀ-ÿÉéèêëïîôùûüç\-]+)/iu';

    private const BIBLIOTHEQUE_EN_LIGNE = '/\bbibliothèque en ligne\b/iu';

    public static function apply(string $markdown, ?RulesTocSlugIndex $index): string
    {
        if (trim($markdown) === '') {
            return $markdown;
        }

        $out = preg_replace_callback(self::PLAIN_SECTION_LINE, function (array $m) use ($index): string {
            if ($index === null || str_contains($m[0], '[[kref:')) {
                return $m[0];
            }

            $number = trim((string) ($m['num'] ?? ''));
            $title = trim((string) ($m['title'] ?? ''));
            $kref = $index->krefForSectionNumber($number, $title);

            return $kref ?? $m[0];
        }, $markdown);

        $out = is_string($out) ? $out : $markdown;

        $out = preg_replace_callback(self::SECTION_IN_TEXT, function (array $m) use ($index): string {
            if ($index === null || str_contains($m[0], '[[kref:')) {
                return $m[0];
            }

            $number = trim((string) ($m['num'] ?? ''));
            $titleRaw = trim((string) ($m['kreflabel'] ?? ''));
            if ($titleRaw === '') {
                $titleRaw = trim((string) ($m['title'] ?? ''));
            }
            [$title, $subKeyword] = self::parseTitleWithArrow($titleRaw);

            $kref = $index->krefForSectionNumber($number, $title, $subKeyword);

            return $kref ?? $m[0];
        }, $markdown);

        $out = is_string($out) ? $out : $markdown;

        $out = preg_replace_callback(self::BIBLIOTHEQUE_IN_TEXT, function (array $m): string {
            $label = trim((string) ($m['label'] ?? ''));
            $slug = self::bibliothequeSlugForLabel($label);
            if ($slug === null) {
                return $m[0];
            }

            return '[[kref:page:'.$slug.'|Bibliothèque → '.$label.']]';
        }, $out);

        $out = is_string($out) ? $out : $markdown;

        $out = (string) preg_replace(
            self::BIBLIOTHEQUE_EN_LIGNE,
            '[[kref:page:bibliotheque-condition|bibliothèque en ligne]]',
            $out,
        );

        $out = preg_replace_callback(self::BIBLIOTHEQUE_INLINE, function (array $m): string {
            if (str_contains($m[0], '[[kref:')) {
                return $m[0];
            }
            $label = trim((string) ($m['label'] ?? ''));
            $slug = self::bibliothequeSlugForLabel($label);
            if ($slug === null) {
                return $m[0];
            }

            return '[[kref:page:'.$slug.'|Bibliothèque → '.$label.']]';
        }, $out);

        return is_string($out) ? $out : $markdown;
    }

    /**
     * @return array{0: string, 1: string|null} titre affiché, mot-clé L3 après « → »
     */
    private static function parseTitleWithArrow(string $titleRaw): array
    {
        if (! str_contains($titleRaw, '→')) {
            return [$titleRaw, null];
        }

        $parts = array_map(trim(...), explode('→', $titleRaw, 2));
        $right = $parts[1] ?? '';
        $display = $right !== '' ? $right : $titleRaw;

        return [$display, $right !== '' ? $right : null];
    }

    private static function bibliothequeSlugForLabel(string $label): ?string
    {
        $normalized = mb_strtolower(trim($label));
        $normalized = str_replace(['’', "'"], '', $normalized);

        return self::BIBLIOTHEQUE_SLUGS[$normalized] ?? null;
    }
}
