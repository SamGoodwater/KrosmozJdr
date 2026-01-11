# Système d'entités — Spécifications

## Principe

KrosmozJDR fonctionne à l'aide d'un système d'entités. Ce sont des objets représentés en base de données.
Ils sont au centre du projet, car ce sont eux qui constituent le contenu du projet.
Chaque entité a ses spécificités (design, propriétés différentes, permissions CRUD).
Ces entités ont néanmoins des points communs, notamment les différents formats d'affichage. Leur construction est similaire d'une entité à l'autre.

Ce fichier décrit le design, le comportement et les interactions avec ces entités, ainsi que les fonctionnalités existantes et celles à améliorer/refactoriser.

## État actuel

**Fonctionnalités existantes et fonctionnelles :**
- ✅ Tableau avec tri, filtres, recherche, visibilité des colonnes, pagination, sélection multiple
- ✅ Layout full-width (le tableau utilise toute la largeur disponible)
- ✅ 4 formats d'affichage (Large, Compact, Minimal, Text)
- ✅ Système de descriptors (resource-descriptors.js comme exemple)
- ✅ Quickedit (panneau latéral et modal)
- ✅ Système d'actions (EntityActions)
- ✅ Système de permissions
- ✅ Classes de configuration (TableConfig, FormConfig, BulkConfig, TableColumnConfig, FormFieldConfig)
- ✅ Classe de base EntityDescriptor avec fonctions communes
- ✅ Utilitaires de formatage (EntityDescriptorHelpers)
- ✅ Constantes centralisées (EntityDescriptorConstants)
- ✅ Composable useBulkEditPanel pour l'agrégation et le dirty state
- ✅ Système de cache pour les descriptors (descriptor-cache)
- ✅ Utilitaires pour générer fieldsConfig depuis descriptors (descriptor-form)
- ✅ Système de routes pour les entités (entityRouteRegistry)
- ✅ Composants génériques (EntityEditForm, EntityRelationsManager)

**À refaire complètement :**
- 🔴 **Système de descriptors** : Refonte complète (trop complexe, mal comportementé, peu scalable)
- 🔴 **Vues Large et Compact** : Passer de la génération automatique à des vues manuelles personnalisables
- 🔴 **Modèles d'entités** : Déplacer la logique de formatage des adapters vers les modèles (approche orientée objet)

**À améliorer/refactoriser :**
- 🔄 Optimiser le système de quickedit
- 🔄 Documenter et standardiser les actions disponibles
- 🔄 Améliorer la cohérence entre les différents formats d'affichage

## Formats d'affichage

La liste des items d'une entité est accessible via un tableau (décrit plus loin).
Une entité peut être affichée dans le tableau, mais aussi dans un modal ou directement ouverte dans une page. Elle peut également être affichée sous forme de carte ou en simple ligne de texte dans une autre page quelconque.

C'est pourquoi chaque entité possède **4 formats d'affichage**, chacun géré par une vue dédiée :

### 1. Large
- **Usage** : Affichage en page dédiée (route complète)
- **Caractéristiques** : Format complet avec toutes les informations détaillées
- **Édition** : Possibilité d'avoir une version éditable
- **Composant** : `EntityViewLarge.vue` ou `ResourceViewLarge.vue` (spécifique par entité)
- **Actions** : Affichées en haut à droite (format boutons icônes)
- **🔄 NOUVEAU** : Vue **manuelle** (pas de génération automatique). Chaque entité peut avoir sa propre vue personnalisée

### 2. Compact
- **Usage** : Affichage en modal
- **Caractéristiques** : Format réduit avec les informations essentielles
- **Édition** : Possibilité d'avoir une version éditable
- **Composant** : `EntityViewCompact.vue` ou `ResourceViewCompact.vue` (spécifique par entité)
- **Actions** : Affichées en haut à côté du bouton fermer (format boutons icônes)
- **🔄 NOUVEAU** : Vue **manuelle** (pas de génération automatique). Chaque entité peut avoir sa propre vue personnalisée

### 3. Minimal
- **Usage** : Intégration dans d'autres pages (non éditable)
- **Caractéristiques** : Petite carte qui s'étend au survol (hover). 2 états : étendu et compact
- **Composant** : `EntityViewMinimal.vue` ou `ResourceViewMinimal.vue` (spécifique par entité)
- **Actions** : Affichées en haut à droite lors de l'extension (format boutons icônes)
- **🔄 NOUVEAU** : Vue **manuelle** (pas de génération automatique). Chaque entité peut avoir sa propre vue personnalisée

### 4. Text
- **Usage** : Intégration minimale dans d'autres pages (non éditable)
- **Caractéristiques** : Juste le nom de l'entité avec son image en version icône. Au survol, affiche la version minimal
- **Composant** : `EntityViewText.vue` ou `ResourceViewText.vue` (spécifique par entité)
- **Actions** : Aucune (affichage minimal)
- **🔄 NOUVEAU** : Vue **manuelle** (pas de génération automatique). Chaque entité peut avoir sa propre vue personnalisée

**Note** : Les données peuvent également être récupérées brutes pour être utilisées dans le tableau ou pour d'autres traitements.
# Les différentes entités 

Le système gère les entités suivantes :

- `attribute`
- `campaign`
- `capability`
- `classe`
- `consumable` et `consumable-type`
- `item` et `item-type`
- `monster` (dépend de `creature` qui est abstraite)
- `npc` (dépend de `creature` qui est abstraite)
- `panoply`
- `resource` et `resource-type`
- `scenario`
- `shop`
- `specialization`
- `spell` et `spell-type`

# Entité côté frontend

## Architecture des classes

Chaque type d'entité possède une classe JavaScript côté frontend.
Il existe une classe parente qui contient l'ensemble des fonctions communes à toutes les entités.
Il existe également un fichier contenant toutes les constantes et les utilitaires de conversion utiles à tout le projet (par exemple pour la rareté : item, consommable, ressource - conversion `1 -> Commun, Color Grey`).

**Structure actuelle :**
- Classes d'entité par type (ex: `Resource`, `Item`, etc.)
- Classe parente commune pour les fonctionnalités partagées
- Utilitaires de conversion centralisés

## Rôle des classes — À AMÉLIORER

**État actuel :**
Les modèles sont actuellement sous-utilisés. Ils font principalement :
- Normalisation des données (getters pour accéder aux propriétés)
- Méthode `toFormData()` pour les formulaires
- Gestion des permissions (via `BaseModel`)

**Problème :**
La logique de formatage est dispersée dans les "adapters" (`buildResourceCell`, `buildItemCell`, etc.) au lieu d'être dans les modèles eux-mêmes.

**Nouvelle approche souhaitée :**

### Délégation aux modèles

**Principe :** Le backend renvoie des données brutes → on transforme en objets Entity → les modèles gèrent le formatage selon la configuration.

**Exemple de flux :**
```javascript
// Backend renvoie des données brutes
const rawData = { id: 1, name: "Bois", rarity: 1, level: 50 };

// Transformation en objet Entity
const resource = new Resource(rawData);

// Le modèle génère la cellule pour le tableau selon la config
const cell = resource.toCell('rarity', {
  context: 'table',
  size: 'sm',  // xs, sm, md, lg, xl (cohérent avec Tailwind CSS)
  config: descriptorConfig  // Configuration du descriptor
});
// Retourne : { type: 'badge', value: 'Commun', params: { color: 'grey', ... } }
```

### Méthodes à ajouter aux modèles

**1. Génération de cellules pour le tableau :**
```javascript
// Dans Resource.js
toCell(fieldKey, options = {}) {
  const { context = 'table', size = 'normal', config = {} } = options;
  
  // Utilise la config du descriptor pour déterminer le format
  // Appelle les méthodes de formatage spécifiques
  return this._buildCell(fieldKey, context, size, config);
}
```

**2. Méthodes de formatage spécifiques :**
```javascript
// Dans Resource.js
formatRarity() {
  // Conversion : 1 -> "Commun", Color Grey
  return {
    label: this._getRarityLabel(this.rarity),
    color: this._getRarityColor(this.rarity),
    value: this.rarity
  };
}

toBadge(fieldKey, options = {}) {
  // Génère un badge configuré pour un champ
  const format = this._getFieldFormat(fieldKey);
  return {
    type: 'badge',
    value: format.value,
    params: {
      color: format.color,
      tooltip: format.tooltip,
      ...
    }
  };
}
```

**3. Méthodes utilitaires de conversion :**
```javascript
// Dans BaseModel ou dans chaque modèle
static fromArray(rawDataArray) {
  // Existant, à conserver
  return rawDataArray.map(data => new this(data));
}

// Nouveau : conversion depuis les données du backend
static fromBackendResponse(response) {
  // Transforme la réponse backend en instances de modèles
  if (Array.isArray(response.data)) {
    return response.data.map(item => new this(item));
  }
  return new this(response.data);
}
```

### Avantages de cette approche

1. **Centralisation** : Toute la logique de formatage est dans les modèles
2. **Réutilisabilité** : Les méthodes peuvent être utilisées partout (tableau, vues, etc.)
3. **Simplicité** : Les descriptors deviennent juste de la configuration
4. **Maintenabilité** : Plus facile de modifier le formatage d'un champ
5. **Testabilité** : Plus facile de tester la logique de formatage

### Architecture proposée

```
Backend → Données brutes
    ↓
Entity Model (Resource, Item, etc.)
    ├── toCell(fieldKey, options) → Cell pour tableau
    ├── toBadge(fieldKey) → Badge configuré
    ├── toIcon(fieldKey) → Icône configuré
    ├── formatRarity() → Formatage spécifique
    └── formatLevel() → Formatage spécifique
    ↓
Descriptor (configuration uniquement)
    ├── TableConfig → Config colonnes
    ├── FormConfig → Config formulaires
    └── BulkConfig → Config quickedit
    ↓
Composants Vue
    ├── Tableau → Utilise entity.toCell()
    ├── Vue Large → Utilise entity.toBadge(), entity.formatRarity(), etc.
    └── Vue Compact → Utilise entity.toBadge(), entity.formatRarity(), etc.
```

**🔄 À implémenter :** Déplacer toute la logique de formatage des adapters vers les modèles.

### Système de Formatters centralisés

**Problème identifié :** La rareté (et d'autres propriétés communes) est dupliquée dans plusieurs fichiers :
- `EntityDescriptorConstants.js` : RARITY_OPTIONS
- `adapter-helpers.js` : RARITY_LABELS et rarityColor()
- `consumable-adapter.js` : RARITY_LABELS et rarityColor() (dupliqué)
- `resource-adapter.js` : RESOURCE_RARITY_LABELS (dupliqué)

**Solution : Système de Formatters centralisés**

Créer un système de formatters réutilisables pour les propriétés communes :

```
Utils/
└── Formatters/
    ├── RarityFormatter.js      # Rareté (Resource, Item, Consumable, etc.)
    ├── LevelFormatter.js       # Niveau (commun à plusieurs entités)
    ├── VisibilityFormatter.js  # Visibilité (commun)
    ├── PriceFormatter.js       # Prix (commun)
    └── BaseFormatter.js        # Classe de base abstraite
```

**Structure proposée :**

```javascript
// Utils/Formatters/RarityFormatter.js
export class RarityFormatter {
  static OPTIONS = Object.freeze([
    { value: 0, label: "Commun", color: "gray", icon: "fa-solid fa-circle" },
    { value: 1, label: "Peu commun", color: "blue", icon: "fa-solid fa-circle" },
    { value: 2, label: "Rare", color: "green", icon: "fa-solid fa-circle" },
    { value: 3, label: "Très rare", color: "purple", icon: "fa-solid fa-circle" },
    { value: 4, label: "Légendaire", color: "orange", icon: "fa-solid fa-star" },
    { value: 5, label: "Unique", color: "red", icon: "fa-solid fa-star" },
  ]);

  /**
   * Formate une valeur de rareté
   * @param {number} value - Valeur de rareté (0-5)
   * @returns {Object} { label, color, icon, value }
   */
  static format(value) {
    const option = this.OPTIONS.find(opt => opt.value === value) || this.OPTIONS[0];
    return {
      label: option.label,
      color: option.color,
      icon: option.icon,
      value: value
    };
  }

  /**
   * Génère une cellule pour le tableau
   * @param {number} value - Valeur de rareté
   * @param {Object} options - Options (context, size, etc.)
   * @returns {Object} Cell object
   */
  static toCell(value, options = {}) {
    const formatted = this.format(value);
    return {
      type: 'badge',
      value: formatted.label,
      params: {
        color: formatted.color,
        tooltip: formatted.label,
        sortValue: value,
        filterValue: String(value),
        searchValue: formatted.label,
        autoScheme: 'rarity',
        autoLabel: String(value),
      }
    };
  }

  /**
   * Retourne uniquement le label
   */
  static getLabel(value) {
    return this.format(value).label;
  }

  /**
   * Retourne uniquement la couleur
   */
  static getColor(value) {
    return this.format(value).color;
  }
}
```

**Utilisation dans les modèles :**

```javascript
// Models/Entity/Resource.js
import { RarityFormatter } from '@/Utils/Formatters/RarityFormatter';

export class Resource extends BaseModel {
  // ...
  
  /**
   * Formate la rareté en utilisant le formatter centralisé
   */
  formatRarity() {
    return RarityFormatter.format(this.rarity);
  }
  
  /**
   * Génère une cellule pour la rareté
   */
  toRarityCell(options = {}) {
    return RarityFormatter.toCell(this.rarity, options);
  }
  
  /**
   * Génère une cellule pour un champ quelconque
   */
  toCell(fieldKey, options = {}) {
    switch (fieldKey) {
      case 'rarity':
        return this.toRarityCell(options);
      case 'level':
        return LevelFormatter.toCell(this.level, options);
      // ...
      default:
        return this._toDefaultCell(fieldKey, options);
    }
  }
}
```

**Avantages :**
- ✅ **Centralisation** : Une seule source de vérité pour chaque propriété commune
- ✅ **Réutilisabilité** : Utilisable partout (modèles, vues, tableaux)
- ✅ **Maintenabilité** : Modification en un seul endroit
- ✅ **Cohérence** : Même formatage partout
- ✅ **Testabilité** : Facile à tester indépendamment

**Analyse des migrations de base de données :**

Après analyse des migrations, voici les colonnes communes identifiées qui nécessitent des formatters :

### Formatters prioritaires (propriétés très communes)

1. **`RarityFormatter`** ✅ (déjà proposé)
   - **Entités** : `resources`, `items`, `consumables`
   - **Type** : `integer` (0-5)
   - **Usage** : Badge coloré avec label

2. **`LevelFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `creatures`, `capabilities`, `classes`
   - **Type** : `string` (ex: "1", "50", "100")
   - **Usage** : Badge ou texte avec formatage niveau

3. **`VisibilityFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `creatures`, `shops`, `classes`, `capabilities`, `scenarios`, `panoplies`, `attributes`, `specializations`, `pages`, `sections`, et tous les types
   - **Type** : `string` (guest, user, game_master, admin)
   - **Usage** : Badge coloré avec label de permission

4. **`UsableFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `creatures`, `shops`, `classes`, `capabilities`, `scenarios`, `panoplies`, `attributes`, `specializations`, et tous les types
   - **Type** : `tinyInteger` (0/1) ou `boolean`
   - **Usage** : Icône ou badge booléen (Oui/Non)

5. **`PriceFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `shops` (et dans les pivots)
   - **Type** : `string` (ex: "1000", "50000")
   - **Usage** : Formatage avec séparateurs (1 000, 50 000) + unité (kamas)

6. **`DofusVersionFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `monsters`, `classes`
   - **Type** : `string` (ex: "3", "2.0")
   - **Usage** : Badge ou texte avec version

7. **`AutoUpdateFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `monsters`, `classes`
   - **Type** : `boolean`
   - **Usage** : Icône ou badge booléen

8. **`DofusdbIdFormatter`**
   - **Entités** : `resources`, `items`, `consumables`, `spells`, `monsters`, `classes`, `panoplies`
   - **Type** : `string` (nullable)
   - **Usage** : Lien externe vers DofusDB ou texte

### Formatters secondaires (propriétés moins communes)

9. **`WeightFormatter`**
   - **Entités** : `resources` uniquement
   - **Type** : `string`
   - **Usage** : Formatage avec unité (kg)

10. **`ImageFormatter`**
    - **Entités** : `resources`, `items`, `consumables`, `spells`, `creatures`, `attributes`
    - **Type** : `string` (URL)
    - **Usage** : Miniature d'image

11. **`OfficialIdFormatter`**
    - **Entités** : `resources`, `items`, `consumables`, `spells`, `monsters`
    - **Type** : `string` ou `integer`
    - **Usage** : Texte ou badge

12. **`DateFormatter`**
    - **Entités** : Toutes (via `timestamps`)
    - **Type** : `timestamp` (created_at, updated_at, deleted_at)
    - **Usage** : Formatage français (date courte, date+heure)

13. **`BooleanFormatter`** (générique)
    - **Entités** : Plusieurs (auto_update, usable, po_editable, etc.)
    - **Type** : `boolean` ou `tinyInteger`
    - **Usage** : Icône ou badge booléen réutilisable

### Formatters spécialisés (propriétés spécifiques)

14. **`HostilityFormatter`**
    - **Entités** : `creatures` uniquement
    - **Type** : `integer` (0-4)
    - **Usage** : Badge coloré (Amical, Curieux, Neutre, Hostile, Agressif)

15. **`ElementFormatter`** (pour les sorts)
    - **Entités** : `spells`
    - **Type** : `integer`
    - **Usage** : Badge avec élément (Terre, Feu, Air, Eau, Neutre)

16. **`CategoryFormatter`** (pour les sorts)
    - **Entités** : `spells`
    - **Type** : `integer`
    - **Usage** : Badge avec catégorie de sort

### Résumé des formatters à créer

**Priorité 1 (très communs, > 5 entités) :**
- ✅ `RarityFormatter`
- ✅ `LevelFormatter`
- ✅ `VisibilityFormatter`
- ✅ `UsableFormatter`
- ✅ `PriceFormatter`
- ✅ `DofusVersionFormatter`
- ✅ `AutoUpdateFormatter`
- ✅ `DofusdbIdFormatter`

**Priorité 2 (moins communs, 2-5 entités) :**
- ✅ `WeightFormatter`
- ✅ `ImageFormatter`
- ✅ `OfficialIdFormatter`
- ✅ `DateFormatter`
- ✅ `BooleanFormatter` (générique)

**Priorité 3 (spécialisés, 1 entité) :**
- ✅ `HostilityFormatter`
- ✅ `ElementFormatter`
- ✅ `CategoryFormatter`

### Tableau récapitulatif des colonnes communes

| Colonne | Type | Entités concernées | Nombre | Formatter |
|---------|------|-------------------|--------|-----------|
| `rarity` | integer | resources, items, consumables | 3 | `RarityFormatter` |
| `level` | string | resources, items, consumables, spells, creatures, capabilities, classes | 7 | `LevelFormatter` |
| `is_visible` | string | resources, items, consumables, spells, creatures, shops, classes, capabilities, scenarios, panoplies, attributes, specializations, pages, sections, + types | 15+ | `VisibilityFormatter` |
| `usable` | tinyInteger | resources, items, consumables, spells, creatures, shops, classes, capabilities, scenarios, panoplies, attributes, specializations, + types | 15+ | `UsableFormatter` |
| `price` | string | resources, items, consumables, shops (+ pivots) | 4+ | `PriceFormatter` |
| `dofus_version` | string | resources, items, consumables, spells, monsters, classes | 6 | `DofusVersionFormatter` |
| `auto_update` | boolean | resources, items, consumables, spells, monsters, classes | 6 | `AutoUpdateFormatter` |
| `dofusdb_id` | string | resources, items, consumables, spells, monsters, classes, panoplies | 7 | `DofusdbIdFormatter` |
| `image` | string | resources, items, consumables, spells, creatures, attributes | 6 | `ImageFormatter` |
| `official_id` | string/integer | resources, items, consumables, spells, monsters, classes | 6 | `OfficialIdFormatter` |
| `weight` | string | resources | 1 | `WeightFormatter` |
| `created_at` / `updated_at` | timestamp | Toutes les entités | Toutes | `DateFormatter` |
| `hostility` | integer | creatures | 1 | `HostilityFormatter` |
| `element` | integer/string | spells, capabilities | 2 | `ElementFormatter` |
| `category` | integer | spells | 1 | `CategoryFormatter` |

**Note :** Les colonnes `name`, `description`, `created_by`, `timestamps`, `softDeletes` sont communes mais ne nécessitent généralement pas de formatters spécifiques (formatage texte standard ou dates via `DateFormatter`).

### Architecture optimisée : Formatters + BaseModel

**Approche hybride :** Combiner les formatters centralisés avec des méthodes dans `BaseModel` pour les propriétés communes.

**1. Formatters centralisés (statiques) :**
```javascript
// Utils/Formatters/RarityFormatter.js
export class RarityFormatter {
  static OPTIONS = [...];
  static format(value) { ... }
  static toCell(value, options) { ... }
}
```

**2. Méthodes communes dans BaseModel :**
```javascript
// Models/BaseModel.js
import { RarityFormatter } from '@/Utils/Formatters/RarityFormatter';
import { LevelFormatter } from '@/Utils/Formatters/LevelFormatter';

export class BaseModel {
  // ...
  
  /**
   * Vérifie si l'entité a une propriété de rareté
   * @returns {boolean}
   */
  hasRarity() {
    return 'rarity' in this._data && this._data.rarity !== null && this._data.rarity !== undefined;
  }
  
  /**
   * Formate la rareté si elle existe
   * @returns {Object|null}
   */
  formatRarity() {
    if (!this.hasRarity()) return null;
    return RarityFormatter.format(this._data.rarity);
  }
  
  /**
   * Génère une cellule pour la rareté si elle existe
   */
  toRarityCell(options = {}) {
    if (!this.hasRarity()) return null;
    return RarityFormatter.toCell(this._data.rarity, options);
  }
  
  /**
   * Vérifie si l'entité a un niveau
   */
  hasLevel() {
    return 'level' in this._data && this._data.level !== null && this._data.level !== undefined;
  }
  
  /**
   * Formate le niveau si il existe
   */
  formatLevel() {
    if (!this.hasLevel()) return null;
    return LevelFormatter.format(this._data.level);
  }
  
  /**
   * Génère une cellule pour le niveau si il existe
   */
  toLevelCell(options = {}) {
    if (!this.hasLevel()) return null;
    return LevelFormatter.toCell(this._data.level, options);
  }
}
```

**3. Utilisation dans les modèles spécifiques :**
```javascript
// Models/Entity/Resource.js
export class Resource extends BaseModel {
  // Les méthodes formatRarity() et toRarityCell() sont héritées de BaseModel
  
  /**
   * Génère une cellule pour un champ quelconque
   */
  toCell(fieldKey, options = {}) {
    // Utilise les méthodes héritées de BaseModel
    switch (fieldKey) {
      case 'rarity':
        return this.toRarityCell(options) || this._toDefaultCell(fieldKey, options);
      case 'level':
        return this.toLevelCell(options) || this._toDefaultCell(fieldKey, options);
      case 'name':
        return this._toNameCell(options);
      // ...
      default:
        return this._toDefaultCell(fieldKey, options);
    }
  }
}
```

**Avantages de cette approche :**
- ✅ **DRY (Don't Repeat Yourself)** : Pas de duplication de code
- ✅ **Réutilisabilité maximale** : Les méthodes communes sont dans `BaseModel`
- ✅ **Flexibilité** : Chaque modèle peut surcharger si nécessaire
- ✅ **Cohérence** : Même comportement pour toutes les entités avec rareté/niveau
- ✅ **Maintenabilité** : Modification en un seul endroit (`BaseModel` ou `Formatter`)

**Alternative : Système de registre de formatters**

Pour encore plus de flexibilité, on peut créer un registre de formatters :

```javascript
// Utils/Formatters/FormatterRegistry.js
export class FormatterRegistry {
  static formatters = new Map();
  
  static register(fieldKey, formatter) {
    this.formatters.set(fieldKey, formatter);
  }
  
  static get(fieldKey) {
    return this.formatters.get(fieldKey);
  }
  
  static format(fieldKey, value, options = {}) {
    const formatter = this.get(fieldKey);
    if (!formatter) return null;
    return formatter.format(value, options);
  }
}

// Enregistrement des formatters
FormatterRegistry.register('rarity', RarityFormatter);
FormatterRegistry.register('level', LevelFormatter);
FormatterRegistry.register('visibility', VisibilityFormatter);
```

**Utilisation :**
```javascript
// Dans BaseModel
toCell(fieldKey, options = {}) {
  const formatter = FormatterRegistry.get(fieldKey);
  if (formatter && this._data[fieldKey] !== undefined) {
    return formatter.toCell(this._data[fieldKey], options);
  }
  return this._toDefaultCell(fieldKey, options);
}
```

Cette approche permet d'ajouter facilement de nouveaux formatters sans modifier `BaseModel`.

### Exemple concret

**Avant (logique dans l'adapter) :**
```javascript
// resources/js/Entities/resource/resource-adapter.js
export function buildResourceCell(colId, entity, ctx = {}, opts = {}) {
  if (colId === "rarity") {
    const rarity = entity?.rarity ?? 0;
    const label = getRarityLabel(rarity);  // Fonction utilitaire externe
    const color = getRarityColor(rarity);  // Fonction utilitaire externe
    return {
      type: "badge",
      value: label,
      params: { color, tooltip: label, ... }
    };
  }
  // ...
}
```

**Après (logique dans le modèle) :**
```javascript
// resources/js/Models/Entity/Resource.js
export class Resource extends BaseModel {
  // ...
  
  /**
   * Génère une cellule pour le tableau
   * @param {string} fieldKey - Clé du champ
   * @param {Object} options - Options (context, size, config)
   * @returns {Object} Cell object { type, value, params }
   */
  toCell(fieldKey, options = {}) {
    const { context = 'table', size = 'normal', config = {} } = options;
    const descriptor = config[fieldKey] || {};
    
    // Détermine le format selon la config et la taille
    const format = this._resolveFormat(fieldKey, descriptor, context, size);
    
    switch (fieldKey) {
      case 'rarity':
        return this._toRarityCell(format, size);
      case 'level':
        return this._toLevelCell(format, size);
      case 'name':
        return this._toNameCell(format, size);
      // ...
      default:
        return this._toDefaultCell(fieldKey, format, size);
    }
  }
  
  /**
   * Génère un badge pour la rareté
   */
  _toRarityCell(format, size) {
    const rarityData = this.formatRarity();
    return {
      type: 'badge',
      value: rarityData.label,
      params: {
        color: rarityData.color,
        tooltip: rarityData.label,
        sortValue: this.rarity,
        filterValue: this.rarity,
        searchValue: rarityData.label,
      }
    };
  }
  
  /**
   * Formate la rareté (conversion 1 -> "Commun", Color Grey)
   */
  formatRarity() {
    const rarityMap = {
      0: { label: 'Commun', color: 'grey' },
      1: { label: 'Peu commun', color: 'blue' },
      2: { label: 'Rare', color: 'green' },
      3: { label: 'Très rare', color: 'purple' },
      4: { label: 'Légendaire', color: 'orange' },
      5: { label: 'Unique', color: 'red' },
    };
    return rarityMap[this.rarity] || rarityMap[0];
  }
  
  /**
   * Génère un badge pour un champ quelconque
   */
  toBadge(fieldKey, options = {}) {
    const cell = this.toCell(fieldKey, options);
    if (cell.type === 'badge') {
      return cell;
    }
    // Convertit en badge si nécessaire
    return {
      type: 'badge',
      value: cell.value,
      params: { ...cell.params, color: options.color || 'primary' }
    };
  }
}

// Utilisation dans le tableau
const resource = new Resource(rawData);
const rarityCell = resource.toCell('rarity', {
  context: 'table',
  size: 'small',
  config: descriptorConfig
});
```

**Avantages :**
- ✅ La logique est dans le modèle (orienté objet)
- ✅ Réutilisable partout (tableau, vues, etc.)
- ✅ Plus facile à tester
- ✅ Les descriptors ne font que de la configuration

## Quickedit

### Principe

Le quickedit est une fonctionnalité liée aux tableaux.
Il permet de sélectionner plusieurs lignes d'un tableau et de les modifier simultanément.

### Interface

**✅ Fonctionnalité existante :** Le quickedit peut s'afficher de deux manières :
1. **Panneau latéral** (`EntityQuickEditPanel`) : Apparaît à droite du tableau (l'ensemble gardant la même largeur totale)
2. **Modal** (`EntityQuickEditModal`) : Modal centré pour l'édition rapide

**En-tête du quickedit :**
- Les noms des entités sélectionnées (avec un bouton "Afficher plus" si la sélection fait plus de 2 lignes)
- Le titre "Modification rapide"
- Un bouton "Retour" / "Fermer" pour fermer le quickedit

**Formulaire :**
- Le formulaire dépend de chaque type d'entité
- Il contient uniquement les champs qui peuvent être modifiés via le quickedit (définis dans le descriptor via `edit.form.bulk.enabled: true`)
- Les champs sont organisés par groupes (définis via `edit.form.group`)

### Comportement des champs

**✅ Fonctionnalité existante :** Géré par le composable `useBulkEditPanel`

Pour chaque champ du formulaire :

- **Si la valeur est commune** à toutes les entités sélectionnées : le champ est pré-rempli avec cette valeur
- **Si les valeurs diffèrent** entre les entités : le champ est vide (ou checkbox/équivalent en état indéfini) avec un placeholder ou un label indiquant "Valeurs différentes"

**Agrégation des valeurs :**
- Le composable `useBulkEditPanel` calcule automatiquement l'agrégation (`aggregate`)
- Pour chaque champ : `{ same: true/false, value: ... }`
- Utilise les données brutes (`_data`) pour détecter les différences (les getters peuvent normaliser)

**État dirty :**
- Le composable gère l'état `dirty` pour chaque champ
- Un champ est "dirty" s'il a été modifié par l'utilisateur
- Permet de savoir quels champs ont été modifiés pour construire le payload

### Actions

À la fin du formulaire, deux boutons sont disponibles :

1. **Réinitialiser** : Annule tous les changements et revient aux valeurs d'origine
2. **Valider** : Applique les modifications sur l'ensemble des entités sélectionnées

**Important** : Si un champ n'a pas été modifié (notamment ceux avec des valeurs différentes), on conserve les valeurs d'origine pour chaque entité. Cela permet de mettre à jour un ou plusieurs champs précis d'une ou plusieurs entités sans toucher aux autres propriétés.

**Construction du payload :**
- Le composable `useBulkEditPanel` fournit `buildPayload()` qui :
  - Ne prend que les champs "dirty" (modifiés)
  - Applique les fonctions `build` du BulkConfig pour transformer les valeurs
  - Gère les valeurs `nullable` (peut envoyer null si vide)
  - Construit le payload final pour l'API backend

**Modes de fonctionnement :**
- **Mode "server"** : Le payload contient uniquement les IDs sélectionnés
- **Mode "client"** : Le payload peut inclure les IDs filtrés (scope "filtered" vs "selected")
## Description (EntityDescriptor) — À REFAIRE

### Problèmes du système actuel

**❌ Problèmes identifiés :**
1. **Mal comportementé** : Le système actuel génère des comportements inattendus
2. **Complexe** : Structure trop imbriquée et difficile à comprendre/maintenir
3. **Peu scalable** : Difficile d'ajouter de nouvelles entités ou fonctionnalités
4. **Génération automatique des vues** : Les vues Large et Compact sont générées automatiquement à partir de listes de champs (`viewFields.compact`, `viewFields.extended`), ce qui limite la flexibilité et le contrôle

**Structure actuelle problématique :**
- Descriptors avec configuration `display.views` et `display.sizes` trop complexe
- Listes de champs séparées (`RESOURCE_VIEW_FIELDS`) pour chaque vue
- Génération automatique des vues en itérant sur ces listes
- Logique de rendu dispersée entre les descriptors et les composants Vue

### Nouvelle approche souhaitée

**🎯 Objectifs :**
1. **Simplicité** : Structure claire et facile à comprendre
2. **Flexibilité** : Contrôle total sur l'affichage de chaque vue
3. **Scalabilité** : Facile d'ajouter de nouvelles entités
4. **Vues manuelles** : Les vues Large et Compact doivent être créées manuellement (pas de génération automatique)

**Principes de la nouvelle architecture :**

1. **Descriptors simplifiés** :
   - Focus sur la configuration du **tableau** uniquement
   - Configuration des **formulaires** (édition simple et bulk/quickedit)
   - Pas de configuration d'affichage pour les vues Large/Compact (elles seront manuelles)

2. **Vues manuelles** :
   - Les vues **Large** et **Compact** sont des composants Vue créés manuellement
   - Chaque entité peut avoir ses propres composants de vue personnalisés


3. **Séparation des responsabilités** :
   - **Descriptors** : Configuration du tableau et des formulaires uniquement
   - **Vues** : Composants Vue personnalisés pour chaque entité
   - **Utilitaires** : Fonctions de formatage/conversion réutilisables

4. **Structure proposée** :
   ```
   Entities/
   ├── resource/
   │   ├── ResourceDescriptor.js      # Configuration tableau + formulaires
   │   ├── ResourceTableConfig.js      # Config spécifique tableau
   │   ├── ResourceFormConfig.js      # Config formulaires
   │   ├── ResourceBulkConfig.js      # Config quickedit
   │   ├── views/
   │   │   ├── ResourceViewLarge.vue   # Vue manuelle
   │   │   ├── ResourceViewCompact.vue # Vue manuelle
   │   │   ├── ResourceViewMinimal.vue # Vue manuelle
   │   │   └── ResourceViewText.vue    # Vue manuelle
   │   └── Resource.js                 # Classe entité
   ```

### Rôles du nouveau descriptor

**Le descriptor doit gérer uniquement :**

1. **Configuration du tableau** :
   - Colonnes (header, sortable, visible, format)
   - Filtres et recherche
   - Permissions d'accès

2. **Configuration des formulaires** :
   - Champs éditables (édition simple)
   - Champs bulk-editables (quickedit)
   - Validation et transformation des données

3. **Utilitaires de formatage** :
   - Formatage des valeurs pour le tableau
   - Conversion des données (ex: rareté 1 → "Commun")
   - Fonctions réutilisables pour les vues manuelles

**Le descriptor NE doit PAS gérer :**
- ❌ La structure/layout des vues Large et Compact
- ❌ L'ordre d'affichage des champs dans les vues
- ❌ La génération automatique des vues

### Classe EntityDescriptor (nouvelle version)

**✅ État actuel :** La classe `EntityDescriptor` existe déjà dans `Entities/entity/EntityDescriptor.js`

**Ce qui existe :**
- Classe de base avec constantes communes (RARITY_OPTIONS, VISIBILITY_OPTIONS, BREAKPOINTS, etc.)
- Fonctions communes de formatage (truncate, capitalize, formatRarity, formatVisibility, formatDate, etc.)
- Helpers de validation (validateOption, getOptionLabel)
- Valeurs par défaut (format, color, showInCompact, required, etc.)

**À améliorer pour la nouvelle version :**
- **Validation** : Valider la configuration du tableau et des formulaires
- **Valeurs par défaut** : Proposer des valeurs par défaut pour faciliter la configuration
- **Méthodes utilitaires** : Fournir des méthodes de formatage/conversion réutilisables (déjà présent)
- **Pas de génération automatique** : Les vues sont créées manuellement
- **Intégration avec Formatters** : Utiliser les formatters centralisés au lieu des fonctions locales

### Architecture proposée pour la refonte

**Structure de fichiers :**
```
Entities/
├── entity/
│   ├── EntityDescriptor.js        # Classe parente abstraite
│   ├── TableConfig.js              # Configuration tableau (existant, à simplifier)
│   ├── FormConfig.js               # Configuration formulaires
│   └── BulkConfig.js               # Configuration quickedit (existant)
│
├── resource/
│   ├── ResourceDescriptor.js       # Descriptor principal (simplifié)
│   ├── ResourceTableConfig.js      # Config tableau spécifique
│   ├── ResourceFormConfig.js       # Config formulaires
│   ├── ResourceBulkConfig.js        # Config quickedit
│   ├── Resource.js                 # Classe entité
│   └── views/
│       ├── ResourceViewLarge.vue   # Vue manuelle (NOUVEAU)
│       └── ResourceViewCompact.vue # Vue manuelle (NOUVEAU)
│
└── [autres entités...]
```

**Responsabilités :**

1. **EntityDescriptor** (classe parente) :
   - ✅ **Existe déjà** : `Entities/entity/EntityDescriptor.js`
   - Méthodes abstraites : `getTableConfig()`, `getFormConfig()`, `getBulkConfig()`
   - Méthodes utilitaires communes : formatage, conversion (déjà présent)
   - Validation de la configuration
   - Constantes communes (RARITY_OPTIONS, BREAKPOINTS, etc.) - déjà présent

2. **TableConfig** :
   - ✅ **Existe déjà** : `Entities/entity/TableConfig.js`
   - Configuration des colonnes (header, sortable, visible, format)
   - Configuration des filtres et recherche
   - Permissions d'accès au tableau
   - Configuration quickEdit et actions

3. **TableColumnConfig** :
   - ✅ **Existe déjà** : `Entities/entity/TableColumnConfig.js`
   - Configuration individuelle de chaque colonne
   - Visibilité responsive (xs, sm, md, lg, xl)
   - Permissions par colonne

4. **FormConfig** :
   - ✅ **Existe déjà** : `Entities/entity/FormConfig.js`
   - Configuration des champs éditables (édition simple)
   - Validation et transformation des données
   - Groupes de champs

5. **FormFieldConfig** :
   - ✅ **Existe déjà** : `Entities/entity/FormFieldConfig.js`
   - Configuration individuelle de chaque champ de formulaire
   - Type, validation, options, groupes

6. **BulkConfig** :
   - ✅ **Existe déjà** : `Entities/entity/BulkConfig.js`
   - Configuration des champs bulk-editables (quickedit)
   - Transformation des valeurs avant envoi
   - Gestion des valeurs nulles/vides
   - Liste des champs quickEdit

7. **ViewConfig et QuickEditViewConfig** :
   - ✅ **Existent déjà** : `Entities/entity/ViewConfig.js`
   - Configuration des vues (Large, Compact, Minimal, Text)
   - Configuration spécifique pour QuickEdit (panel/modal)
   - ⚠️ **À adapter** : Ces classes sont pour la génération automatique, à revoir pour les vues manuelles

8. **Vues manuelles** :
   - `ResourceViewLarge.vue` : Vue complète personnalisée
   - `ResourceViewCompact.vue` : Vue compacte personnalisée
   - `ResourceViewMinimal.vue` : Vue minimale personnalisée
   - `ResourceViewText.vue` : Vue texte personnalisée
   - Utilisent les méthodes du modèle (`toCell()`, `formatRarity()`, etc.)
   - Contrôle total sur le layout et l'affichage

9. **Utilitaires et Composables** :
   - ✅ **EntityDescriptorHelpers** : Fonctions de formatage communes (truncate, formatRarity, etc.)
   - ✅ **EntityDescriptorConstants** : Constantes centralisées (RARITY_OPTIONS, BREAKPOINTS, etc.)
   - ✅ **useBulkEditPanel** : Composable pour l'agrégation et le dirty state dans quickedit
   - ✅ **descriptor-cache** : Système de cache pour les descriptors (TTL 5 minutes)
   - ✅ **descriptor-form** : Utilitaires pour générer fieldsConfig depuis descriptors
   - ✅ **entityRouteRegistry** : Système de routes pour les entités

10. **Composants génériques** :
    - ✅ **EntityEditForm** : Formulaire d'édition générique basé sur fieldsConfig
    - ✅ **EntityRelationsManager** : Gestion des relations many-to-many

**Avantages de cette approche :**
- ✅ Simplicité : Structure claire et séparée
- ✅ Flexibilité : Contrôle total sur les vues
- ✅ Scalabilité : Facile d'ajouter de nouvelles entités
- ✅ Maintenabilité : Code plus lisible et compréhensible
- ✅ Réutilisabilité : Utilitaires partagés pour le formatage

### Changements concrets à apporter

**1. Supprimer la génération automatique des vues :**
- ❌ Supprimer les listes `viewFields.compact`, `viewFields.extended`, `viewFields.minimal`
- ❌ Supprimer la logique d'itération automatique dans `EntityViewLarge.vue`, `EntityViewCompact.vue`, `EntityViewMinimal.vue`, `EntityViewText.vue`
- ✅ Créer des composants Vue manuels pour chaque entité (ex: `ResourceViewLarge.vue`, `ResourceViewCompact.vue`, `ResourceViewMinimal.vue`, `ResourceViewText.vue`)

**2. Simplifier les descriptors :**
- ❌ Supprimer `display.views` (trop complexe, remplacé par vues manuelles)
- ✅ Garder `display.sizes` mais utiliser xs, sm, md, lg, xl (au lieu de small/normal/large)
- ✅ Garder uniquement la configuration pour le tableau (`display.sizes` pour les cellules du tableau selon la taille)
- ✅ Garder la configuration des formulaires (`edit.form`)

**3. Réorganiser la structure :**
- ✅ Séparer clairement : TableConfig, FormConfig, BulkConfig
- ✅ Créer un dossier `views/` par entité pour les vues manuelles
- ✅ Centraliser les utilitaires de formatage dans la classe parente

**4. Exemple de migration :**

**Avant (génération automatique) :**
```javascript
// resource-descriptors.js
export const RESOURCE_VIEW_FIELDS = {
  compact: ["rarity", "resource_type", "level", ...],
  extended: ["rarity", "resource_type", "level", ...]
};

// EntityViewCompact.vue (génère automatiquement)
const compactFields = computed(() => {
  const list = cfg?.viewFields?.compact || [];
  // Itère et génère automatiquement...
});
```

**Après (vue manuelle) :**
```vue
<!-- ResourceViewCompact.vue -->
<template>
  <div class="resource-compact">
    <div class="field-group">
      <Badge :value="entity.rarity" />
      <Badge :value="entity.resource_type" />
      <Badge :value="entity.level" />
    </div>
    <!-- Layout personnalisé, contrôle total -->
  </div>
</template>
```

### Description du comportement du tableau

#### Layout du tableau

**✅ Fonctionnalité existante :** Le tableau utilise toute la largeur disponible dans le layout (`w-full`).
- Pas de limitation de largeur (`max-w-4xl` retiré)
- Scroll horizontal automatique avec `overflow-x-auto` sur les conteneurs de tableaux
- Responsive préservé : le tableau ne passe jamais sous le menu de gauche

#### Taille du tableau (tableSize)

En fonction de la taille de l'écran et de l'espace disponible pour le tableau, on déduit une taille du tableau `tableSize` (xs à xl).
Cela permet de gérer plus facilement l'affichage et le responsive de tout le reste en se basant sur cette variable.
De plus, lorsque le quickedit s'active et que le tableau se rétrécit, on peut recalculer sa taille.

#### Configuration globale du tableau

C'est dans le fichier de description que l'on définit le comportement du tableau.

**✅ Fonctionnalités existantes (à conserver) :**
- **Tri** : Système de tri des colonnes (TanStack Table)
- **Filtres** : Filtres par colonne (types : boolean, text, select, multi)
- **Recherche** : Barre de recherche globale (recherche dans les colonnes searchable)
- **Visibilité des colonnes** : Affichage/masquage des colonnes (préférences sauvegardées dans localStorage)
- **Pagination** : Pagination avec choix du nombre d'éléments par page (10, 25, 50, 100)
- **Sélection multiple** : Checkboxes pour sélectionner plusieurs lignes

**Paramètres globaux à configurer dans le descriptor :**
- Le niveau de permission pour y accéder
- Si on peut utiliser le quickedit (si l'utilisateur a les permissions)
- Activation/désactivation des filtres, de la barre de recherche, du système de tri des colonnes
- Activation/désactivation de la sélection multiple

#### Configuration par propriété

Pour chacune des propriétés de l'entité, on indique : 

**HEADER (En-tête de colonne) :**
- Le label dans l'en-tête
- Le helper (texte d'aide) lié à ce label
- L'icône si présente pour l'en-tête

**COLUMN (Colonne) :**
- Si la colonne est triable (sortable)
- À partir de quand on affiche la colonne :
  - `true` : toujours affichée
  - `false` : jamais affichée
  - `never` : ne peut pas être affichée
  - `xs` à `xl` : la colonne s'affichera uniquement si le tableau a une taille compatible

**VALUE (Valeurs) :**
- Si les valeurs de la propriété sont filtrables
- Si les valeurs de la propriété sont recherchables (searchable)

**FORMAT VALUE (Format d'affichage des valeurs) :**
- En fonction de la taille du tableau (xs à xl), on peut proposer différents formats : icône, badge, texte, route, image, forme, etc.
- Il faut un utilitaire rattaché à `EntityDescriptor` qui permet de prendre les paramètres définis à cet endroit puis de les transmettre à la vue qui sera capable de générer le bon élément.

**✅ Formats d'affichage existants (exemple resource-descriptors.js) :**
- `mode: "badge"` : Badge coloré
- `mode: "icon"` : Icône seule
- `mode: "boolIcon"` : Icône pour les booléens
- `mode: "boolBadge"` : Badge pour les booléens
- `mode: "text"` : Texte simple
- `mode: "route"` : Lien cliquable vers la page de l'entité
- `mode: "routeExternal"` : Lien externe
- `mode: "thumb"` : Miniature d'image
- `mode: "dateShort"` : Date courte
- `mode: "dateTime"` : Date et heure
- `truncate: number` : Troncature du texte à N caractères
### Description du Quickedit

Pour chacune des propriétés de l'entité, on indique dans le descriptor :

- **Si la propriété peut être modifiée via le quickedit**

Si oui, on précise :
- **Le groupe** : pour organiser les champs dans le quickedit (regroupement logique)
- **Le type du champ** : `number`, `checkbox`, `input`, `select`, etc.
- **Les autres paramètres** : selon le type de champ (required, min, max, options, etc.)

**Génération automatique :**
La classe parente doit pouvoir générer automatiquement le formulaire du quickedit avec cette description en utilisant les composants du système Atomic Design.

**✅ Structure existante :**

**Classes de configuration :**
- `BulkConfig` : Classe pour configurer l'édition en masse (`Entities/entity/BulkConfig.js`)
- `TableConfig` : Classe pour configurer le tableau (`Entities/entity/TableConfig.js`)
- `FormConfig` : Classe pour configurer les formulaires (`Entities/entity/FormConfig.js`)

**Configuration dans les descriptors :**
- `edit.form.bulk.enabled` : Active l'édition en masse pour un champ
- `edit.form.bulk.nullable` : Permet null/vide en bulk
- `edit.form.bulk.build` : Fonction de transformation de la valeur avant envoi au backend
- `RESOURCE_VIEW_FIELDS.quickEdit` : Liste des champs affichés dans le quickedit (doit être alignée avec le backend)

**Composables et utilitaires :**
- `useBulkEditPanel` : Composable pour l'agrégation, dirty state, et construction du payload
- `createBulkFieldMetaFromDescriptors` : Génère la meta des champs bulk depuis les descriptors
- `EntityQuickEditPanel` : Composant panneau latéral
- `EntityQuickEditModal` : Composant modal

**Génération automatique :**
- Le formulaire quickedit est généré automatiquement depuis les descriptors
- Utilise `createFieldsConfigFromDescriptors` pour créer les champs
- Utilise `useBulkEditPanel` pour gérer l'état et l'agrégation
## Les actions

### Principe

Chaque entité possède plusieurs actions. Leur accès dépend de :
- Si elles sont configurées à `true` dans le descriptor de l'entité
- Si l'utilisateur a les droits nécessaires pour les utiliser

### Emplacements des actions

On retrouve les actions à **5 endroits**, soit sous forme de liste avec leur nom (comme un dropdown), soit sous forme d'enchaînement d'icônes :

1. **1ère colonne dans les tableaux** : Bouton qui ouvre le menu liste des actions
2. **Clic droit sur une ligne** : Ouvre le menu liste des actions
3. **En haut à côté du bouton fermer** : Dans les modals d'entité (format Compact)
4. **En haut à droite** : Sur une page d'entité (format Large)
5. **En haut à droite** : Lors de l'extension du format minimal d'une entité

### Liste des actions

**✅ Fonctionnalité existante :** Le système d'actions est géré par `EntityActions.vue` et `entity-actions-config.js`

Les différentes actions disponibles sont :

| Action | Description | Disponibilité | Clé dans le code |
|--------|-------------|---------------|------------------|
| `view` / `showPage` | Affiche l'entité en page complète | Généralement Compact et Tableau | `view` |
| `quick-view` / `showModal` | Affiche l'entité dans un modal | Généralement Minimal et Tableau | `quick-view` |
| `edit` / `editPage` | Édite l'entité en page complète | Généralement Large et Tableau | `edit` |
| `quick-edit` / `editModal` | Édite l'entité dans un modal | Généralement Compact, Minimal | `quick-edit` |
| `copy-link` / `copyUrl` | Copie l'URL de l'entité | Généralement Minimal, Compact, Large, Tableau | `copy-link` |
| `download-pdf` / `downloadPDF` | Télécharge le PDF de l'entité | Généralement Minimal, Compact, Large, Tableau | `download-pdf` |
| `refresh` / `refreshDofusdb` | Rafraîchit les données (scrapping) | Généralement Compact, Large, Tableau | `refresh` |
| `delete` | Supprime l'entité | Si permission delete | `delete` |
| `minimize` | Réduit la modal en forme d'icône en bas (pas encore développé) | Généralement Minimal, Compact, Large, Tableau | `minimize` |

**Configuration des actions :**
- Les actions sont configurées dans `entity-actions-config.js`
- Chaque entité peut avoir sa propre configuration d'actions
- Les actions sont filtrées selon les permissions de l'utilisateur
- Le composant `EntityActions` gère l'affichage selon le format (buttons, dropdown, context)

### Action spéciale : quickEdit

**`quickEdit`** est une action particulière qui n'est pas dans la liste des actions standard.
Elle permet une édition d'éléments multiples depuis le tableau.
Elle est accessible lors de la sélection d'une ou plusieurs lignes dans un tableau si l'utilisateur a les droits `Write`.

**✅ Fonctionnalité existante :**
- Le quickedit est déclenché automatiquement lors de la sélection de lignes dans le tableau
- Il nécessite la permission `updateAny` (configurable dans `TableConfig.withQuickEdit()`)
- Les champs éditables sont définis dans le descriptor via `edit.form.bulk.enabled: true`

## Permissions

### Système de permissions

**✅ Fonctionnalité existante :** Le système utilise `usePermissions` et `RoleManager` pour gérer les permissions.

**Permissions CRUD standard :**
- `read` : Lecture (généralement public)
- `create` : Création
- `update` : Modification de ses propres entités
- `updateAny` : Modification de toutes les entités
- `delete` : Suppression de ses propres entités
- `deleteAny` : Suppression de toutes les entités

**Utilisation dans les descriptors :**
- `visibleIf` : Fonction conditionnelle pour la visibilité d'un champ
- `editableIf` : Fonction conditionnelle pour l'édition d'un champ
- Les permissions sont passées via le contexte (`ctx.capabilities` ou `ctx.meta.capabilities`)

**Exemple :**
```javascript
visibleIf: () => canUpdateAny, // Affiche le champ uniquement si l'utilisateur peut modifier n'importe quelle entité
```

## Récapitulatif des fonctionnalités

### ✅ Fonctionnalités existantes (à conserver)

1. **Tableau (TanStackTable)**
   - Tri des colonnes
   - Filtres par colonne (boolean, text, select, multi)
   - Recherche globale
   - Visibilité des colonnes (localStorage)
   - Pagination
   - Sélection multiple
   - Layout full-width

2. **Formats d'affichage**
   - Large (EntityViewLarge)
   - Compact (EntityViewCompact)
   - Minimal (EntityViewMinimal)
   - Text (EntityViewText)

3. **Quickedit**
   - Panneau latéral (EntityQuickEditPanel)
   - Modal (EntityQuickEditModal)
   - Configuration via descriptors

4. **Descriptors**
   - Structure complète pour `resource` (exemple)
   - Configuration d'affichage (display)
   - Configuration d'édition (edit.form)
   - Configuration bulk (edit.form.bulk)

5. **Actions**
   - EntityActions component
   - Actions contextuelles selon le format d'affichage

6. **Permissions**
   - Système de permissions via `usePermissions`
   - Gestion des permissions dans les descriptors

### 🔴 À refaire complètement

1. **Système de descriptors**
   - **Problème actuel** : Trop complexe, mal comportementé, peu scalable
   - **Solution** : Refonte complète avec une structure simplifiée
   - **Focus** : Uniquement tableau et formulaires (pas de configuration des vues Large/Compact)
   - **Vues manuelles** : Les vues Large et Compact seront créées manuellement pour chaque entité

2. **Vues Large et Compact**
   - **Problème actuel** : Génération automatique à partir de listes de champs, peu flexible
   - **Solution** : Créer des composants Vue manuels pour chaque entité
   - **Avantage** : Contrôle total sur le layout et l'affichage

3. **Modèles d'entités (approche orientée objet)**
   - **Problème actuel** : Logique de formatage dispersée dans les adapters (`buildResourceCell`, etc.)
   - **Solution** : Déplacer toute la logique de formatage dans les modèles
   - **Méthodes à ajouter** :
     - `toCell(fieldKey, options)` : Génère une cellule pour le tableau
     - `toBadge(fieldKey)` : Génère un badge configuré
     - `toIcon(fieldKey)` : Génère une icône configurée
     - `formatRarity()`, `formatLevel()`, etc. : Formatage spécifique par champ
   - **Avantage** : Centralisation, réutilisabilité, maintenabilité
   - **Flux** : Backend → Données brutes → Entity Model → Formatage → Composants Vue

4. **Système de Formatters centralisés**
   - **Problème actuel** : Propriétés communes (rareté, niveau, etc.) dupliquées dans plusieurs fichiers
   - **Solution** : Créer un système de formatters réutilisables (`RarityFormatter`, `LevelFormatter`, etc.)
   - **Structure** : `Utils/Formatters/` avec des classes statiques pour chaque propriété commune
   - **Avantage** : Une seule source de vérité, réutilisable partout, facile à maintenir
   - **Utilisation** : Les modèles utilisent ces formatters pour générer les cellules

### 🔄 À améliorer/refactoriser

1. **Optimisation du quickedit**
   - Améliorer la génération automatique du formulaire
   - Standardiser les groupes de champs

2. **Documentation des actions**
   - Documenter toutes les actions disponibles
   - Standardiser les actions par format d'affichage

3. **Système de permissions**
   - Améliorer la gestion des permissions dans les descriptors
   - Standardiser les fonctions `visibleIf` et `editableIf`

4. **Utilitaires de formatage**
   - ✅ **Existe déjà** : `EntityDescriptorHelpers.js` (truncate, formatRarity, formatVisibility, etc.)
   - ✅ **Existe déjà** : `EntityDescriptorConstants.js` (RARITY_OPTIONS, BREAKPOINTS, etc.)
   - ⚠️ **À refactoriser** : Utiliser les formatters centralisés au lieu des fonctions locales
   - Faciliter leur réutilisation dans les vues manuelles

5. **Système de cache**
   - ✅ **Existe déjà** : `descriptor-cache.js` (TTL 5 minutes, invalidation automatique)
   - Cache les descriptors pour éviter de recalculer à chaque fois
   - Invalidation basée sur le hash du contexte (capabilities, etc.)

6. **Système de routes**
   - ✅ **Existe déjà** : `entityRouteRegistry.js`
   - Gestion centralisée des routes pour les entités
   - Utilisé par EntityActions pour générer les URLs

7. **Composants génériques**
   - ✅ **EntityEditForm** : Formulaire d'édition générique basé sur fieldsConfig
   - ✅ **EntityRelationsManager** : Gestion des relations many-to-many avec pivots
   - ✅ **CreateEntityModal** : Modal de création d'entité (utilise EntityEditForm)
   - Ces composants utilisent les descriptors pour générer les formulaires automatiquement

### Fonctionnalités de création et édition

**✅ CreateEntityModal** (`Pages/Organismes/entity/CreateEntityModal.vue`)
- Modal de création d'entité
- Utilise `EntityEditForm` avec `fieldsConfig` généré depuis les descriptors
- Utilise `createDefaultEntityFromDescriptors()` pour les valeurs par défaut
- **À conserver** : Composant fonctionnel

**✅ EntityEditForm** (`Pages/Organismes/entity/EntityEditForm.vue`)
- Formulaire d'édition générique
- Deux modes : `large` et `compact`
- Génération automatique depuis `fieldsConfig`
- Validation intégrée avec notifications
- Support de tous les types de champs (text, textarea, select, file, number, etc.)
- Gestion des images avec prévisualisation
- **À conserver** : Composant fonctionnel

**✅ EntityRelationsManager** (`Pages/Organismes/entity/EntityRelationsManager.vue`)
- Gestion générique des relations many-to-many
- Support des relations simples (sans pivot)
- Support des relations avec pivot (`quantity`, `price`, `comment`)
- Recherche et ajout dynamique d'entités
- Sauvegarde avec gestion des pivots
- Affichage des relations existantes avec possibilité de suppression
- **À conserver** : Composant fonctionnel

---

## 🔍 Validation et analyse du système refactorisé

### Clarification des points — Solutions basées sur l'existant

#### 1. Flux de données Backend → Frontend

**✅ État actuel analysé :**

**Ce qui existe :**
- `adaptResourceEntitiesTableResponse` transforme `{ meta, entities }` → `{ meta, rows }`
- Chaque `row` contient :
  - `id` : ID de l'entité
  - `cells` : **TOUTES les cellules pré-générées** (image, name, level, rarity, etc.)
  - `rowParams.entity` : **Entité brute** (pour quickedit/modals)
- Les modèles sont créés **uniquement dans les pages Index** pour les modals/quickedit :
  ```javascript
  const model = Resource.fromArray([raw])[0]; // Dans handleRowDoubleClick
  ```
- `EntityTanStackTable` utilise `responseAdapter` pour transformer la réponse backend

**Dépendances identifiées :**
- ✅ `rowParams.entity` est utilisé pour quickedit (`selectedEntities` utilise `rowParams.entity`)
- ✅ Les cellules sont pré-générées dans l'adapter (pas de génération à la volée)
- ✅ Les modèles sont créés à la demande dans les pages (pas dans l'adapter)

**✅ Solution proposée (basée sur l'existant) :**

**Option A : Adapter simplifié qui crée les modèles (RECOMMANDÉ)**
```javascript
// resource-adapter.js (nouveau)
export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];
  
  // ✅ Créer les modèles dans l'adapter
  const resourceModels = Resource.fromArray(entities);
  
  const rows = resourceModels.map((resource) => {
    return {
      id: resource.id,
      // ❌ NE PAS pré-générer les cellules ici
      // Les cellules seront générées à la volée dans le tableau
      cells: {}, // Sera rempli par le tableau via resource.toCell()
      rowParams: { 
        entity: resource, // ✅ Passer le modèle au lieu de l'entité brute
        rawEntity: resource.toRaw() // ✅ Garder les données brutes pour compatibilité
      }
    };
  });
  
  return { meta, rows };
}
```

**Option B : Génération des cellules dans le composant tableau**
```javascript
// Dans EntityTanStackTable.vue ou TanStackTable.vue
const rowsWithCells = computed(() => {
  return activeRows.value.map(row => {
    const entity = row.rowParams?.entity; // Modèle Resource
    if (!entity || typeof entity.toCell !== 'function') {
      return row; // Fallback si pas de modèle
    }
    
    // Générer les cellules à la volée pour les colonnes visibles
    const cells = {};
    resolvedConfig.value.columns.forEach(col => {
      if (col.id && col.id !== 'actions') {
        cells[col.id] = entity.toCell(col.id, {
          context: 'table',
          size: tableSize.value,
          config: descriptorConfig
        });
      }
    });
    
    return { ...row, cells };
  });
});
```

**✅ Décision : Option A (Adapter simplifié)**
- **Avantage** : Les modèles sont créés une seule fois
- **Avantage** : Compatible avec `rowParams.entity` existant (on passe le modèle)
- **Avantage** : Les cellules sont générées à la demande (meilleure performance)
- **Migration** : Facile, on garde la même structure de `row`

**⚠️ Points d'attention :**
- Vérifier que `selectedEntities` fonctionne avec les modèles (déjà le cas : `Resource.fromArray([raw])`)
- Vérifier que quickedit fonctionne avec les modèles (déjà le cas : utilise `toRaw()` ou `toFormData()`)

#### 2. Intégration Modèles ↔ Descriptors

**✅ État actuel analysé :**

**Ce qui existe :**
- `buildResourceCell(colId, entity, ctx, opts)` utilise :
  - `getResourceFieldDescriptors(ctx)` pour récupérer la config
  - `resolveViewConfigFor(descriptor, { view })` pour déterminer la taille
  - `display.views` et `display.sizes` dans le descriptor pour le format
- Le descriptor détermine le format via `display.views[context].mode` et `display.sizes[size].mode`
- Le contexte (`ctx`) contient `{ meta, capabilities }`

**Dépendances identifiées :**
- ✅ Le descriptor est la source de vérité pour le format d'affichage
- ✅ Le contexte (`ctx`) est nécessaire pour les permissions (`visibleIf`, `editableIf`)
- ✅ La taille du tableau (`xs-xl`) n'est pas calculée actuellement (utilise `small/normal/large`)

**✅ Solution proposée (basée sur l'existant) :**

**Le modèle a besoin du descriptor pour :**
1. Déterminer le format selon la taille (`display.sizes[size].mode`)
2. Gérer les permissions (`visibleIf`)
3. Obtenir les options de filtres (`meta.filterOptions`)

**Architecture proposée :**
```javascript
// Dans Resource.js
toCell(fieldKey, options = {}) {
  const { 
    context = 'table', 
    size = 'normal', // xs, sm, md, lg, xl (à calculer dans le tableau)
    config = {}, // Descriptor config pour ce champ
    ctx = {} // Contexte avec meta, capabilities
  } = options;
  
  // 1. Récupérer le descriptor pour ce champ
  const descriptor = config[fieldKey] || {};
  
  // 2. Déterminer le format selon la taille (comme buildResourceCell actuellement)
  const viewCfg = this._resolveViewConfig(descriptor, context);
  const sizeCfg = descriptor?.display?.sizes?.[size] || {};
  const mode = viewCfg?.mode || sizeCfg?.mode || null;
  
  // 3. Utiliser les formatters pour les propriétés communes
  switch (fieldKey) {
    case 'rarity':
      return this.toRarityCell({ mode, ctx });
    case 'level':
      return this.toLevelCell({ mode, ctx });
    // ...
    default:
      return this._toDefaultCell(fieldKey, { mode, descriptor, ctx });
  }
}

// Dans le composant tableau
const tableSize = computed(() => {
  // Calculer xs, sm, md, lg, xl selon la largeur disponible
  return calculateTableSize(); // À implémenter
});

const descriptorConfig = computed(() => {
  return getResourceFieldDescriptors({ 
    meta: serverMeta.value,
    capabilities: serverMeta.value?.capabilities 
  });
});

// Pour chaque colonne
const cell = entity.toCell(colId, {
  context: 'table',
  size: tableSize.value, // xs, sm, md, lg, xl
  config: descriptorConfig.value,
  ctx: { meta: serverMeta.value }
});
```

**✅ Décision : Modèle + Descriptor (Option B)**
- **Raison** : Le descriptor reste la source de vérité pour le format
- **Raison** : Compatible avec l'existant (`display.views`, `display.sizes`)
- **Raison** : Permet de gérer les permissions et les options de filtres

#### 3. Vues Minimal et Text

**✅ État actuel analysé :**

**Ce qui existe :**
- `EntityViewMinimal.vue` : Composant générique qui génère automatiquement la vue
- `EntityViewText.vue` : Composant générique qui génère automatiquement la vue
- Les deux utilisent `entityConfig.buildCell()` pour générer les cellules
- `EntityViewMinimal` utilise `minimalImportantFields` depuis `entityConfig.defaults`
- `EntityViewText` utilise `buildCell('name')` pour le nom

**Dépendances identifiées :**
- ✅ `EntityViewMinimal` itère sur `minimalFields` et génère les cellules automatiquement
- ✅ `EntityViewText` est très simple (nom + icône)
- ✅ Les deux sont utilisés dans plusieurs endroits (modals, tooltips, etc.)

**✅ Solution proposée (basée sur l'existant) :**

**Minimal et Text : DEVIENNENT MANUELLES (comme Large et Compact)**

**Raison :**
- Cohérence avec Large et Compact (toutes les vues sont manuelles)
- Contrôle total sur l'affichage pour chaque entité
- Flexibilité pour personnaliser chaque vue selon les besoins spécifiques

**Structure proposée :**
```
Entities/
├── resource/
│   ├── views/
│   │   ├── ResourceViewLarge.vue   # Vue manuelle
│   │   ├── ResourceViewCompact.vue # Vue manuelle
│   │   ├── ResourceViewMinimal.vue # Vue manuelle (NOUVEAU)
│   │   └── ResourceViewText.vue    # Vue manuelle (NOUVEAU)
```

**Migration :**
- Créer `ResourceViewMinimal.vue` et `ResourceViewText.vue` manuellement
- Utiliser les méthodes du modèle (`entity.toCell()`, `entity.formatRarity()`, etc.)
- Supprimer les composants génériques `EntityViewMinimal` et `EntityViewText` (ou les garder comme fallback)

**✅ Décision : Minimal et Text deviennent manuelles**
- **Avantage** : Cohérence avec Large et Compact
- **Avantage** : Contrôle total sur l'affichage
- **Avantage** : Personnalisation par entité possible
- **Note** : Plus de code à maintenir, mais plus de flexibilité

#### 4. Entity Registry et Adapters

**✅ État actuel analysé :**

**Ce qui existe :**
- `entity-registry.js` expose :
  - `getDescriptors` : Fonction pour récupérer les descriptors
  - `buildCell` : Fonction pour générer une cellule (utilisée par Minimal/Text)
  - `responseAdapter` : Fonction pour adapter la réponse backend
  - `viewFields` : Listes de champs par vue (compact, extended, quickEdit)
  - `defaults` : Valeurs par défaut (minimalImportantFields, etc.)

**Dépendances identifiées :**
- ✅ `EntityViewMinimal` utilise `entityConfig.buildCell()`
- ✅ `EntityViewText` utilise `entityConfig.buildCell()`
- ✅ Les pages Index utilisent `responseAdapter` via `EntityTanStackTable`
- ✅ `viewFields` est utilisé pour les vues générées (à supprimer pour Large/Compact)

**✅ Solution proposée (basée sur l'existant) :**

**Entity Registry : Évolution progressive**

**Phase 1 : Ajouter les modèles sans casser l'existant**
```javascript
// entity-registry.js (évolution)
export function getEntityConfig(entityType) {
  const key = normalizeEntityType(entityType);
  switch (key) {
    case "resources":
      return {
        key,
        // ✅ Existant (à garder pour compatibilité)
        getDescriptors: getResourceFieldDescriptors,
        buildCell: buildResourceCell, // ⚠️ Déprécié mais gardé pour Minimal/Text
        responseAdapter: adaptResourceEntitiesTableResponse,
        viewFields: RESOURCE_VIEW_FIELDS, // ⚠️ Déprécié (sauf quickEdit)
        defaults: { minimalImportantFields: ["level", "resource_type", "rarity"] },
        
        // ✅ Nouveau
        Model: Resource, // Classe du modèle
        getTableConfig: () => ResourceTableConfig, // Nouveau
        getFormConfig: () => ResourceFormConfig, // Nouveau
        getBulkConfig: () => ResourceBulkConfig, // Nouveau
      };
    // ...
  }
}
```

**Phase 2 : Migration progressive**
- Les composants Minimal/Text continuent d'utiliser `buildCell` (compatibilité)
- Les nouveaux composants utilisent `Model` et `model.toCell()`
- `buildCell` devient un wrapper qui appelle `model.toCell()` :
  ```javascript
  // buildResourceCell (wrapper temporaire)
  export function buildResourceCell(colId, entity, ctx = {}, opts = {}) {
    // Si entity est déjà un modèle, utiliser directement
    if (entity instanceof Resource) {
      return entity.toCell(colId, { ...opts, ctx, config: getResourceFieldDescriptors(ctx) });
    }
    // Sinon, créer le modèle
    const resource = new Resource(entity);
    return resource.toCell(colId, { ...opts, ctx, config: getResourceFieldDescriptors(ctx) });
  }
  ```

**✅ Décision : Évolution progressive de l'Entity Registry**
- **Avantage** : Pas de breaking changes
- **Avantage** : Migration entité par entité possible
- **Avantage** : Compatibilité avec l'existant (Minimal/Text)

#### 5. Formatage conditionnel selon la taille

**✅ État actuel analysé :**

**Ce qui existe :**
- Le descriptor utilise `display.sizes` avec `small/normal/large` (pas xs-xl)
- `buildResourceCell` utilise `sizeToTruncateScale(size)` pour la troncature
- La taille est déterminée par le contexte (`opts.size` ou `viewCfg.size`)
- Pas de calcul automatique de la taille du tableau actuellement
- **⚠️ Le projet utilise xs, sm, md, lg, xl partout (Tailwind CSS)**

**Dépendances identifiées :**
- ✅ `display.sizes` existe dans les descriptors (small/normal/large) - **À REFACTORISER**
- ✅ `sizeToTruncateScale` existe pour la troncature - **À ADAPTER**
- ✅ La taille est passée via `opts.size` dans `buildResourceCell`

**✅ Solution proposée (basée sur l'existant) :**

**Refactoriser pour utiliser xs, sm, md, lg, xl (cohérence avec le projet)**

**1. Mettre à jour les descriptors pour utiliser xs-xl**
```javascript
// resource-descriptors.js (nouveau)
export const DEFAULT_RESOURCE_FIELD_VIEWS = Object.freeze({
  table: { size: "sm" },    // Au lieu de "small"
  text: { size: "md" },    // Au lieu de "normal"
  compact: { size: "sm" }, // Au lieu de "small"
  minimal: { size: "sm" }, // Au lieu de "small"
  extended: { size: "lg" }, // Au lieu de "large"
});

// Dans le descriptor
display: {
  sizes: {
    xs: { mode: "icon" },      // Au lieu de "small"
    sm: { mode: "badge" },     // Au lieu de "small"
    md: { mode: "badge" },     // Au lieu de "normal"
    lg: { mode: "text" },      // Au lieu de "large"
    xl: { mode: "text" },      // Nouveau
  }
}
```

**2. Calculer la taille du tableau dans le composant (xs-xl)**
```javascript
// Dans EntityTanStackTable.vue ou TanStackTable.vue
const tableSize = computed(() => {
  // Calculer selon la largeur disponible (breakpoints Tailwind)
  // xs: < 640px, sm: 640px, md: 768px, lg: 1024px, xl: 1280px
  const width = tableRef.value?.offsetWidth || 0;
  if (width < 640) return 'xs';
  if (width < 768) return 'sm';
  if (width < 1024) return 'md';
  if (width < 1280) return 'lg';
  return 'xl';
});
```

**3. Le modèle utilise directement xs-xl (pas de conversion)**
```javascript
// Dans Resource.js
toCell(fieldKey, options = {}) {
  const { size = 'md', config = {}, ctx = {} } = options; // xs, sm, md, lg, xl
  
  const descriptor = config[fieldKey] || {};
  const sizeCfg = descriptor?.display?.sizes?.[size] || {}; // Utilise directement xs-xl
  const mode = sizeCfg?.mode || null;
  
  // Utiliser le mode pour formater
  // ...
}
```

**4. Adapter sizeToTruncateScale pour xs-xl**
```javascript
// Utils/entity/text-truncate.js (modifié)
export function sizeToTruncateScale(size) {
  // size est maintenant xs, sm, md, lg, xl
  const scaleMap = {
    xs: 0.5,  // Très petit
    sm: 0.75, // Petit
    md: 1,    // Normal
    lg: 1.25, // Grand
    xl: 1.5,  // Très grand
  };
  return scaleMap[size] || 1;
}
```

**✅ Décision : Refactoriser pour utiliser xs-xl**
- **Avantage** : Cohérence avec le projet (Tailwind CSS)
- **Avantage** : Plus de granularité (5 tailles au lieu de 3)
- **Avantage** : Aligné avec les breakpoints Tailwind
- **Note** : Nécessite de refactoriser les descriptors existants (migration progressive)

#### 6. Relations entre entités

**✅ État actuel analysé :**

**Ce qui existe :**
- Les relations sont des objets bruts dans les données backend
- `buildResourceCell('resource_type')` utilise `entity?.resourceType?.name`
- `buildResourceCell('created_by')` utilise `entity?.createdBy?.name || entity?.createdBy?.email`
- Les relations ne sont pas transformées en modèles actuellement

**Dépendances identifiées :**
- ✅ Les relations sont accessibles via `entity.relationName`
- ✅ Le formatage utilise directement les propriétés de la relation
- ✅ Pas de transformation en modèles pour les relations

**✅ Solution proposée (basée sur l'existant) :**

**Relations : Rester en objets bruts (pour l'instant)**

**Raison :**
- Les relations sont souvent partielles (juste `id` et `name`)
- Transformer toutes les relations en modèles serait coûteux
- Le formatage actuel fonctionne bien avec les objets bruts

**Architecture proposée :**
```javascript
// Dans Resource.js
get resourceType() {
  // Retourner l'objet brut (comme actuellement)
  return this._data.resourceType || null;
}

toResourceTypeCell(options = {}) {
  const resourceType = this.resourceType;
  if (!resourceType) {
    return {
      type: 'text',
      value: '-',
      params: {}
    };
  }
  
  const { mode = null, ctx = {} } = options;
  const typeName = resourceType.name || '-';
  const typeId = resourceType.id || this._data.resource_type_id;
  
  // Utiliser le mode du descriptor pour déterminer le format
  if (mode === 'badge') {
    return {
      type: 'badge',
      value: typeName,
      params: {
        color: 'neutral',
        tooltip: typeName,
        filterValue: typeId ? String(typeId) : '',
        sortValue: typeName,
        searchValue: typeName === '-' ? '' : typeName,
      }
    };
  }
  
  return {
    type: 'text',
    value: typeName,
    params: {
      tooltip: typeName,
      filterValue: typeId ? String(typeId) : '',
      sortValue: typeName,
      searchValue: typeName === '-' ? '' : typeName,
    }
  };
}
```

**✅ Décision : Relations en objets bruts**
- **Avantage** : Performance (pas de transformation inutile)
- **Avantage** : Compatible avec l'existant
- **Avantage** : Simple à maintenir
- **Note** : Si besoin futur, on pourra transformer certaines relations en modèles (lazy)

#### 7. Compatibilité avec l'existant

**✅ État actuel analysé :**

**Ce qui existe :**
- 15+ entités avec leurs adapters (`buildResourceCell`, `buildItemCell`, etc.)
- Toutes les entités utilisent le même pattern
- Les pages Index utilisent `responseAdapter` via `EntityTanStackTable`
- Les vues Minimal/Text utilisent `buildCell` via `entity-registry`

**Dépendances identifiées :**
- ✅ Toutes les entités suivent le même pattern (adapter + buildCell)
- ✅ `EntityTanStackTable` est générique (fonctionne avec n'importe quel adapter)
- ✅ Les vues Minimal/Text sont génériques (fonctionnent avec n'importe quel buildCell)

**✅ Solution proposée (basée sur l'existant) :**

**Migration progressive entité par entité**

**Stratégie :**

**1. Créer un wrapper de compatibilité**
```javascript
// buildResourceCell (wrapper temporaire)
export function buildResourceCell(colId, entity, ctx = {}, opts = {}) {
  // Si entity est déjà un modèle Resource, utiliser directement
  if (entity instanceof Resource) {
    const config = getResourceFieldDescriptors(ctx);
    return entity.toCell(colId, {
      ...opts,
      ctx,
      config
    });
  }
  
  // Sinon, créer le modèle et utiliser toCell()
  const resource = new Resource(entity);
  const config = getResourceFieldDescriptors(ctx);
  return resource.toCell(colId, {
    ...opts,
    ctx,
    config
  });
}
```

**2. Adapter simplifié qui crée les modèles**
```javascript
// adaptResourceEntitiesTableResponse (nouveau)
export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];
  
  // Créer les modèles
  const resourceModels = Resource.fromArray(entities);
  
  const rows = resourceModels.map((resource) => {
    return {
      id: resource.id,
      cells: {}, // Sera généré par le tableau ou pré-généré si besoin
      rowParams: { 
        entity: resource, // Modèle
        rawEntity: resource.toRaw() // Pour compatibilité
      }
    };
  });
  
  return { meta, rows };
}
```

**3. Période de transition**
- Les entités migrées utilisent le nouveau système (modèles + toCell)
- Les entités non migrées continuent d'utiliser l'ancien système (buildCell)
- Les deux systèmes coexistent via les wrappers de compatibilité

**✅ Décision : Migration progressive**
- **Avantage** : Pas de breaking changes
- **Avantage** : Testable entité par entité
- **Avantage** : Rollback possible si problème
- **Ordre suggéré** : Resource → Item → Consumable → Autres

### Optimisations possibles

#### 1. Simplification de l'Entity Registry

**Problème actuel :**
- L'entity-registry expose `buildCell` qui sera remplacé par `model.toCell()`
- Duplication entre adapter et modèle

**Optimisation :**
```javascript
// Simplifier l'entity-registry
export function getEntityConfig(entityType) {
  const Model = getEntityModel(entityType); // Resource, Item, etc.
  const Descriptor = getEntityDescriptor(entityType); // ResourceDescriptor, etc.
  
  return {
    key: entityType,
    Model, // Classe du modèle
    Descriptor, // Instance du descriptor
    // Plus besoin de buildCell, responseAdapter simplifié
  };
}
```

#### 2. Unification des méthodes de formatage

**Problème actuel :**
- Les modèles auront `toCell()`, `toBadge()`, `formatRarity()`, etc.
- Risque de duplication si chaque modèle implémente tout

**Optimisation :**
- Utiliser `BaseModel` avec méthodes communes (comme proposé)
- Utiliser `FormatterRegistry` pour éviter les switch/case dans chaque modèle
- Méthode générique `toCell()` dans `BaseModel` qui utilise le registre

#### 3. Cache des cellules générées

**Optimisation possible :**
- Mettre en cache les cellules générées pour éviter de recalculer
- Invalider le cache si les données changent
- Utile pour les tableaux avec beaucoup de lignes

#### 4. Lazy loading des formatters

**Optimisation possible :**
- Charger les formatters uniquement quand nécessaire
- Réduire le bundle initial

### Incohérences identifiées

#### 1. Formatage dans les exemples

**Incohérence :**
- L'exemple montre `formatRarity()` qui utilise un `rarityMap` local
- Mais on propose aussi `RarityFormatter` centralisé
- Les deux approches sont mélangées

**À clarifier :**
- Utiliser uniquement les formatters centralisés
- Les modèles appellent les formatters, pas de logique locale

#### 2. Structure des vues

**Incohérence :**
- Document dit : vues Large/Compact manuelles
- Mais l'exemple montre `ResourceViewCompact.vue` qui utilise encore `entity.rarity` directement
- Pas clair comment utiliser les méthodes du modèle dans les vues

**À clarifier :**
```vue
<!-- ResourceViewCompact.vue -->
<template>
  <div class="resource-compact">
    <!-- Comment utiliser les méthodes du modèle ? -->
    <Badge :value="entity.formatRarity().label" :color="entity.formatRarity().color" />
    <!-- Ou -->
    <Badge v-bind="entity.toBadge('rarity')" />
  </div>
</template>
```

#### 3. Responsabilités floues

**Incohérence :**
- Le descriptor doit "gérer les utilitaires de formatage"
- Mais les formatters sont dans `Utils/Formatters/`
- Et les modèles ont aussi des méthodes de formatage

**À clarifier :**
- **Formatters** : Logique pure de conversion (1 → "Commun")
- **Modèles** : Utilisent les formatters pour générer les cellules
- **Descriptors** : Configuration uniquement (pas de logique de formatage)

### Zones d'ombres à documenter

#### 1. Gestion des erreurs

**Non documenté :**
- Que se passe-t-il si un formatter n'existe pas ?
- Que se passe-t-il si une valeur est invalide ?
- Comment gérer les valeurs null/undefined ?

#### 2. Performance

**Non documenté :**
- Impact sur les performances de générer les cellules dans les modèles
- Faut-il mettre en cache ?
- Comment optimiser pour les gros tableaux (1000+ lignes) ?

#### 3. Tests

**Non documenté :**
- Comment tester les modèles avec `toCell()` ?
- Comment tester les formatters ?
- Comment tester l'intégration modèles + descriptors ?

#### 4. Migration

**Non documenté :**
- Plan de migration détaillé
- Ordre de migration des entités
- Comment gérer la période de transition

#### 5. Relations et relations imbriquées

**Non documenté :**
- Comment gérer `resource.resourceType.name` dans une cellule ?
- Faut-il transformer les relations en modèles aussi ?
- Comment gérer les relations nullables ?

#### 6. Édition dans les vues

**Non documenté :**
- Comment les vues Large/Compact éditable fonctionnent-elles avec les modèles ?
- Les modèles doivent-ils avoir des méthodes `toFormField()` ?
- Comment valider les données avant envoi au backend ?

### Questions de validation

**Avant de commencer la refactorisation, valider :**

1. ✅ **Flux de données** : Backend → Adapter simplifié → Modèles → Cellules ?
2. ✅ **Formatters** : Centralisés dans `Utils/Formatters/` et utilisés par les modèles ?
3. ✅ **BaseModel** : Contient les méthodes communes (hasRarity, formatRarity, toRarityCell) ?
4. ✅ **Vues Large/Compact** : Manuelles, utilisent les méthodes du modèle ?
5. ✅ **Vues Minimal/Text** : Générées ou manuelles ?
6. ✅ **Descriptors** : Configuration uniquement (tableau + formulaires) ?
7. ✅ **Entity Registry** : Expose les modèles et descriptors, plus les adapters ?
8. ✅ **Migration** : Progressive, entité par entité ?

### Recommandations

1. **Commencer par un POC** : Implémenter le nouveau système pour `Resource` uniquement
2. **Valider le flux** : Tester le flux complet Backend → Modèle → Tableau
3. **Documenter les patterns** : Créer des exemples concrets pour chaque cas d'usage
4. **Migrer progressivement** : Une entité à la fois, en gardant l'ancien système pour les autres
5. **Tests** : Écrire les tests avant de migrer pour valider le comportement

### Architecture détaillée du nouveau flux

#### Flux actuel (à remplacer)

```
Backend Response
  ↓
adaptResourceEntitiesTableResponse()
  ├── Transforme { meta, entities } → { meta, rows }
  └── Pour chaque entity :
      └── buildResourceCell(colId, entity) → Cell
          └── Utilise resource-descriptors.js
              └── Logique de formatage dispersée
```

#### Nouveau flux proposé

```
Backend Response { meta, entities: rawData[] }
  ↓
responseAdapter simplifié
  ├── Transforme entities en modèles : entities.map(raw => new Resource(raw))
  └── Retourne { meta, entities: Resource[] }
  ↓
Composant Tableau
  ├── Pour chaque colonne configurée :
  │   └── entity.toCell(fieldKey, { size, config })
  │       ├── Utilise FormatterRegistry ou méthodes BaseModel
  │       └── Retourne Cell { type, value, params }
  └── Génère les rows avec les cells
```

**Avantages :**
- ✅ Les modèles sont créés une seule fois
- ✅ Les cellules sont générées à la demande
- ✅ Pas de duplication de logique

#### Points d'attention

**1. Performance :**
- Générer les cellules à la volée peut être coûteux pour les gros tableaux
- **Solution** : Mettre en cache les cellules générées dans le modèle
- **Alternative** : Pré-générer les cellules dans l'adapter (mais perd l'avantage du modèle)

**2. Mémoire :**
- Créer des modèles pour toutes les entités peut consommer de la mémoire
- **Solution** : Lazy loading ou création à la demande

**3. Compatibilité :**
- Les composants existants utilisent `buildCell(entity, colId)`
- **Solution** : Créer un wrapper temporaire qui appelle `entity.toCell(colId)`

### Plan de migration proposé

#### Phase 1 : Préparation (sans casser l'existant)

1. **Créer les formatters centralisés**
   - `RarityFormatter`, `LevelFormatter`, `VisibilityFormatter`, etc.
   - Tests unitaires pour chaque formatter

2. **Enrichir BaseModel**
   - Ajouter les méthodes communes (`hasRarity`, `formatRarity`, `toRarityCell`, etc.)
   - Utiliser les formatters centralisés

3. **Créer un modèle de référence (Resource)**
   - Implémenter `toCell()` complet pour Resource
   - Tester avec un tableau de test

#### Phase 2 : Migration Resource (POC)

1. **Créer ResourceViewLarge.vue et ResourceViewCompact.vue**
   - Vues manuelles utilisant les méthodes du modèle
   - Tester l'affichage

2. **Simplifier ResourceDescriptor**
   - Retirer la config des vues Large/Compact
   - Garder uniquement TableConfig et FormConfig

3. **Adapter le tableau Resource**
   - Utiliser `resource.toCell()` au lieu de `buildResourceCell()`
   - Tester le tableau complet

4. **Valider le POC**
   - Tester toutes les fonctionnalités (tri, filtres, recherche, quickedit)
   - Comparer les performances avec l'ancien système

#### Phase 3 : Migration progressive

1. **Migrer les entités prioritaires** (Item, Consumable)
   - Même pattern que Resource
   - Réutiliser les formatters créés

2. **Migrer les autres entités** une par une
   - En gardant l'ancien système pour les non migrées

3. **Nettoyer l'ancien système**
   - Supprimer les adapters obsolètes
   - Supprimer `buildCell` de l'entity-registry
   - Supprimer les listes `viewFields`

### Schéma d'intégration Modèle ↔ Descriptor ↔ Tableau

```
┌─────────────────────────────────────────────────────────────┐
│                    Composant Tableau                        │
│  - Calcule tableSize (xs-xl)                               │
│  - Récupère TableConfig depuis Descriptor                  │
│  - Pour chaque row : entity.toCell(fieldKey, options)      │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    Entity Model (Resource)                  │
│  - toCell(fieldKey, { size, config })                      │
│    ├── Utilise FormatterRegistry si disponible             │
│    ├── Sinon utilise méthodes BaseModel (formatRarity, etc.)│
│    └── Retourne Cell { type, value, params }               │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│              FormatterRegistry / Formatters                 │
│  - RarityFormatter.toCell(value, options)                  │
│  - LevelFormatter.toCell(value, options)                   │
│  - etc.                                                     │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│                    Descriptor (TableConfig)                 │
│  - Configuration des colonnes                              │
│  - Détermine le format selon la taille (xs-xl)             │
│  - Permissions et visibilité                                │
└─────────────────────────────────────────────────────────────┘
```

### Décisions à prendre

#### Décision 1 : Où transformer les données brutes en modèles ?

**Option A : Dans l'adapter (recommandé)**
```javascript
// resource-adapter.js (simplifié)
export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = (payload?.entities || []).map(raw => new Resource(raw));
  
  return {
    meta,
    rows: entities.map(entity => ({
      id: entity.id,
      cells: {}, // Sera généré dans le composant tableau
      rowParams: { entity } // Passe le modèle entier
    }))
  };
}
```

**Option B : Dans le composant tableau**
```javascript
// Dans EntityTanStackTable.vue
const rows = computed(() => {
  return (props.rows || []).map(raw => {
    const entity = new Resource(raw);
    return {
      id: entity.id,
      cells: generateCells(entity), // Génère toutes les cellules
      rowParams: { entity }
    };
  });
});
```

**Recommandation : Option A** (dans l'adapter simplifié)

#### Décision 2 : Comment le modèle accède-t-il à la config du descriptor ?

**Option A : Passer la config en paramètre**
```javascript
entity.toCell('rarity', { 
  config: descriptorConfig,
  size: tableSize 
})
```

**Option B : Le modèle récupère la config lui-même**
```javascript
// Dans Resource.js
toCell(fieldKey, options = {}) {
  const descriptor = ResourceDescriptor.getFieldDescriptor(fieldKey);
  // Utilise le descriptor pour déterminer le format
}
```

**Recommandation : Option A** (plus explicite, plus testable)

#### Décision 3 : FormatterRegistry ou méthodes BaseModel ?

**Option A : FormatterRegistry (plus flexible)**
- Permet d'ajouter des formatters sans modifier BaseModel
- Plus dynamique

**Option B : Méthodes BaseModel (plus simple)**
- Plus direct, moins d'abstraction
- Mais nécessite de modifier BaseModel pour chaque nouveau formatter

**Recommandation : FormatterRegistry** (meilleure scalabilité)

#### Décision 4 : Vues Minimal et Text

**Option A : Restent générées**
- Plus simple, moins de code à maintenir
- Utilisent les modèles pour le formatage

**Option B : Deviennent aussi manuelles**
- Cohérence avec Large/Compact
- Plus de contrôle

**Recommandation : Option A** (générées mais utilisent les modèles)

### Résumé des décisions prises

Après analyse du code existant, voici les décisions finales pour chaque point :

| Point | Décision | Raison |
|-------|----------|--------|
| **1. Flux de données** | Adapter simplifié qui crée les modèles | Compatible avec `rowParams.entity`, modèles créés une seule fois |
| **2. Modèles ↔ Descriptors** | Modèle + Descriptor (passer config) | Le descriptor reste la source de vérité pour le format |
| **3. Vues Minimal/Text** | Deviennent manuelles (comme Large/Compact) | Cohérence avec Large/Compact, contrôle total, personnalisation par entité |
| **4. Entity Registry** | Évolution progressive (ajout Model) | Pas de breaking changes, migration progressive possible |
| **5. Formatage par taille** | Refactoriser pour utiliser xs, sm, md, lg, xl | Cohérence avec le projet (Tailwind CSS), plus de granularité |
| **6. Relations** | Rester en objets bruts | Performance, compatible avec l'existant, simple |
| **7. Compatibilité** | Migration progressive entité par entité | Pas de breaking changes, testable, rollback possible |

### Checklist de validation finale

Avant de commencer la refactorisation, valider que :

- [x] **Flux de données** : ✅ Adapter simplifié → Modèles → Tableau (cellules générées à la volée)
- [x] **Formatters** : ✅ Tous les formatters prioritaires identifiés (16 formatters documentés)
- [ ] **BaseModel** : ⏳ Méthodes communes à implémenter (hasRarity, formatRarity, toRarityCell, etc.)
- [ ] **Modèles** : ⏳ Interface `toCell()` à implémenter (avec support descriptor + formatters)
- [x] **Descriptors** : ✅ Responsabilités définies (tableau + formulaires uniquement, pas de vues Large/Compact)
- [x] **Vues** : ✅ Toutes les vues manuelles (Large, Compact, Minimal, Text) - Cohérence totale
- [x] **Entity Registry** : ✅ Structure évolutive définie (ajout Model, buildCell devient wrapper)
- [x] **Migration** : ✅ Plan détaillé entité par entité (Resource → Item → Consumable → Autres)
- [ ] **Tests** : ⏳ Stratégie de tests à définir (formatters, modèles, intégration)
- [ ] **Performance** : ⏳ Stratégie de cache/memoization à définir (cellules générées à la volée)
- [x] **Compatibilité** : ✅ Période de transition gérée (wrappers de compatibilité, deux systèmes coexistent)

---

## 📦 Éléments existants à réutiliser

### Classes de configuration (déjà existantes)

**✅ TableConfig** (`Entities/entity/TableConfig.js`)
- Configuration du tableau (colonnes, features, quickEdit, actions)
- Méthodes : `withQuickEdit()`, `withActions()`, `addColumn()`, `withFeatures()`
- Gère les permissions et la visibilité responsive des colonnes
- **À utiliser** : Pour créer la configuration du tableau dans les descriptors

**✅ TableColumnConfig** (`Entities/entity/TableColumnConfig.js`)
- Configuration individuelle de chaque colonne
- Visibilité responsive (xs, sm, md, lg, xl) - **Déjà en xs-xl !**
- Permissions, tri, recherche, filtres
- **À utiliser** : Pour configurer chaque colonne du tableau

**✅ FormConfig** (`Entities/entity/FormConfig.js`)
- Configuration des formulaires d'édition
- Groupes de champs, validation
- **À utiliser** : Pour créer la configuration des formulaires dans les descriptors

**✅ FormFieldConfig** (`Entities/entity/FormFieldConfig.js`)
- Configuration individuelle de chaque champ de formulaire
- Type, validation, options, groupes
- **À utiliser** : Pour configurer chaque champ de formulaire

**✅ BulkConfig** (`Entities/entity/BulkConfig.js`)
- Configuration de l'édition en masse (quickedit)
- Champs bulk-editables, transformation, nullable
- Liste des champs quickEdit
- **À utiliser** : Pour créer la configuration du quickedit dans les descriptors

**✅ EntityDescriptor** (`Entities/entity/EntityDescriptor.js`)
- Classe de base avec constantes et fonctions communes
- Constantes : RARITY_OPTIONS, BREAKPOINTS, SCREEN_SIZES, etc.
- Fonctions : truncate, formatRarity, formatVisibility, formatDate, etc.
- **À adapter** : Utiliser les formatters centralisés au lieu des fonctions locales

### Utilitaires (déjà existants)

**✅ EntityDescriptorHelpers** (`Entities/entity/EntityDescriptorHelpers.js`)
- Fonctions de formatage communes :
  - `truncate()`, `capitalize()`
  - `formatRarity()`, `formatVisibility()`, `formatHostility()`
  - `formatDate()`, `formatNumber()`, `formatValue()`
  - `getCurrentScreenSize()`, `subtractSize()`, `addSize()`
  - `validateOption()`, `getOptionLabel()`
- **À adapter** : Utiliser les formatters centralisés au lieu de ces fonctions

**✅ EntityDescriptorConstants** (`Entities/entity/EntityDescriptorConstants.js`)
- Constantes centralisées :
  - `RARITY_OPTIONS`, `VISIBILITY_OPTIONS`, `HOSTILITY_OPTIONS`
  - `BREAKPOINTS` (xs, sm, md, lg, xl) - **Déjà en xs-xl !**
  - `SCREEN_SIZES` (xs, sm, md, lg, xl) - **Déjà en xs-xl !**
  - `CELL_TYPES`, `FORM_TYPES`, `DISPLAY_MODES`, `FIELD_FORMATS`
  - `RECOMMENDED_GROUPS`
- **À adapter** : Intégrer avec les formatters centralisés

**✅ descriptor-cache** (`Utils/entity/descriptor-cache.js`)
- Système de cache pour les descriptors (TTL 5 minutes)
- Invalidation automatique basée sur le hash du contexte
- Fonctions : `getCachedDescriptors()`, `invalidateDescriptorCache()`
- **À conserver** : Système de cache fonctionnel

**✅ descriptor-form** (`Utils/entity/descriptor-form.js`)
- Utilitaires pour générer les configurations de formulaires :
  - `createFieldsConfigFromDescriptors()` → fieldsConfig pour EntityEditForm
  - `createBulkFieldMetaFromDescriptors()` → fieldMeta pour useBulkEditPanel
  - `createDefaultEntityFromDescriptors()` → defaultEntity pour création
- **À conserver** : Utilitaires fonctionnels

**✅ entityRouteRegistry** (`Composables/entity/entityRouteRegistry.js`)
- Système de routes centralisé pour les entités
- Fonctions : `getEntityRouteConfig()`, `resolveEntityRouteUrl()`, `resolveEntityRouteHref()`
- Gère les exceptions de nommage (kebab-case, param keys, etc.)
- **À conserver** : Utilisé par EntityActions

### Composables (déjà existants)

**✅ useBulkEditPanel** (`Composables/entity/useBulkEditPanel.js`)
- Gestion complète du quickedit :
  - Agrégation des valeurs (valeur commune vs valeurs différentes)
  - État dirty (champs modifiés)
  - Construction du payload (avec fonctions build, nullable)
  - Modes : "server" (IDs sélectionnés) ou "client" (IDs filtrés)
- **À conserver** : Composable fonctionnel et complet

**✅ usePermissions** (`Composables/permissions/usePermissions.js`)
- Gestion des permissions CRUD
- Fonctions : `can()`, `canCreate()`, `canUpdate()`, `canUpdateAny()`, etc.
- **À conserver** : Système de permissions fonctionnel

### Composants (déjà existants)

**✅ EntityQuickEditPanel** (`Pages/Organismes/entity/EntityQuickEditPanel.vue`)
- Panneau latéral d'édition en masse
- Utilise `useBulkEditPanel` et les descriptors
- Génération automatique du formulaire
- **À conserver** : Composant fonctionnel

**✅ EntityQuickEditModal** (`Pages/Organismes/entity/EntityQuickEditModal.vue`)
- Modal d'édition en masse
- Utilise `useBulkEditPanel` et les descriptors
- **À conserver** : Composant fonctionnel

**✅ EntityEditForm** (`Pages/Organismes/entity/EntityEditForm.vue`)
- Formulaire d'édition générique basé sur fieldsConfig
- Deux modes : `large` et `compact`
- Validation intégrée
- **À conserver** : Composant fonctionnel

**✅ EntityRelationsManager** (`Pages/Organismes/entity/EntityRelationsManager.vue`)
- Gestion des relations many-to-many
- Support des pivots (quantity, price, comment)
- Recherche et ajout dynamique
- **À conserver** : Composant fonctionnel

**✅ EntityActions** (`Pages/Organismes/entity/EntityActions.vue`)
- Système d'actions pour les entités
- Formats : `buttons`, `dropdown`, `context`
- Affichage : `icon-only`, `icon-text`
- **À conserver** : Composant fonctionnel

### Résumé : Ce qui existe vs Ce qui manque

| Élément | Existe | Statut | Action |
|---------|--------|--------|--------|
| **TableConfig** | ✅ | Fonctionnel | Réutiliser tel quel |
| **TableColumnConfig** | ✅ | Fonctionnel (xs-xl) | Réutiliser tel quel |
| **FormConfig** | ✅ | Fonctionnel | Réutiliser tel quel |
| **FormFieldConfig** | ✅ | Fonctionnel | Réutiliser tel quel |
| **BulkConfig** | ✅ | Fonctionnel | Réutiliser tel quel |
| **EntityDescriptor** | ✅ | Fonctionnel | Adapter pour utiliser formatters |
| **EntityDescriptorHelpers** | ✅ | Fonctionnel | Adapter pour utiliser formatters |
| **EntityDescriptorConstants** | ✅ | Fonctionnel (xs-xl) | Adapter pour intégrer formatters |
| **descriptor-cache** | ✅ | Fonctionnel | Conserver tel quel |
| **descriptor-form** | ✅ | Fonctionnel | Conserver tel quel |
| **entityRouteRegistry** | ✅ | Fonctionnel | Conserver tel quel |
| **useBulkEditPanel** | ✅ | Fonctionnel | Conserver tel quel |
| **usePermissions** | ✅ | Fonctionnel | Conserver tel quel |
| **EntityQuickEditPanel** | ✅ | Fonctionnel | Conserver tel quel |
| **EntityQuickEditModal** | ✅ | Fonctionnel | Conserver tel quel |
| **EntityEditForm** | ✅ | Fonctionnel | Conserver tel quel |
| **EntityRelationsManager** | ✅ | Fonctionnel | Conserver tel quel |
| **EntityActions** | ✅ | Fonctionnel | Conserver tel quel |
| **Formatters centralisés** | ❌ | À créer | Créer `Utils/Formatters/` |
| **Méthodes toCell() dans modèles** | ❌ | À créer | Ajouter dans BaseModel et modèles |
| **Vues manuelles** | ❌ | À créer | Créer ResourceViewLarge, ResourceViewCompact, ResourceViewMinimal, ResourceViewText |
| **Adapter simplifié** | ❌ | À créer | Refactoriser adaptResourceEntitiesTableResponse |

---

## ✅ Vérification finale — Rien n'a été oublié

### Éléments existants documentés

**Classes de configuration :**
- ✅ TableConfig, TableColumnConfig, FormConfig, FormFieldConfig, BulkConfig
- ✅ EntityDescriptor (classe de base)

**Utilitaires :**
- ✅ EntityDescriptorHelpers, EntityDescriptorConstants
- ✅ descriptor-cache, descriptor-form
- ✅ entityRouteRegistry

**Composables :**
- ✅ useBulkEditPanel, usePermissions, useEntityActions

**Composants :**
- ✅ EntityQuickEditPanel, EntityQuickEditModal
- ✅ EntityEditForm, CreateEntityModal
- ✅ EntityRelationsManager
- ✅ EntityActions

**Fonctionnalités :**
- ✅ Système de tableau (TanStack Table) avec toutes les features
- ✅ Quickedit avec agrégation et dirty state
- ✅ Système d'actions avec permissions
- ✅ Système de permissions
- ✅ Formats d'affichage (Large, Compact, Minimal, Text)
- ✅ Création et édition d'entités
- ✅ Gestion des relations

### Éléments à créer documentés

**Formatters :**
- ⚠️ 16 formatters prioritaires identifiés (RarityFormatter, LevelFormatter, etc.)

**Modèles :**
- ⚠️ Méthodes `toCell()`, `toBadge()`, `formatRarity()`, etc. dans BaseModel et modèles

**Vues manuelles :**
- ⚠️ ResourceViewLarge.vue, ResourceViewCompact.vue, ResourceViewMinimal.vue, ResourceViewText.vue

**Adapters :**
- ⚠️ Refactorisation des adapters pour créer les modèles

### Points d'attention identifiés

1. **ViewConfig et QuickEditViewConfig** : Existent mais sont pour la génération automatique - À revoir pour les vues manuelles
2. **EntityDescriptorHelpers** : Utilise des fonctions locales - À adapter pour utiliser les formatters centralisés
3. **EntityDescriptorConstants** : Utilise small/normal/large - À adapter pour xs-xl (BREAKPOINTS et SCREEN_SIZES sont déjà en xs-xl)
4. **descriptors existants** : Utilisent small/normal/large - À migrer vers xs-xl progressivement
5. **buildCell dans entity-registry** : Devient un wrapper temporaire pendant la transition

### Conclusion

Le document est maintenant **complet** et couvre :
- ✅ Toutes les fonctionnalités existantes
- ✅ Tous les éléments à réutiliser
- ✅ Tous les éléments à créer
- ✅ Toutes les décisions prises
- ✅ Le plan de migration
- ✅ Les points d'attention

**Le système est prêt pour validation et implémentation.**

---

## 🔍 Analyse d'optimisation, DRY et structure

### Problèmes identifiés et solutions

#### 1. ❌ Duplication du pattern `hasX()`, `formatX()`, `toXCell()` dans BaseModel

**Problème :**
Le pattern se répète pour chaque propriété commune (rarity, level, visibility, etc.) :
```javascript
hasRarity() { return 'rarity' in this._data && ... }
formatRarity() { if (!this.hasRarity()) return null; return RarityFormatter.format(...) }
toRarityCell() { if (!this.hasRarity()) return null; return RarityFormatter.toCell(...) }

hasLevel() { return 'level' in this._data && ... }
formatLevel() { if (!this.hasLevel()) return null; return LevelFormatter.format(...) }
toLevelCell() { if (!this.hasLevel()) return null; return LevelFormatter.toCell(...) }
// ... répété pour chaque propriété
```

**✅ Solution optimisée : Méthodes génériques dans BaseModel**

```javascript
// Models/BaseModel.js
export class BaseModel {
  // ...
  
  /**
   * Vérifie si l'entité a une propriété
   * @param {string} fieldKey - Clé du champ
   * @returns {boolean}
   */
  has(fieldKey) {
    return fieldKey in this._data && 
           this._data[fieldKey] !== null && 
           this._data[fieldKey] !== undefined;
  }
  
  /**
   * Formate une propriété en utilisant le formatter correspondant
   * @param {string} fieldKey - Clé du champ
   * @returns {Object|null}
   */
  format(fieldKey) {
    if (!this.has(fieldKey)) return null;
    const formatter = FormatterRegistry.get(fieldKey);
    if (!formatter || typeof formatter.format !== 'function') return null;
    return formatter.format(this._data[fieldKey]);
  }
  
  /**
   * Génère une cellule pour une propriété en utilisant le formatter correspondant
   * @param {string} fieldKey - Clé du champ
   * @param {Object} options - Options (context, size, config, ctx)
   * @returns {Object|null} Cell object
   */
  toCell(fieldKey, options = {}) {
    if (!this.has(fieldKey)) return null;
    
    const formatter = FormatterRegistry.get(fieldKey);
    if (formatter && typeof formatter.toCell === 'function') {
      return formatter.toCell(this._data[fieldKey], options);
    }
    
    // Fallback : cellule par défaut
    return this._toDefaultCell(fieldKey, options);
  }
  
  /**
   * Méthodes de convenance (pour compatibilité et lisibilité)
   * Générées automatiquement via FormatterRegistry
   */
  hasRarity() { return this.has('rarity'); }
  formatRarity() { return this.format('rarity'); }
  toRarityCell(options) { return this.toCell('rarity', options); }
  
  hasLevel() { return this.has('level'); }
  formatLevel() { return this.format('level'); }
  toLevelCell(options) { return this.toCell('level', options); }
  
  // ... autres méthodes de convenance si nécessaire
}
```

**Avantage :**
- ✅ **DRY** : Une seule logique pour toutes les propriétés
- ✅ **Automatique** : FormatterRegistry gère tout
- ✅ **Extensible** : Ajouter un formatter = automatiquement disponible
- ✅ **Méthodes de convenance** : Garde la lisibilité (`resource.formatRarity()`)

#### 2. ❌ Duplication du switch/case dans `toCell()` de chaque modèle

**Problème :**
Chaque modèle (Resource, Item, Consumable) aura un switch/case similaire :
```javascript
toCell(fieldKey, options) {
  switch (fieldKey) {
    case 'rarity': return this.toRarityCell(options);
    case 'level': return this.toLevelCell(options);
    // ... répété dans chaque modèle
  }
}
```

**✅ Solution optimisée : FormatterRegistry + BaseModel.toCell() générique**

```javascript
// Models/BaseModel.js
export class BaseModel {
  /**
   * Génère une cellule pour un champ (méthode principale)
   * @param {string} fieldKey - Clé du champ
   * @param {Object} options - Options (context, size, config, ctx)
   * @returns {Object} Cell object
   */
  toCell(fieldKey, options = {}) {
    const { context = 'table', size = 'md', config = {}, ctx = {} } = options;
    const descriptor = config[fieldKey] || {};
    
    // 1. Résoudre le format selon le descriptor et la taille
    const format = this._resolveFormat(fieldKey, descriptor, context, size);
    
    // 2. Essayer d'utiliser le formatter centralisé
    const formatter = FormatterRegistry.get(fieldKey);
    if (formatter && typeof formatter.toCell === 'function' && this.has(fieldKey)) {
      return formatter.toCell(this._data[fieldKey], { ...options, format });
    }
    
    // 3. Essayer une méthode spécifique du modèle (pour les champs personnalisés)
    const specificMethod = `_to${this._capitalize(fieldKey)}Cell`;
    if (typeof this[specificMethod] === 'function') {
      return this[specificMethod](format, size, options);
    }
    
    // 4. Fallback : cellule par défaut
    return this._toDefaultCell(fieldKey, format, size, options);
  }
  
  /**
   * Résout le format selon le descriptor et la taille
   * @private
   */
  _resolveFormat(fieldKey, descriptor, context, size) {
    const viewCfg = descriptor?.display?.views?.[context] || {};
    const sizeCfg = descriptor?.display?.sizes?.[size] || {};
    return {
      mode: viewCfg?.mode || sizeCfg?.mode || null,
      truncate: viewCfg?.truncate || sizeCfg?.truncate || null,
    };
  }
  
  /**
   * Génère une cellule par défaut (texte simple)
   * @private
   */
  _toDefaultCell(fieldKey, format, size, options) {
    const value = this._data[fieldKey];
    return {
      type: 'text',
      value: value === null || value === undefined || value === '' ? '-' : String(value),
      params: {
        sortValue: value,
        searchValue: value === null || value === undefined || value === '' ? '' : String(value),
      }
    };
  }
  
  /**
   * Capitalise la première lettre (helper)
   * @private
   */
  _capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
}

// Models/Entity/Resource.js
export class Resource extends BaseModel {
  // Pas besoin de surcharger toCell() pour rarity/level (géré par FormatterRegistry)
  
  // Seulement pour les champs spécifiques à Resource
  _toResourceTypeCell(format, size, options) {
    const resourceType = this.resourceType;
    if (!resourceType) {
      return { type: 'text', value: '-', params: {} };
    }
    // ... logique spécifique
  }
  
  // Surcharge toCell() uniquement pour ajouter les champs spécifiques
  toCell(fieldKey, options = {}) {
    // D'abord, essayer la méthode de base (gère les formatters)
    const baseCell = super.toCell(fieldKey, options);
    if (baseCell && baseCell.type !== 'text' || !baseCell.value || baseCell.value !== '-') {
      return baseCell; // Si la méthode de base a trouvé quelque chose, l'utiliser
    }
    
    // Sinon, gérer les champs spécifiques à Resource
    switch (fieldKey) {
      case 'resource_type':
        return this._toResourceTypeCell(format, size, options);
      case 'name':
        return this._toNameCell(format, size, options);
      // ...
      default:
        return baseCell; // Fallback vers la méthode de base
    }
  }
}
```

**Avantage :**
- ✅ **DRY** : Pas de duplication du switch/case
- ✅ **Automatique** : Les formatters sont utilisés automatiquement
- ✅ **Flexible** : Chaque modèle peut surcharger pour ses champs spécifiques
- ✅ **Maintenable** : Ajouter un formatter = automatiquement disponible partout

#### 3. ❌ Duplication entre EntityDescriptorHelpers et Formatters

**Problème :**
- `EntityDescriptorHelpers.js` contient `formatRarity()`, `formatVisibility()`, etc.
- Les formatters proposés font la même chose
- Risque de duplication et d'incohérence

**✅ Solution optimisée : Migration progressive**

**Phase 1 : Formatters utilisent EntityDescriptorHelpers (transition)**
```javascript
// Utils/Formatters/RarityFormatter.js
import { formatRarity as formatRarityHelper, RARITY_OPTIONS } from '@/Entities/entity/EntityDescriptorHelpers';

export class RarityFormatter {
  static OPTIONS = RARITY_OPTIONS; // Réutilise les constantes existantes
  
  static format(value) {
    // Utilise temporairement la fonction existante
    return formatRarityHelper(value, { showLabel: true, showIcon: true });
  }
  
  static toCell(value, options = {}) {
    const formatted = this.format(value);
    // ... génère la cellule
  }
}
```

**Phase 2 : Déplacer la logique dans les formatters (final)**
```javascript
// Utils/Formatters/RarityFormatter.js
export class RarityFormatter {
  static OPTIONS = Object.freeze([...]); // Source de vérité unique
  
  static format(value) {
    // Logique déplacée ici (supprime la dépendance à EntityDescriptorHelpers)
  }
}

// EntityDescriptorHelpers.js (déprécié progressivement)
export function formatRarity(value, options = {}) {
  // ⚠️ DEPRECATED : Utiliser RarityFormatter.format() à la place
  console.warn('formatRarity() est déprécié, utiliser RarityFormatter.format()');
  return RarityFormatter.format(value);
}
```

**Avantage :**
- ✅ **Migration progressive** : Pas de breaking changes
- ✅ **Source de vérité unique** : Les formatters deviennent la référence
- ✅ **Rétrocompatibilité** : EntityDescriptorHelpers devient un wrapper

#### 4. ❌ Duplication de la logique `_resolveFormat()` dans chaque modèle

**Problème :**
La logique de résolution du format (descriptor + taille) serait dupliquée dans chaque modèle.

**✅ Solution optimisée : Méthode dans BaseModel**

```javascript
// Models/BaseModel.js
export class BaseModel {
  /**
   * Résout le format selon le descriptor et la taille
   * @protected
   */
  _resolveFormat(fieldKey, descriptor, context, size) {
    // Normaliser la taille (xs-xl)
    const normalizedSize = this._normalizeSize(size);
    
    // Récupérer la config de la vue
    const viewCfg = descriptor?.display?.views?.[context] || {};
    
    // Récupérer la config de la taille
    const sizeCfg = descriptor?.display?.sizes?.[normalizedSize] || {};
    
    return {
      mode: viewCfg?.mode || sizeCfg?.mode || null,
      truncate: viewCfg?.truncate || sizeCfg?.truncate || null,
      size: normalizedSize,
    };
  }
  
  /**
   * Normalise la taille (xs-xl)
   * @protected
   */
  _normalizeSize(size) {
    // Si déjà en xs-xl, retourner tel quel
    if (['xs', 'sm', 'md', 'lg', 'xl'].includes(size)) {
      return size;
    }
    // Migration depuis small/normal/large (temporaire)
    const sizeMap = { small: 'sm', normal: 'md', large: 'lg' };
    return sizeMap[size] || 'md';
  }
}
```

**Avantage :**
- ✅ **DRY** : Une seule implémentation dans BaseModel
- ✅ **Réutilisable** : Tous les modèles l'héritent
- ✅ **Maintenable** : Modification en un seul endroit

#### 5. ⚠️ Performance : Génération des cellules à la volée

**Problème :**
Générer les cellules à la volée peut être coûteux pour les gros tableaux (1000+ lignes).

**✅ Solution optimisée : Cache dans le modèle**

```javascript
// Models/BaseModel.js
export class BaseModel {
  constructor(rawData) {
    this._raw = rawData;
    this._data = this._extractData(rawData);
    this._cellCache = new Map(); // Cache des cellules générées
  }
  
  /**
   * Génère une cellule avec cache
   * @param {string} fieldKey - Clé du champ
   * @param {Object} options - Options
   * @returns {Object} Cell object
   */
  toCell(fieldKey, options = {}) {
    // Créer une clé de cache basée sur fieldKey + options pertinentes
    const cacheKey = this._getCacheKey(fieldKey, options);
    
    // Vérifier le cache
    if (this._cellCache.has(cacheKey)) {
      return this._cellCache.get(cacheKey);
    }
    
    // Générer la cellule
    const cell = this._generateCell(fieldKey, options);
    
    // Mettre en cache
    this._cellCache.set(cacheKey, cell);
    
    return cell;
  }
  
  /**
   * Crée une clé de cache
   * @private
   */
  _getCacheKey(fieldKey, options) {
    const { context = 'table', size = 'md' } = options;
    // Inclure la valeur du champ dans la clé (si elle change, le cache doit être invalidé)
    const value = this._data[fieldKey];
    return `${fieldKey}:${context}:${size}:${value}`;
  }
  
  /**
   * Génère réellement la cellule (sans cache)
   * @private
   */
  _generateCell(fieldKey, options) {
    // ... logique de génération
  }
  
  /**
   * Invalide le cache (utile si les données changent)
   */
  invalidateCache() {
    this._cellCache.clear();
  }
}
```

**Avantage :**
- ✅ **Performance** : Les cellules sont mises en cache
- ✅ **Mémoire contrôlée** : Cache par instance (garbage collecté avec le modèle)
- ✅ **Invalidation** : Possibilité de vider le cache si nécessaire

#### 6. ❌ Structure : Incohérence entre FormatterRegistry et méthodes BaseModel

**Problème :**
Deux approches sont proposées :
- Option A : Méthodes explicites dans BaseModel (`hasRarity()`, `formatRarity()`, `toRarityCell()`)
- Option B : FormatterRegistry avec méthodes génériques (`has()`, `format()`, `toCell()`)

**✅ Solution optimisée : Approche hybride (meilleur des deux)**

```javascript
// Models/BaseModel.js
export class BaseModel {
  // Méthodes génériques (utilisent FormatterRegistry)
  has(fieldKey) { /* ... */ }
  format(fieldKey) { /* ... */ }
  toCell(fieldKey, options) { /* ... */ }
  
  // Méthodes de convenance pour les propriétés très communes (pour la lisibilité)
  // Générées automatiquement via un système de proxy ou manuellement pour les plus utilisées
  hasRarity() { return this.has('rarity'); }
  formatRarity() { return this.format('rarity'); }
  toRarityCell(options) { return this.toCell('rarity', options); }
  
  hasLevel() { return this.has('level'); }
  formatLevel() { return this.format('level'); }
  toLevelCell(options) { return this.toCell('level', options); }
  
  // Pour les autres propriétés moins communes, utiliser directement :
  // resource.has('visibility')
  // resource.format('visibility')
  // resource.toCell('visibility', options)
}
```

**Avantage :**
- ✅ **Flexibilité** : Méthodes génériques pour tout
- ✅ **Lisibilité** : Méthodes de convenance pour les propriétés communes
- ✅ **Extensibilité** : Pas besoin de modifier BaseModel pour chaque nouvelle propriété

#### 7. ⚠️ Structure : BaseFormatter pour éviter la duplication

**Problème :**
Chaque formatter (RarityFormatter, LevelFormatter, etc.) aura une structure similaire.

**✅ Solution optimisée : BaseFormatter abstrait**

```javascript
// Utils/Formatters/BaseFormatter.js
export class BaseFormatter {
  /**
   * Options disponibles (à surcharger dans les classes filles)
   * @type {Array}
   */
  static OPTIONS = [];
  
  /**
   * Formate une valeur
   * @param {any} value - Valeur à formater
   * @param {Object} options - Options de formatage
   * @returns {Object} { label, color, icon, value }
   */
  static format(value, options = {}) {
    const option = this.OPTIONS.find(opt => opt.value === value) || this.OPTIONS[0];
    return {
      label: option?.label || String(value),
      color: option?.color || 'neutral',
      icon: option?.icon || null,
      value: value
    };
  }
  
  /**
   * Génère une cellule pour le tableau
   * @param {any} value - Valeur à formater
   * @param {Object} options - Options (context, size, mode, etc.)
   * @returns {Object} Cell object
   */
  static toCell(value, options = {}) {
    const { mode = null, ctx = {} } = options;
    const formatted = this.format(value);
    
    // Déterminer le type de cellule selon le mode
    if (mode === 'icon') {
      return {
        type: 'icon',
        value: formatted.icon || 'fa-solid fa-circle',
        params: {
          alt: formatted.label,
          tooltip: formatted.label,
          sortValue: value,
          filterValue: String(value),
        }
      };
    }
    
    // Par défaut : badge
    return {
      type: 'badge',
      value: formatted.label,
      params: {
        color: formatted.color,
        tooltip: formatted.label,
        sortValue: value,
        filterValue: String(value),
        searchValue: formatted.label,
        autoScheme: this.constructor.name.replace('Formatter', '').toLowerCase(),
        autoLabel: String(value),
      }
    };
  }
  
  /**
   * Retourne uniquement le label
   */
  static getLabel(value) {
    return this.format(value).label;
  }
  
  /**
   * Retourne uniquement la couleur
   */
  static getColor(value) {
    return this.format(value).color;
  }
}

// Utils/Formatters/RarityFormatter.js
import { BaseFormatter } from './BaseFormatter.js';

export class RarityFormatter extends BaseFormatter {
  static OPTIONS = Object.freeze([
    { value: 0, label: "Commun", color: "gray", icon: "fa-solid fa-circle" },
    { value: 1, label: "Peu commun", color: "blue", icon: "fa-solid fa-circle" },
    { value: 2, label: "Rare", color: "green", icon: "fa-solid fa-circle" },
    { value: 3, label: "Très rare", color: "purple", icon: "fa-solid fa-circle" },
    { value: 4, label: "Légendaire", color: "orange", icon: "fa-solid fa-star" },
    { value: 5, label: "Unique", color: "red", icon: "fa-solid fa-star" },
  ]);
  
  // format() et toCell() sont hérités de BaseFormatter
  // Surcharger uniquement si comportement spécifique nécessaire
}

// Utils/Formatters/LevelFormatter.js
import { BaseFormatter } from './BaseFormatter.js';

export class LevelFormatter extends BaseFormatter {
  // Pour le niveau, on peut surcharger toCell() pour gérer le mode "badge" vs "text"
  static toCell(value, options = {}) {
    const { mode = null } = options;
    
    // Si mode "badge", utiliser le formatage de base
    if (mode === 'badge') {
      return super.toCell(value, {
        ...options,
        formatted: { label: String(value), color: 'auto', icon: null }
      });
    }
    
    // Sinon, texte simple
    return {
      type: 'text',
      value: value === null || value === undefined || value === '' ? '-' : String(value),
      params: {
        sortValue: Number(value) || 0,
        filterValue: String(value || ''),
        searchValue: String(value || ''),
      }
    };
  }
}
```

**Avantage :**
- ✅ **DRY** : Logique commune dans BaseFormatter
- ✅ **Réutilisabilité** : Chaque formatter hérite et surcharge si nécessaire
- ✅ **Cohérence** : Même structure pour tous les formatters
- ✅ **Maintenabilité** : Modification de la logique commune en un seul endroit

### Résumé des optimisations proposées

| Problème | Solution | Bénéfice |
|----------|----------|----------|
| **Duplication pattern hasX/formatX/toXCell** | Méthodes génériques `has()`, `format()`, `toCell()` + FormatterRegistry | ✅ DRY, extensible |
| **Duplication switch/case dans toCell()** | FormatterRegistry automatique + surcharge uniquement pour champs spécifiques | ✅ DRY, automatique |
| **Duplication EntityDescriptorHelpers vs Formatters** | Migration progressive : Formatters deviennent source de vérité | ✅ Source unique, rétrocompatible |
| **Duplication _resolveFormat()** | Méthode dans BaseModel (héritée) | ✅ DRY, réutilisable |
| **Performance cellules à la volée** | Cache dans le modèle (Map par instance) | ✅ Performance, mémoire contrôlée |
| **Incohérence FormatterRegistry vs méthodes** | Approche hybride : génériques + convenance | ✅ Flexible, lisible |
| **Duplication structure formatters** | BaseFormatter abstrait | ✅ DRY, cohérent |

### Architecture finale optimisée

```
Utils/Formatters/
├── BaseFormatter.js          # Classe abstraite avec logique commune
├── FormatterRegistry.js     # Registre centralisé des formatters
├── RarityFormatter.js        # Hérite de BaseFormatter
├── LevelFormatter.js         # Hérite de BaseFormatter
└── ...

Models/
├── BaseModel.js
│   ├── has(fieldKey)         # Générique
│   ├── format(fieldKey)      # Générique (utilise FormatterRegistry)
│   ├── toCell(fieldKey, options) # Générique (utilise FormatterRegistry)
│   ├── _resolveFormat()      # Logique commune
│   ├── _cellCache            # Cache des cellules
│   └── hasRarity(), formatRarity(), toRarityCell() # Méthodes de convenance
└── Entity/
    └── Resource.js
        └── toCell()          # Surcharge uniquement pour champs spécifiques (resource_type, name)
```

**Flux optimisé :**
```
Backend → Adapter → Modèles (créés une fois)
  ↓
Tableau appelle entity.toCell(fieldKey, options)
  ↓
BaseModel.toCell() :
  1. Vérifie le cache
  2. Cherche dans FormatterRegistry
  3. Si trouvé → utilise le formatter
  4. Sinon → méthode spécifique du modèle
  5. Sinon → cellule par défaut
  6. Met en cache
```

**Avantages finaux :**
- ✅ **DRY maximal** : Pas de duplication de code
- ✅ **Performance** : Cache des cellules générées
- ✅ **Extensibilité** : Ajouter un formatter = automatiquement disponible
- ✅ **Maintenabilité** : Modification en un seul endroit
- ✅ **Structure claire** : Séparation des responsabilités

### Prochaines étapes

1. **Implémenter les formatters prioritaires** (RarityFormatter, LevelFormatter, etc.)
2. **Enrichir BaseModel** avec les méthodes communes
3. **Créer le POC Resource** (modèle avec toCell(), adapter simplifié, vue Large manuelle)
4. **Tester le POC** (tableau, quickedit, modals, vues)
5. **Migrer progressivement** les autres entités