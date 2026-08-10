# Entity views — IA

> Système d'affichage des entités.

## Fichiers pivots

- `resources/js/Utils/entity/resolveEntityViewComponent.js`
- `resources/js/Pages/Molecules/entity/<type>/`
- `resources/js/Pages/Organismes/entity/EntityEditForm.vue`
- Action `view-dofusdb` → store Pinia `dofusDbReference` + `DofusDbReferencePanel` (layout Main)
- Favoris BDD → `useFavoriteEntityIds` + `api.favorites.*` ; UI `FavoritesModal` / page `/favoris`
- Caractéristiques runtime : `CharacteristicsCard` densités `icon|labeled|spacious`, groupes via `creatureCharacteristicGroups.manifest.js`, `CharacteristicGroup` (`levelEffective` → `runtime.levels`), popover `CharacteristicDecompositionBody`

Voir aussi [../../features/entities/_ai.md](../../features/entities/_ai.md) et
[../../features/characteristics/COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
