# Guide des Models

**Version** : 2.0

---

## 🎯 Rôle

Les **models** encapsulent la logique métier et le formatage des données. Ils fournissent :
- Propriétés normalisées (id, created_at, etc.)
- Méthode `toCell()` pour générer les cellules formatées
- Gestion des permissions (`can.*`)

---

## 📁 Emplacement

```
Models/BaseModel.js          # Classe de base
Models/Entity/Resource.js    # Modèle spécifique
Models/Entity/*.js           # Autres modèles
```

---

## 🔑 Méthode principale : `toCell()`

### Signature
```javascript
toCell(fieldKey, options = {}) → { type, value, params }
```

### Processus
1. Vérifie le cache (`_cellCache`)
2. Appelle `getFormatter(fieldKey)` → trouve le formatter
3. Appelle `Formatter.toCell(value, options)`
4. Met en cache le résultat
5. Retourne l'objet `Cell`

### Exemple
```javascript
const cell = entity.toCell('rarity', { size: 'md' });
// → { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }
```

---

## 📋 Propriétés communes

- `id` : Identifiant unique
- `created_at`, `updated_at` : Dates
- `can.*` : Permissions depuis le backend

---

## 🔗 Liens

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [BaseModel.js](../../resources/js/Models/BaseModel.js) — Code source
