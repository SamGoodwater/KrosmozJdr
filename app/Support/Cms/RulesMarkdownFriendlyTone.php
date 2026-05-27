<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Assouplit le ton des fichiers Markdown des règles (tutoiement, moins scolaire).
 *
 * Ne supprime ni tableaux ni exemples : conversions sûres de formules récurrentes.
 *
 * @example RulesMarkdownFriendlyTone::apply($markdown);
 */
final class RulesMarkdownFriendlyTone
{
    /**
     * Impératifs « vous » → tutoiement (ordre : formes longues d'abord).
     *
     * @var array<string, string>
     */
    private const IMPERATIVE_REPLACEMENTS = [
        'N\'oubliez pas' => 'N\'oublie pas',
        'n\'oubliez pas' => 'n\'oublie pas',
        'Ne demandez pas' => 'Ne demande pas',
        'ne demandez pas' => 'ne demande pas',
        'Ne permettez pas' => 'Ne permets pas',
        'ne permettez pas' => 'ne permets pas',
        'Répartissez' => 'Répartis',
        'répartissez' => 'répartis',
        'Régénérez' => 'Régénère',
        'régénérez' => 'régénère',
        'Résolvez' => 'Résous',
        'résolvez' => 'résous',
        'Lancez' => 'Lance',
        'lancez' => 'lance',
        'Ajoutez' => 'Ajoute',
        'ajoutez' => 'ajoute',
        'Utilisez' => 'Utilise',
        'utilisez' => 'utilise',
        'Comparez' => 'Compare',
        'comparez' => 'compare',
        'Déterminez' => 'Détermine',
        'déterminez' => 'détermine',
        'Commencez' => 'Commence',
        'commencez' => 'commence',
        'Consultez' => 'Consulte',
        'consultez' => 'consulte',
        'Vérifiez' => 'Vérifie',
        'vérifiez' => 'vérifie',
        'Notez' => 'Note',
        'notez' => 'note',
        'Demandez' => 'Demande',
        'demandez' => 'demande',
        'Permettez' => 'Permets',
        'permettez' => 'permets',
        'Fixez' => 'Fixe',
        'fixez' => 'fixe',
        'Ajustez' => 'Ajuste',
        'ajustez' => 'ajuste',
        'Rappelez' => 'Rappelle',
        'rappelez' => 'rappelle',
        'Annoncez' => 'Annonce',
        'annoncez' => 'annonce',
        'Calculez' => 'Calcule',
        'calculez' => 'calcule',
        'Choisissez' => 'Choisis',
        'choisissez' => 'choisis',
        'Évitez' => 'Évite',
        'évitez' => 'évite',
        'Appliquez' => 'Applique',
        'appliquez' => 'applique',
        'Gérez' => 'Gère',
        'gérez' => 'gère',
        'Placez' => 'Place',
        'placez' => 'place',
        'Divisez' => 'Divise',
        'divisez' => 'divise',
        'Multipliez' => 'Multiplie',
        'multipliez' => 'multiplie',
        'Arrondissez' => 'Arrondis',
        'arrondissez' => 'arrondis',
        'Incluez' => 'Inclus',
        'incluez' => 'inclus',
        'Reportez' => 'Reporte',
        'reportez' => 'reporte',
        'Référez' => 'Réfère',
        'référez' => 'réfère',
        'Soustrayez' => 'Soustrais',
        'soustrayez' => 'soustrais',
        'Relisez' => 'Relis',
        'relisez' => 'relis',
        'Indiquez' => 'Indique',
        'indiquez' => 'indique',
        'Préparez' => 'Prépare',
        'préparez' => 'prépare',
        'Attribuez' => 'Attribue',
        'attribuez' => 'attribue',
        'Enregistrez' => 'Enregistre',
        'enregistrez' => 'enregistre',
        'Déclarez' => 'Déclare',
        'déclarez' => 'déclare',
        'Proposez' => 'Propose',
        'proposez' => 'propose',
        'Adaptez' => 'Adapte',
        'adaptez' => 'adapte',
    ];

    /**
     * Formulations scolaires → ton direct (contexte règles de jeu).
     *
     * @var array<string, string>
     */
    private const PHRASE_REPLACEMENTS = [
        'KrosmozJDR utilise le **dé à 20 faces (d20)** comme base pour résoudre la plupart des actions.' => 'Pour presque tout ce que tu fais à table, tu lances un **d20**.',
        'KrosmozJDR utilise le **dé à 20 faces (d20)**' => 'Pour presque tout, tu lances un **d20**',
        'KrosmozJDR s\'inspire fortement du système de **Donjons & Dragons 5ème édition**' => 'Krosmoz s\'appuie sur **Donjons & Dragons 5e**',
        'KrosmozJDR a été conçu avec trois objectifs principaux' => 'Krosmoz vise trois choses',
        'Le joueur ' => 'Tu ',
        'Le MJ détermine le DD' => 'Tu fixes le DD (en MJ)',
        'Le MJ détermine' => 'Tu détermines (en MJ)',
        'Le MJ peut' => 'En MJ, tu peux',
        'Le MJ a la liberté' => 'En MJ, tu es libre',
        'Chaque personnage dispose de ressources limitées qu\'il peut utiliser pendant son tour.' => 'À ton tour, tu as un budget de ressources — dépense-les comme tu veux (dans les limites des règles).',
        'Chaque personnage a un nombre de **points d\'action' => 'Tu as un nombre de **points d\'action',
        'Il ne peut pas utiliser plus de' => 'Tu ne peux pas utiliser plus de',
        'Il est possible de' => 'Tu peux',
        'Il faudra lancer' => 'Lance',
        'Lorsqu\'un joueur obtient un **20 naturel**' => 'Si tu fais **20 naturel**',
        'Lorsqu\'un joueur obtient un **1 naturel**' => 'Si tu fais **1 naturel**',
        'Les joueurs peuvent rapidement comprendre' => 'Tu peux vite comprendre',
        'Cette base solide permet une prise en main rapide pour les joueurs familiers de D&D, tout en offrant une structure éprouvée et équilibrée.' => 'Si tu viens de D&D, tu te sentiras chez toi — le socle est éprouvé, le reste sent bon le Dofus.',
        'Ces éléments apportent la saveur unique de Dofus tout en conservant la flexibilité d\'un jeu de rôle sur table.' => 'Résultat : l\'univers Dofus, la liberté du JdR autour de la table.',
        'Pour éviter un livre de règles trop volumineux' => 'Pour ne pas te noyer sous 800 pages',
        'Cette approche permet de se concentrer sur les **règles** plutôt que sur les **données**' => 'Ici, on te donne les **règles** ; les **listes** (sorts, objets, mobs) vivent sur le site',
    ];

    /**
     * Réécritures de lignes **Description** (infinitif administratif → accroche lisible).
     *
     * @var array<string, string> regex pattern => replacement
     */
    private const DESCRIPTION_PATTERNS = [
        '/^\*\*Description\*\* : Expliquer (.+)\.$/u' => '**Description** : $1 — expliqué clairement, avec exemples à table.',
        '/^\*\*Description\*\* : Présenter (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Décrire (.+)\.$/u' => '**Description** : $1 — tout le détail utile en jeu.',
        '/^\*\*Description\*\* : Définir (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Détaille (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Explique (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Introduit (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Résumer (.+)\.$/u' => '**Description** : $1 — version courte avant le détail.',
        '/^\*\*Description\*\* : Donner (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Réunir (.+)\.$/u' => '**Description** : $1 — pour trancher vite à table.',
        '/^\*\*Description\*\* : Proposer (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Fournir (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Classer (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Lister (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Identifier (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Cette sous-partie présente (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Cette sous-partie guide (.+)\.$/u' => '**Description** : $1.',
        '/^\*\*Description\*\* : Astuces pour (.+)\.$/u' => '**Description** : Astuces pour $1.',
        '/^\*\*Description\*\* : Aperçu (.+)\.$/u' => '**Description** : $1.',
    ];

    public static function apply(string $markdown): string
    {
        $instance = new self;

        return $instance->transform($markdown);
    }

    private function transform(string $markdown): string
    {
        $lines = preg_split('/\R/u', $markdown) ?: [$markdown];
        $out = [];

        foreach ($lines as $line) {
            $line = $this->rewriteDescriptionLine($line);
            $line = str_replace(array_keys(self::PHRASE_REPLACEMENTS), array_values(self::PHRASE_REPLACEMENTS), $line);
            $line = str_replace(array_keys(self::IMPERATIVE_REPLACEMENTS), array_values(self::IMPERATIVE_REPLACEMENTS), $line);
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    private function rewriteDescriptionLine(string $line): string
    {
        if (! str_starts_with($line, '**Description** :')) {
            return $line;
        }

        if (preg_match('/^(Tu |Comment |Voici |En bref |Tout sur |Si tu )/ui', substr($line, 17))) {
            return $line;
        }

        foreach (self::DESCRIPTION_PATTERNS as $pattern => $replacement) {
            $rewritten = preg_replace($pattern, $replacement, $line);
            if (is_string($rewritten) && $rewritten !== $line) {
                return $rewritten;
            }
        }

        return $line;
    }
}
