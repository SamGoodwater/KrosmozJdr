# Migration — Réorganisation des fichiers Entities/entity

**Date de création** : 2026-01-XX  
**Statut** : ✅ Fichiers créés, ⏳ Migration des imports en cours

---

## 📋 Changements effectués

### Nouveaux emplacements

**Avant :**
```
Entities/entity/
├── EntityDescriptor.js (déprécié, à supprimer)
├── EntityDescriptorHelpers.js (partiellement déprécié)
├── EntityDescriptorConstants.js
├── TableConfig.js
├── TableColumnConfig.js
├── FormConfig.js
├── FormFieldConfig.js
├── BulkConfig.js
├── TableConfigHelpers.js
└── BulkConfigHelpers.js
```

**Après :**
```
Utils/Entity/
├── Configs/
│   ├── TableConfig.js
│   ├── TableColumnConfig.js
│   ├── FormConfig.js
│   ├── FormFieldConfig.js
│   ├── BulkConfig.js
│   ├── TableConfigHelpers.js
│   └── BulkConfigHelpers.js
├── Constants.js (renommé depuis EntityDescriptorConstants.js)
├── Helpers.js (nettoyé depuis EntityDescriptorHelpers.js)
└── Validation.js (extrait depuis EntityDescriptor.js)
```

---

## 🔄 Mapping des imports

### Classes de configuration

| Ancien import | Nouveau import |
|---------------|----------------|
| `from "../entity/TableConfig.js"` | `from "@/Utils/Entity/Configs/TableConfig.js"` |
| `from "../entity/TableColumnConfig.js"` | `from "@/Utils/Entity/Configs/TableColumnConfig.js"` |
| `from "../entity/FormConfig.js"` | `from "@/Utils/Entity/Configs/FormConfig.js"` |
| `from "../entity/FormFieldConfig.js"` | `from "@/Utils/Entity/Configs/FormFieldConfig.js"` |
| `from "../entity/BulkConfig.js"` | `from "@/Utils/Entity/Configs/BulkConfig.js"` |
| `from "../entity/TableConfigHelpers.js"` | `from "@/Utils/Entity/Configs/TableConfigHelpers.js"` |
| `from "../entity/BulkConfigHelpers.js"` | `from "@/Utils/Entity/Configs/BulkConfigHelpers.js"` |

### Constantes et helpers

| Ancien import | Nouveau import |
|---------------|----------------|
| `from "../../Entities/entity/EntityDescriptorConstants.js"` | `from "@/Utils/Entity/Constants.js"` |
| `from "../entity/EntityDescriptorConstants.js"` | `from "@/Utils/Entity/Constants.js"` |
| `from "@/Entities/entity/EntityDescriptorConstants.js"` | `from "@/Utils/Entity/Constants.js"` |
| `from "../../Entities/entity/EntityDescriptorHelpers.js"` | `from "@/Utils/Entity/Helpers.js"` |
| `from "../entity/EntityDescriptorHelpers.js"` | `from "@/Utils/Entity/Helpers.js"` |
| `from "@/Entities/entity/EntityDescriptorHelpers.js"` | `from "@/Utils/Entity/Helpers.js"` |

### Validation

| Ancien import | Nouveau import |
|---------------|----------------|
| `from "../entity/EntityDescriptor.js"` (pour validation) | `from "@/Utils/Entity/Validation.js"` |

---

## 📝 Fichiers à mettre à jour

### Fichiers de configuration d'entités (59 fichiers)

Tous les fichiers `*TableConfig.js`, `*FormConfig.js`, `*BulkConfig.js` dans :
- `Entities/resource/`
- `Entities/resource-type/`
- `Entities/item/`
- `Entities/consumable/`
- `Entities/spell/`
- `Entities/monster/`
- `Entities/creature/`
- `Entities/npc/`
- `Entities/classe/`
- `Entities/campaign/`
- `Entities/scenario/`
- `Entities/attribute/`
- `Entities/panoply/`
- `Entities/capability/`
- `Entities/specialization/`
- `Entities/shop/`

### Formatters (3 fichiers)

- `Utils/Formatters/RarityFormatter.js`
- `Utils/Formatters/VisibilityFormatter.js`
- `Utils/Formatters/HostilityFormatter.js`

### Composants Vue (3 fichiers)

- `Pages/Organismes/table/TanStackTable.vue`
- `Pages/Organismes/entity/EntityQuickEditPanel.vue`
- `Pages/Organismes/entity/EntityModal.vue`

### Autres (2 fichiers)

- `Utils/entity/resolveEntityViewComponent.js`
- `Composables/entity/useEntityActions.js`

---

## 🔧 Script de migration

```bash
# Mettre à jour les imports dans tous les fichiers
find resources/js -type f \( -name "*.js" -o -name "*.vue" \) -exec sed -i \
  -e 's|from "../entity/TableConfig.js"|from "@/Utils/Entity/Configs/TableConfig.js"|g' \
  -e 's|from "../entity/TableColumnConfig.js"|from "@/Utils/Entity/Configs/TableColumnConfig.js"|g' \
  -e 's|from "../entity/FormConfig.js"|from "@/Utils/Entity/Configs/FormConfig.js"|g' \
  -e 's|from "../entity/FormFieldConfig.js"|from "@/Utils/Entity/Configs/FormFieldConfig.js"|g' \
  -e 's|from "../entity/BulkConfig.js"|from "@/Utils/Entity/Configs/BulkConfig.js"|g' \
  -e 's|from "../entity/TableConfigHelpers.js"|from "@/Utils/Entity/Configs/TableConfigHelpers.js"|g' \
  -e 's|from "../entity/BulkConfigHelpers.js"|from "@/Utils/Entity/Configs/BulkConfigHelpers.js"|g' \
  -e 's|from.*EntityDescriptorConstants.js|from "@/Utils/Entity/Constants.js"|g' \
  -e 's|from.*EntityDescriptorHelpers.js|from "@/Utils/Entity/Helpers.js"|g' \
  {} \;
```

---

## ✅ Checklist

- [x] Créer `Utils/Entity/Configs/` avec toutes les classes de config
- [x] Créer `Utils/Entity/Constants.js` (nettoyé)
- [x] Créer `Utils/Entity/Helpers.js` (nettoyé des fonctions dépréciées)
- [x] Créer `Utils/Entity/Validation.js` (extrait)
- [ ] Mettre à jour tous les imports (59 fichiers)
- [ ] Supprimer `Entities/entity/` (dossier complet)
- [ ] Vérifier que les tests passent
- [ ] Vérifier que l'application fonctionne

---

## 📚 Références

- [ANALYSE_CLEANUP_ENTITY_FOLDER.md](./ANALYSE_CLEANUP_ENTITY_FOLDER.md) — Analyse détaillée
- [REORGANISATION_ENTITY_FILES.md](./REORGANISATION_ENTITY_FILES.md) — Plan de réorganisation
