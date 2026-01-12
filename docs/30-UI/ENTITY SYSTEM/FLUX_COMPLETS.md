# Flux complets — De la base de données aux vues

**Version** : 2.0  
**Date** : 2026-01-XX

---

## 📊 Flux 1 : Tableau

### Chemin complet

```
Base de données
  → API Laravel (GET /api/resources)
    → createEntityAdapter(Resource, ResourceMapper)
      → ResourceMapper.fromApiArray(entities) [si mapper existe]
        → [Resource instances]
      → { meta, rows: [{ id, cells: {}, rowParams: { entity } }] }
        → EntityTanStackTable
          → Pour chaque colonne (depuis TableConfig) :
            → entity.toCell(fieldKey, { size })
              → getFormatter(fieldKey)
                → RarityFormatter.toCell(value, options)
                  → SharedConstants.RARITY_GRADIENT
                    → { type: 'badge', value: 'Rare', params: {...} }
                      → CellRenderer
                        → <Badge color="success">Rare</Badge>
```

### Fichiers impliqués

- **Adapter** : `Utils/Entity/createEntityAdapter.js`
- **Mapper** : `Mappers/Entity/ResourceMapper.js` (optionnel)
- **Model** : `Models/Entity/Resource.js`
- **Formatter** : `Utils/Formatters/RarityFormatter.js`
- **Constants** : `Utils/Entity/SharedConstants.js`
- **Descriptor** : `Entities/resource/resource-descriptors.js`
- **Config** : `Utils/Entity/Configs/TableConfig.js`
- **Renderer** : `Pages/Organismes/entity/EntityTanStackTable.vue`

---

## 🖼️ Flux 2 : Vue Large

### Chemin complet

```
Base de données
  → API Laravel (GET /api/resources/{id})
    → Resource instance
      → EntityModal
        → resolveEntityViewComponent('resource', 'large')
          → ResourceViewLarge.vue
            → new Resource(entity)
            → getResourceFieldDescriptors(ctx)
            → Pour chaque champ :
              → entity.toCell(fieldKey)
                → (même processus que tableau)
              → descriptors[fieldKey].general.label
              → descriptors[fieldKey].general.icon
              → descriptors[fieldKey].permissions.visibleIf(ctx)
                → Layout manuel (badges, sections, etc.)
```

### Fichiers impliqués

- **Model** : `Models/Entity/Resource.js`
- **Descriptor** : `Entities/resource/resource-descriptors.js`
- **Resolver** : `Utils/entity/resolveEntityViewComponent.js`
- **Vue** : `Pages/Molecules/entity/resource/ResourceViewLarge.vue`

---

## 📝 Flux 3 : Édition Large

### Chemin complet

```
Base de données
  → API Laravel (GET /api/resources/{id})
    → Resource instance
      → ResourceEditLarge.vue
        → getResourceFieldDescriptors(ctx)
        → createFieldsConfigFromDescriptors(descriptors, ctx)
          → fieldsConfig = [{ key, type, required, validation, ... }]
        → initializeFormFromEntity(entity, fieldsConfig)
          → form = { name: "...", rarity: 2, ... }
        → EntityFormField (pour chaque champ)
          → SelectField / InputField / TextareaField / etc.
        → useEntityFormSubmit(form, entityType)
          → router.put('/api/resources/{id}', form)
            → API Laravel (PUT /api/resources/{id})
              → Base de données (UPDATE)
```

### Fichiers impliqués

- **Descriptor** : `Entities/resource/resource-descriptors.js`
- **Helper** : `Utils/entity/descriptor-form.js`
- **Helper** : `Utils/entity/form-helpers.js`
- **Composable** : `Composables/entity/useEntityFormSubmit.js`
- **Vue** : `Pages/Molecules/entity/resource/ResourceEditLarge.vue`
- **Component** : `Pages/Molecules/entity/EntityFormField.vue`

---

## ⚡ Flux 4 : QuickEdit

### Chemin complet

```
Base de données
  → API Laravel (GET /api/resources)
    → [Resource instances] (sélection multiple)
      → EntityQuickEditPanel
        → resolveEntityViewComponentSync('resource', 'quickedit')
          → EntityQuickEdit.vue (ou ResourceQuickEdit.vue)
            → getResourceFieldDescriptors(ctx)
            → createFieldsConfigFromDescriptors(descriptors, ctx)
            → createBulkFieldMetaFromDescriptors(descriptors, ctx)
            → useBulkEditPanel(selectedEntities, fieldMeta)
              → aggregate = { rarity: { same: true, value: 2 }, ... }
              → form = { rarity: '2', ... }
              → dirty = { rarity: false, ... }
            → EntityFormField (pour chaque champ)
              → Affiche "valeurs différentes" si aggregate[key].same === false
            → buildPayload()
              → getMapperForEntityType('resources')
                → ResourceMapper.fromBulkForm(form)
                  → { rarity: 2, level: 15, ... }
              → router.put('/api/resources/bulk', payload)
                → API Laravel (PUT /api/resources/bulk)
                  → Base de données (UPDATE multiple)
```

### Fichiers impliqués

- **Descriptor** : `Entities/resource/resource-descriptors.js`
- **Helper** : `Utils/entity/descriptor-form.js`
- **Composable** : `Composables/entity/useBulkEditPanel.js`
- **Mapper** : `Mappers/Entity/ResourceMapper.js`
- **Registry** : `Utils/Entity/MapperRegistry.js`
- **Vue** : `Pages/Molecules/entity/EntityQuickEdit.vue`
- **Component** : `Pages/Molecules/entity/EntityFormField.vue`

---

## 🔄 Résumé des transformations

| Étape | Données | Format |
|-------|---------|--------|
| Base de données | `{ id: 1, name: "Bois", rarity: 2 }` | SQL |
| API Laravel | `{ id: 1, name: "Bois", rarity: 2 }` | JSON |
| Adapter | `{ meta, rows: [{ id: 1, rowParams: { entity } }] }` | Object JS |
| Mapper (optionnel) | `Resource instance` | Model |
| Model | `entity.toCell('rarity')` | `{ type: 'badge', value: 'Rare', params: {...} }` |
| Formatter | `RarityFormatter.toCell(2)` | Cell object |
| Descriptor | `descriptors.rarity.general.label` | `"Rareté"` |
| Config | `TableColumnConfig` | Config object |
| Renderer | `EntityTanStackTable` | Vue component |
| Vue | `ResourceViewLarge.vue` | Vue component |

---

## 🎯 Points d'entrée principaux

### 1. Tableau
- **Page** : `Pages/Pages/entity/{entity}/Index.vue`
- **Composant** : `EntityTanStackTable`
- **Config** : `TableConfig.fromDescriptors(descriptors, ctx)`

### 2. Vue Large
- **Modal** : `EntityModal`
- **Vue** : `ResourceViewLarge.vue`
- **Résolution** : `resolveEntityViewComponent('resource', 'large')`

### 3. Édition Large
- **Page** : `Pages/Pages/entity/{entity}/Edit.vue`
- **Vue** : `ResourceEditLarge.vue`
- **Helper** : `createFieldsConfigFromDescriptors(descriptors, ctx)`

### 4. QuickEdit
- **Panneau** : `EntityQuickEditPanel`
- **Vue** : `EntityQuickEdit.vue`
- **Composable** : `useBulkEditPanel(selectedEntities, fieldMeta)`
