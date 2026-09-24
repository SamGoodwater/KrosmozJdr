# Frontend

Frontend Vue 3 en JavaScript, servi par Inertia.js. L'UI suit Atomic Design et utilise Tailwind CSS + DaisyUI.

## Sous-domaines

- [Inertia](inertia/README.md) : résolution des pages, props partagées, Ziggy.
- [Atomic Design](atomic-design/README.md) : Atoms, Molecules, Organismes.
- [Entity views](entity-views/README.md) : vues `minimal`, `line`, `text`, `full`, `edit`.

## Layout

Le shell est `resources/js/Pages/Layouts/Main.vue` (header, sidebar, contenu, footer).

### Matrice responsive (chrome)

Alignée sur `useDevice` / `viewport-breakpoints.js` (**md = 768px**, **lg = 1024px**) :

| Mode | Largeur | Header | Dock mobile | Toggle flottant | Sidebar |
| --- | --- | --- | --- | --- | --- |
| Mobile | &lt; md | masqué | visible (bas d’écran) | masqué | drawer |
| Tablette | md → &lt; lg | visible | masqué | visible | drawer |
| Desktop | ≥ lg | visible | masqué | visible | panneau fixe |

Le contenu scrollable prévoit un padding bas en mobile pour ne pas passer sous le dock ; FAB et bandeau cookies sont décalés au-dessus. Classes centralisées dans `LAYOUT_*` de `viewport-breakpoints.js`.

Le pied de page desktop (`Layouts/Footer.vue`) tient sur deux lignes compactes : nom + version à gauche, logo centré, liens contact / Discord / GitHub à droite ; en dessous le texte de présentation et le bouton cookies. Sous `md`, ce bandeau est remplacé par le dock mobile.

## Build Vite

`vite.config.js` ne découpe en chunks que des paquets `node_modules` (`vendor`, `cally`, `utils`). Le code applicatif (layout, formatters) reste dans le graphe principal : un `manualChunks` sur `Main.vue` / `Utils/Formatters` croise les imports `SharedConstants` et casse le boot.

Les formatters s’enregistrent via l’import statique `@/Utils/Formatters` dans `app.js`.

Favicon : `resources/views/app.blade.php` pointe vers `logo_mini.webp` / `.png` / `.ico` (pas de `.svg` dans `storage/app/public/images/logos/`). Un fichier public absent sous `/storage/…` n’est pas un 404 : la route Laravel `storage.local` sert le disque privé et répond 403.

## Fichiers pivots

- `resources/js/app.js`
- `vite.config.js`
- `resources/views/app.blade.php`
- `resources/js/Pages/Layouts/Main.vue`
- `resources/js/Pages/Layouts/Footer.vue`
- `resources/js/Composables/`
- `resources/js/Entities/entity-registry.js`
- `resources/js/Pages/Organismes/table/EntityTanStackTable.vue`
