# Système d'affichage des propriétés

## Vue d'ensemble

> **Refactorisation** : Voir [AUDIT_SERVICE_AFFICHAGE_CARACTERISTIQUES.md](../50-Fonctionnalités/Characteristics-DB/AUDIT_SERVICE_AFFICHAGE_CARACTERISTIQUES.md) pour l'audit des duplications et le plan de centralisation dans un service universel.

Affichage standardisé des propriétés/caractéristiques avec icônes personnalisées, couleurs et tooltips. Les caractéristiques proviennent des tables BDD (`characteristics`, `characteristic_creature`, `characteristic_object`, `characteristic_spell`).

**Groupes** : creature (monster, class, npc), object (item, consumable, resource, panoply), spell (spell, **capability**).

## Modes d'affichage des propriétés (PROPERTY_DISPLAY_MODES)

Constantes : `resources/js/Utils/Entity/Constants.js`

| Mode | Rendu | Composant |
|------|-------|-----------|
| **minimal** | icône + valeur + unité | CharacteristicChip, CharacteristicEffectsGrid (`labelMode="icon-only"`) |
| **compact** | icône + label abrégé + valeur + unité | `labelMode="short"` |
| **extended** | icône + label complet + valeur + unité | `labelMode="full"` |
| **detailed** | extended + détails (formules, valeurs par niveau) ; partie visible + partie au hover | CharacteristicFormula (`displayMode="detailed"`) |

**Mapping vue entité → mode propriété** : Minimal/Line → minimal ; Compact → compact ; Large → extended ou detailed (si formule).

## Composants

### EntityPropertyDisplay (Molecule)

`resources/js/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue`

Affichage unifié des propriétés d'entité selon le mode (`minimal`, `compact`, `extended`, `detailed`). Utilise le composable `useEntityPropertyDisplay` pour résoudre les métadonnées (icône, label, unité, couleur) et la valeur.

**Props** : `fieldKey`, `entity`, `entityType`, `displayMode`, `descriptors`, `tableMeta`, `size`, `formulaResolved`, `formulaRaw`, `levelTable`.

**Composable** : `resources/js/Composables/entity/useEntityPropertyDisplay.js` — attend un objet réactif (ex. `computed`) pour rester à jour.

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

Affichage icon + valeur + unité pour les listes (CharacteristicInlineGroup). Utilisé pour les cellules `chips` des tableaux.

**Props** : `item` (icon, color, name, shortLabel, value, unit, tooltip), `labelMode` (`full` | `short` | `icon-only`).

### CharacteristicEffectsGrid (Molecule)

Grille responsive d'effets/caractéristiques. Affiche l'unité (`item.unit`) après la valeur quand disponible.

**Props** : `items`, `labelMode` (`full` | `short` | `icon-only`).

### CharacteristicFormula (Atom)

Caractéristique à formule (valeur + unité). Aligné sur `PROPERTY_DISPLAY_MODES` (voir ci-dessous).

**Props** : `def`, `value`, `formulaResolved`, `formulaRaw`, `levelTable`, `unit`, `displayMode` (`minimal` | `compact` | `extended` | `detailed`), `compact` (legacy).

- **minimal** : icône + valeur + unité
- **compact** : icône + label abrégé + valeur + unité
- **extended** : icône + label complet + valeur + unité (carte)
- **detailed** : extended + panneau hover (formule, tableau par niveau)

### Flux des données

1. **Backend** : `CharacteristicMetaByDbColumnService::buildAllForFrontend()` → Inertia share `characteristics` au démarrage
2. **Frontend** : `useCharacteristicsStore` lit `usePage().props.characteristics` ; `getEntityCharacteristicsByDbColumn` (entity-view-ui) priorise le store puis tableMeta (fallback)
3. **Résolution** : `resolveEntityFieldUi({ fieldKey, tableMeta, descriptors, entityType })` → priorise store (icône, couleur, tooltip) puis descriptors
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

### EntityPropertyDisplay (composant unifié)

Les vues suivantes utilisent **EntityPropertyDisplay** pour les champs de propriétés :

- **Large** : Spell, Resource, Attribute (migration complète)
- **À migrer** : Monster, Item, Consumable, Capability, Npc, Panoply (utilisent encore PropertyDisplay + resolveEntityFieldUi manuel)

### PropertyDisplay (legacy)

- **Compact** : Spell, Capability, Monster, Resource utilisent encore `PropertyDisplay` + `resolveEntityFieldUi` pour les metas
- **Consumable, Panoply, NPC** : `resolveEntityFieldUi` pour icônes/labels (grilles de champs)
- **Minimal** : déjà basées sur `resolveEntityFieldUi` (icônes avec tooltips)

## Tableaux (TanStack Table)

- **EntityTanStackTable** : fusionne `serverMeta` (filterOptions) dans `_metadata.context` ; les caractéristiques ne sont plus dans meta (chargées au démarrage via Inertia share).
- **CellRenderer** : type `chips` → `CharacteristicInlineGroup` → **CharacteristicChip** (icône + valeur + tooltip, couleurs hex ou token Tailwind).
- **Models** : `toCell()` et `buildCharacteristicEffectCell` lisent le store `useCharacteristicsStore` pour enrichir les chips (icon, color, tooltip).

Voir `SpellViewLarge.vue` et `CapabilityViewLarge.vue` pour l'intégration.
