# Frontend — IA

> Vue 3 JavaScript + Inertia + Atomic Design.

## Attention

- Pas de TypeScript généralisé.
- `vue-i18n` est documenté historiquement mais non branché côté `resources/js`.
- State principal : props Inertia (`@inertiajs/vue3` 3), composables, Pinia 4 (peer `@vue/devtools-api`).
- Tableaux : TanStack Table 9 (`useTable` + `tableFeatures` dans `TanStackTable.vue`). Chargement Minimal/Line : `EntityViewSkeleton` (plusieurs cartes image+texte, pas un seul bloc). Vue colonnes : `TanStackTableSkeletonBody`.
- CMS sections : hors tableaux `md:w-2/3` centrées ; `*_table` en `w-full` ; overflow-x si dépassement (`SectionRenderer`, `PageRenderer` / `EntitySectionsRenderer`). Lazy / fetch : `SectionContentSkeleton` calqué sur le template.
- Sélection vue minimale : checkbox haut-gauche visible seulement carte **déployée** (ou déjà cochée).
- `Container fluid` : `w-full min-w-0` (évite le shrink-to-fit dans un parent flex).
- Footer desktop (`Layouts/Footer.vue`) : 2 lignes, logo centré ; padding via le layout, pas la molécule.
- Dashboard `/admin/content` : cartes Atelier DofusDB (3 modes) + Génération IA ; camemberts inchangés.
- Modal **Sources** : deux onglets (DofusDB algo + IA). Après écriture, tableau avant/après avec choix par cellule ; Enregistrer / Rétablir.
- Formules de dés : bande min/moy/max sous la recherche globale et dans `DiceRollerModal` (`diceParser.js`, `DiceFormulaStrip`).
- `vite.config.js` `manualChunks` : uniquement `node_modules` (`vendor` / `cally` / `utils`). Ne pas extraire `Main.vue` ni `Utils/Formatters` : cycle de chunks au boot.

## Descendre

- [inertia/_ai.md](inertia/_ai.md)
- [atomic-design/_ai.md](atomic-design/_ai.md)
- [entity-views/_ai.md](entity-views/_ai.md)
