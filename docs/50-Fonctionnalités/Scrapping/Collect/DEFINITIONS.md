# Définitions des données DofusDB - Service Data-collect

## 🎯 Objectif

Ce document définit les structures de données et les endpoints de l'API DofusDB que le service Collect doit récupérer. Il décrit le format exact des données brutes sans aucune transformation, pour permettre une compréhension complète de la source de données.

## 📡 API DofusDB

### **Base URL**
```
https://api.dofusdb.fr
```

### **Paramètres globaux**
- `lang=fr` : Langue des données (fr, en, de, es, pt)
- `$populate=false` : Désactive la population des relations
- `$limit=10` : Limite le nombre de résultats
- `$skip=0` : Pagination (offset)
- `$sort[id]=-1` : Tri par ID décroissant

## 🏗️ Structure des endpoints

### **1. Monstres (`/monsters`)**

#### **Endpoint de base**
```
GET /monsters?$sort[id]=-1&$limit=10&$populate=false&$skip=0&lang=fr
```

#### **Structure de réponse**
```json
{
  "total": 4900,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "687f71631c81517356066b52",
      "m_flags": 40913,
      "id": 8210,
      "gfxId": 2557,
      "race": 50,
      "grades": [
        {
          "bonusCharacteristics": {
            "lifePoints": 0,
            "strength": 0,
            "wisdom": 0,
            "chance": 0,
            "agility": 0,
            "intelligence": 0,
            "earthResistance": 0,
            "fireResistance": 0,
            "waterResistance": 0,
            "airResistance": 0,
            "neutralResistance": 0,
            "tackleEvade": 0,
            "tackleBlock": 0,
            "bonusEarthDamage": 0,
            "bonusFireDamage": 0,
            "bonusWaterDamage": 0,
            "bonusAirDamage": 0,
            "aPRemoval": 0
          },
          "grade": 1,
          "monsterId": 8210,
          "level": 200,
          "lifePoints": 6600,
          "actionPoints": 12,
          "movementPoints": 6,
          "vitality": 0,
          "paDodge": 0,
          "pmDodge": 0,
          "wisdom": 800,
          "earthResistance": 0,
          "airResistance": 0,
          "fireResistance": 0,
          "waterResistance": 0,
          "neutralResistance": 20,
          "gradeXp": 0,
          "damageReflect": 0,
          "hiddenLevel": 0,
          "strength": 800,
          "intelligence": 800,
          "chance": 800,
          "agility": 800,
          "startingSpellId": 0,
          "bonusRange": 0
        }
      ],
      "look": "{9577||1=#6C5634,2=#6A3665,3=#C02C1E,4=#7F7F7F,5=#7F7F7F,6=#D94125|110}",
      "drops": [],
      "temporisDrops": [],
      "subareas": [],
      "spells": [31671, 31672, 31673, 31674, 31675, 31676],
      "spellGrades": [
        "1,200;1,200;1,200;1,200;1,200;1,null;1,null;1,null;1,null"
      ],
      "favoriteSubareaId": 0,
      "correspondingMiniBossId": 0,
      "speedAdjust": 0,
      "creatureBoneId": 0,
      "summonCost": 1,
      "incompatibleIdols": [],
      "incompatibleChallenges": [],
      "aggressiveZoneSize": 3,
      "aggressiveLevelDiff": 50,
      "aggressiveImmunityCriterion": "PO=9910",
      "aggressiveAttackDelay": 10000,
      "scaleGradeRef": 5,
      "characRatios": [
        [0, 1.5],
        [10, 0.9999439716339111],
        [11, 0],
        [12, 0.9999439716339111],
        [13, 0.9999439716339111],
        [14, 0.9999439716339111],
        [15, 0.9999439716339111],
        [19, 1],
        [23, 1],
        [25, 0.0012499999720603228]
      ],
      "name": {
        "id": "1144856",
        "de": "Grimaslan",
        "en": "Grimaslan",
        "es": "Grimaslan",
        "fr": "Grimaslan",
        "pt": "Grimaslan"
      },
      "useSummonSlot": true,
      "useBombSlot": false,
      "isBoss": false,
      "isMiniBoss": false,
      "isQuestMonster": true,
      "fastAnimsFun": false,
      "canPlay": true,
      "canTackle": true,
      "canBePushed": true,
      "canSwitchPos": true,
      "canSwitchPosOnTarget": true,
      "canBeCarried": true,
      "canUsePortal": true,
      "allIdolsDisabled": false,
      "useRaceValues": false,
      "soulCaptureForbidden": true,
      "className": "Monsters",
      "m_id": 8210,
      "slug": {
        "de": "grimaslan",
        "en": "grimaslan",
        "es": "grimaslan",
        "fr": "grimaslan",
        "pt": "grimaslan"
      },
      "tags": ["boostShield", "fire", "poi"],
      "createdAt": "2024-12-03T00:35:17.778Z",
      "updatedAt": "2025-07-23T18:52:33.312Z",
      "img": "https://api.dofusdb.fr/img/monsters/2506.png"
    }
  ]
}
```

#### **Champs principaux**
- `id` : Identifiant unique du monstre
- `name` : Nom multilingue du monstre
- `grades` : Array des grades du monstre (1-5)
- `spells` : Array des IDs des sorts
- `race` : ID de la race du monstre
- `isBoss` : Booléen pour les boss
- `isQuestMonster` : Booléen pour les monstres de quête

### **2. Objets (`/items`)**

#### **Endpoint de base**
```
GET /items?typeId[$ne]=203&$sort=-id&level[$gte]=0&level[$lte]=200&$and[0][effects][$elemMatch][characteristic]=23&$skip=20&lang=fr
```

#### **Paramètres de recherche**
- `typeId[$ne]=203` : Exclure le type 203
- `level[$gte]=0` : Niveau minimum
- `level[$lte]=200` : Niveau maximum
- `$and[0][effects][$elemMatch][characteristic]=23` : Filtre sur les effets

#### **Structure de réponse (à compléter)**
```json
{
  "total": 0,
  "limit": 10,
  "skip": 0,
  "data": [
    {
      "_id": "string",
      "id": "number",
      "name": {
        "id": "string",
        "fr": "string",
        "en": "string",
        "de": "string",
        "es": "string",
        "pt": "string"
      },
      "typeId": "number",
      "level": "number",
      "description": {
        "id": "string",
        "fr": "string",
        "en": "string",
        "de": "string",
        "es": "string",
        "pt": "string"
      },
      "effects": [
        {
          "characteristic": "number",
          "value": "number",
          "min": "number",
          "max": "number"
        }
      ],
      "img": "string",
      "createdAt": "string",
      "updatedAt": "string"
    }
  ]
}
```

### **3. Sorts (`/spells`)**

#### **Endpoint de base**
```
GET /spells/23731?lang=fr
```

#### **Structure de réponse (à compléter)**
```json
{
  "_id": "string",
  "id": "number",
  "name": {
    "id": "string",
    "fr": "string",
    "en": "string",
    "de": "string",
    "es": "string",
    "pt": "string"
  },
  "description": {
    "id": "string",
    "fr": "string",
    "en": "string",
    "de": "string",
    "es": "string",
    "pt": "string"
  },
  "breedId": "number",
  "img": "string",
  "createdAt": "string",
  "updatedAt": "string"
}
```

### **4. Niveaux de sorts (`/spell-levels`)**

#### **Endpoint de base**
```
GET /spell-levels/42195?lang=fr
```

#### **Structure de réponse (à compléter)**
```json
{
  "_id": "string",
  "id": "number",
  "spellId": "number",
  "grade": "number",
  "apCost": "number",
  "range": "number",
  "criticalHitProbability": "number",
  "effects": [
    {
      "characteristic": "number",
      "value": "number",
      "min": "number",
      "max": "number"
    }
  ],
  "createdAt": "string",
  "updatedAt": "string"
}
```

### **5. Effets (`/effects`)**

#### **Endpoint de base**
```
GET /effects/243?lang=fr
```

#### **Structure de réponse (à compléter)**
```json
{
  "_id": "string",
  "id": "number",
  "characteristic": "number",
  "description": {
    "id": "string",
    "fr": "string",
    "en": "string",
    "de": "string",
    "es": "string",
    "pt": "string"
  },
  "createdAt": "string",
  "updatedAt": "string"
}
```

### **6. Looks (`/look`)**

#### **Endpoint de base**
```
GET /look?breedId=1&sexe=m&itemIds[]=30536&itemIds[]=32107&lang=fr
```

#### **Paramètres**
- `breedId=1` : ID de la classe
- `sexe=m` : Sexe (m/f)
- `itemIds[]=30536` : Array des IDs d'objets équipés

#### **Structure de réponse (à compléter)**
```json
{
  "_id": "string",
  "id": "string",
  "breedId": "number",
  "sexe": "string",
  "itemIds": ["number"],
  "look": "string",
  "createdAt": "string",
  "updatedAt": "string"
}
```

## 🔍 Paramètres de recherche avancés

### **Opérateurs de comparaison**
- `[$eq]` : Égal
- `[$ne]` : Différent
- `[$gt]` : Supérieur
- `[$gte]` : Supérieur ou égal
- `[$lt]` : Inférieur
- `[$lte]` : Inférieur ou égal
- `[$in]` : Dans la liste
- `[$nin]` : Pas dans la liste

### **Opérateurs logiques**
- `$and` : ET logique
- `$or` : OU logique
- `$not` : NON logique

### **Opérateurs de tableau**
- `[$elemMatch]` : Correspondance d'élément
- `[$size]` : Taille du tableau

### **Exemples de requêtes complexes**

#### **Recherche d'objets avec effets spécifiques**
```
/items?$and[0][effects][$elemMatch][characteristic]=23&$and[1][level][$gte]=50&$and[2][level][$lte]=100
```

#### **Recherche de monstres par race et niveau**
```
/monsters?race=50&$and[0][grades][$elemMatch][level][$gte]=100&$and[1][grades][$elemMatch][level][$lte]=200
```

#### **Recherche de sorts par classe**
```
/spells?breedId=1&$sort=-id&$limit=50
```

## 📊 Métadonnées de réponse

### **Structure commune**
```json
{
  "total": "number",      // Nombre total d'éléments
  "limit": "number",      // Limite par page
  "skip": "number",       // Offset de pagination
  "data": ["array"]       // Données
}
```

### **Informations de pagination**
- `total` : Nombre total d'éléments disponibles
- `limit` : Nombre d'éléments par page
- `skip` : Nombre d'éléments à ignorer
- `data` : Array des éléments de la page courante

## 🌐 Gestion multilingue

### **Structure des champs multilingues**
```json
{
  "id": "string",         // ID de traduction
  "fr": "string",         // Français
  "en": "string",         // Anglais
  "de": "string",         // Allemand
  "es": "string",         // Espagnol
  "pt": "string"          // Portugais
}
```

### **Langues supportées**
- `fr` : Français (par défaut)
- `en` : Anglais
- `de` : Allemand
- `es` : Espagnol
- `pt` : Portugais

## 🔧 Paramètres de requête

### **Paramètres de base**
- `lang=fr` : Langue des données
- `$populate=false` : Désactive la population des relations
- `$limit=10` : Limite le nombre de résultats
- `$skip=0` : Offset de pagination
- `$sort[id]=-1` : Tri (1=asc, -1=desc)

### **Paramètres de performance**
- `$select=id,name` : Sélection de champs spécifiques
- `$populate=spells` : Population de relations
- `$distinct=true` : Valeurs uniques

## 📝 Notes importantes

### **Limitations**
- **Rate limiting** : Respecter les limites de l'API
- **Pagination** : Utiliser `$skip` et `$limit` pour les gros volumes
- **Cache** : Mettre en cache les données fréquemment consultées
- **Timeouts** : Gérer les timeouts pour les requêtes complexes

### **Bonnes pratiques**
- **Requêtes optimisées** : Utiliser les filtres appropriés
- **Pagination** : Traiter les données par lots
- **Cache** : Mettre en cache les métadonnées
- **Retry** : Gérer les échecs temporaires

### **Évolution**
- **Structure variable** : Les champs peuvent évoluer
- **Nouveaux endpoints** : L'API peut s'enrichir
- **Dépréciation** : Certains champs peuvent être dépréciés

---

**Note** : Ce document sera enrichi au fur et à mesure de l'exploration de l'API DofusDB et de la découverte de nouveaux endpoints et structures de données.
