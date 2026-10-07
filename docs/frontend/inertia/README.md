# Inertia

Les contrôleurs Laravel rendent les pages Vue via `Inertia::render()`. La résolution se fait depuis `resources/js/app.js` avec `import.meta.glob('./Pages/**/*.vue')`. Le client npm est `@inertiajs/vue3` 3 ; l’adaptateur PHP est `inertiajs/inertia-laravel` 3.

Les visites et formulaires passent par `router` et `useForm` importés depuis `@inertiajs/vue3`. Axios reste pour les appels API métier (`bootstrap.js`) : un 419 sur une visite Inertia est géré côté Laravel (`Inertia::location` dans `bootstrap/app.php`).

Le fichier `resources/js/ssr.js` compile avec Vite. Le SSR Inertia est désactivé (`INERTIA_SSR_ENABLED`).

## Props partagées

`app/Http/Middleware/HandleInertiaRequests.php` partage notamment : utilisateur connecté, flash messages, permissions, confirmation de mot de passe, `ziggy_location`. `permissions`, `oauth_enabled_providers` et `loadingTips` passent par `shareOnce` : le client les mémorise après la première visite.

## Routing JS

Ziggy expose `route()` côté Vue via le catalogue généré `resources/js/ziggy.js` (bundle Vite). Le plugin `resources/js/Plugins/inertia-ziggy.js` et `resources/js/ziggy-global.js` réalignent **`Ziggy.url` / `port`** (pas seulement `location`) sur l’origine du navigateur via `ziggy_location` / `window.location` — sinon un écart `localhost` vs `127.0.0.1` (APP_URL du bundle) envoie les PATCH Inertia en cross-origin et produit un toast « Erreur lors de la sauvegarde » sans erreurs de validation. Les soumissions d’`EntityEditForm` utilisent une URL relative (`route(..., false)`) en filet de sécurité.
