# Phase K — Polish UI/UX (release 1.3.2)

## Bandeaux Alert

- Atome `Alert.vue` : prop **`glass`** (défaut `true`) — fond sombre semi-opaque, `backdrop-blur-md`, coins `rounded-sm`, texte clair par couleur (`*-100` sur `*-950/85`).
- Désactiver : `<Alert :glass="false" variant="soft" />` pour le rendu DaisyUI historique.

## Notifications toast

- Durée par défaut : **14 s** (`DEFAULT_DURATION` dans `useNotificationStore.js`).
- Mode déplié : **50 %** du temps total (`FULL_DISPLAY_RATIO = 0.5`).
- **Pause** au survol ou au focus clavier (`pauseNotification` / `resumeNotification`).

## Accessibilité

- Lien d'évitement « Aller au contenu principal » dans `resources/views/app.blade.php` → `#main-content` (`Main.vue`).
- `meta color-scheme=dark` sur le shell Inertia.
- Toasts : `aria-live="polite"`, focusables (`tabindex="0"`).

## Responsive (layouts)

- `Main.vue` : `min-w-0`, padding réduit mobile (`max-sm:p-2`).
- `Header.vue` : titre tronqué, taille adaptive (`text-lg` → `sm:text-2xl`).

## Documentation

Le nettoyage exhaustif de `/docs/` (reclassement, suppression historiques) reste une passe dédiée post-1.3.2 ; cette phase couvre le **comportement produit** et les composants UI critiques.
