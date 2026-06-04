# Frontend — carte IA (degré 1) [STUB]

> Vue 3 (Composition API, **JavaScript**, pas TypeScript) + Inertia.js, Atomic Design, Tailwind 4 + DaisyUI. État : props Inertia + composables + Pinia minimal. Routes JS via Ziggy.

> Statut : stub. Pointe vers la doc/code existants ; à remixer dans `docV2`.

## Quand lire ce nœud

- Travailler sur des composants, pages, layouts, stores/composables, le design system.

## Structure (réelle)

| Zone | Chemin |
| --- | --- |
| Entrée | `resources/js/app.js`, `bootstrap.js`, `ssr.js` |
| Atomic Design | `resources/js/Pages/{Atoms,Molecules,Organismes}/` (+ index `*.index.json`) |
| Pages Inertia | `resources/js/Pages/Pages/`, `resources/js/Pages/Admin/` |
| Layouts | `resources/js/Pages/Layouts/` (`Main.vue`…) |
| Composables | `resources/js/Composables/` (entity, table, layout, overlay, form…) |
| État | Pinia : `useCharacteristicsPiniaStore` ; sinon composables module-level + localStorage |
| Registre entités | `resources/js/Entities/entity-registry.js` |
| Routing | Ziggy (`resources/js/ziggy.js`, `Plugins/inertia-ziggy.js`) |

## Points d'attention

- **i18n** : `vue-i18n` documenté mais **non branché** dans `resources/js` — libellés FR en dur.
- **Tailwind dynamique interdit** : voir rule `.cursor/rules/ui-design-system.mdc`.
- **Vues d'entités** : voir [features/entities/_ai.md](../features/entities/_ai.md) et rule `.cursor/rules/entity-views.mdc`.

## Fichiers pivots

- `resources/js/Pages/Layouts/Main.vue` — layout principal.
- `resources/js/Pages/Organismes/section/PageRenderer.vue` — rendu CMS.
- `resources/js/Pages/Organismes/table/EntityTanStackTable.vue` — tables.
- `resources/js/Composables/permissions/usePermissions.js` — droits front.

## Descendre

- Doc existante (L2) : `docs/30-UI/ATOMIC_DESIGN.md`, `docs/30-UI/ENTITY_VIEWS.md`, `docs/10-BestPractices/PROJECT_STRUCTURE.md`.
