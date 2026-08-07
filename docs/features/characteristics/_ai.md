# Caractéristiques — IA

> Valeurs, formules, limites, normes et composition base / objets / contexte.

## Quand lire ce nœud

- Modifier une formule, une limite ou une norme de caractéristique.
- Comprendre comment une fiche monstre / PNJ calcule ses stats selon le niveau.
- Brancher l’UI (sélecteur de niveau, popover de décomposition, édition de formules).

## Concepts clés

- **Définitions** : JSON seeders → tables `characteristics` + pivots `characteristic_*`.
- **Composition** : `total = base + objets + contexte`, sauf si un **total explicite** (colonne) est présent. Détail : [COMPUTED_VALUES.md](./COMPUTED_VALUES.md).
- **Grammaire** : `{ expression }` + suffixe d’arrondi ; domaines `[x-y]` / `[ndX]` **uniquement sur le niveau**.
- **Runtime créature** : `CreatureRuntimeStatsService` → `levels[]` pour le sélecteur de niveau.
- **Conversion Dofus** : pipeline séparé (`conversion_formula`, `[d]`).

## Fichiers pivots

- `app/Services/Characteristic/Formula/FormulaExpressionParser.php`
- `app/Services/Characteristic/Domain/LevelDomainResolver.php`
- `app/Services/Creature/Runtime/CreatureRuntimeStatsService.php`
- `app/Support/Creature/CreatureComposableColumns.php`
- `resources/js/Utils/characteristic/formulaGrammar.js`
- `resources/js/Pages/Molecules/data-input/CharacteristicFormulaField.vue`
- `resources/js/Pages/Organismes/data-display/CharacteristicsCard.vue`
- `database/seeders/*CharacteristicSeeder.php`

## Liens

- Doc humaine : [README.md](./README.md), [COMPUTED_VALUES.md](./COMPUTED_VALUES.md)
- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Effets : [../effects/_ai.md](../effects/_ai.md)
- Vues entités : [../../frontend/entity-views/_ai.md](../../frontend/entity-views/_ai.md)
