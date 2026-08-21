# Entity views — IA

> Système d'affichage des entités.

## Fichiers pivots

- `resources/js/Utils/entity/resolveEntityViewComponent.js`
- `resources/js/Pages/Molecules/entity/<type>/`
- `resources/js/Pages/Organismes/entity/EntityEditForm.vue`
- Action `view-dofusdb` → store Pinia `dofusDbReference` + `DofusDbReferencePanel` (layout Main)
- Favoris BDD → `useFavoriteEntityIds` + `api.favorites.*` ; UI `FavoritesModal` / page `/favoris`
- Menu d’options : overflow titre → bord (`useHorizontalOverflowCount`, `EntityActionsDropdown`) sur toutes les vues
- Monstres : sorts **et** équipements (`MonsterCreatureSpellsList` / `MonsterCreatureItemsList`) si la créature en a
- Caractéristiques runtime : `CharacteristicsCard` densités `icon|labeled|spacious`, groupes via `creatureCharacteristicGroups.manifest.js`, `CharacteristicGroup` (`levelEffective` → `runtime.levels`), popover `CharacteristicDecompositionBody`

Voir aussi [../../features/entities/_ai.md](../../features/entities/_ai.md) et
[../../features/characteristics/COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
