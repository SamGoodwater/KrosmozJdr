# Système d'affichage des propriétés

## Vue d'ensemble

> **Refactorisation** : Voir [AUDIT_SERVICE_AFFICHAGE_CARACTERISTIQUES.md](../50-Fonctionnalités/Characteristics-DB/AUDIT_SERVICE_AFFICHAGE_CARACTERISTIQUES.md) pour l'audit des duplications et le plan de centralisation dans un service universel.

Affichage standardisé des propriétés/caractéristiques avec icônes personnalisées, couleurs et tooltips. Les caractéristiques proviennent des tables BDD (`characteristics`, `characteristic_creature`, `characteristic_object`, `characteristic_spell`).

**Groupes** : creature (monster, class, npc), object (item, consumable, resource, panoply), spell (spell, **capability**).

## Baseline (état du dépôt) — avant refonte unifiée

Cette section fige l’existant pour éviter la dérive documentaire ; la cible est un **pipeline unique** (voir section suivante).

### Source de données

- **Métadonnées** : `CharacteristicMetaByDbColumnService::buildAllForFrontend()` → partage Inertia `props.characteristics` (voir flux ci-dessous).
- **Store** : [`useCharacteristicsStore.js`](../../resources/js/Composables/store/useCharacteristicsStore.js) — résolution par `byDbColumn`, `byCharacteristicKey`, `byComputedKey`, `byDofusdbId` selon le **groupe** (creature / item / spell…).
- **Affichage transverse** : [`useCharacteristicDisplay.js`](../../resources/js/Composables/entity/useCharacteristicDisplay.js) — `resolveDef`, `getDisplayValue`, styles couleur / conteneur, cas spéciaux (portée CAC, critique, etc.).

### Chevauchements / duplications

| Zone | Problème |
|------|----------|
| Plusieurs atomes | `PropertyDisplay`, `CharacteristicFormula`, `CharacteristicChip`, rendu inline dans `EntityPropertyDisplay` — mêmes besoins (icône, couleur, valeur, tooltip) avec APIs différentes. |
| Résolution « entité » | [`useEntityPropertyDisplay.js`](../../resources/js/Composables/entity/useEntityPropertyDisplay.js) + `resolveEntityFieldUi` (descriptors) en parallèle du store pur. |
| Créatures | [`buildCreatureCharacteristicGroups.js`](../../resources/js/Utils/Entity/buildCreatureCharacteristicGroups.js) **recalcule** une partie des valeurs en JS (modificateurs, sauvegardes, etc.) en parallèle du **backend runtime** (`GET /entities/creatures/{id}/resolved-stats`). Risque de divergence tant que les deux coexistent sans fusion. |
| `EntityPropertyDisplay` | Doc / props mentionnent `PropertyDisplay` et `variant` ; le template actuel n’utilise pas `PropertyDisplay` pour les modes non détaillés (markup dupliqué). |

### Schéma cible (refonte)

- **Composable** : [`useCharacteristicViewModel.js`](../../resources/js/Composables/entity/useCharacteristicViewModel.js) — assemble définition BDD + valeur + optionnellement payload **runtime** (formule substituée, placeholders par variable). Helpers : `viewModelFromFormulaGroupItem`, `viewModelFromLegacyProperty`, `viewModelFromChipItem`, `mergeRuntimeIntoViewModel`.
- **Runtime créature** : [`useCreatureResolvedStats.js`](../../resources/js/Composables/entity/useCreatureResolvedStats.js) — `GET /entities/creatures/{id}/resolved-stats` ; transmis à [`CharacteristicsCard`](../../resources/js/Pages/Organismes/data-display/CharacteristicsCard.vue) → [`CharacteristicGroup`](../../resources/js/Pages/Molecules/data-display/CharacteristicGroup.vue).
- **Composants** : [`CharacteristicProperty.vue`](../../resources/js/Pages/Atoms/data-display/CharacteristicProperty.vue) (densité `CHARACTERISTIC_PROPERTY_*`, badge, layout) et [`CharacteristicPropertyTooltip.vue`](../../resources/js/Pages/Molecules/data-display/CharacteristicPropertyTooltip.vue) (tooltip riche unique).
- **Wrappers** : `PropertyDisplay`, `CharacteristicFormula`, `CharacteristicChip` délèguent à `CharacteristicProperty` (API historique conservée).
- **Référence visuelle** : [Vue propriétés.svg](./Vue%20propriétés.svg) (schéma Excalidraw).

Voir aussi [CHARACTERISTICS_CARD_SCHEMA.md](./CHARACTERISTICS_CARD_SCHEMA.md) pour l’organisme carte / groupes.

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

Affichage unifié des propriétés d'entité selon le mode (`minimal`, `compact`, `extended`, `detailed`). S’appuie sur **`useCharacteristicViewModel`** (qui encapsule `useEntityPropertyDisplay` + résolution runtime) puis **`CharacteristicProperty`**.

**Props** : `fieldKey`, `entity`, `entityType`, `displayMode`, `descriptors`, `tableMeta`, `size`, `variant`, `formulaResolved`, `formulaRaw`, `levelTable`, **`runtime`** (optionnel, même schéma que `resolved-stats` : racine avec `computed` indexé par clé caractéristique).

**Fournir le runtime sans le répéter sur chaque composant**

1. **Prop explicite** : `:runtime="payload"` sur un `EntityPropertyDisplay` (prioritaire).
2. **Contexte Vue** : la vue large (`SpellViewLarge`, `ResourceViewLarge`, etc.) accepte **`characteristicRuntime`** et appelle **`provideCharacteristicRuntime(computed(() => props.characteristicRuntime))`** — tous les `EntityPropertyDisplay` descendants reçoivent le payload si la prop `runtime` n’est pas passée.
3. **Pages Inertia** : les fiches `spell/Show.vue` et `resource/Show.vue` lisent **`page.props.characteristicRuntime`** et la passent à la vue large — dès qu’un contrôleur ajoutera cette clé au render, les tooltips s’enrichiront sans autre changement front.
4. **Fetch générique** : [`useCharacteristicRuntimeFetch.js`](../../resources/js/Composables/entity/useCharacteristicRuntimeFetch.js) pour tout `GET` renvoyant le même JSON ; les créatures utilisent [`useCreatureResolvedStats.js`](../../resources/js/Composables/entity/useCreatureResolvedStats.js) (URL Ziggy + query `entity`).

### PropertyDisplay (Atom)

`resources/js/Pages/Atoms/data-display/PropertyDisplay.vue`

Wrapper vers **`CharacteristicProperty`** (`viewModelFromLegacyProperty`). Variants inchangés : `badge`, `icon`, `inline`.

**Props** : `property` (icon, label, tooltip, color), `value`, `variant`, `size`.

### CharacteristicChip (Atom)

Wrapper vers **`CharacteristicProperty`** avec `viewModelFromChipItem`. Utilisé pour les cellules `chips` des tableaux (CharacteristicInlineGroup).

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
