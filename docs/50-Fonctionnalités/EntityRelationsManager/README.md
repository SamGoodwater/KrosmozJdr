# EntityRelationsManager - Gestion des relations many-to-many

## 📋 Vue d'ensemble

`EntityRelationsManager` est un composant générique Vue 3 permettant de gérer les relations many-to-many entre entités. Il supporte les relations simples (sans pivot) et les relations avec données pivot (quantité, prix, commentaire, etc.).

## 🎯 Fonctionnalités

- ✅ Gestion générique des relations many-to-many
- ✅ Support des relations simples (sans pivot)
- ✅ Support des relations avec pivot (`quantity`, `price`, `comment`, etc.)
- ✅ Recherche et ajout dynamique d'entités
- ✅ Affichage des relations existantes avec possibilité de suppression
- ✅ Sauvegarde avec gestion des pivots
- ✅ Notifications intégrées

## 📦 Utilisation

### Exemple basique (relation simple)

```vue
<EntityRelationsManager
    :relations="spell.classes"
    :available-items="availableClasses"
    :entity-id="spell.id"
    entity-type="spell"
    relation-type="classes"
    relation-name="classes"
    :config="{
        displayFields: ['name', 'description'],
        searchFields: ['name'],
        itemLabel: 'classe',
        itemLabelPlural: 'classes'
    }"
/>
```

### Exemple avec pivot (quantité)

```vue
<EntityRelationsManager
    :relations="item.resources"
    :available-items="availableResources"
    :entity-id="item.id"
    entity-type="item"
    relation-type="resources"
    relation-name="resources"
    :config="{
        displayFields: ['name', 'description', 'level'],
        searchFields: ['name', 'description'],
        itemLabel: 'ressource',
        itemLabelPlural: 'ressources',
        pivotFields: ['quantity']
    }"
/>
```

### Exemple avec pivots multiples (quantité, prix, commentaire)

```vue
<EntityRelationsManager
    :relations="shop.items"
    :available-items="availableItems"
    :entity-id="shop.id"
    entity-type="shop"
    relation-type="items"
    relation-name="items"
    :config="{
        displayFields: ['name', 'description', 'level'],
        searchFields: ['name', 'description'],
        itemLabel: 'objet',
        itemLabelPlural: 'objets',
        pivotFields: ['quantity', 'price', 'comment']
    }"
/>
```

## 🔧 Props

| Prop | Type | Requis | Description |
|------|------|---------|-------------|
| `relations` | Array | Oui | Liste des éléments actuellement liés |
| `availableItems` | Array | Oui | Liste de tous les éléments disponibles (pour recherche) |
| `entityId` | Number | Oui | ID de l'entité principale |
| `entityType` | String | Oui | Type d'entité (panoply, creature, scenario, etc.) |
| `relationType` | String | Oui | Type de relation (items, spells, resources, etc.) |
| `relationName` | String | Oui | Nom de la relation (pour les labels et routes) |
| `config` | Object | Non | Configuration optionnelle (voir ci-dessous) |

### Configuration (`config`)

| Propriété | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `displayFields` | Array | `['name', 'description', 'level']` | Champs à afficher pour chaque élément |
| `searchFields` | Array | `['name', 'description']` | Champs utilisés pour la recherche |
| `routeName` | String | `null` | Nom de la route (si null, construit automatiquement) |
| `itemLabel` | String | `'élément'` | Label au singulier pour les éléments |
| `itemLabelPlural` | String | `'éléments'` | Label au pluriel pour les éléments |
| `pivotFields` | Array | `null` | Champs de pivot (ex: `['quantity']` ou `['quantity', 'price', 'comment']`) |

## 🔄 Événements

| Événement | Payload | Description |
|-----------|---------|-------------|
| `update:relations` | `Array` | Émis lorsque les relations sont mises à jour |

## 🛠️ Routes backend

Le composant construit automatiquement le nom de la route selon le pattern :
```
entities.{entityType}.update{RelationType}
```

Exemples :
- `entities.spells.updateClasses` pour `spell` → `classes`
- `entities.items.updateResources` pour `item` → `resources`
- `entities.shops.updateItems` pour `shop` → `items`

### Format de la requête

Pour les relations simples :
```json
{
  "classes": [1, 2, 3]
}
```

Pour les relations avec pivot :
```json
{
  "resources": {
    "1": { "quantity": 5 },
    "2": { "quantity": 10 }
  }
}
```

Pour les relations avec pivots multiples :
```json
{
  "items": {
    "1": { "quantity": 1, "price": 100, "comment": "En stock" },
    "2": { "quantity": 2, "price": 200, "comment": "Rupture de stock" }
  }
}
```

## 📝 Exemples d'utilisation dans les pages d'édition

### Spell → Classes

```vue
<EntityRelationsManager
    :relations="spell.classes || []"
    :available-items="availableClasses"
    :entity-id="spell.id"
    entity-type="spell"
    relation-type="classes"
    relation-name="classes"
    :config="{
        displayFields: ['name', 'description'],
        searchFields: ['name'],
        itemLabel: 'classe',
        itemLabelPlural: 'classes'
    }"
/>
```

### Item → Resources (avec quantité)

```vue
<EntityRelationsManager
    :relations="item.resources || []"
    :available-items="availableResources"
    :entity-id="item.id"
    entity-type="item"
    relation-type="resources"
    relation-name="resources"
    :config="{
        displayFields: ['name', 'description', 'level'],
        searchFields: ['name', 'description'],
        itemLabel: 'ressource',
        itemLabelPlural: 'ressources',
        pivotFields: ['quantity']
    }"
/>
```

### Shop → Items (avec quantité, prix, commentaire)

```vue
<EntityRelationsManager
    :relations="shop.items || []"
    :available-items="availableItems"
    :entity-id="shop.id"
    entity-type="shop"
    relation-type="items"
    relation-name="items"
    :config="{
        displayFields: ['name', 'description', 'level'],
        searchFields: ['name', 'description'],
        itemLabel: 'objet',
        itemLabelPlural: 'objets',
        pivotFields: ['quantity', 'price', 'comment']
    }"
/>
```

## 🧪 Tests

Les tests sont couverts par les tests des contrôleurs d'entités :
- `ItemControllerTest` : Tests pour `updateResources`
- `SpellControllerTest` : Tests pour `updateClasses` et `updateSpellTypes`
- `PanoplyControllerTest` : Tests pour `updateItems`
- `CreatureControllerTest` : Tests pour les relations avec pivots
- `ShopControllerTest` : Tests pour les relations avec pivots multiples

## 📚 Documentation associée

- [Analyse des entités](./ANALYSE_ENTITES.md) : Analyse des entités et priorisation de l'implémentation
- [EntityEditForm](../EntityEditForm/README.md) : Composant d'édition générique

## 🔗 Fichiers liés

- **Composant** : `resources/js/Pages/Organismes/entity/EntityRelationsManager.vue`
- **Tests** : `tests/Feature/Entity/*ControllerTest.php`

---

**Date de création** : 2025-11-30
**Dernière mise à jour** : 2025-11-30

