# Caractéristiques — Phase E (release 1.3.2)

**Objectif** : fiabiliser l’édition MJ (normes / graphique), les effets de sorts sans chips structurés, l’audit des définitions JSON et la recette scrapping panoplies.

**Voir aussi** : [Feuille de route Phase E](../110-%20To%20Do/To%20do%201.3.1%20vers%201.3.2.md#caractéristiques) · [CHECKLIST release 1.3.2](../110-%20To%20Do/CHECKLIST-release-1.3.2.md)

## Composants livrés

| Élément | Rôle |
| --- | --- |
| `CharacteristicNormsHelpButton.vue` | Bouton `?` → modal `NormsViewer` via `GET /api/characteristics/{key}/norms/{entity}`. |
| `EntityEditFormFieldBody.vue` | Affiche le bouton pour les champs liés au store (`characteristicsGroup` ou `creature.byMonsterField` : taille, boss, etc.). |
| `Spell._toEffectSummaryCell` | Si `effect_usages_chips` vide : texte `effect_usages_summary`, sinon cellule effet HTML (`_toEffectCell`), sinon `—`. |
| `characteristics:audit-definitions` | Commande d’audit des JSON sous `database/seeders/data/characteristic-definitions/`. |

## Édition monstre / sorts

- **Monstre** : `getMonsterFieldMeta()` mappe `size`, `is_boss` → clés `monster_size`, `monster_is_boss` ; entité API `monster`.
- **Sort** : `characteristics-group="spell"` sur `SpellEditFormContent` ; champs booléens / numériques avec métadonnées BDD.

## Revue JSON (ligne par ligne)

Priorité éditoriale : **créature → objet → sort** (voir feuille de route). L’audit automatique ne remplace pas la revue métier ; il garantit nommage et structure.

```bash
php artisan characteristics:audit-definitions
php artisan characteristics:validate-creature-formula-placeholders
```

## Scrapping panoplies

Tests de non-régression :

- `tests/Feature/Scrapping/ScrappingRelationsTest.php` — `test_import_panoply_with_items_imports_missing_items_and_creates_relations`, `test_import_panoply_resolves_equipment_recipe_relations`
- `tests/Feature/Scrapping/ScrappingOrchestratorTest.php` — `test_import_panoply_complete_workflow`

Recette : import panoplie avec `$populate=items` (items membres + relations).

## Tests

- `tests/Unit/Services/Characteristics/CharacteristicDefinitionReaderTest.php` — cohérence globale JSON (282 fichiers).
- `tests/unit/models/spell-effect-summary-fallback.test.js` — fallback colonne Effets sans chips.

## Suite (hors Phase E)

Revue éditoriale continue des 282 définitions, alignement éléments sorts avec `docs/400- Jeu` (Phase F / tableaux).
