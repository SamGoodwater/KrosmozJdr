# Inertia — IA

> Pont Laravel → Vue. PHP `inertiajs/inertia-laravel` ^3, client `@inertiajs/vue3` ^3.

## Fichiers pivots

- `resources/js/app.js` — `createInertiaApp` + `setup({ el, App, props, plugin })`
- `resources/js/ssr.js` — compilé par Vite ; SSR off (`INERTIA_SSR_ENABLED`)
- `app/Http/Middleware/HandleInertiaRequests.php`
- `resources/js/Plugins/inertia-ziggy.js` — `router.on('navigate')` ; ignore `ziggy` absent (`shareOnce`)
- `resources/js/Composables/notifications/useFlashNotifications.js` — `router.on('success')` + `event.detail.page.props.flash`

## Contrats

- Visites : `import { router } from '@inertiajs/vue3'` (pas `$inertia`).
- Formulaires : `useForm` ; `processing` redevient `false` dans `onFinish`.
- Axios (`bootstrap.js`) : XHR métier seulement. 419 Inertia → Laravel `Inertia::location` (`bootstrap/app.php`).
- `shareOnce` : `permissions`, `ziggy`, `oauth_enabled_providers` — mémorisés côté client après la 1re visite.
- Pas de `@inertiajs/vite` : `laravel-vite-plugin` + Vite 6.
