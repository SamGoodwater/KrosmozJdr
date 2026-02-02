# Décision : stockage des bonus / effets d’équipement

Ce document compare **garder le bonus en JSON** (état actuel) et **passer à une table ou un système plus robuste** (type DofusDB), pour les caractéristiques / bonus que peuvent apporter les équipements (et panoplies). Il vise à trancher pour la refonte du scrapping et l’évolution du modèle de données.

---

## 1. État actuel (KrosmozJDR)

| Entité   | Colonne(s) | Type  | Contenu |
|----------|------------|--------|---------|
| **Item** | `bonus`    | string (JSON) | Couche A : effets DofusDB normalisés (`EffectInstance[]`). |
| **Item** | `effect`   | string (JSON) | Couche B : bonus Krosmoz (stats, res_percent, res_fixed, damage_fixed, unmapped). |
| **Panoply** | `bonus` | string (JSON) | Bonus de panoplie (structure à confirmer). |

Référence : [EFFECTS_SYSTEM.md](../EFFECTS_SYSTEM.md) — normalisation DofusDB → `EffectInstance`, puis mapping vers bonus Krosmoz ; stockage temporaire en JSON pour éviter des migrations immédiates.

---

## 2. Côté DofusDB

- **Dictionnaire** : `GET /effects` — définitions d’effets (characteristic, elementId, etc.).
- **Instances** : sur les items, champ `effects[]` (tableau d’instances : effectId, valeurs, cible, etc.) ; sur les spell-levels, `effects[]` également.
- Pas une table “item_effects” en base DofusDB côté API : les effets sont **imbriqués** dans la ressource (item / spell-level). La structure est néanmoins **normalisée** (effectId + paramètres) et **référentielle** (effectId pointe vers le dictionnaire).

Donc DofusDB a un **modèle sémantique** robuste (dictionnaire + instances structurées), mais la **représentation** dans l’API est du JSON imbriqué, pas des tables séparées exposées telles quelles.

---

## 3. Options de stockage

### Option A : Garder JSON en base (état actuel, éventuellement structuré)

**Idée** : conserver une ou deux colonnes JSON (`bonus`, `effect` ou une seule colonne unifiée) sur `items` et `panoplies`, avec un **schéma documenté** (forme du JSON) pour faciliter validation et évolution.

| Avantages | Inconvénients |
|------------|----------------|
| Pas de migration DB immédiate ; refonte scrapping plus simple. | Requêtes SQL par type de bonus (ex. “tous les items avec +force”) difficiles ou lentes (JSON path selon SGBD). |
| Flexible : nouveau type de bonus = nouveau champ dans le JSON, pas d’ALTER TABLE. | Pas de clés étrangères ni contraintes au niveau “effet” (intégrité moins forte). |
| Un seul enregistrement par item ; chargement simple (un row = un item avec tous ses bonus). | Agrégations / stats (“moyenne de force donnée par les équipements”) plus compliquées. |
| Aligné avec la sortie du pipeline (conversion produit déjà un objet structuré). | Duplication si le même “type” d’effet apparaît sur beaucoup d’items (pas de table de référence). |

### Option B : Table(s) normalisée(s) type DofusDB

**Idée** : modéliser comme DofusDB avec un **dictionnaire d’effets** (définitions) et des **instances** liées aux items (et éventuellement panoplies).

Exemple possible :

- **effect_definitions** (ou `effects`) : id, dofusdb_effect_id, characteristic, element_id, name, …
- **item_effects** (ou `item_bonus`) : id, item_id, effect_definition_id (ou effect_id), value_min, value_max, raw_json (optionnel pour traçabilité).

Pour la “couche B” Krosmoz (stats, résistances, etc.), soit :
- on dérive ces stats à la lecture à partir des `item_effects` (calcul ou vue),  
- soit on ajoute une table **item_bonus_krosmoz** (item_id, bonus_type, value) pour les bonus déjà mappés.

| Avantages | Inconvénients |
|------------|----------------|
| Requêtes faciles : “items avec tel effet”, “liste des effets par type”, agrégations. | Migrations + refonte du scrapping pour remplir les tables (conversion → insert dans item_effects / item_bonus_krosmoz). |
| Intégrité : FK vers effect_definitions et items ; contraintes possibles. | Modèle plus lourd ; plus de jointures pour “un item avec tous ses bonus”. |
| Évolution du dictionnaire centralisée ; pas de duplication de la définition d’un effet. | Définir et maintenir le schéma (effect_definitions, item_effects, couche B) prend du temps. |
| Aligné avec la sémantique DofusDB (dictionnaire + instances). | Risque de décalage si l’API DofusDB change (il faudra adapter le mapping). |

### Option C : Hybride (JSON + tables optionnelles)

**Idée** : garder le JSON pour le **stockage principal** (comme aujourd’hui), et ajouter éventuellement des **tables dérivées** ou des **vues** pour les cas où on a besoin de requêtes (ex. index de recherche “items avec +force”). On peut aussi **documenter un schéma JSON strict** (et valider en conversion) pour préparer une future migration vers des tables.

| Avantages | Inconvénients |
|------------|----------------|
| Refonte scrapping peu impactée (on continue à produire du JSON). | Deux représentations à maintenir si on ajoute des tables dérivées. |
| Possibilité d’ajouter plus tard des tables / vues sans casser l’existant. | Complexité accrue si on synchronise JSON ↔ tables. |

---

## 4. Critères de choix

| Critère | JSON (A) | Tables (B) | Hybride (C) |
|---------|-----------|------------|-------------|
| **Refonte scrapping simple** | Oui | Non (il faut écrire dans les tables) | Oui |
| **Requêtes par type de bonus** (ex. “items +force”) | Faible (JSON path) | Oui | Oui si tables dérivées |
| **Agrégations / stats** | Faible | Oui | Oui si tables |
| **Évolution du modèle de bonus** | Flexible | Plus structurée mais plus coûteuse | Flexible + structuré si besoin |
| **Alignement DofusDB** | Non (structure différente) | Oui (dictionnaire + instances) | Partiel |
| **Intégrité (FK, contraintes)** | Faible | Forte | Forte sur les tables dérivées |
| **Charge de travail immédiate** | Faible | Forte | Moyenne |

---

## 5. Recommandation

**Pour la refonte scrapping (court terme)**  
- **Garder le stockage en JSON** (option A), avec **un schéma de JSON documenté et stable** (ex. forme exacte de `effect` / `bonus` pour items et panoplies).  
- Raisons : la refonte vise d’abord Collect → Conversion → Validation → Intégration ; changer en plus le modèle de persistance des bonus compliquerait fortement le périmètre. Le pipeline actuel produit déjà du JSON ; on évite des migrations et du code d’écriture dans des tables.

**Pour l’évolution du modèle (moyen terme)**  
- **Viser un système plus robuste** (option B ou C) **si** vous avez besoin de :  
  - requêtes du type “tous les équipements qui donnent +X à la force”,  
  - agrégations / stats sur les bonus,  
  - intégrité forte (dictionnaire d’effets, pas de valeurs libres),  
  - ou alignement fort avec le modèle DofusDB (dictionnaire + instances).  
- Dans ce cas :  
  1. **Décider du schéma** : tables `effect_definitions` + `item_effects` (et éventuellement `item_bonus_krosmoz` pour la couche B).  
  2. **Documenter** ce schéma dans la doc de refonte (ou un fichier dédié) pour que la **conversion** puisse, plus tard, produire soit du JSON (comme aujourd’hui), soit des lignes pour ces tables.  
  3. Introduire les tables et la logique d’écriture **après** que la chaîne CLI (collect → convert → validate → integrate) soit validée avec le JSON.

**Réponse directe aux questions**  
- **Garder l’architecture actuelle (JSON) en base ?** Oui pour la refonte, avec un schéma JSON clair et stable.  
- **Ou imiter DofusDB avec une table / un système plus robuste ?** Oui **si** les besoins (requêtes, agrégations, intégrité) le justifient ; à faire **après** la refonte scrapping, en s’appuyant sur une conversion qui produit déjà des structures bien définies (ce qui facilitera un futur remplissage de tables).

---

## 6. Actions proposées

1. **Refonte scrapping** : ne pas changer le stockage des bonus ; garder `bonus` / `effect` en JSON sur Item (et bonus sur Panoply). Documenter dans la config “mapping” ou dans un fichier dédié le **schéma JSON** attendu (couche A et couche B) pour validation et évolution.
2. **Si passage aux tables plus tard** : ajouter dans la doc de refonte (ou ici) une **esquisse du schéma cible** (effect_definitions, item_effects, éventuellement item_bonus_krosmoz) et préciser que la conversion devra pouvoir alimenter soit le JSON, soit ces tables, pour éviter de refaire toute la chaîne.
3. **Vérifier l’API DofusDB** : lors de la découverte API (voir [PLAN_IMPLEMENTATION.md](./PLAN_IMPLEMENTATION.md)), noter la structure exacte de `effects[]` sur les items (et spell-levels) pour s’assurer que le mapping JSON actuel (ou futur vers tables) reste aligné avec l’API.

Ce document pourra être mis à jour lorsque le choix “table(s)” sera acté et que le schéma cible sera fixé.
