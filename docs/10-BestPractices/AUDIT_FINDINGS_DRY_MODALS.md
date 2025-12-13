# Rapport d'audit — DRY & Duplications (Modals/Renderer/Templates)

**Date** : 2025-01-13  
**Périmètre** : Modals Pages/Sections + Renderer + Templates

---

## 📋 Analyse des duplications

### **1. Modals Pages** (`CreatePageModal` vs `EditPageModal`)

#### Duplications identifiées (60-70% de code partagé)

| Aspect | CreatePageModal | EditPageModal | Duplication ? |
|--------|----------------|---------------|---------------|
| FormFields (title, slug, is_visible, etc.) | ✅ | ✅ | **OUI** (même structure) |
| Validation inline | ✅ | ✅ | **OUI** (computed identiques) |
| Options selects (state, visibility, parent) | ✅ | ✅ | **OUI** (via composable) |
| Génération slug auto | ✅ | ✅ | **OUI** (TransformService) |
| Logique submit (useForm + Inertia) | ✅ | ✅ | **PARTIEL** (route différente) |
| Gestion des onglets | ❌ | ✅ (General + Sections) | NON |
| Actions supplémentaires (delete, copy URL) | ❌ | ✅ | NON |

**Constat** : Les deux modals partagent ~70% de code (formulaire, validation, slug auto, options). La différence principale : `EditPageModal` a des onglets + actions supplémentaires.

---

### **2. Modals Sections** (`CreateSectionModal` vs `SectionParamsModal`)

#### Duplications identifiées (30-40% de code partagé)

| Aspect | CreateSectionModal | SectionParamsModal | Duplication ? |
|--------|-------------------|-------------------|---------------|
| Sélection template | ✅ (grille de cartes) | ❌ | NON |
| Champs communs (title, slug, order, etc.) | ❌ (utilise defaults) | ✅ | NON |
| Paramètres spécifiques au template | ❌ | ✅ (via `templateConfig.parameters`) | NON |
| Logique submit (useSectionAPI) | ✅ (createSection) | ✅ (updateSection) | **PARTIEL** |
| Génération des champs de formulaire | ❌ | ✅ (via `SectionParameterService`) | NON |
| Gestion des onglets | ❌ | ✅ (Paramètres + Sections associées) | NON |

**Constat** : Peu de duplication (30%). `CreateSectionModal` est une simple grille de sélection, `SectionParamsModal` est un formulaire complexe généré dynamiquement.

---

### **3. Renderer & Templates**

#### ✓ Architecture déjà DRY

- **`SectionRenderer.vue`** : charge dynamiquement les templates (Read/Edit) via `import()`
- **Templates** : chaque template a 2 versions (`*Read.vue`, `*Edit.vue`) avec contrat unifié (props `section`, `data`, `settings`)
- **Services** : `SectionStyleService`, `SectionParameterService`, `SectionMapper` → logique partagée centralisée
- **Composables** : `useSectionAPI`, `useSectionUI`, `useSectionMode` → réutilisables

**Constat** : Architecture propre, pas de duplication significative. Les templates suivent un contrat strict.

---

## 🛠️ Propositions de refactoring (DRY)

### **Priorité 1 : Composable partagé `usePageFormModal`**

Créer un composable qui centralise la logique commune des modals Pages.

```javascript
// resources/js/Composables/pages/usePageFormModal.js
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { TransformService } from '@/Utils/Services';

export function usePageFormModal(initialData = null) {
  const slugManuallyEdited = ref(false);
  
  const form = useForm({
    title: initialData?.title || '',
    slug: initialData?.slug || '',
    is_visible: initialData?.isVisible || 'guest',
    can_edit_role: initialData?.canEditRole || 'admin',
    in_menu: initialData?.inMenu ?? true,
    state: initialData?.state || 'draft',
    parent_id: initialData?.parentId || null,
    menu_order: initialData?.menuOrder || 0
  });
  
  // Auto-génération slug
  watch(() => form.title, (newTitle) => {
    if (newTitle && !slugManuallyEdited.value) {
      form.slug = TransformService.generateSlugFromTitle(newTitle);
    }
  });
  
  const handleSlugInput = () => {
    slugManuallyEdited.value = true;
  };
  
  const resetForm = () => {
    form.reset();
    form.clearErrors();
    slugManuallyEdited.value = false;
  };
  
  // Validation computed
  const fieldValidation = (fieldName) => computed(() => {
    if (!form.errors[fieldName]) return null;
    return {
      state: 'error',
      message: form.errors[fieldName],
      showNotification: false
    };
  });
  
  return {
    form,
    handleSlugInput,
    resetForm,
    fieldValidation
  };
}
```

**Bénéfices** :
- ✅ Élimine 150+ lignes de duplication
- ✅ Centralise la logique de génération de slug
- ✅ Facilite la maintenance (1 seul endroit à modifier)

**Effort** : 2h (création composable + refactor des 2 modals + tests)

---

### **Priorité 2 : Composant partagé `PageFormFields.vue`**

Créer un composant qui regroupe les champs communs (title, slug, parent, in_menu, etc.).

```vue
<!-- resources/js/Pages/Organismes/section/components/PageFormFields.vue -->
<template>
  <div class="space-y-4">
    <InputField
      v-model="form.title"
      label="Titre"
      type="text"
      required
      :validation="fieldValidation('title')"
      placeholder="Titre de la page"
    />
    
    <InputField
      v-model="form.slug"
      label="Slug"
      type="text"
      required
      :validation="fieldValidation('slug')"
      placeholder="slug-de-la-page"
      @input="handleSlugInput"
    />
    
    <!-- Autres champs... -->
  </div>
</template>
```

**Bénéfices** :
- ✅ Élimine 100+ lignes de duplication
- ✅ Garantit une cohérence visuelle entre les modals
- ✅ Facilite l'ajout de nouveaux champs

**Effort** : 1h30

---

### **Priorité 3 (Nice-to-have) : Service de mapping unifié**

Créer un service qui normalise les payloads avant envoi au backend.

```javascript
// resources/js/Utils/Services/PagePayloadService.js
export class PagePayloadService {
  static prepareCreatePayload(formData) {
    return {
      title: formData.title,
      slug: formData.slug,
      is_visible: formData.is_visible,
      can_edit_role: formData.can_edit_role,
      in_menu: formData.in_menu,
      state: formData.state,
      parent_id: formData.parent_id,
      menu_order: formData.menu_order
    };
  }
  
  static prepareUpdatePayload(formData, onlyChangedFields = true) {
    // Logique pour ne retourner que les champs modifiés
    // ...
  }
}
```

**Bénéfices** :
- ✅ Logique de mapping centralisée
- ✅ Évite les erreurs de typage
- ✅ Facilite les modifications de structure

**Effort** : 1h

---

## 📊 Résumé DRY

| Zone | État actuel | Duplication | Priorité refactoring |
|------|-------------|-------------|---------------------|
| Modals Pages | ⚠️ Acceptable | 70% | **P1 - Important** |
| Modals Sections | ✅ Bon | 30% | P3 - Nice-to-have |
| Renderer | ✅ Excellent | 5% | Aucune |
| Templates | ✅ Excellent | 10% | Aucune |
| Services | ✅ Bon | 15% | P3 - Nice-to-have |

**Score DRY global** : **7/10** (bon, quelques améliorations à planifier)

---

## 🎯 Backlog DRY (priorisé)

### P1 - Important (planifier pour v1.1)
1. **Composable `usePageFormModal`** : centralise formulaire + validation + slug auto (effort : 2h, gain : 150 lignes)
2. **Composant `PageFormFields`** : regroupe champs communs Pages (effort : 1h30, gain : 100 lignes)

### P2 - Optionnel (backlog v1.2+)
3. **Service `PagePayloadService`** : normalise payloads avant envoi (effort : 1h, gain : maintenance)
4. **Tests Vitest** : tester composables/services partagés (effort : 3h)

### P3 - Nice-to-have
5. **Documentation** : créer guide d'architecture des modals (effort : 1h)

---

## ✅ Points forts actuels (à conserver)

- **Architecture templates** : contrat unifié (`section`, `data`, `settings`), découplage Read/Edit
- **Composables existants** : `usePageFormOptions`, `useSectionAPI`, `useSectionUI` → réutilisables
- **Services centralisés** : `TransformService`, `SectionParameterService`, `SectionMapper`
- **Génération dynamique** : `SectionParamsModal` génère les champs depuis `config.parameters` (pas de code dupliqué par template)

---

## 🔗 Fichiers clés

- Modals Pages : `resources/js/Pages/Organismes/section/modals/{Create,Edit}PageModal.vue`
- Modals Sections : `resources/js/Pages/Organismes/section/modals/{CreateSection,SectionParams}Modal.vue`
- Renderer : `resources/js/Pages/Organismes/section/SectionRenderer.vue`
- Templates : `resources/js/Pages/Organismes/section/templates/*/{*Read,*Edit}.vue`
- Services : `resources/js/Utils/Services/{Transform,SectionParameter,SectionMapper}Service.js`
- Composables : `resources/js/Composables/pages/usePageFormOptions.js`, `resources/js/Pages/Organismes/section/composables/{useSectionAPI,useSectionUI}.js`

