# Plan d'adaptation — Éléments multiples (Spell & Capability)

> **Statut** : Implémenté (2026-03-08)

## Contexte

Les entités **Spell** et **Capability** utilisent un champ `element` pour représenter l'affinité élémentaire. Les éléments de base sont : **Neutre**, **Terre**, **Feu**, **Air**, **Eau**. Une entité peut avoir 1 à 5 éléments (combinaisons).

L'utilisateur souhaite :
- Utiliser une **constante numérique** (0-29) pour les éléments et combinaisons (pas de table pivot)
- Associer **couleurs** et **dégradés** pour l'affichage multi-élément
- Réutiliser les **icônes** existantes pour les éléments primaires
- Créer un **composant Vue** dédié pour l'affichage

---

## État actuel (audit)

### Backend

| Fichier | Élément | Type | Valeurs |
|---------|---------|------|---------|
| `app/Models/Entity/Spell.php` | `Spell::ELEMENT` | `int` 0-29 | Référentiel complet (Neutre → Neutre-Terre-Feu-Air-Eau) |
| `database/migrations/...spells_table.php` | `element` | `integer` | OK |
| `app/Models/Entity/Capability.php` | `element` | `string` | Pas de constante, valeurs hétérogènes |
| `database/migrations/...capabilities_table.php` | `element` | `string` default 'neutral' | À migrer vers `integer` |
| `CapabilityTableController` | filterOptions | strings '0','1','2','4','5','6' | Incohérent (manque 3=Air, valeur 6=Chance) |
| `CapabilityBulkController` | validation | `string` | Exemple "fire" — à aligner |
| `ImportLegacyCapabilitiesCommand` | mapping | `string` | Conserve "0","1",etc. du JSON legacy |

### Frontend

| Fichier | Usage |
|---------|-------|
| `SharedConstants.js` | `SPELL_ELEMENT_OPTIONS` (0-29) — aligné avec Spell |
| `SharedConstants.js` | `CREATURE_ELEMENT_ICONS` — neutre, terre, feu, air, eau (icônes) |
| `ElementFormatter.js` | Options 0-6 seulement, mapping erroné (3=Air, 4=Air, 5=Eau) |
| `capability-descriptors.js` | Options string: neutral, fire, water, earth, air |
| `spell-descriptors.js` | Options via `getSpellElementOptions` (0-29) |
| `spell/Edit.vue` | Select limité à 0-4 (5 éléments de base) — incomplet |
| `SpellViewLarge/Compact/Minimal` | `element` dans metaFields, Badge générique |
| `CapabilityViewLarge` | `element` dans extendedFields |
| `Capability.js` ( modèle ) | `_toElementCell` délègue à ElementFormatter |
| `Spell.js` ( modèle ) | Icône `fa-fire` fixe dans chips, pas d’icône par élément |

### Données legacy (capability.json)

- `element` : `"0"`, `"1"`, `"2"`, `"4"`, `"5"`, `"6"` (valeurs string numériques)
- Mapping actuel : 0 Neutre, 1 Terre, 2 Feu, 4 Air (3 absent), 5 Eau, 6 Chance

### Thème SCSS

- `_theme-caracs.scss` : `terre`=brown, `feu`=red, `air`=green, `eau`=blue
- Pas de variables dédiées aux éléments pour gradients

---

## Référentiel éléments (source de vérité)

### Constantes numériques (aligné Spell::ELEMENT)

| Valeur | Libellé |
|--------|---------|
| 0 | Neutre |
| 1 | Terre |
| 2 | Feu |
| 3 | Air |
| 4 | Eau |
| 5 | Neutre-Terre |
| 6 | Neutre-Feu |
| 7 | Neutre-Air |
| 8 | Neutre-Eau |
| 9 | Terre-Feu |
| 10 | Terre-Air |
| 11 | Terre-Eau |
| 12 | Feu-Air |
| 13 | Feu-Eau |
| 14 | Air-Eau |
| 15-29 | Combinaisons 3, 4, 5 éléments |

**Note** : La valeur 6 legacy (« Chance ») peut être mappée vers 0 (Neutre) ou conservée comme alias lors de l’import.

---

## Plan d’implémentation

### Phase 1 — Backend : unification et migration Capability

1. **Constante partagée PHP**
   - Créer `app/Support/ElementConstants.php` (ou `app/Models/Concerns/HasElement.php`) avec la constante ELEMENT alignée sur `Spell::ELEMENT`.
   - Faire référencer cette constante par `Spell` et `Capability` pour éviter la duplication.

2. **Modèle Capability**
   - Ajouter la constante `ELEMENT` (réutiliser celle de Spell ou la source centralisée).
   - Caster `element` en `integer` dans `$casts`.

3. **Migration Capability**
   - Nouvelle migration : `element` `string` → `unsignedTinyInteger` (ou `integer`).
   - Script de conversion : "0"→0, "1"→1, "2"→2, "3"→3, "4"→4, "5"→5, "6"→0 (Chance→Neutre), "neutral"/"fire"/etc. → mapping numérique.

4. **Contrôleurs et validation**
   - `CapabilityTableController` : filterOptions basés sur la constante ELEMENT (0-29 ou sous-ensemble).
   - `CapabilityBulkController` : validation `element` en `integer` `in:0,1,...,29`.
   - `StoreCapabilityRequest` / `UpdateCapabilityRequest` : `element` `nullable|integer|in:0,1,...,29`.

5. **Import legacy**
   - Adapter `ImportLegacyCapabilitiesCommand` pour écrire un `integer` (0-29) au lieu d’une string.

---

### Phase 2 — Frontend : référentiel et formatter

1. **Source de vérité JS**
   - Centraliser dans `SharedConstants.js` ou `resources/js/Utils/Entity/Elements.js` :
     - Liste complète 0-29 (labels, icônes par élément primaire).
   - Mapping décomposition : valeur numérique → tableau d’éléments primaires (ex. 9 → [1,2] pour Terre-Feu).

2. **ElementFormatter**
   - Étendre les options à 0-29.
   - Pour les combinaisons : déterminer la couleur via dégradé ou règle (ex. moyenne des couleurs).
   - Ou : déléguer au nouveau composant `ElementDisplay` pour le rendu.

3. **Spell Edit**
   - Remplacer les options 0-4 par `getSpellElementOptions()` (0-29).

4. **Capability descriptors**
   - Remplacer les options string par des options numériques 0-29 (ou sous-ensemble pertinent).

---

### Phase 3 — SCSS : couleurs et dégradés

1. **Variables élémentaires**
   - Fichier `scss/themes/_theme-elements.scss` (ou extension de `_theme-caracs.scss`) :
     ```scss
     // Couleurs primaires (hex ou variables existantes)
     $element-neutre: ...;
     $element-terre: #...;   // brown / terre
     $element-feu: #...;     // red
     $element-air: #...;     // green / air
     $element-eau: #...;     // blue
     ```

2. **Classes utilitaires**
   - `.element-badge-0` à `.element-badge-29` avec fond solide ou dégradé.
   - Pour 2+ éléments : `linear-gradient(90deg, $elem1 0%, $elem2 100%)` (ex. 90deg, 120deg selon nb d’éléments).

3. **Map pour génération**
   - Map SCSS ou JS définissant pour chaque valeur 0-29 : liste des couleurs (1 à 5) pour construire le gradient.

---

### Phase 4 — Composant Vue `ElementDisplay`

1. **Emplacement**
   - `resources/js/Pages/Atoms/data-display/ElementDisplay.vue` (ou `Molecules` selon complexité).

2. **Props**
   - `element` : `number` (0-29)
   - `size` : `'xs'|'sm'|'md'|'lg'`
   - `showIcon` : `boolean`
   - `showLabel` : `boolean`
   - `variant` : `'badge'|'chip'|'inline'`

3. **Comportement**
   - Affiche icône(s) pour les éléments primaires (1 seul si mono-élément).
   - Pour multi-élément : petite icône par élément ou une icône dominante + label.
   - Fond : classe SCSS dégradé selon la valeur numérique.

4. **Intégration**
   - Utilisé par `CellRenderer` pour les cellules `element` (via `cell.component`).
   - Utilisé par `SpellViewLarge`, `CapabilityViewLarge`, etc. à la place du Badge générique pour le champ `element`.

5. **Index**
   - Ajouter dans `atoms.index.json` (ou `molecules.index.json`).

---

### Phase 5 — Intégration dans les vues et tableau

1. **Descriptors**
   - Pour Spell et Capability : `element` avec `cell` / `display` utilisant `ElementDisplay` comme composant.

2. **CapabilityTableController**
   - Renvoyer des cellules de type `element` avec `component: 'ElementDisplay'` (ou équivalent) pour le rendu cohérent.

3. **Modèles JS**
   - `Spell.js` : remplacer l’icône fixe `fa-fire` par une icône dérivée de la valeur élément.
   - `Capability.js` : s’assurer que `_toElementCell` produit une cellule exploitable par `ElementDisplay`.

4. **Tests**
   - Tests unitaires pour `ElementConstants`, mapping décomposition, conversion legacy.
   - Tests E2E ou snapshot pour `ElementDisplay`.

---

## Ordre recommandé

1. Phase 1 (Backend)
2. Phase 3 (SCSS) — possible en parallèle
3. Phase 2 (Frontend référentiel)
4. Phase 4 (Composant)
5. Phase 5 (Intégration)

---

## Fichiers impactés (liste)

### Backend
- `app/Support/ElementConstants.php` (nouveau)
- `app/Models/Entity/Spell.php` (référence constante partagée)
- `app/Models/Entity/Capability.php` (constante, cast)
- `database/migrations/xxxx_alter_capabilities_element_to_integer.php` (nouveau)
- `app/Console/Commands/ImportLegacyCapabilitiesCommand.php`
- `app/Http/Controllers/Api/Table/CapabilityTableController.php`
- `app/Http/Controllers/Api/CapabilityBulkController.php`
- `app/Http/Requests/Entity/StoreCapabilityRequest.php`
- `app/Http/Requests/Entity/UpdateCapabilityRequest.php` (si existe)

### Frontend
- `resources/js/Utils/Entity/Elements.js` (nouveau, ou extension SharedConstants)
- `resources/js/Utils/Formatters/ElementFormatter.js`
- `resources/js/Utils/Entity/SharedConstants.js`
- `resources/js/Entities/capability/capability-descriptors.js`
- `resources/js/Entities/spell/spell-descriptors.js`
- `resources/js/Pages/Atoms/data-display/ElementDisplay.vue` (nouveau)
- `resources/js/Pages/Pages/entity/spell/Edit.vue`
- `resources/js/Models/Entity/Spell.js`
- `resources/js/Models/Entity/Capability.js`

### SCSS
- `resources/scss/themes/_theme-elements.scss` (nouveau)
- `resources/scss/theme.scss` (import)

### Documentation
- `docs/20-Content/SCHEMA.md` (si schéma Capability modifié)
- `docs/20-Content/21-Entities/ENTITY_CAPABILITIES.md`
- `docs/50-Fonctionnalités/Import-Legacy/IMPORT_LEGACY_CAPABILITIES.md`

---

## Risques et points d’attention

- **Données existantes** : vérifier toutes les valeurs `element` en base pour Capability avant migration.
- **Chance (6)** : décider du mapping (Neutre vs. éventuelle future valeur dédiée).
- **Rétrocompatibilité** : l’import legacy doit rester idempotent ou documenter le changement de schéma.
