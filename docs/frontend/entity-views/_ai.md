# Entity views — IA

> Système d'affichage des entités.

## Fichiers pivots

- `resources/js/Utils/entity/resolveEntityViewComponent.js`
- `resources/js/Pages/Molecules/entity/<type>/`
- `resources/js/Pages/Organismes/entity/EntityEditForm.vue`
- Action `view-dofusdb` → store Pinia `dofusDbReference` + `DofusDbReferencePanel` (layout Main)
- Favoris BDD → `useFavoriteEntityIds` + `api.favorites.*` ; UI `FavoritesModal` / page `/favoris`
- Tableau objets (vue Colonnes) : image + nom + niveau + type + rareté + **bonus** (`items.bonus`, chips) ; description / résumé / prix masqués ; `state` réservé aux éditeurs.
- Tooltips d’en-tête : `TanStackTableHeader` lit `column.tooltip` (`getEntityFieldTooltip` depuis descriptors).
- Badges type / rareté / niveau (ressources, équipements, consommables) : `EntityFieldTooltip` (description courte hors tableau `characteristics` ; helper + limites min/max si caractéristique BDD). `FIELD_HELPERS` dans `SharedConstants`.
- Share Inertia `characteristics` : `limit_min` / `limit_max` (entiers figés du pivot, pas les formules).
- Rareté (items, ressources, consommables) : libellés uniques 0–5 (Commun … Unique) via `Resource::RARITY` / `RARITY_GRADIENT` ; les filtres tableau reprennent la même liste.
- Tableaux d’entités : filtres auto-appliqués (server-side), `filters[k][]`, tri via `sort.field` / alias SQL, recherche `search=` ou nom côté client. Défauts de colonnes (`defaultByLabel`) seulement quand les options de **ce** filtre arrivent ; `initialFilterValues` fusionne par contenu, sans réécrire la saisie en cours.
- Catalogue objets : filtre Type précoché sur les emplacements de jeu (pas apparats / costumes) ; `GAMEPLAY_ITEM_TYPE_*` + `defaultByLabel`.
- Catalogue ressources : filtre Type précoché sur les types métier (bois, minerai, plante, runes…) ; `GAMEPLAY_RESOURCE_TYPE_*` + `defaultByLabel`. Quêtes, zones et souvenirs restent décochés.
- Catalogue consommables : filtre Type précoché sur potions, nourritures, parchemins, pierres d’âme, etc. ; `GAMEPLAY_CONSUMABLE_TYPE_*` + `defaultByLabel`. Certificats, coffres, fées d’artifice restent décochés.
- Catalogue états : filtre État précoché hors Brut (`CONDITION_CATALOG_STATE_DEFAULT`) ; API exclut `raw` si le filtre est absent. Flags mécaniques en chips (`ConditionMechanicalFlags`) + colonne Effets. Éditeur d’effets : picker hors `raw` (canons JDR).
- Overlay sort depuis un monstre : payload `effect_usages_chips` + `SpellViewMinimal` étendu (actions à droite du titre, overflow dropdown). Eager-load `visibleToUser` (brouillon masqué). Fetch `whitelist[]=` seulement si les chips manquent.
- Overlay fiche minimale (`EntityViewTextLink`, recherche) : panneau `chromeless` — pas de seconde boîte tooltip autour de la carte. `displayMode: extended` n’affiche que le slot expanded (hauteur du contenu). Actions sans fond. Carte `hover` : reste déployée tant qu’un tooltip OverlayTrigger issu d’elle est ouvert (`entityMinimalCardOverlayHold`). Tooltips / popovers d’entités : une seule surface (`OverlayTrigger` n’empile plus `tooltip-floating-surface` si le contenu ou `panelClass` a déjà le chrome ; sorts `SpellUsageCharacteristicTooltipPanel` / chips d’effets en `glass=false`).
- Tableaux d’entités : ouverture en vue **minimal** (prefs v4 : l’ancien défaut `line` est migré ; `table` conservé).
- Minimal déployé (consommables, ressources, équipements) : description complète (pas de `line-clamp`).
- Barre d’actions (`EntityActions`) : raccourcis inline mesurés (`useHorizontalOverflowCount`, `measureFlexRowLeftoverPx`) ; ce qui ne tient pas reste dans le « ⋮ ».
- Monstres : sorts **et** équipements (`MonsterCreatureSpellsList` / `MonsterCreatureItemsList`) si la créature en a
- **Panoplies** : pas d’image propre — vignette = images des équipements (sinon initiales du nom). Pièces en vue texte (`EntityViewTextLink` → `ItemViewMinimal`) ; le payload lecture/catalogue n’embarque que les pièces `visibleToUser`. Bonus `{ "2": {…} }` : sur la fiche panoplie, chips aplatis ; sur un **équipement** du set, `ItemPanoplyMark` (icône seule en minimal compact, icône+nom ailleurs, full = nom + pièces + paliers chiffre seul ; payload serveur filtré `view`). Tooltip panoplie : mêmes vues texte (clic → popover Minimal). Édition : pièces puis bonus de set en tête de page (cartes `bg-base-100/30`) ; nom / droits en bas. Picker API + `PanoplyBonusEditor` (caractéristique + valeur par palier).
- **Afficher (toutes entités)** : vue Minimal / Line / liste → modal full (`quick-view`). Page Show seulement depuis la modal (**Agrandir** / Ctrl+clic). Preset `minimalLine` sans `view`. Shell : `useEntityMinimalShell`. CMS : `SectionEntityTableRead`.
- **Éditer (toutes entités)** : raccourci des options → page Modifier (`edit`). Pas de modal unitaire « Édition rapide ». Le panneau tableau `EntityQuickEditPanel` reste pour la sélection multiple.
- Caractéristiques runtime : `CharacteristicsCard` densités `icon|labeled|spacious`, groupes via `creatureCharacteristicGroups.manifest.js`, `CharacteristicGroup` (`levelEffective` → `runtime.levels`), popover `CharacteristicDecompositionBody`

Voir aussi [../../features/entities/_ai.md](../../features/entities/_ai.md) et
[../../features/characteristics/COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
