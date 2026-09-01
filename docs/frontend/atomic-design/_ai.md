# Atomic Design — IA

> Composants UI réutilisables.

## Attention

- `Btn` : `color` = teinte DaisyUI, `variant` = style (`glass`/`ghost`/`outline`/…). Une couleur passée en `variant` (`variant="primary"`) est remappée vers `color` + `glass` (compat) — les usages corrects ne changent pas.
- `InputCore` : v-model via `vnode.props.onUpdate:modelValue` (pas `$attrs` — emits déclarés). `InputField` continue de passer `value` via `inputAttrs`.
- `RangeDualCore` : slider min/max sur une barre, valeurs au-dessus des curseurs. Prop `accent` (CSS) pour teinter avec la couleur caractéristique. Filtres tableau (`type: "range"`) inline, sans menu.

## Fichiers pivots

- `resources/js/Pages/Atoms/atoms.index.json`
- `resources/js/Pages/Molecules/molecules.index.json`
- `resources/js/Pages/Organismes/organisms.index.json`
- `resources/js/Pages/Atoms/`
- `resources/js/Pages/Molecules/`
- `resources/js/Pages/Organismes/`
