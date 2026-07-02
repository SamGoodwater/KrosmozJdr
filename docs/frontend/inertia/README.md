# Inertia

Les contrôleurs Laravel rendent les pages Vue via `Inertia::render()`. La résolution se fait depuis `resources/js/app.js` avec `import.meta.glob('./Pages/**/*.vue')`.

## Props partagées

`app/Http/Middleware/HandleInertiaRequests.php` partage notamment : utilisateur connecté, flash messages, permissions, Ziggy, confirmation de mot de passe.

## Routing JS

Ziggy expose `route()` côté Vue. Le plugin `resources/js/Plugins/inertia-ziggy.js` synchronise les routes avec les props Inertia.
