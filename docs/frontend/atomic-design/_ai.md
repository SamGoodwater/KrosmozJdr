# Atomic Design — IA

> Composants UI réutilisables.

## Attention

- `Btn` : `color` = teinte DaisyUI, `variant` = style (`glass`/`ghost`/`outline`/…). Une couleur passée en `variant` (`variant="primary"`) est remappée vers `color` + `glass` (compat) — les usages corrects ne changent pas.
- États de publication : jetons `--color-state-raw|draft|auto|playable|archived` (`_theme-states.scss`, `_app.save.css`). Brouillon = umber, auto = indigo ; brut/jouable = error/success.
- `InputCore` : v-model via `vnode.props.onUpdate:modelValue` (pas `$attrs` — emits déclarés). `InputField` continue de passer `value` via `inputAttrs`.
- Tooltips hover (`Tooltip` / `OverlayTrigger`) : le panneau capte le pointeur (pont CSS `overlay-hover-bridge` + délai de fermeture). Le survol du tooltip ne le ferme pas. Sur tactile (`hover: none` / `pointer: coarse`), le mode passe en **clic** + fermeture hors panneau (`canUseHoverOverlay`) — évite les tooltips collants sur fiches d’entité. Une seule surface : pas de `tooltip-floating-surface` empilé si `chromeless` / `glass=false` / `panelClass` déjà chromé. Classes de positionnement du déclencheur (`fixed`, etc.) via `triggerClass` sur le nœud Floating UI (pas un enfant). Mode `opaque` / dialog recherche : fond `base-100` solide. `Tooltip` force `trigger="hover"` (sinon un slot `#content` = kind `component` basculait `auto` en clic).
- `Alert` (glass) : fond type carte minimale (`bg-glass-2xl`), texte `base-content`, bordure fine + ombre teintées par `color-*` (plus de texte blanc sur fond clair).
- `Dropdown` ouvert depuis `EntityMinimalCard` : `useEntityMinimalCardOverlayHold` + `[data-dropdown-open]` (menu téléporté sur `body`, sinon la carte se replie et démonte le raccourci d’état).
- En-tête de page et lexique des actions : [page-header-actions.md](page-header-actions.md). `PageHeader` hors CMS / entités ; un seul « Enregistrer » par page (`usePageForms`) ; libellés via `Utils/atomic-design/actionLabels.js` (« Annuler les modifications », « Retour », « Fermer », jamais « Reset » / « Sauvegarder »).
- `GlassMenuItem` : `iconColorizeOnHover` = niveaux de gris au repos, couleurs au hover / actif (menu classes).

## Fichiers pivots

- `resources/js/Pages/Atoms/atoms.index.json`
- `resources/js/Pages/Molecules/molecules.index.json`
- `resources/js/Pages/Organismes/organisms.index.json`
- `resources/js/Pages/Atoms/`
- `resources/js/Pages/Molecules/`
- `resources/js/Pages/Organismes/`
