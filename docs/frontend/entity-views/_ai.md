# Entity views — IA

> Système d'affichage des entités.

## Fichiers pivots

- `resources/js/Utils/entity/resolveEntityViewComponent.js`
- `resources/js/Pages/Molecules/entity/<type>/`
- `resources/js/Pages/Organismes/entity/EntityEditForm.vue`
- Action `view-dofusdb` → store Pinia `dofusDbReference` + `DofusDbReferencePanel` (layout Main)
- Favoris BDD → `useFavoriteEntityIds` + `api.favorites.*` ; UI `FavoritesModal` / page `/favoris`
- Tableau objets (vue Colonnes) : image + nom + niveau + type + rareté + bonus ; description / résumé / prix masqués ; `state` réservé aux éditeurs. Tooltips d’en-tête : `TanStackTableHeader` lit `column.tooltip`.
- Tableaux d’entités : filtres auto-appliqués (server-side), `filters[k][]`, tri via `sort.field` / alias SQL, recherche `search=` ou nom côté client.
- Overlay sort depuis un monstre : payload `effect_usages_chips` + `SpellViewMinimal` étendu (actions à droite du titre, overflow dropdown). Fetch `whitelist[]=` seulement si les chips manquent.
- Barre d’actions (`EntityActions`) : raccourcis inline mesurés (`useHorizontalOverflowCount`) ; ce qui ne tient pas reste dans le « ⋮ ».

Voir aussi [../../features/entities/_ai.md](../../features/entities/_ai.md) et
[../../features/characteristics/COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
