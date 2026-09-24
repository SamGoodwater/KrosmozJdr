# Frontend — IA

> Vue 3 JavaScript + Inertia + Atomic Design.

## Attention

- Pas de TypeScript généralisé.
- `vue-i18n` est documenté historiquement mais non branché côté `resources/js`.
- State principal : props Inertia (`@inertiajs/vue3` 3), composables, Pinia 4 (peer `@vue/devtools-api`).
- Tableaux : TanStack Table 9 (`useTable` + `tableFeatures` dans `TanStackTable.vue`). Chargement Minimal/Line : `EntityViewSkeleton` (plusieurs cartes image+texte, pas un seul bloc). Vue colonnes : `TanStackTableSkeletonBody`.
- CMS sections : hors tableaux `md:w-2/3` centrées ; `*_table` en `w-full` ; overflow-x si dépassement (`SectionRenderer`, `PageRenderer` / `EntitySectionsRenderer`). Lazy / fetch : `SectionContentSkeleton` calqué sur le template. Bibliothèques `entity_table` : bouton Créer (`CreateEntityModal`) si `can.create`.
- Sélection vue minimale : checkbox haut-gauche visible seulement carte **déployée** (ou déjà cochée).
- `Container fluid` : `w-full min-w-0` (évite le shrink-to-fit dans un parent flex).
- Footer desktop (`Layouts/Footer.vue`) : 2 lignes, logo centré ; padding via le layout, pas la molécule.
- Chrome responsive : mobile `&lt; md` = dock bas + pas de header ; tablette `md–lg` = header + hamburger ; desktop `≥ lg` = sidebar fixe. Constantes `LAYOUT_*` dans `viewport-breakpoints.js` (padding bas dock, FAB, cookies).
- Tableaux : filtres / presets en Drawer `side="end"` sous conteneur étroit ; racine `@container` ; modes de vue icon-only en conteneur étroit.
- Dashboard `/admin/content` : cartes Atelier DofusDB (3 modes) + Génération IA ; camemberts inchangés.- Modal **Sources** : deux onglets (DofusDB algo + IA). Après écriture, tableau avant/après avec choix par cellule ; Enregistrer / Rétablir.
- Formules de dés : bande min/moy/max + **Valeur** / icône ghost Lancer + historique session ; reconnu si dé, tranche ou opérateur (`50-17`). `diceParser.js`, `DiceFormulaStrip`.
- Recherche globale / filtres tableau : pastilles d’état via jetons `--color-state-*` (`getEntityStateChipClass`). Brut = error, jouable = success, brouillon = umber, auto = indigo, archivé = info.
- `vite.config.js` `manualChunks` : uniquement `node_modules` (`vendor` / `cally` / `utils`). Ne pas extraire `Main.vue` ni `Utils/Formatters` : cycle de chunks au boot.

## Descendre

- [inertia/_ai.md](inertia/_ai.md)
- [atomic-design/_ai.md](atomic-design/_ai.md)
- [entity-views/_ai.md](entity-views/_ai.md)
