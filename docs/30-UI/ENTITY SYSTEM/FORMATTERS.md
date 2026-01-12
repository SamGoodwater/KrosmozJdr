# Guide des Formatters

**Version** : 2.0

---

## 🎯 Rôle

Les **formatters** centralisent le formatage des valeurs en labels, badges et cellules.

---

## 📁 Emplacement

```
Utils/Formatters/BaseFormatter.js      # Classe abstraite
Utils/Formatters/FormatterRegistry.js # Registre centralisé
Utils/Formatters/*.js                 # Formatters spécifiques
Utils/Entity/SharedConstants.js       # Constantes partagées
```

---

## 🔑 Structure d'un formatter

```javascript
class RarityFormatter extends BaseFormatter {
  static name = 'RarityFormatter';
  static fieldKeys = ['rarity'];
  
  static format(value) {
    // → "Rare"
  }
  
  static toCell(value, options) {
    // → { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }
  }
}
```

---

## 📋 Formatters disponibles

- `RarityFormatter` : Rareté (0-5) → badges colorés
- `LevelFormatter` : Niveau (1-30) → badges avec dégradé
- `VisibilityFormatter` : Visibilité → badges
- `BooleanFormatter` : Booléens → icônes/badges
- `DateFormatter` : Dates → formatage
- `PriceFormatter` : Prix → formatage
- Etc.

---

## 🎨 Constantes partagées

`SharedConstants.js` centralise :
- `FIELD_LABELS` : Labels traduits
- `FIELD_ICONS` : Icônes FontAwesome
- `LEVEL_COLORS` : Dégradé niveaux 1-30
- `RARITY_GRADIENT` : Dégradé rareté 0-5
- `USER_ROLES` : Rôles avec traductions et couleurs

---

## 🔗 Liens

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [SharedConstants.js](../../resources/js/Utils/Entity/SharedConstants.js) — Code source
