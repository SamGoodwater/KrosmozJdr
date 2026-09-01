# Caractéristiques — IA

> Valeurs, formules, limites, normes et composition base / objets / contexte.

## Quand lire ce nœud

- Modifier une formule, une limite ou une norme de caractéristique.
- Comprendre comment une fiche monstre / PNJ calcule ses stats selon le niveau.
- Brancher l’UI (sélecteur de niveau, popover de décomposition, édition de formules).

## Concepts clés

- **Définitions** : JSON seeders → tables `characteristics` + pivots `characteristic_*`. Objets ciblés (chapeau, cape, amulette…) : `item_type_dofus_ids` (IDs DofusDB). Reprise : `php artisan characteristics:definitions-apply --item-types`.
- **Share Inertia** : `CharacteristicMetaByDbColumnService` expose `helper` / `descriptions` et `limit_min` / `limit_max` (entiers figés du pivot ; les formules sont ignorées). Cache `characteristics:frontend:v3`.
- **Composition** : `total = base + objets + contexte`, sauf si un **total explicite** (colonne) est présent. Détail : [COMPUTED_VALUES.md](./COMPUTED_VALUES.md).
- **DO mult.** : colonne composable `do_fixe_multiple` (`fixed_damage_multiple_creature`), visible dans Dommages.
- **Grammaire** : `{ expression }` + suffixe d’arrondi ; domaines `[x-y]` / `[ndX]` **uniquement sur le niveau**.
- **Runtime créature** : `CreatureRuntimeStatsService` → `levels[]` pour le sélecteur de niveau. Endpoint `resolved-stats` : `CreaturePolicy::viewResolvedStats` (visibilité monstre/PNJ).
- **Conversion Dofus** : pipeline séparé (`conversion_formula`, `[d]`).
- **Bonus équipement (MJ)** : `EquipmentBonusTableService` projette `formula` JSON par bandes 1–2…19–20 + types d’item ; API `GET /api/characteristics/equipment-bonus-table` (rôle ≥ MJ).

## Fichiers pivots

- `app/Services/Characteristic/Formula/FormulaExpressionParser.php`
- `app/Services/Characteristic/Domain/LevelDomainResolver.php`
- `app/Services/Creature/Runtime/CreatureRuntimeStatsService.php`
- `app/Support/Creature/CreatureComposableColumns.php`
- `app/Support/Creature/CreatureMasteryColumns.php`
- `resources/js/Utils/Entity/creatureCharacteristicGroups.manifest.js`
- `resources/js/Utils/Entity/buildCreatureCharacteristicGroups.js`
- `resources/js/Utils/Entity/buildCreatureCompetenceGroups.js`
- `resources/js/Utils/characteristic/formulaGrammar.js`
- `resources/js/Pages/Molecules/data-input/CharacteristicFormulaField.vue`
- `resources/js/Pages/Molecules/data-display/AbilityScoreStack.vue`
- `resources/js/Pages/Organismes/data-display/CharacteristicsCard.vue`
- `database/seeders/*CharacteristicSeeder.php`
- `app/Services/Characteristic/Reference/EquipmentBonusTableService.php`

## Liens

- Doc humaine : [README.md](./README.md), [COMPUTED_VALUES.md](./COMPUTED_VALUES.md)
- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Effets : [../effects/_ai.md](../effects/_ai.md)
- Vues entités : [../../frontend/entity-views/_ai.md](../../frontend/entity-views/_ai.md)
