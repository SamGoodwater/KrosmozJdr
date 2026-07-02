# Architecture Sections et Pages - Flux de données

## Vue d'ensemble

L'architecture des sections et pages suit un flux de données structuré qui sépare clairement les responsabilités :

```
Backend → Service → Controller → Resource → Frontend → Mapper → Model → Adapter → UI
```

## Flux complet

### 1. Backend (Laravel)

#### Service (`SectionService`)
- **Responsabilité** : Logique métier centralisée
- **Localisation** : `app/Services/SectionService.php`
- **Méthodes principales** :
  - `create()` : Création avec valeurs par défaut
  - `update()` : Mise à jour avec validation
  - `delete()` : Suppression (soft delete)
  - `reorder()` : Réorganisation
  - `getDisplayableSections()` : Récupération des sections affichables
  - `getDefaultValues()` : Valeurs par défaut par template

**Exemple** :
```php
$section = SectionService::create($data, $user);
$sections = SectionService::getDisplayableSections($page, $user);
```

#### Controller (`SectionController`)
- **Responsabilité** : Gestion des requêtes HTTP
- **Localisation** : `app/Http/Controllers/SectionController.php`
- **Utilise** : `SectionService` pour la logique métier
- **Retourne** : `SectionResource` (format Inertia)

**Exemple** :
```php
public function store(StoreSectionRequest $request)
{
    $this->authorize('create', Section::class);
    $section = SectionService::create($request->validated(), $request->user());
    NotificationService::notifyEntityCreated($section, $request->user());
    return redirect()->route('pages.show', $section->page->slug);
}
```

#### Resource (`SectionResource`)
- **Responsabilité** : Transformation Entity → Format API/Frontend
- **Localisation** : `app/Http/Resources/SectionResource.php`
- **Inclut** : Relations, permissions, formatage des données

### 2. Frontend (Vue.js)

#### Mapper (`sectionMapper`)
- **Responsabilité** : Transformation Entity → Model
- **Localisation** : `resources/js/Pages/Organismes/section/mappers/sectionMapper.js`
- **Fonctions principales** :
  - `mapToSectionModel()` : Transforme les données brutes en instance `Section`
  - `mapToSectionModels()` : Transforme un tableau de sections
  - `mapToFormData()` : Transforme pour un formulaire

**Exemple** :
```javascript
import { mapToSectionModel } from './mappers/sectionMapper';

const sectionModel = mapToSectionModel(rawSectionData);
console.log(sectionModel.template); // Accès normalisé
```

#### Model (`Section`)
- **Responsabilité** : Représentation normalisée des données
- **Localisation** : `resources/js/Models/Section.js`
- **Propriétés** : Accès normalisé aux données (résout les Proxies Vue/Inertia)
- **Méthodes** : `toFormData()`, `toRaw()`, etc.

**Exemple** :
```javascript
import { Section } from '@/Models';

const section = new Section(props.section);
console.log(section.canUpdate); // Permission
console.log(section.template); // Template normalisé
```

#### Adapter UI (`sectionUIAdapter`)
- **Responsabilité** : Transformation Model → Données UI
- **Localisation** : `resources/js/Pages/Organismes/section/adapters/sectionUIAdapter.js`
- **Transforme** :
  - États → Couleurs, badges
  - Templates → Icônes
  - Visibilités → Labels
  - Rôles → Labels

**Exemple** :
```javascript
import { adaptSectionToUI } from './adapters/sectionUIAdapter';

const uiData = adaptSectionToUI(sectionModel);
// { color: 'success', icon: 'fa-file-text', badge: { text: 'Publié', ... }, ... }
```

#### Composable UI (`useSectionUI`)
- **Responsabilité** : Interface unifiée pour l'UI
- **Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionUI.js`
- **Combine** : Mapper + Adapter
- **Fournit** : Section normalisée + Données UI + Permissions + Informations

**Exemple** :
```javascript
import { useSectionUI } from './composables/useSectionUI';

const { sectionModel, uiData, status, canEdit, templateInfo, stateInfo } = useSectionUI(props.section);

// Utilisation dans le template
<Badge :color="stateInfo.badge.color">{{ stateInfo.badge.text }}</Badge>
<Icon :source="templateInfo.icon" />
```

## Structure des fichiers

```
Backend:
app/
├── Services/
│   └── SectionService.php          # Logique métier
├── Http/
│   ├── Controllers/
│   │   └── SectionController.php  # Gestion HTTP
│   └── Resources/
│       └── SectionResource.php     # Format API

Frontend:
resources/js/
├── Models/
│   └── Section.js                   # Modèle normalisé
└── Pages/Organismes/section/
    ├── mappers/
    │   └── sectionMapper.js        # Entity → Model
    ├── adapters/
    │   └── sectionUIAdapter.js     # Model → UI
    └── composables/
        └── useSectionUI.js         # Interface unifiée
```

## Utilisation dans les composants

### Exemple 1 : Affichage simple

```vue
<script setup>
import { useSectionUI } from './composables/useSectionUI';

const props = defineProps({
  section: { type: Object, required: true }
});

const { sectionModel, uiData, canEdit } = useSectionUI(props.section);
</script>

<template>
  <div :class="uiData.containerClass">
    <Badge :color="uiData.badge.color">
      {{ uiData.badge.text }}
    </Badge>
    <Icon :source="uiData.icon" />
    <span>{{ sectionModel.title }}</span>
  </div>
</template>
```

### Exemple 2 : Affichage avec permissions

```vue
<script setup>
import { useSectionUI } from './composables/useSectionUI';

const props = defineProps({
  section: { type: Object, required: true }
});

const { sectionModel, canEdit, canDelete, stateInfo, templateInfo } = useSectionUI(props.section);
</script>

<template>
  <div class="section-card">
    <div class="section-header">
      <Icon :source="templateInfo.icon" />
      <h3>{{ sectionModel.title }}</h3>
      <Badge :color="stateInfo.color">{{ stateInfo.label }}</Badge>
    </div>
    
    <div v-if="canEdit" class="section-actions">
      <Btn @click="edit">Éditer</Btn>
      <Btn v-if="canDelete" @click="delete" color="error">Supprimer</Btn>
    </div>
  </div>
</template>
```

### Exemple 3 : Liste de sections

```vue
<script setup>
import { computed } from 'vue';
import { mapToSectionModels } from './mappers/sectionMapper';
import { useSectionUI } from './composables/useSectionUI';

const props = defineProps({
  sections: { type: Array, default: () => [] }
});

const sectionModels = computed(() => mapToSectionModels(props.sections));
</script>

<template>
  <div v-for="section in sectionModels" :key="section.id">
    <SectionCard :section="section" />
  </div>
</template>
```

## Avantages de cette architecture

1. **Séparation des responsabilités** : Chaque couche a un rôle clair
2. **Réutilisabilité** : Services, mappers et adapters peuvent être réutilisés
3. **Testabilité** : Chaque couche peut être testée indépendamment
4. **Maintenabilité** : Modifications isolées par couche
5. **Évolutivité** : Facile d'ajouter de nouvelles transformations

## Domain Models vs UI Logic

- **Domain Models** (`Section`, `Page`) : Aucune notion d'UI, uniquement les données métier
- **UI Logic** : Isolée dans les adapters et composables
- **Helpers/Adapters** : Transformations spécifiques à l'UI (couleurs, icônes, badges)

## Bonnes pratiques

1. **Toujours utiliser les services** dans les controllers
2. **Toujours mapper** les données brutes en Models
3. **Utiliser les adapters** pour les transformations UI
4. **Utiliser les composables** pour une interface unifiée
5. **Ne pas mélanger** la logique métier et la logique UI

## Migration depuis l'ancienne architecture

### Avant
```javascript
// Accès direct aux données brutes
const template = props.section.template;
const canEdit = props.section.can?.update;
```

### Après
```javascript
// Utilisation du composable unifié
const { sectionModel, canEdit, templateInfo } = useSectionUI(props.section);
const template = templateInfo.value;
```

## Prochaines étapes

1. ✅ Service backend créé (`SectionService`)
2. ✅ Mapper créé (`sectionMapper`)
3. ✅ Adapter UI créé (`sectionUIAdapter`)
4. ✅ Composable UI créé (`useSectionUI`)
5. ✅ Controllers refactorisés
6. ⏳ Migration progressive des composants existants
7. ⏳ Documentation des cas d'usage spécifiques

