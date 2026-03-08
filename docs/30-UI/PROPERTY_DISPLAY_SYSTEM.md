# Système d'affichage des propriétés

## Vue d'ensemble

Affichage standardisé des propriétés/caractéristiques avec icônes personnalisées, couleurs et tooltips. Les caractéristiques proviennent des tables BDD (`characteristics`, `characteristic_creature`, `characteristic_object`, `characteristic_spell`).

**Groupes** : creature (monster, class, npc), object (item, consumable, resource, panoply), spell (spell, **capability**).

## Composants

### PropertyDisplay (Atom)

`resources/js/Pages/Atoms/data-display/PropertyDisplay.vue`

Affichage d'une propriété avec variants :

| Variant | Rendu |
|---------|-------|
| `badge` | Badge coloré avec icône + valeur |
| `icon` | Icône seule avec tooltip |
| `inline` | Icône + texte (style chip) |

**Props** : `property` (icon, label, tooltip, color), `value`, `variant`, `size`.

### CharacteristicChip (Atom)

Affichage icon + valeur pour les listes (CharacteristicInlineGroup). Utilisé pour les cellules `chips` des tableaux.

### Flux des données

1. **Backend** : `CharacteristicMetaByDbColumnService` → `buildSpellByDbColumn()`, `buildCreatureByDbColumn()`, `buildObjectByDbColumn()`
2. **API Table** : expose `meta.characteristics.<groupe>.byDbColumn`
3. **Frontend** : `resolveEntityFieldUi({ fieldKey, tableMeta, descriptors, entityType })` → priorise caractéristiques BDD (icône, couleur, tooltip) puis descriptors
4. **Composant** : `PropertyDisplay` ou `CharacteristicChip` avec la config résolue

## Icônes

- **BDD** : chemin relatif (ex. `actionPoints.webp`) → préfixé `icons/caracteristics/`
- **FontAwesome** : préfixe `fa-` (fallback dans descriptors)
- **Stockage** : `storage/app/public/images/icons/caracteristics/`

## Couleurs

- **Hex** : `#e93323` (depuis characteristic_icons_colors)
- **Token Tailwind** : `blue-600` → `var(--color-blue-600)`
- `resolveEntityBadgeUi` utilise désormais la couleur caractéristique quand disponible (hex ou token).

## Utilisation

```vue
<PropertyDisplay
  :property="getFieldUi(fieldKey)"
  :value="getCell(fieldKey)?.value"
  variant="badge"
  size="sm"
/>
```

## Vues d'entités mises à jour

Les vues suivantes utilisent désormais `PropertyDisplay` et `resolveEntityFieldUi` pour les metas :

- **Large** : Spell, Capability, Monster, Resource, Item
- **Compact** : Spell, Capability, Monster, Resource, Item
- **Consumable, Panoply, NPC** : `resolveEntityFieldUi` pour icônes/labels (grilles de champs)
- **Minimal** : déjà basées sur `resolveEntityFieldUi` (icônes avec tooltips)

## Tableaux (TanStack Table)

- **EntityTanStackTable** : fusionne `serverMeta` (characteristics, filterOptions) dans `_metadata.context` de manière réactive.
- **CellRenderer** : type `chips` → `CharacteristicInlineGroup` → **CharacteristicChip** (icône + valeur + tooltip, couleurs hex ou token Tailwind).
- **Models** : `toCell()` reçoit `options.ctx` avec `characteristics.<groupe>.byDbColumn` pour enrichir les chips (icon, color, tooltip).

Voir `SpellViewLarge.vue` et `CapabilityViewLarge.vue` pour l'intégration.
