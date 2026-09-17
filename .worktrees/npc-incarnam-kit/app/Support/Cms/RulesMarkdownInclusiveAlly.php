<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Normalise « allié / alliés / alliée » vers la forme inclusive **allié·e** (point médian).
 *
 * @example RulesMarkdownInclusiveAlly::apply($markdown);
 */
final class RulesMarkdownInclusiveAlly
{
    /**
     * Remplacements ordonnés (expressions longues d'abord).
     *
     * @var array<string, string>
     */
    private const PHRASE_REPLACEMENTS = [
        'créatures alliées' => 'créatures allié·e·s',
        'comme alliées' => 'comme allié·e·s',
        'un ou une allié·e' => 'un·e allié·e',
        'un ou une alliée' => 'un·e allié·e',
        'Aide d\'alliés' => 'Aide d\'allié·e·s',
        'l\'aide d\'alliés' => 'l\'aide d\'allié·e·s',
        'entre les alliés' => 'entre les allié·e·s',
        'pour les alliés' => 'pour les allié·e·s',
        'avec l\'aide d\'un allié' => 'avec l\'aide d\'un·e allié·e',
        'Aide d\'un allié' => 'Aide d\'un·e allié·e',
        'aide d\'un allié' => 'aide d\'un·e allié·e',
        'donner un avantage à un allié' => 'donner un avantage à un·e allié·e',
        'bonus à mon allié' => 'bonus à mon allié·e',
        'sur mon allié' => 'sur mon allié·e',
        'Ton allié obtient' => 'Ton allié·e obtient',
        'L\'allié attaque' => 'L\'allié·e attaque',
        'L\'allié éclaire' => 'L\'allié·e éclaire',
        'l\'aide de l\'allié' => 'l\'aide de l\'allié·e',
        'peut être allié ou ennemi' => 'peut être allié·e ou ennemi·e',
        'allié trahit' => 'allié·e trahit',
        'allié inattendu' => 'allié·e inattendu·e',
        'allié temporaire' => 'allié·e temporaire',
        'allié blessé' => 'allié·e blessé·e',
        'avec un allié' => 'avec un·e allié·e',
        'Parler brièvement avec un allié' => 'Parler brièvement avec un·e allié·e',
        'une alliée lointaine' => 'un·e allié·e lointain·e',
        'à une alliée' => 'à un·e allié·e',
        'ton alliée' => 'ton allié·e',
        'Un allié t\'aide' => 'Un·e allié·e t\'aide',
        'Un allié peut' => 'Un·e allié·e peut',
        'un allié t\'aide' => 'un·e allié·e t\'aide',
        'un allié peut' => 'un·e allié·e peut',
        'un seul allié' => 'un·e seul·e allié·e',
        'L\'allié doit' => 'L\'allié·e doit',
        'alliés uniquement' => 'allié·e·s uniquement',
        'alliés défensifs' => 'allié·e·s défensif·ve·s',
        'ennemis, alliés' => 'ennemis, allié·e·s',
        'Un allié ' => 'Un·e allié·e ',
        'Un allié.' => 'Un·e allié·e.',
        'Un allié·e' => 'Un·e allié·e',
        'Une allié·e' => 'Un·e allié·e',
        'Une alliée' => 'Un·e allié·e',
    ];

    public static function apply(string $markdown): string
    {
        $text = str_replace(array_keys(self::PHRASE_REPLACEMENTS), array_values(self::PHRASE_REPLACEMENTS), $markdown);

        $wordReplacements = [
            'alliées' => 'allié·e·s',
            'alliés' => 'allié·e·s',
            'alliée' => 'allié·e',
            'allié' => 'allié·e',
        ];

        foreach ($wordReplacements as $from => $to) {
            if ($from === 'allié') {
                // Ne pas retraiter allié·e / allié·e·s.
                $text = preg_replace('/\ballié\b(?!·)/u', $to, $text) ?? $text;

                continue;
            }

            $text = preg_replace('/\b'.preg_quote($from, '/').'\b/u', $to, $text) ?? $text;
        }

        return str_replace(
            ['un·e un·e', 'allié·e·e·s', 'Un·e Un·e', 'Une allié·e'],
            ['un·e', 'allié·e·s', 'Un·e', 'Un·e allié·e'],
            $text
        );
    }
}
