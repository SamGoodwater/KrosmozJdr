# Optimisations UI — Janvier 2026

**Date** : 2026-01-06  
**Statut** : ✅ **TERMINÉ**

---

## 📋 Résumé

Optimisations de l'interface utilisateur pour améliorer l'expérience avec les tableaux et les actions d'entité.

---

## ✅ Optimisations réalisées

### 1. Checkboxes dans les tableaux

**Problème** : Les checkboxes n'apparaissaient que sur les lignes sélectionnées, créant un décalage visuel.

**Solution** :
- ✅ Affichage des checkboxes sur **toutes les lignes** dès qu'au moins une ligne est sélectionnée
- ✅ Taille réduite : `size="xs"` (au lieu de `sm`) et `w-8` (au lieu de `w-12`)
- ✅ Logique `showSelectionCheckboxes` : affiche les checkboxes si `selectedCount > 0` (mode `auto`)

**Fichiers modifiés** :
- `resources/js/Pages/Molecules/table/TanStackTableHeader.vue`
- `resources/js/Pages/Molecules/table/TanStackTableRow.vue`
- `resources/js/Pages/Molecules/table/TanStackTableSkeletonBody.vue`
- `resources/js/Pages/Organismes/table/TanStackTable.vue`

---

### 2. Layout full-width pour les tableaux

**Problème** : Les tableaux étaient limités à `max-w-4xl` (896px), ne profitant pas de toute la largeur disponible.

**Solution** :
- ✅ Retrait de `max-w-4xl` dans le layout principal
- ✅ Utilisation de `w-full` pour utiliser toute la largeur disponible
- ✅ Scroll horizontal automatique avec `overflow-x-auto` sur les conteneurs de tableaux
- ✅ Responsive préservé : le tableau ne passe jamais sous le menu de gauche

**Fichiers modifiés** :
- `resources/js/Pages/Layouts/Main.vue`
- `resources/js/Pages/Pages/entity/*/Index.vue` (16 pages)

---

### 3. Nom de l'entité dans les menus

**Problème** : Dans les menus dropdown et contextuels, il n'était pas évident de savoir quelle entité était sélectionnée.

**Solution** :
- ✅ Affichage du nom de l'entité en haut des menus dropdown et contextuels
- ✅ Style discret : `text-xs text-base-content/60 font-medium`
- ✅ Bordure de séparation pour une meilleure lisibilité

**Fichiers modifiés** :
- `resources/js/Pages/Molecules/entity/EntityActionsDropdown.vue`
- `resources/js/Pages/Organismes/entity/EntityActions.vue`

---

### 4. Actions contextuelles améliorées

**Problème** : Certaines actions n'avaient pas de sens dans certains contextes (ex: "Ouvrir" quand on est déjà sur la page).

**Solution** :
- ✅ Masquage de `view` et `quick-view` quand `inPage: true`
- ✅ Masquage de `edit` quand `inModal: true`
- ✅ Masquage de `quick-edit` quand `inPage: true`
- ✅ Action `expand` visible uniquement dans les modaux
- ✅ Labels et tooltips dynamiques selon le contexte

**Fichiers modifiés** :
- `resources/js/Entities/entity-actions-config.js`
- `resources/js/Composables/entity/useEntityActions.js`
- `resources/js/Pages/Molecules/entity/EntityViewLarge.vue`
- `resources/js/Pages/Organismes/entity/EntityModal.vue`

---

## 📊 Impact

### Avant
- ❌ Checkboxes visibles seulement sur les lignes sélectionnées
- ❌ Tableaux limités à 896px de largeur
- ❌ Pas d'indication de l'entité sélectionnée dans les menus
- ❌ Actions redondantes affichées dans certains contextes

### Après
- ✅ Checkboxes visibles sur toutes les lignes dès qu'une est sélectionnée
- ✅ Tableaux utilisent toute la largeur disponible
- ✅ Nom de l'entité affiché dans les menus
- ✅ Actions contextuelles intelligentes

---

## 🔧 Détails techniques

### Checkboxes

```vue
<!-- Header -->
<th v-if="showSelection" class="w-8">
  <CheckboxCore size="xs" ... />
</th>

<!-- Row -->
<td v-if="showSelection" class="w-8">
  <CheckboxCore size="xs" ... />
</td>
```

### Layout

```vue
<!-- Main.vue -->
<div class="flex-1 w-full p-4">
  <Container fluid>
    <slot />
  </Container>
</div>

<!-- Index.vue -->
<div class="space-y-6 pb-8 w-full">
  <div class="min-w-0 overflow-x-auto">
    <EntityTanStackTable ... />
  </div>
</div>
```

### Nom dans menus

```vue
<!-- EntityActionsDropdown.vue -->
<li v-if="showEntityName" class="px-3 py-2 mb-1 border-b border-base-300">
  <div class="text-xs text-base-content/60 font-medium truncate">
    {{ entityName }}
  </div>
</li>
```

### Actions contextuelles

```javascript
// entity-actions-config.js
visibleIf: (context) => {
  if (context?.inModal) return false;
  if (context?.inPage) return false;
  return true;
}
```

---

## 📚 Documentation mise à jour

- ✅ `docs/30-UI/ENTITY_ACTIONS_GUIDE.md` — Guide d'utilisation des actions
- ✅ `docs/30-UI/TANSTACK_TABLE.md` — Documentation des tableaux
- ✅ `docs/100- Done/OPTIMISATIONS_UI_2026_01.md` — Ce document

---

## 🧹 Nettoyage

### Fichiers supprimés
- `bootstrap/app.php.tmp` — Fichier temporaire inutile

### Fichiers conservés (compatibilité)
- `resources/js/Pages/Organismes/entity/EntityActionsMenu.vue` — Wrapper legacy `@deprecated`, conservé pour compatibilité

---

## ✅ Tests

- ✅ Aucune erreur de linting
- ✅ Toutes les pages Index.vue optimisées (16/16)
- ✅ Checkboxes fonctionnelles sur toutes les lignes
- ✅ Scroll horizontal fonctionnel
- ✅ Actions contextuelles correctes

---

## 📝 Notes

- Les pages `Edit.vue` et `Show.vue` conservent `Container` car elles n'ont pas de tableaux larges
- `EntityActionsMenu.vue` est marqué `@deprecated` mais conservé pour compatibilité avec le code existant
- Le layout responsive est préservé : le tableau ne passe jamais sous le menu de gauche
