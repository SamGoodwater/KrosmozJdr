# Frontend

Frontend Vue 3 en JavaScript, servi par Inertia.js. L'UI suit Atomic Design et utilise Tailwind CSS + DaisyUI.

## Sous-domaines

- [Inertia](inertia/README.md) : résolution des pages, props partagées, Ziggy.
- [Atomic Design](atomic-design/README.md) : Atoms, Molecules, Organismes.
- [Entity views](entity-views/README.md) : vues `minimal`, `line`, `text`, `full`, `edit`.

## Layout

Le shell est `resources/js/Pages/Layouts/Main.vue` (header, sidebar, contenu, footer).

Le pied de page desktop (`Layouts/Footer.vue`) tient sur deux lignes compactes : nom + version à gauche, logo centré, liens contact / Discord / GitHub à droite ; en dessous le texte de présentation et le bouton cookies. Sous `sm`, ce bandeau est remplacé par le dock mobile.

## Fichiers pivots

- `resources/js/app.js`
- `resources/js/Pages/Layouts/Main.vue`
- `resources/js/Pages/Layouts/Footer.vue`
- `resources/js/Composables/`
- `resources/js/Entities/entity-registry.js`
- `resources/js/Pages/Organismes/table/EntityTanStackTable.vue`
