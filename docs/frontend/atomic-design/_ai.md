# Atomic Design — IA

> Composants UI réutilisables.

## Attention

- `Btn` : `color` = teinte DaisyUI, `variant` = style (`glass`/`ghost`/`outline`/…). Une couleur passée en `variant` (`variant="primary"`) est remappée vers `color` + `glass` (compat) — les usages corrects ne changent pas.
- `InputCore` : v-model via `vnode.props.onUpdate:modelValue` (pas `$attrs` — emits déclarés). `InputField` continue de passer `value` via `inputAttrs`.
- Tooltips hover (`Tooltip` / `OverlayTrigger`) : le panneau capte le pointeur (pont CSS `overlay-hover-bridge` + délai de fermeture). Le survol du tooltip ne le ferme pas.

## Fichiers pivots

- `resources/js/Pages/Atoms/atoms.index.json`
- `resources/js/Pages/Molecules/molecules.index.json`
- `resources/js/Pages/Organismes/organisms.index.json`
- `resources/js/Pages/Atoms/`
- `resources/js/Pages/Molecules/`
- `resources/js/Pages/Organismes/`
