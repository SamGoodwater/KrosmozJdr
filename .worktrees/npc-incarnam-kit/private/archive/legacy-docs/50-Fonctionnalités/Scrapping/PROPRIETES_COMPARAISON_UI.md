# Propriétés affichées dans la comparaison (Brut / Converti / Krosmoz)

## Comportement

Dans le tableau de résultats du scrapping, le détail dépliable (et la modale Comparer) affichent pour chaque ligne les propriétés en trois colonnes : **Brut (DofusDB)**, **Converti**, **Krosmoz (existant)**.

Seules les **propriétés jugées utiles** sont affichées : celles qui ont une règle de conversion (mapping). Les autres champs bruts ou internes sont masqués pour garder une liste lisible.

## Source de vérité : le mapping

La liste des propriétés affichées est dérivée du **mapping** de chaque entité :

- **Fichiers** : `resources/scrapping/config/sources/dofusdb/entities/*.json` (ex. `monster.json`, `spell.json`, `item.json`).
- Chaque entrée du tableau `mapping` possède une clé **`key`** (ex. `name`, `level`, `dofusdb_id`).
- L’API **GET /api/scrapping/config** expose ces clés sous le nom **`comparisonKeys`** par type d’entité.
- Le frontend (composable `useScrappingCompare`) ne garde que les lignes dont la propriété correspond à une de ces clés (ou à une clé imbriquée, ex. `name.fr` pour la clé `name`).

Donc : **ce qui est dans le mapping est ce qui est affiché** dans la comparaison. Pas de liste séparée à maintenir.

## Référence détaillée

Voir **MAPPING_ENTRIES_REFERENCE.md** pour la liste des clés par entité (monster, spell, item), les propriétés DofusDB non mappées et les entrées en réserve (`_mappingUnused` / `_mappingUnusedDocumentation` dans les JSON).

## Modifier les propriétés affichées

Pour **ajouter** une propriété à la comparaison :

1. Ajouter une entrée dans le **mapping** du JSON de l’entité concernée (avec `key`, `from`, `to`, `formatters` si besoin).
2. Recharger la config (rafraîchir la page scrapping ou rappeler l’API config).

Pour **retirer** une propriété de l’affichage (sans supprimer la conversion) :

- Aujourd’hui, la liste affichée = liste du mapping. Retirer du mapping supprimerait aussi la conversion.
- Si un jour on souhaite une liste d’affichage plus courte que le mapping, il faudrait introduire une clé dédiée (ex. `comparisonDisplayKeys`) dans la config et l’utiliser côté front à la place de `comparisonKeys` pour le filtre d’affichage uniquement.

## Détail technique (frontend)

- **Composable** : `useScrappingCompare.js`
- **Config** : `configRef` (ex. `configEntitiesByKey`) fourni par le Dashboard, issu de `/api/scrapping/config`.
- **Filtre** : `filterAllowedComparisonKeys(keys, entityTypeStr)` ne garde que les clés présentes dans `comparisonKeys` (ou dont le nom se termine par `.comparisonKey` pour les variantes imbriquées).
- Le filtre est appliqué **dès que** la config définit des `comparisonKeys` pour le type d’entité (y compris lorsqu’un enregistrement Krosmoz existant est présent), afin d’éviter d’afficher trop de champs.

## Références

- Backend : `App\Http\Controllers\Scrapping\ScrappingConfigController::extractComparisonKeys()` (extrait les `key` du mapping).
- Config loader : `App\Services\Scrapping\Core\Config\ConfigLoader` (charge les JSON d’entités ; le mapping peut être surchargé par la BDD via `ScrappingMappingService`).
