# Caractéristiques — IA

> Valeurs, formules, limites, normes et composition base / objets / contexte.

## Quand lire ce nœud

- Modifier une formule, une limite ou une norme de caractéristique.
- Comprendre comment une fiche monstre / PNJ calcule ses stats selon le niveau.
- Brancher l’UI (sélecteur de niveau, popover de décomposition, édition de formules).

## Concepts clés

- **Définitions** : JSON seeders → tables `characteristics` + pivots `characteristic_*`. Objets ciblés (chapeau, cape, amulette…) : `item_type_dofus_ids` (IDs DofusDB). Reprise : `php artisan characteristics:definitions-apply --item-types`.
- **Share Inertia** : `CharacteristicMetaByDbColumnService` expose `helper` / `descriptions` et `limit_min` / `limit_max` (entiers figés du pivot ; les formules sont ignorées). Cache `characteristics:frontend:v3`.
- **Limites** : `min`/`max` numériques = plafond absolu (UI + validation + clamp scrapping). Pas de clamp post-formule sur le runtime créature : les mods joueur embarquent `⌊niv/4⌋+2` et le max **+7** dans la formule ; scores principaux `max=24` (absolu), le plafond progressif `14+⌊niv/2⌋` est une règle de répartition, pas une formule de colonne.
- **Composition** : `total = base + objets + contexte`, sauf si un **total explicite** (colonne) est présent. Détail : [COMPUTED_VALUES.md](./COMPUTED_VALUES.md).
- **DO mult.** : colonne composable `do_fixe_multiple` (`fixed_damage_multiple_creature`), visible dans Dommages.
- **Grammaire** : `{ expression }` + suffixe d’arrondi ; domaines `[x-y]` / `[ndX]` **uniquement sur le niveau**.
- **Runtime créature** : `CreatureRuntimeStatsService` → `levels[]` pour le sélecteur de niveau. Endpoint `resolved-stats` : `CreaturePolicy::viewResolvedStats` (visibilité monstre/PNJ) + objets `visibleToUser` avant agrégation.
- **Conversion Dofus** : pipeline séparé (`conversion_formula`, `[d]`).
- **Bonus équipement (MJ)** : `EquipmentBonusTableService` projette `formula` JSON par bandes 1–2…19–20 + types d’item ; API `GET /api/characteristics/equipment-bonus-table` (rôle ≥ MJ).
- **Runes de forgemagie (public)** : `ForgemagieRuneTableService` filtre `characteristic_object` sur `forgemagie_max > 0` + `rune_price_per_unit` non nul, joint `characteristic_object_item_type` (vide = tous les équipements) ; API `GET /api/characteristics/forgemagie-rune-table`. Source de vérité des prix : la base, pas les règles.
- **Prix équipements / consommables** : `EquipmentPriceCalculator` (bonus × `base_price_per_unit` + 150×niveau + 200×rareté), `ConsumablePriceCalculator` (somme recette), `EntityPriceRecalculator`. Plus de multiplicateur puissance.

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
- `app/Services/Characteristic/Pricing/EquipmentPriceCalculator.php`
- `app/Services/Characteristic/Pricing/ConsumablePriceCalculator.php`
- `app/Services/Characteristic/Pricing/EntityPriceRecalculator.php`

## Liens

- Doc humaine : [README.md](./README.md), [COMPUTED_VALUES.md](./COMPUTED_VALUES.md)
- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Effets : [../effects/_ai.md](../effects/_ai.md)
- Vues entités : [../../frontend/entity-views/_ai.md](../../frontend/entity-views/_ai.md)
