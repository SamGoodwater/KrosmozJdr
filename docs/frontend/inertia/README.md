# Inertia

Les contrôleurs Laravel rendent les pages Vue via `Inertia::render()`. La résolution se fait depuis `resources/js/app.js` avec `import.meta.glob('./Pages/**/*.vue')`. Le client npm est `@inertiajs/vue3` 3 ; l’adaptateur PHP est `inertiajs/inertia-laravel` 3.

Les visites et formulaires passent par `router` et `useForm` importés depuis `@inertiajs/vue3`. Axios reste pour les appels API métier (`bootstrap.js`) : un 419 sur une visite Inertia est géré côté Laravel (`Inertia::location` dans `bootstrap/app.php`).

Le fichier `resources/js/ssr.js` compile avec Vite. Le SSR Inertia est désactivé (`INERTIA_SSR_ENABLED`).

## Props partagées

`app/Http/Middleware/HandleInertiaRequests.php` partage notamment : utilisateur connecté, flash messages, permissions, Ziggy, confirmation de mot de passe. `permissions`, `ziggy` et `oauth_enabled_providers` passent par `shareOnce` : le client les mémorise après la première visite.

## Routing JS

Ziggy expose `route()` côté Vue. Le plugin `resources/js/Plugins/inertia-ziggy.js` synchronise les routes avec les props Inertia et ignore `ziggy` s’il est absent d’une visite suivante.
