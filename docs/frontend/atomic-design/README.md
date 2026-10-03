# Atomic Design

Les composants UI sont organisés dans `resources/js/Pages/Atoms`, `Molecules` et `Organismes`.

## Niveaux

- Atoms : boutons, inputs, badges, affichages simples.
- Molecules : champs complets, blocs de table, vues d'entité.
- Organismes : tables, rendu CMS, overlays, formulaires complexes.

Les index `atoms.index.json`, `molecules.index.json`, `organisms.index.json` servent à explorer les composants sans lire tout le code.

## Tooltips

Les infobulles hover (`Tooltip.vue` → `OverlayTrigger`) restent ouvertes tant que le pointeur est sur le déclencheur **ou** sur le panneau. Un pont CSS (`overlay-hover-bridge`) couvre l’écart Floating UI ; le délai de fermeture est `hoverCloseDelayMs` (250 ms). Une seule surface visuelle : `glass=false` / `chromeless` quand le slot fournit déjà le chrome (fiches minimales, panneaux de sorts) ; sinon `panelClass` (Popover) ou la surface tooltip par défaut. Les classes de positionnement du déclencheur (`fixed`, etc.) passent par `triggerClass` sur le nœud mesuré par Floating UI. Dans un dialog (recherche globale), le mode `opaque` utilise un fond `base-100` solide.

## Alertes

En mode glass (défaut), `Alert` pose un voile semi-opaque de la couleur d’alerte (`.color-*` et `.bg-color-*-400`), un flou `.bd-glass-lg` et un texte `base-content`. Le HTML CMS qui utilise encore `alert alert-info` (ou success, warning, error) reçoit la même surface via `resources/scss/src/_alert.scss`. `glass="false"` retrouve l’aplat DaisyUI.

Un `Dropdown` ouvert depuis une carte minimale (`EntityMinimalCard`) maintient la carte déployée (`entityMinimalCardOverlayHold` et `data-dropdown-open`) : le menu est téléporté hors de la carte.
