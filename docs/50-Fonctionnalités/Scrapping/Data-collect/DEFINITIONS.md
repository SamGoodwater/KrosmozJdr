# Définitions - Service DataCollect

## 📋 Vue d'ensemble

Le service `DataCollect` est responsable de la récupération de données brutes depuis l'API DofusDB. Il gère la collecte, le cache, la limitation de débit et la gestion des erreurs pour tous les types d'entités.

## 🌐 API DofusDB

### **Informations générales**
- **URL de base** : `https://api.dofusdb.fr`
- **Langues supportées** : `fr`, `en`, `de`, `es`, `pt`
- **Format de réponse** : JSON
- **Limite par défaut** : 10 entités par requête
- **Pagination** : Supportée via `$skip` et `$limit`

### **Structure des réponses**
```json
{
  "total": 20853,
  "limit": 10,
  "skip": 0,
  "data": [...]
}
```

## 🏷️ Types d'objets (Items)

### **Hiérarchie complète des types d'objets**

#### **SuperType 1 : Amulette**
- **Type 1** : Arme (322 objets)

#### **SuperType 2 : Arme**
- **Type 2** : Arc (76 objets)
- **Type 3** : Bouclier (78 objets)
- **Type 4** : Bâton (95 objets)
- **Type 5** : Dague (77 objets)
- **Type 6** : Épée (122 objets)
- **Type 7** : Marteau (95 objets)
- **Type 8** : Pelle (60 objets)
- **Type 19** : Hache (76 objets)
- **Type 20** : Outil (31 objets)

#### **SuperType 3 : Anneau**
- **Type 9** : Anneau (370 objets)

#### **SuperType 4 : Ceinture**
- **Type 10** : Amulette (354 objets)

#### **SuperType 5 : Bottes**
- **Type 11** : Ceinture (364 objets)

#### **SuperType 6 : Consommable**
- **Type 12** : Potion (153 objets)
- **Type 13** : Parchemin d'expérience (36 objets)
- **Type 14** : Objet de dons (48 objets)

#### **SuperType 9 : Ressource**
- **Type 15** : Ressource diverse (484 objets)
- **Type 35** : Fleur (37 objets)

#### **SuperType 10 : Chapeau**
- **Type 16** : Chapeau (366 objets)

#### **SuperType 11 : Cape**
- **Type 17** : Cape (301 objets)

#### **SuperType 12 : Familier**
- **Type 18** : Familier (124 objets)

#### **SuperType 14 : Objet de quête**
- **Type 205** : Monture (55 objets)

#### **SuperType 26 : Certificat**
- **Type 203** : Cosmétique (3,011 objets)

### **Structure d'un objet**
```json
{
  "_id": "674e524064788cc741418521",
  "id": 270,
  "typeId": 2,
  "iconId": 2005,
  "level": 58,
  "realWeight": 10,
  "price": 5800,
  "name": {
    "fr": "Xaveur",
    "en": "Xaver",
    "de": "Xaveur",
    "es": "Javiador",
    "pt": "Xaveur"
  },
  "description": {
    "fr": "Le Xaveur est un arc efficace...",
    "en": "Xaver is an efficient bow...",
    "de": "Der Xaveur ist ein sehr wirkungsvoller Bogen...",
    "es": "El Javiador es un arco eficaz...",
    "pt": "O Xaveur é um arco eficiente..."
  },
  "type": {
    "_id": "674e524064788cc741418405",
    "id": 2,
    "superTypeId": 2,
    "categoryId": 0,
    "name": {
      "fr": "Arc",
      "en": "Bow",
      "de": "Bogen",
      "es": "Arco",
      "pt": "Arco"
    },
    "superType": {
      "_id": "60abc36ba664687005dfd1bb",
      "id": 2,
      "name": {
        "fr": "Arme",
        "en": "Weapon",
        "de": "Waffe",
        "es": "Arma",
        "pt": "Arma"
      }
    }
  },
  "effects": [...],
  "img": "https://api.dofusdb.fr/img/items/2005.png"
}
```

## 🎭 Classes (Breeds)

### **Endpoint**
```
GET /breeds?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 19,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e523d64788cc7414157b1",
      "id": 1,
      "guideItemId": 244,
      "maleLook": "{1|10||53}",
      "femaleLook": "{1|11||52}",
      "creatureBonesId": 15,
      "maleArtwork": 198,
      "femaleArtwork": 199,
      "statsPointsForStrength": [[0, 1], [100, 2], [200, 3], [300, 4]],
      "statsPointsForIntelligence": [[0, 1], [100, 2], [200, 3], [300, 4]],
      "statsPointsForChance": [[0, 1]],
      "description": {
        "id": "12345",
        "fr": "Description de la classe...",
        "en": "Class description...",
        "de": "Klassenbeschreibung...",
        "es": "Descripción de la clase...",
        "pt": "Descrição da classe..."
      }
    }
  ]
}
```

**Note importante** : Les classes n'ont pas de champ `name` direct, mais une `description` multilingue.

## 🐉 Monstres

### **Endpoint**
```
GET /monsters?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 4900,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e523e64788cc74141615c",
      "id": 31,
      "name": {
        "fr": "Nom du monstre",
        "en": "Monster name",
        "de": "Monster Name",
        "es": "Nombre del monstruo",
        "pt": "Nome do monstro"
      },
      "level": 1,
      "lifePoints": 100,
      "actionPoints": 6,
      "movementPoints": 3,
      "experience": 10,
      "kamas": 5,
      "img": "https://api.dofusdb.fr/img/monsters/31.png"
    }
  ]
}
```

## 🔮 Sorts (Spells)

### **Endpoint**
```
GET /spells?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 16187,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e525164788cc741445990",
      "id": 24510,
      "typeId": 2699,
      "iconId": 12133,
      "spellLevels": [62502],
      "name": {
        "fr": "Téléfrag",
        "en": "Telefrag",
        "de": "Telefrag",
        "es": "Telefrag",
        "pt": "Telefrag"
      },
      "description": {
        "fr": "L'état Téléfrag permet d'appliquer des effets supplémentaires...",
        "en": "The Telefrag state allows you to apply additional effects...",
        "de": "Der Zustand „Telefrag" erlaubt das Wirken zusätzlicher Effekte...",
        "es": "El estado Telefrag permite aplicar efectos adicionales...",
        "pt": "O estado Telefrag permite aplicar efeitos adicionais..."
      },
      "img": "https://api.dofusdb.fr/img/spells/sort_12133.png"
    }
  ]
}
```

## 📚 Niveaux de sorts (Spell Levels)

### **Endpoint**
```
GET /spell-levels?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 33019,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e524c64788cc74143836f",
      "id": 1001,
      "spellId": 201,
      "grade": 1,
      "spellBreed": 425,
      "apCost": 4,
      "minRange": 1,
      "range": 2,
      "criticalHitProbability": 10,
  "effects": [
    {
          "effectUid": 2776,
          "effectId": 98,
          "order": 0,
          "targetId": 0,
          "targetMask": "a,A",
          "duration": 0,
          "random": 0,
          "group": 0,
          "modificator": 0,
          "dispellable": 1,
          "delay": 0,
          "triggers": "I",
          "effectElement": 4,
          "spellId": 201,
          "zoneDescr": {
            "cellIds": [],
            "shape": 80,
            "param1": 1,
            "param2": 0,
            "damageDecreaseStepPercent": 10,
            "maxDamageDecreaseApplyCount": 4
          }
        }
      ]
    }
  ]
}
```

## ⚡ Effets

### **Endpoint**
```
GET /effects?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 823,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e523e64788cc74141615c",
      "id": 2,
      "iconId": 0,
      "characteristic": 0,
      "category": 0,
      "characteristicOperator": "",
      "showInTooltip": false,
      "useDice": false,
      "forceMinMax": false,
      "boost": false,
      "active": false,
      "oppositeId": 0,
      "theoreticalPattern": 1,
      "showInSet": false,
      "bonusType": 0,
      "useInFight": false,
      "effectPriority": 0,
      "effectPowerRate": 0,
      "elementId": -1,
      "isInPercent": false,
      "hideValueInTooltip": true,
      "textIconReferenceId": 0,
      "effectTriggerDuration": 0,
      "actionFiltersId": [],
  "description": {
        "id": "1098969",
        "fr": "Téléporte sur la map ciblée",
        "en": "Teleports to the targeted map",
        "de": "Teleportiert auf die angezielte Karte",
        "es": "Teletransporta al mapa objetivo",
        "pt": "Teletransporta para o mapa selecionado"
      }
    }
  ]
}
```

## 🎁 Ensembles d'items (Item Sets)

### **Endpoint**
```
GET /item-sets?lang=fr&$limit=10
```

### **Structure de réponse**
```json
{
  "total": 856,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "674e523f64788cc7414180d3",
      "id": 1,
      "items": [
        {
          "_id": "674e524064788cc741418a03",
          "id": 2411,
          "typeId": 16,
          "iconId": 16041,
          "level": 20,
          "realWeight": 10,
          "price": 1,
          "itemSetId": 1,
          "possibleEffects": [...],
          "name": {
            "fr": "Nom de l'objet",
            "en": "Item name",
            "de": "Gegenstand Name",
            "es": "Nombre del objeto",
            "pt": "Nome do item"
          }
        }
      ]
    }
  ]
}
```

## 🔧 Paramètres de requête

### **Paramètres de base**
- `lang` : Langue de la réponse (`fr`, `en`, `de`, `es`, `pt`)
- `$limit` : Nombre maximum d'entités à retourner
- `$skip` : Nombre d'entités à ignorer (pagination)

### **Paramètres spécifiques aux objets**
- `typeId` : Filtre par type d'objet (1-20, 35, 203, 205)
- `superTypeId` : Filtre par super-type
- `categoryId` : Filtre par catégorie
- `level` : Filtre par niveau minimum
- `price` : Filtre par prix minimum

### **Exemples de requêtes**
```bash
# Obtenir 5 armes de niveau 20+
GET /items?typeId=1&level=20&lang=fr&$limit=5

# Obtenir 10 ressources
GET /items?typeId=15&lang=fr&$limit=10

# Obtenir 5 sorts avec pagination
GET /spells?lang=fr&$limit=5&$skip=10

# Obtenir tous les anneaux
GET /items?typeId=9&lang=fr&$limit=100
```

## 📊 Métadonnées et limites

### **Limites de l'API**
- **Temps de réponse** : Variable (5-15 secondes selon la complexité)
- **Taille des réponses** : Jusqu'à plusieurs MB pour les grandes collections
- **Rate limiting** : Non documenté, mais recommandé de respecter 1 requête/seconde

### **Gestion des erreurs**
- **404** : Entité non trouvée
- **500** : Erreur serveur
- **Timeout** : Après 15-30 secondes

### **Recommandations de collecte**
- **Objets** : Collecter par typeId pour éviter les timeouts
- **Entités** : Utiliser la pagination avec des limites raisonnables
- **Cache** : Mettre en cache les réponses pour éviter les requêtes répétées
- **Retry** : Implémenter une logique de retry avec backoff exponentiel
