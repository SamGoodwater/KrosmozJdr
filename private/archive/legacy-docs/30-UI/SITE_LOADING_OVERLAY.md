# Écran de chargement (tunnel / zoom)

Overlay plein page affiché au premier chargement du site, avant que l’app Vue/Inertia soit prête.

## Comportement

- Image : `storage/app/public/images/backgrounds/loading.webp` (repli `loading.png`).
- Animation **pendant le chargement** : zoom lent 5 s → dézoom 5 s (boucle `pulse`, démarrage immédiat).
- Animation **après chargement** : zoom linéaire infini 5 s (`ready`), puis fondu (~0,9 s de maintien).
- Titre **Krosmoz** / **JDR** (grand, 2 lignes) + **Chargement** avec `Loading` DaisyUI (`dots`).
- Entrée du texte : fondu + léger scale (1 s).
- Splash HTML dans `app.blade.php` (même pulse + texte) avant hydratation Vue.
- Fermeture auto : `document.complete` → phase `ready` → délai min ~1,4 s + maintien ~0,9 s, max ~22 s.
- Croix en haut à droite : fermeture manuelle (secours) ; mémorisée en `sessionStorage` pour la session (`krosmoz-site-loading-dismissed`).
- `prefers-reduced-motion` : zoom statique léger.

## Fichiers

| Fichier | Rôle |
|---------|------|
| `resources/js/Pages/Organismes/feedback/SiteLoadingOverlay.vue` | UI overlay + Teleport |
| `resources/js/Composables/layout/useSiteLoadingOverlay.js` | État, timers, dismiss |
| `resources/js/app.js` | Montage global, barre Inertia sans spinner |
| `resources/views/app.blade.php` | Preload + splash boot |

## Exemple d’extension

Pour réafficher l’overlay en dev : `sessionStorage.removeItem('krosmoz-site-loading-dismissed')` puis recharger.
