# Guide des Renderers

**Version** : 2.0

---

## 🎯 Rôle

Les **renderers** sont des composants Vue génériques qui utilisent les configs pour rendre les interfaces.

---

## 📁 Emplacement

```
Pages/Organismes/entity/EntityTanStackTable.vue    # Tableau principal
Pages/Organismes/entity/EntityModal.vue             # Modal d'affichage
Pages/Organismes/entity/EntityQuickEditPanel.vue    # Panneau quickedit
Pages/Organismes/entity/EntityActions.vue          # Menu d'actions
```

---

## 🔑 EntityTanStackTable

### Fonctionnement
1. Reçoit `tableConfig` (depuis `TableConfig.build()`)
2. Pour chaque cellule : appelle `entity.toCell(fieldKey)` pour générer le formatage
3. Utilise `CellRenderer` pour afficher les cellules (badge, text, route, image, etc.)

### Props
- `entity-type` : Type d'entité (ex: "resources")
- `table-config` : Configuration du tableau
- `response-adapter` : Adapter pour transformer les réponses backend

---

## 🔑 EntityModal

### Fonctionnement
1. Charge dynamiquement les vues via `resolveEntityViewComponent(entityType, view)`
2. Passe l'entité au composant de vue
3. Gère la navigation entre les vues (Large, Compact, Minimal, Text)

### Props
- `entity-type` : Type d'entité
- `entity` : Données de l'entité
- `view` : Vue à afficher (large, compact, minimal, text)

---

## 🔑 EntityQuickEditPanel

### Fonctionnement
1. Charge `EntityQuickEdit.vue` (générique) ou `ResourceQuickEdit.vue` (spécifique)
2. Utilise `useBulkEditPanel` pour gérer l'agrégation et le dirty state
3. Gère la soumission via `buildPayload()`

### Props
- `entity-type` : Type d'entité
- `selected-entities` : Entités sélectionnées
- `is-admin` : Permissions admin

---

## 🔗 Liens

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [VIEWS.md](./VIEWS.md) — Guide des vues
