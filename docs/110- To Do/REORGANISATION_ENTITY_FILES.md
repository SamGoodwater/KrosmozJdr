# Réorganisation des fichiers Entities/entity

**Date de création** : 2026-01-XX  
**Objectif** : Réorganiser les fichiers dans une structure plus logique

---

## 🎯 Nouvelle structure proposée

### Structure actuelle (illogique)
```
Entities/entity/
├── EntityDescriptor.js (déprécié)
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

### Structure nouvelle (logique)
```
Utils/Entity/
├── Configs/                    # Classes builder pour les configurations
│   ├── TableConfig.js
│   ├── TableColumnConfig.js
│   ├── FormConfig.js
│   ├── FormFieldConfig.js
│   ├── BulkConfig.js
│   ├── TableConfigHelpers.js   # Helpers pour générer TableConfig
│   └── BulkConfigHelpers.js    # Helpers pour générer BulkConfig
├── Constants.js                # Constantes partagées
├── Helpers.js                  # Fonctions utilitaires
└── Validation.js               # Validation des descriptors
```

---

## 📋 Plan de migration

### 1. Créer la nouvelle structure

**Utils/Entity/Configs/** — Classes de configuration
- `TableConfig.js` — Builder pour configurer un tableau
- `TableColumnConfig.js` — Builder pour configurer une colonne
- `FormConfig.js` — Builder pour configurer un formulaire
- `FormFieldConfig.js` — Builder pour configurer un champ
- `BulkConfig.js` — Builder pour configurer le bulk edit
- `TableConfigHelpers.js` — Helpers pour générer TableConfig depuis descriptors
- `BulkConfigHelpers.js` — Helpers pour générer BulkConfig depuis descriptors

**Utils/Entity/** — Utilitaires
- `Constants.js` — Constantes partagées (RARITY_OPTIONS, etc.)
- `Helpers.js` — Fonctions utilitaires (truncate, getCurrentScreenSize, etc.)
- `Validation.js` — Validation des descriptors

### 2. Actions de nettoyage

**À supprimer :**
- ❌ `EntityDescriptor.js` — Complètement obsolète

**À nettoyer :**
- ⚠️ `EntityDescriptorHelpers.js` → Supprimer les fonctions dépréciées (formatRarity, formatVisibility, formatHostility, formatDate)

**À extraire :**
- ✅ Validation depuis `EntityDescriptor.js` → `Validation.js`

### 3. Mise à jour des imports

Tous les fichiers qui importent depuis `Entities/entity/` devront être mis à jour :
- `Entities/entity/TableConfig` → `Utils/Entity/Configs/TableConfig`
- `Entities/entity/EntityDescriptorHelpers` → `Utils/Entity/Helpers`
- `Entities/entity/EntityDescriptorConstants` → `Utils/Entity/Constants`
- etc.

---

## ✅ Avantages

1. **Structure logique** : Les configs sont dans `Configs/`, les utils dans `Utils/Entity/`
2. **Pas de dossier "entity" dans Entities** : Plus clair
3. **Séparation claire** : Configs, Constants, Helpers, Validation
4. **Cohérence** : Aligné avec la structure existante `Utils/entity/`

---

## 📝 Checklist

- [ ] Créer `Utils/Entity/Configs/`
- [ ] Déplacer les classes de config dans `Configs/`
- [ ] Déplacer les helpers de config dans `Configs/`
- [ ] Créer `Utils/Entity/Constants.js` (renommé depuis EntityDescriptorConstants)
- [ ] Créer `Utils/Entity/Helpers.js` (nettoyé depuis EntityDescriptorHelpers)
- [ ] Créer `Utils/Entity/Validation.js` (extrait depuis EntityDescriptor)
- [ ] Supprimer `EntityDescriptor.js`
- [ ] Supprimer le dossier `Entities/entity/`
- [ ] Mettre à jour tous les imports
- [ ] Vérifier que les tests passent
