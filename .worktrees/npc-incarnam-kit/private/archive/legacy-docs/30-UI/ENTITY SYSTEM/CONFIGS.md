# Guide des Configs

**Version** : 2.0

---

## 🎯 Rôle

Les **configs** génèrent les configurations utilisables par les composants Vue depuis les descriptors.

---

## 📁 Emplacement

```
Utils/Entity/Configs/TableConfig.js       # Configuration tableaux
Utils/Entity/Configs/TableColumnConfig.js  # Configuration colonne
Utils/Entity/Configs/BulkConfig.js        # Configuration bulk
Utils/Entity/Configs/FormConfig.js        # Configuration formulaires
Utils/entity/descriptor-form.js           # Helpers génération
```

---

## 🔑 TableConfig

### Génération depuis descriptors
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const tableConfig = TableConfig.fromDescriptors(descriptors, ctx);
```

### Processus
1. `createColumnFromDescriptor()` : Crée une `TableColumnConfig` depuis un descriptor
2. Extrait : `general.label`, `general.icon`, `table.defaultVisible`, `table.visibleIf`, `table.cell.sizes`
3. Génère la config complète avec headers, visibilité, formatage

---

## 🔑 BulkConfig

### Génération depuis descriptors
```javascript
const bulkConfig = BulkConfig.fromDescriptors(descriptors, ctx);
```

### Processus
1. Itère sur les champs avec `edition.bulk.enabled: true`
2. `createBulkFieldFromDescriptor()` : Crée la config d'un champ bulk
3. Génère la liste des champs quickedit

---

## 🔑 FormConfig

### Génération depuis descriptors
```javascript
const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx);
```

### Processus
1. Itère sur les champs avec `edition.form.*`
2. Extrait : `type`, `required`, `validation`, `options`, `placeholder`, etc.
3. Génère la config complète pour chaque champ

---

## 🔗 Liens

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [DESCRIPTORS.md](./DESCRIPTORS.md) — Guide des descriptors
