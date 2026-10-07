# Inertia — IA

> Pont Laravel → Vue. PHP `inertiajs/inertia-laravel` ^3, client `@inertiajs/vue3` ^3.

## Fichiers pivots

- `resources/js/app.js` — `createInertiaApp` + `setup({ el, App, props, plugin })` ; import statique `@/Utils/Formatters` (registre avant montage)
- `resources/js/ssr.js` — compilé par Vite ; SSR off (`INERTIA_SSR_ENABLED`)
- `app/Http/Middleware/HandleInertiaRequests.php`
- `resources/js/Plugins/inertia-ziggy.js` — `router.on('navigate')` ; met à jour `ziggy_location` ; catalogue routes via `ziggy.js`
- `resources/js/Composables/notifications/useFlashNotifications.js` — `router.on('success')` + `event.detail.page.props.flash`

## Contrats

- Visites : `import { router } from '@inertiajs/vue3'` (pas `$inertia`).
- Formulaires : `useForm` ; `processing` redevient `false` dans `onFinish`.
- Axios (`bootstrap.js`) : XHR métier seulement. 419 Inertia → Laravel `Inertia::location` (`bootstrap/app.php`).
- `shareOnce` : `permissions`, `oauth_enabled_providers`, `loadingTips` — mémorisés côté client après la 1re visite.
- Ziggy : `resources/js/ziggy.js` (bundle) + prop `ziggy_location` ; `ziggy-global.js` / `Utils/ziggy-origin.js` synchronisent `Ziggy.url`+`port` sur l’origine courante (`localhost` ≠ `127.0.0.1` sinon PATCH cross-origin). `EntityEditForm` soumet en URL relative. Pas de prop `ziggy` Inertia.
- Pas de `@inertiajs/vite` : `laravel-vite-plugin` + Vite 6.
