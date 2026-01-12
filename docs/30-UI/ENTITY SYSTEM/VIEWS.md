# Guide des Vues

**Version** : 2.0

---

## 🎯 Rôle

Les **vues** sont des composants Vue **manuels** qui définissent le layout et utilisent les méthodes du modèle.

---

## 📁 Emplacement

```
Pages/Molecules/entity/{entity}/{Entity}ViewLarge.vue
Pages/Molecules/entity/{entity}/{Entity}ViewCompact.vue
Pages/Molecules/entity/{entity}/{Entity}ViewMinimal.vue
Pages/Molecules/entity/{entity}/{Entity}ViewText.vue
Pages/Molecules/entity/{entity}/{Entity}EditLarge.vue
Pages/Molecules/entity/{entity}/{Entity}EditCompact.vue
Pages/Molecules/entity/{entity}/{Entity}QuickEdit.vue (optionnel)
Pages/Molecules/entity/EntityQuickEdit.vue (générique, fallback)
```

---

## 🖼️ Vues d'affichage (Large, Compact, Minimal, Text)

### Structure
```vue
<script setup>
import { Resource } from '@/Models/Entity/Resource';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';

const props = defineProps({
  resource: { type: Object, required: true }
});

const entity = computed(() => new Resource(props.resource));
const descriptors = computed(() => getResourceFieldDescriptors(ctx));
</script>

<template>
  <!-- Layout manuel -->
  <div class="flex gap-2">
    <Badge v-bind="entity.toCell('rarity').params" />
    <Badge v-bind="entity.toCell('level').params" />
  </div>
</template>
```

### Utilisation
- `entity.toCell(fieldKey)` : Obtient la cellule formatée
- `descriptors[fieldKey].general.label` : Obtient le label
- `descriptors[fieldKey].general.icon` : Obtient l'icône
- `descriptors[fieldKey].permissions.visibleIf(ctx)` : Vérifie la visibilité

---

## 📝 Vues d'édition (EditLarge, EditCompact, QuickEdit)

### Structure
```vue
<script setup>
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { EntityFormField } from '@/Pages/Molecules/entity/EntityFormField';
import { useBulkEditPanel } from '@/Composables/entity/useBulkEditPanel';

const descriptors = getResourceFieldDescriptors(ctx);
const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx);
const { form, dirty, aggregate, buildPayload } = useBulkEditPanel(selectedEntities, fieldMeta);
</script>

<template>
  <EntityFormField
    v-for="field in fieldsConfig"
    :key="field.key"
    :field-config="field"
    :model-value="form[field.key]"
    @update:model-value="form[field.key] = $event"
  />
</template>
```

### Utilisation
- `createFieldsConfigFromDescriptors()` : Génère la config des champs
- `EntityFormField` : Rend chaque champ selon sa config
- `useBulkEditPanel` : Gère l'agrégation et le dirty state (QuickEdit uniquement)
- `useForm` : Gère la soumission (EditLarge/Compact)

---

## 🔄 Résolution dynamique

**`resolveEntityViewComponent(entityType, view)` :**
- Charge le composant approprié selon le type d'entité et la vue
- Utilise `import.meta.glob` pour que Vite puisse résoudre les imports dynamiques
- Fallback vers `EntityQuickEdit.vue` si le composant spécifique n'existe pas

---

## 🔗 Liens

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [FLUX_COMPLETS.md](./FLUX_COMPLETS.md) — Flux détaillés
