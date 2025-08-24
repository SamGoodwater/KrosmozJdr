# 🔍 Analyse Détaillée de l'API DofusDB

## 📊 Résumé de l'Analyse

**Date d'analyse** : $(date)  
**Source** : [dofusdb.fr](https://dofusdb.fr/fr/)  
**Méthode** : Analyse des requêtes réseau via Playwright

## 🌐 Structure de l'API

### Base URL
```
https://api.dofusdb.fr/
```

### Format des Réponses
- **Format** : JSON
- **Encodage** : UTF-8
- **Langue** : Paramètre `lang=fr` pour le français

## 📋 Endpoints Analysés

### 1. **Items/Objets**
```
GET https://api.dofusdb.fr/items
```

**Paramètres identifiés :**
- `$sort[id]=-1` : Tri par ID décroissant
- `typeId[$ne]=203` : Exclure le type 203
- `typeId[$in][]=1` : Inclure le type 1
- `level[$gte]=0&level[$lte]=200` : Filtre par niveau
- `$skip=X` : Pagination
- `$limit=Y` : Limite de résultats
- `lang=fr` : Langue française

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/items?typeId[$ne]=203&$sort=-id&typeId[$in][]=1&level[$gte]=0&level[$lte]=200&$skip=20&lang=fr
```

### 2. **Monstres**
```
GET https://api.dofusdb.fr/monsters
```

**Paramètres identifiés :**
- `$sort[id]=-1` : Tri par ID décroissant
- `$limit=10` : Limite de résultats
- `$populate=false` : Ne pas peupler les relations
- `$skip=X` : Pagination
- `lang=fr` : Langue française

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/monsters?$sort[id]=-1&$limit=10&$populate=false&$skip=40&lang=fr
```

### 3. **Sorts**
```
GET https://api.dofusdb.fr/spells
```

**Paramètres identifiés :**
- `lang=fr` : Langue française

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/spells/31671?lang=fr
```

### 4. **Niveaux de Sorts**
```
GET https://api.dofusdb.fr/spell-levels
```

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/spell-levels/83429?lang=fr
```

### 5. **Effets**
```
GET https://api.dofusdb.fr/effects
```

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/effects/1160?lang=fr
```

### 6. **Types d'Objets**
```
GET https://api.dofusdb.fr/item-types
```

**Paramètres identifiés :**
- `$skip=X` : Pagination
- `$limit=Y` : Limite de résultats
- `lang=fr` : Langue française

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/item-types?$skip=0&$limit=50&lang=fr
```

### 7. **Caractéristiques**
```
GET https://api.dofusdb.fr/characteristics
```

**Paramètres identifiés :**
- `$skip=X` : Pagination
- `lang=fr` : Langue française

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/characteristics?$skip=0&lang=fr
```

### 8. **Critères**
```
GET https://api.dofusdb.fr/criterion
```

**Exemple d'URL complète :**
```
https://api.dofusdb.fr/criterion/PE!337?lang=fr
```

## 🔗 Relations Identifiées

### Relations Monstres ↔ Sorts
- Les monstres ont des sorts associés
- Les sorts ont des niveaux spécifiques
- Relation via `spell-levels`

### Relations Objets ↔ Effets
- Les objets ont des effets associés
- Les effets sont référencés par ID
- Relation via `effects`

### Relations Objets ↔ Types
- Les objets appartiennent à des types
- Filtrage possible par type
- Relation via `item-types`

### Relations Objets ↔ Critères
- Les objets ont des critères d'utilisation
- Les critères sont référencés par ID
- Relation via `criterion`

## 📊 Structure des Données

### Format de Réponse Général
```json
{
  "total": 0,
  "limit": 10,
  "skip": 0,
  "data": []
}
```

### Champs Communs Identifiés
- `id` : Identifiant unique
- `name` : Nom de l'élément
- `description` : Description
- `level` : Niveau
- `lang` : Langue

## 🎯 Observations Importantes

### 1. **Pagination**
- Utilisation de `$skip` et `$limit`
- Pagination par 50 éléments pour les types
- Pagination par 10 éléments pour les monstres

### 2. **Filtrage**
- Filtrage par type d'objet
- Filtrage par niveau
- Exclusion de types spécifiques

### 3. **Relations**
- Relations non peuplées par défaut (`$populate=false`)
- Références par ID vers d'autres entités
- Chargement à la demande des relations

### 4. **Images**
- Endpoint séparé pour les images : `https://api.dofusdb.fr/img/`
- Structure : `https://api.dofusdb.fr/img/{type}/{id}.png`
- Types identifiés : `items`, `monsters`, `achievements`

## ⚠️ Limitations Identifiées

1. **Rate Limiting** : Pas d'information sur les limites
2. **Authentification** : Pas d'authentification requise
3. **CORS** : Pas de restrictions CORS détectées
4. **Cache** : Pas d'informations sur le cache

## 🔄 Prochaines Étapes

1. **Tester chaque endpoint** individuellement
2. **Analyser la structure complète** des réponses
3. **Identifier tous les types** d'objets
4. **Mapper les relations** complètes
5. **Créer des scripts** de collecte

---

*Analyse basée sur les requêtes réseau capturées le $(date)*
