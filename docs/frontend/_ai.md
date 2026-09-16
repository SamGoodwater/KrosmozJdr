# Frontend — IA

> Vue 3 JavaScript + Inertia + Atomic Design.

## Attention

- Pas de TypeScript généralisé.
- `vue-i18n` est documenté historiquement mais non branché côté `resources/js`.
- State principal : props Inertia (`@inertiajs/vue3` 3), composables, Pinia 4 (peer `@vue/devtools-api`).
- Tableaux : TanStack Table 9 (`useTable` + `tableFeatures` dans `TanStackTable.vue`).
- Footer desktop (`Layouts/Footer.vue`) : 2 lignes, logo centré ; padding via le layout, pas la molécule.
- Dashboard `/admin/content` : cartes Atelier DofusDB (3 modes) + Génération IA ; camemberts inchangés.
- `vite.config.js` `manualChunks` : uniquement `node_modules` (`vendor` / `cally` / `utils`). Ne pas extraire `Main.vue` ni `Utils/Formatters` : cycle de chunks au boot.

## Descendre

- [inertia/_ai.md](inertia/_ai.md)
- [atomic-design/_ai.md](atomic-design/_ai.md)
- [entity-views/_ai.md](entity-views/_ai.md)
