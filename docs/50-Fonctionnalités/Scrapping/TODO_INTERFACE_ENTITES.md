# TODO : Interface tableau pour chaque entité

**Objectif** : Créer une interface en format tableau pour chaque entité avec un lien vers la page de détail au centre.

## 📋 Analyse de l'existant

### Structure actuelle
- **Architecture** : Inertia.js + Vue 3 + Atomic Design + DaisyUI
- **Exemple de tableau** : `SectionList.vue` (Organisme) utilise `table.table` de DaisyUI
- **Composants disponibles** :
  - `Container` (Atom) - Conteneur principal
  - `Btn` (Atom) - Boutons d'action
  - `Route` (Atom) - Liens de navigation
  - `Tooltip` (Atom) - Tooltips
  - `Loading` (Atom) - Indicateur de chargement
  - `Badge` (Atom) - Badges pour les statuts
  - `List` (Atom) - Liste DaisyUI
  - `Row` (Atom) - Ligne de liste
- **Pattern de routing** : `/entities/{entity}/index` → `Pages/Pages/{entity}/Index.vue`
- **Pattern de controller** : `Inertia::render()` avec Resources pour les données

### Contrôleurs actuels
- Les contrôleurs Entity retournent actuellement du **JSON** (`response()->json()`)
- Il faut les adapter pour retourner des **vues Inertia** avec des **Resources**

## 🎯 Todo List

### Phase 1 : Infrastructure backend (Routes + Controllers)

#### 1.1 Créer les routes web pour toutes les entités
- [ ] Créer `routes/entities.php` (ou fichiers séparés par entité)
- [ ] Routes à créer pour chaque entité (15 entités) :
  - `GET /entities/{entity}/index` → `{Entity}Controller@index`
  - `GET /entities/{entity}/{id}` → `{Entity}Controller@show`
  - `GET /entities/{entity}/create` → `{Entity}Controller@create` (si nécessaire)
  - `GET /entities/{entity}/{id}/edit` → `{Entity}Controller@edit` (si nécessaire)
- [ ] Exemples de routes :
  - `/entities/attributes` → Liste des attributs
  - `/entities/campaigns` → Liste des campagnes
  - `/entities/classes` → Liste des classes
  - etc.

#### 1.2 Créer les Resources pour toutes les entités
- [ ] Créer `app/Http/Resources/Entity/AttributeResource.php`
- [ ] Créer `app/Http/Resources/Entity/CampaignResource.php`
- [ ] Créer `app/Http/Resources/Entity/CapabilityResource.php`
- [ ] Créer `app/Http/Resources/Entity/ClasseResource.php`
- [ ] Créer `app/Http/Resources/Entity/ConsumableResource.php`
- [ ] Créer `app/Http/Resources/Entity/CreatureResource.php`
- [ ] Créer `app/Http/Resources/Entity/ItemResource.php`
- [ ] Créer `app/Http/Resources/Entity/MonsterResource.php`
- [ ] Créer `app/Http/Resources/Entity/NpcResource.php`
- [ ] Créer `app/Http/Resources/Entity/PanoplyResource.php`
- [ ] Créer `app/Http/Resources/Entity/ResourceResource.php`
- [ ] Créer `app/Http/Resources/Entity/ScenarioResource.php`
- [ ] Créer `app/Http/Resources/Entity/ShopResource.php`
- [ ] Créer `app/Http/Resources/Entity/SpecializationResource.php`
- [ ] Créer `app/Http/Resources/Entity/SpellResource.php`

#### 1.3 Adapter les Controllers pour Inertia
- [ ] Modifier `AttributeController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `CampaignController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `CapabilityController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ClasseController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ConsumableController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `CreatureController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ItemController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `MonsterController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `NpcController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `PanoplyController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ResourceController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ScenarioController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `ShopController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `SpecializationController@index` : `Inertia::render()` au lieu de `response()->json()`
- [ ] Modifier `SpellController@index` : `Inertia::render()` au lieu de `response()->json()`

**Note** : Garder les méthodes API (JSON) pour les appels API, mais ajouter les méthodes Inertia pour les vues web.

### Phase 2 : Composants réutilisables (Atomic Design)

#### 2.1 Créer un composant Table réutilisable
- [ ] Créer `resources/js/Pages/Molecules/data-display/EntityTable.vue`
  - Props : `entities` (array), `columns` (array), `entityType` (string)
  - Colonnes configurables
  - Lien vers le détail au centre (colonne principale)
  - Actions (éditer, supprimer) à droite
  - Pagination intégrée
  - Recherche/filtres (optionnel)

#### 2.2 Créer un composant TableRow réutilisable
- [ ] Créer `resources/js/Pages/Molecules/data-display/EntityTableRow.vue`
  - Props : `entity` (object), `columns` (array), `entityType` (string)
  - Lien cliquable vers le détail au centre
  - Affichage des colonnes selon configuration
  - Actions conditionnelles selon permissions

#### 2.3 Créer un composant TableHeader réutilisable
- [ ] Créer `resources/js/Pages/Molecules/data-display/EntityTableHeader.vue`
  - Props : `columns` (array), `sortable` (boolean)
  - Tri des colonnes (optionnel)
  - Responsive

### Phase 3 : Pages Index pour chaque entité

#### 3.1 Créer les pages Index.vue (15 entités)
- [ ] `resources/js/Pages/Pages/entity/attribute/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/campaign/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/capability/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/classe/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/consumable/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/creature/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/item/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/monster/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/npc/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/panoply/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/resource/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/scenario/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/shop/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/specialization/Index.vue`
- [ ] `resources/js/Pages/Pages/entity/spell/Index.vue`

**Structure de chaque page** :
- En-tête avec titre et bouton "Créer" (si autorisé)
- Tableau avec colonnes pertinentes
- Lien vers le détail au centre (nom de l'entité)
- Actions (éditer, supprimer) à droite
- Pagination en bas

### Phase 4 : Configuration des colonnes

#### 4.1 Définir les colonnes pour chaque entité

**Format** : `[Colonne]` = Affichage | `(Colonne)` = Optionnel | **Nom** = Lien cliquable (centré)

- [ ] **Attribute** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Description (tronqué), Usable, Visible, Créé par, Actions
  - Lien : `/entities/attributes/{id}`

- [ ] **Campaign** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Slug, État (badge), Public, Créé par, Actions
  - Lien : `/entities/campaigns/{id}`

- [ ] **Capability** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, PA, PO, Élément, Créé par, Actions
  - Lien : `/entities/capabilities/{id}`

- [ ] **Classe** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Vie, Vie dé, Spécificité, dofusdb_id, Créé par, Actions
  - Lien : `/entities/classes/{id}`

- [ ] **Consumable** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, Rareté (badge), Type, Créé par, Actions
  - Lien : `/entities/consumables/{id}`

- [ ] **Creature** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, Hostilité, Vie, Créé par, Actions
  - Lien : `/entities/creatures/{id}`

- [ ] **Item** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, Rareté (badge), Type, dofusdb_id, Créé par, Actions
  - Lien : `/entities/items/{id}`

- [ ] **Monster** : 
  - Colonnes : ID (optionnel), **Nom** (via Creature, lien), Race, Taille, Boss, Créé par, Actions
  - Lien : `/entities/monsters/{id}`

- [ ] **Npc** : 
  - Colonnes : ID (optionnel), **Nom** (via Creature, lien), Classe, Spécialisation, Actions
  - Lien : `/entities/npcs/{id}`

- [ ] **Panoply** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Bonus (tronqué), Items (count), dofusdb_id, Créé par, Actions
  - Lien : `/entities/panoplies/{id}`

- [ ] **Resource** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, Type, Rareté (badge), dofusdb_id, Créé par, Actions
  - Lien : `/entities/resources/{id}`

- [ ] **Scenario** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Slug, État (badge), Public, Créé par, Actions
  - Lien : `/entities/scenarios/{id}`

- [ ] **Shop** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Localisation, NPC, Items (count), Créé par, Actions
  - Lien : `/entities/shops/{id}`

- [ ] **Specialization** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Description (tronqué), Capacités (count), Créé par, Actions
  - Lien : `/entities/specializations/{id}`

- [ ] **Spell** : 
  - Colonnes : ID (optionnel), **Nom** (lien), Niveau, PA, PO, Zone, Type, dofusdb_id, Créé par, Actions
  - Lien : `/entities/spells/{id}`

### Phase 5 : Navigation et intégration

#### 5.1 Ajouter les liens dans la navigation
- [ ] Ajouter un menu "Entités" dans `Aside.vue`
- [ ] Sous-menu avec toutes les entités
- [ ] Icônes appropriées pour chaque entité
- [ ] Permissions selon les policies

#### 5.2 Créer une page d'index général (optionnel)
- [ ] `resources/js/Pages/Pages/entity/Index.vue`
- [ ] Liste des entités disponibles avec liens
- [ ] Statistiques (nombre d'entités par type)

### Phase 6 : Fonctionnalités avancées (optionnel)

#### 6.1 Recherche et filtres
- [ ] Barre de recherche globale
- [ ] Filtres par colonne (dropdown, date range, etc.)
- [ ] Tri des colonnes (asc/desc)

#### 6.2 Pagination
- [ ] Utiliser la pagination Laravel/Inertia
- [ ] Composant de pagination réutilisable

#### 6.3 Actions en masse
- [ ] Sélection multiple
- [ ] Actions groupées (supprimer, exporter, etc.)

## 📝 Structure des fichiers à créer

```
routes/
  └── entities.php (ou entities/*.php)

app/Http/Resources/Entity/
  ├── AttributeResource.php
  ├── CampaignResource.php
  ├── CapabilityResource.php
  ├── ClasseResource.php
  ├── ConsumableResource.php
  ├── CreatureResource.php
  ├── ItemResource.php
  ├── MonsterResource.php
  ├── NpcResource.php
  ├── PanoplyResource.php
  ├── ResourceResource.php
  ├── ScenarioResource.php
  ├── ShopResource.php
  ├── SpecializationResource.php
  └── SpellResource.php

resources/js/Pages/
  ├── Molecules/data-display/
  │   ├── EntityTable.vue
  │   ├── EntityTableRow.vue
  │   └── EntityTableHeader.vue
  └── Pages/entity/
      ├── attribute/
      │   └── Index.vue
      ├── campaign/
      │   └── Index.vue
      ├── capability/
      │   └── Index.vue
      ├── classe/
      │   └── Index.vue
      ├── consumable/
      │   └── Index.vue
      ├── creature/
      │   └── Index.vue
      ├── item/
      │   └── Index.vue
      ├── monster/
      │   └── Index.vue
      ├── npc/
      │   └── Index.vue
      ├── panoply/
      │   └── Index.vue
      ├── resource/
      │   └── Index.vue
      ├── scenario/
      │   └── Index.vue
      ├── shop/
      │   └── Index.vue
      ├── specialization/
      │   └── Index.vue
      └── spell/
          └── Index.vue
```

## 🎨 Design Pattern

### Structure d'une page Index
```vue
<template>
  <Container>
    <!-- En-tête -->
    <div class="flex justify-between items-center">
      <h1>Liste des {Entity}</h1>
      <Btn v-if="canCreate" @click="create">Créer</Btn>
    </div>

    <!-- Tableau -->
    <EntityTable
      :entities="entities"
      :columns="columns"
      :entity-type="entityType"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
  </Container>
</template>
```

### Structure du tableau
- **Colonne 1** : ID (optionnel, peut être masqué)
- **Colonne 2** : Nom (lien cliquable vers le détail) ← **CENTRE**
- **Colonnes suivantes** : Champs pertinents selon l'entité
- **Dernière colonne** : Actions (Voir, Éditer, Supprimer)

## ✅ Priorités

### Priorité 1 : Infrastructure de base
1. Routes web pour toutes les entités
2. Resources pour toutes les entités
3. Adaptation des Controllers (méthode `index()` en Inertia)

### Priorité 2 : Composants réutilisables
1. `EntityTable.vue` (composant principal)
2. `EntityTableRow.vue` (ligne de tableau)
3. `EntityTableHeader.vue` (en-tête de tableau)

### Priorité 3 : Pages Index
1. Créer les 15 pages Index.vue
2. Utiliser les composants réutilisables
3. Configurer les colonnes pour chaque entité

### Priorité 4 : Navigation
1. Ajouter le menu dans Aside.vue
2. Permissions et visibilité

### Priorité 5 : Fonctionnalités avancées
1. Recherche
2. Filtres
3. Tri
4. Pagination avancée

## 📊 Estimation

- **Phase 1** : ~2-3h (Routes + Resources + Controllers)
- **Phase 2** : ~2h (Composants réutilisables)
- **Phase 3** : ~4-5h (15 pages Index)
- **Phase 4** : ~1h (Configuration colonnes)
- **Phase 5** : ~1h (Navigation)
- **Phase 6** : ~3-4h (Fonctionnalités avancées)

**Total estimé** : ~13-16h

## 🔗 Références

- Exemple de tableau : `resources/js/Pages/Organismes/section/SectionList.vue`
- Exemple de controller Inertia : `app/Http/Controllers/PageController.php`
- Exemple de Resource : `app/Http/Resources/PageResource.php`
- Design system : Atomic Design + DaisyUI

