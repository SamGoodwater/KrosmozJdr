<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Passe inclusive globale sur les Markdown des règles (allié·e, en-têtes, exemples de classes).
 *
 * @example RulesMarkdownInclusiveText::apply($markdown);
 */
final class RulesMarkdownInclusiveText
{
    /** @var array<int, string> */
    private const CLASS_NAMES = [
        'Iop', 'Eniripsa', 'Crâ', 'Cra', 'Enutrof', 'Xélor', 'Osamodas', 'Sram', 'Féca',
        'Pandawa', 'Sacrieur', 'Sadida', 'Ecaflip', 'Roublard', 'Steamer', 'Zobal', 'Huppermage',
    ];

    /**
     * @var array<string, string>
     */
    private const PHRASE_REPLACEMENTS = [
        '### Pour le MJ' => '### En MJ',
        '### Pour les débutants' => '### Si tu débutes',
        '### Pour les fans de Dofus' => '### Si tu viens de Dofus',
        'Stabiliser un compagnon inconscient' => 'Stabiliser un·e allié·e inconscient·e',
        'Des compagnons' => 'Des allié·e·s',
        'L\'Eniripsa est le meilleur soigneur, capable de maintenir son équipe en vie.' => 'Un personnage **Eniripsa** excelle au soin d\'équipe.',
        'Les soins de l\'Eniripsa sont plus efficaces' => 'Les soins d\'un personnage **Eniripsa** sont plus efficaces',
        '**Description** : Le Sram peut' => '**Description** : Un personnage **Sram** peut',
        '**Description** : Le Féca peut' => '**Description** : Un personnage **Féca** peut',
        '**Description** : Le Xélor peut' => '**Description** : Un personnage **Xélor** peut',
        '**Impact** : Le Sram contrôle' => '**Impact** : Le personnage contrôle',
        '**Impact** : Le Féca crée' => '**Impact** : Le personnage crée',
        '**Impact** : Le Xélor contrôle' => '**Impact** : Le personnage contrôle',
        'Pourquoi ton personnage a-t-il choisi cette voie ?' => 'Pourquoi as-tu choisi cette voie pour ton personnage ?',
        '**Exemple** : Un Iop niveau 1 connaît' => '**Exemple** : Un personnage **Iop** niv. 1 connaît',
        '- Il pourra mieux immobiliser ses adversaires' => '- Meilleure immobilisation des adversaires',
        '- Il pourra soulever des charges plus lourdes' => '- Capacité à soulever des charges plus lourdes',
        '- Il résistera mieux aux sorts ennemis' => '- Meilleure résistance aux sorts ennemis',
        '- Il percevra mieux les dangers' => '- Meilleure perception des dangers',
        '- Il esquivera mieux les attaques' => '- Meilleure esquive des attaques',
        '- Il se déplacera plus efficacement' => '- Déplacements plus efficaces',
        '- Ses sorts Terre feront plus de dégâts' => '- Sorts Terre plus puissants',
        '- Ses sorts de soin seront plus efficaces' => '- Sorts de soin plus efficaces',
        '- Ses sorts Air seront plus précis et puissants' => '- Sorts Air plus précis et puissants',
        '- Il équipe un objet' => '- Équipement d\'un objet',
        '- Il consomme une potion' => '- Consommation d\'une potion',
        '- Il peut bénéficier de 14' => '- Peut bénéficier temporairement de 14',
        '> **Résultat** : Il obtient **+2' => '> **Résultat** : Bonus **+2',
        'Si le voleur obtient un résultat plus élevé : Il passe inaperçu' => 'Si le voleur obtient un résultat plus élevé : passage inaperçu',
        'Si le garde obtient un résultat plus élevé : Il détecte le voleur' => 'Si le garde obtient un résultat plus élevé : le voleur est repéré',
        'Si le marchand obtient un résultat plus élevé : Il détecte la supercherie' => 'Si le marchand obtient un résultat plus élevé : supercherie détectée',
        'Si le personnage 1 obtient un résultat plus élevé : Il pousse la porte dans sa direction' => 'Si le personnage 1 obtient un résultat plus élevé : la porte cède de son côté',
        'Si le personnage 2 obtient un résultat plus élevé : Il pousse la porte dans sa direction' => 'Si le personnage 2 obtient un résultat plus élevé : la porte cède de son côté',
    ];

    public static function apply(string $markdown): string
    {
        $text = RulesMarkdownInclusiveAlly::apply($markdown);
        $text = str_replace(array_keys(self::PHRASE_REPLACEMENTS), array_values(self::PHRASE_REPLACEMENTS), $text);
        $text = self::prefixClassExamples($text);
        $text = self::rewriteOsamodasExplorationExample($text);

        return $text;
    }

    private static function prefixClassExamples(string $text): string
    {
        $pattern = '/\b(Un|Une|L\')('.implode('|', self::CLASS_NAMES).')\b/u';

        return preg_replace_callback($pattern, static function (array $m): string {
            $article = $m[1];
            $class = $m[2];

            if (str_starts_with($article, 'L\'')) {
                return 'Un personnage **'.$class.'**';
            }

            if ($article === 'Une') {
                return 'Un personnage **'.$class.'**';
            }

            // Déjà normalisé.
            if (preg_match('/personnage \*\*'.preg_quote($class, '/').'\*\*/u', $m[0])) {
                return $m[0];
            }

            return 'Un personnage **'.$class.'**';
        }, $text) ?? $text;
    }

    private static function rewriteOsamodasExplorationExample(string $text): string
    {
        $old = '**Exemple** : Lorsqu\'un personnage **Osamodas** invoque un bouftou hors combat, elle a une **réussite automatique**. Elle peut, si elle le souhaite, lancer les dés. Dans ce cas, le sort est réussi si elle ne fait pas d\'échec critique. L\'intérêt est de faire un coup critique.';

        $new = '**Exemple** : Lorsqu\'un personnage **Osamodas** invoque un bouftou hors combat, c\'est une **réussite automatique**. Le personnage peut quand même lancer les dés : sans échec critique, le sort réussit — utile pour tenter un critique.';

        return str_replace($old, $new, $text);
    }
}
