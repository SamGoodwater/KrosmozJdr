# Pattern Mappers — Transformation backend → frontend

**Date de création** : 2026-01-XX  
**Contexte** : Introduction des mappers pour découpler le backend du frontend

---

## 🎯 Principe

Les **mappers** sont des classes statiques et pures qui transforment les données brutes du backend en instances de modèles frontend.

**Avantages :**
- ✅ Les modèles deviennent indépendants du backend
- ✅ Centralisation des transformations (renommage, normalisation, conversion)
- ✅ Les migrations backend ne cassent plus le frontend
- ✅ Testable sans Vue, sans API

---

## 📐 Architecture

```
Backend JSON
   ↓
EntityMapper (statique, pur)
   ↓
Model (Resource, Item…)
```

**Règle d'or :** Un mapper est statique, pur et testable. Il ne contient aucune logique métier, seulement de la transformation de données.

---

## 📝 Structure d'un mapper

### Exemple : ResourceMapper

```javascript
// Mappers/Entity/ResourceMapper.js
import { Resource } from '@/Models/Entity/Resource';

export class ResourceMapper {
  /**
   * Transforme une réponse API backend en instance Resource
   */
  static fromApi(payload) {
    if (!payload) {
      return new Resource({});
    }

    return new Resource({
      // Identifiants
      id: payload.id ?? null,
      dofusdb_id: payload.dofusdb_id ?? payload.dofusdbId ?? null,

      // Propriétés de base (normalisation)
      name: payload.name ?? '',
      rarity: payload.rarity !== undefined ? Number(payload.rarity) : 0,
      level: payload.level !== undefined ? Number(payload.level) : null,

      // Renommage de champs
      image: payload.image_url ?? payload.image ?? '',

      // Normalisation de valeurs
      isVisible: payload.is_visible === 'guest',
      usable: Boolean(payload.usable),

      // Conversion de dates
      created_at: payload.created_at
        ? new Date(payload.created_at)
        : payload.createdAt
          ? new Date(payload.createdAt)
          : null,

      // Relations (conservées telles quelles)
      resourceType: payload.resource_type ?? payload.resourceType ?? null,
    });
  }

  /**
   * Transforme un tableau de réponses API en tableau d'instances Resource
   */
  static fromApiArray(list) {
    if (!Array.isArray(list)) {
      return [];
    }
    return list.map((item) => this.fromApi(item));
  }

  /**
   * Transforme les données d'un formulaire en instance Resource
   */
  static fromForm(formData) {
    if (!formData) {
      return new Resource({});
    }

    return new Resource({
      id: formData.id ?? null,
      name: formData.name ?? '',
      rarity: formData.rarity !== undefined ? Number(formData.rarity) : 0,
      // ... autres champs
    });
  }

  /**
   * Transforme une instance Resource en données pour l'API backend
   */
  static toApi(resource) {
    if (!resource || !(resource instanceof Resource)) {
      return {};
    }

    return {
      id: resource.id,
      name: resource.name,
      rarity: resource.rarity,
      // ... autres champs
    };
  }
}
```

---

## 🔄 Utilisation dans les adapters

### Avant (sans mapper)

```javascript
// resource-adapter.js
export function adaptResourceEntitiesTableResponse(payload) {
  const entities = payload.entities || [];
  const rows = entities.map((entityData) => {
    const resource = new Resource(entityData);  // ❌ Dépendance directe
    return { id: resource.id, rowParams: { entity: resource } };
  });
  return { meta: payload.meta, rows };
}
```

### Après (avec mapper)

```javascript
// resource-adapter.js
import { ResourceMapper } from "@/Mappers/Entity/ResourceMapper";

export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // ✅ Utiliser le mapper pour transformer les données
  const resources = ResourceMapper.fromApiArray(entities);

  const rows = resources.map((resource) => {
    return {
      id: resource.id,
      cells: {},
      rowParams: { entity: resource },
    };
  });

  return { meta, rows };
}
```

---

## 📋 Méthodes standard d'un mapper

### 1. `fromApi(payload)`
Transforme une réponse API backend en instance de modèle.

**Paramètres :**
- `payload` (Object) : Données brutes du backend

**Retourne :** Instance du modèle

**Exemple :**
```javascript
const resource = ResourceMapper.fromApi({
  id: 1,
  name: "Bois",
  rarity: "1",  // String → sera converti en Number
  image_url: "/images/bois.png"  // Renommé en "image"
});
```

### 2. `fromApiArray(list)`
Transforme un tableau de réponses API en tableau d'instances de modèles.

**Paramètres :**
- `list` (Array<Object>) : Tableau de données brutes du backend

**Retourne :** Array<Model>

**Exemple :**
```javascript
const resources = ResourceMapper.fromApiArray([
  { id: 1, name: "Bois", rarity: "1" },
  { id: 2, name: "Fer", rarity: "2" }
]);
```

### 3. `fromForm(formData)`
Transforme les données d'un formulaire en instance de modèle.

**Paramètres :**
- `formData` (Object) : Données du formulaire

**Retourne :** Instance du modèle

**Exemple :**
```javascript
const resource = ResourceMapper.fromForm({
  name: "Bois",
  rarity: 1,
  level: 50
});
```

### 4. `toApi(model)`
Transforme une instance de modèle en données pour l'API backend.

**Paramètres :**
- `model` (Model) : Instance du modèle

**Retourne :** Object (données formatées pour l'API)

**Exemple :**
```javascript
const apiData = ResourceMapper.toApi(resource);
// { id: 1, name: "Bois", rarity: 1, ... }
```

---

## 🎯 Transformations courantes

### Renommage de champs
```javascript
image: payload.image_url ?? payload.image ?? ''
```

### Conversion de types
```javascript
rarity: payload.rarity !== undefined ? Number(payload.rarity) : 0
level: payload.level !== undefined ? Number(payload.level) : null
```

### Normalisation de valeurs booléennes
```javascript
usable: Boolean(payload.usable)
auto_update: Boolean(payload.auto_update ?? payload.autoUpdate)
```

### Conversion de dates
```javascript
created_at: payload.created_at
  ? new Date(payload.created_at)
  : payload.createdAt
    ? new Date(payload.createdAt)
    : null
```

### Valeurs par défaut
```javascript
name: payload.name ?? ''
rarity: payload.rarity !== undefined ? Number(payload.rarity) : 0
```

### Gestion des relations
```javascript
resourceType: payload.resource_type ?? payload.resourceType ?? null
createdBy: payload.created_by ?? payload.createdBy ?? null
```

---

## ✅ Bonnes pratiques

### 1. Toujours gérer les valeurs nulles/undefined
```javascript
// ✅ Bon
id: payload.id ?? null
name: payload.name ?? ''

// ❌ Mauvais
id: payload.id  // Peut être undefined
```

### 2. Utiliser le nullish coalescing (`??`)
```javascript
// ✅ Bon
level: payload.level ?? null

// ❌ Mauvais
level: payload.level || null  // 0 serait converti en null
```

### 3. Convertir explicitement les types
```javascript
// ✅ Bon
rarity: payload.rarity !== undefined ? Number(payload.rarity) : 0

// ❌ Mauvais
rarity: payload.rarity  // Peut être une string
```

### 4. Gérer les multiples noms de champs
```javascript
// ✅ Bon (compatibilité avec anciennes versions)
image: payload.image_url ?? payload.image ?? ''

// ❌ Mauvais
image: payload.image  // Peut ne pas exister
```

### 5. Tester les mappers indépendamment
```javascript
// ✅ Testable sans Vue, sans API
describe('ResourceMapper', () => {
  it('should transform API payload to Resource', () => {
    const payload = { id: 1, name: "Bois", rarity: "1" };
    const resource = ResourceMapper.fromApi(payload);
    expect(resource.id).toBe(1);
    expect(resource.rarity).toBe(1);  // Converti en number
  });
});
```

---

## 🔗 Intégration avec l'architecture

Les mappers s'intègrent dans le flux de données global :

```
Backend (données brutes)
   ↓
Mappers (transformation backend → frontend)
   ↓
Models (logique métier + formatage)
   ↓
Formatters (formatage centralisé)
   ↓
Descriptors (configuration déclarative)
   ↓
Renderers (table, actions, formulaires)
   ↓
Vues (Large / Compact / Minimal / Text)
```

**Voir [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) pour la vue d'ensemble complète.**

---

## 📚 Références

- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Vue d'ensemble de l'architecture
- [ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md](./ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md) — Structure des fichiers
