# Référence API DofusDB — Sorts et spell-levels

Ce document décrit la **structure réelle** des réponses API DofusDB pour les sorts, telle que découverte sur les endpoints publics. Il sert de référence pour le mapping (chemins `from.path`) dans `spell.json` et le seeder `scrapping_entity_mappings`.

---

## 1. Les trois niveaux de données

| Niveau | Endpoint | Rôle |
|--------|----------|------|
| **Sort** | `GET /spells?id[]=31158&lang=fr` (ou `/spells/{id}`) | Métadonnées du sort : nom, description, image, **liste d’IDs de niveaux** |
| **Niveaux** | `GET /spell-levels?spellId=31158&$sort=grade&lang=fr` | Caractéristiques par grade : PA, portée, zone, **effets** (diceNum, diceSide, zoneDescr, effectId) |
| **Définition effet** | `GET /effects/754?lang=fr` | Caractéristique(s), catégorie, description de l’effet (effectId) |

Les données de **spell-levels** sont fusionnées dans `raw['levels']` par l’orchestrateur avant la conversion. Tous les chemins du mapping sort partent donc de `levels.0.*` (premier niveau) ou `levels.0.effects.*`.

---

## 2. GET /spells — Réponse sort

Exemple (extrait) :

```json
{
  "id": 31158,
  "name": { "fr": "Bouffe-tout", "en": "Gobs-all", … },
  "description": { "fr": "…", … },
  "img": "https://api.dofusdb.fr/img/spells/sort_12819.png",
  "spellLevels": [82274, 82630, 82631],
  "typeId": 3998,
  …
}
```

- **Pas d’objet** `range`, `minRange`, `apCost`, etc. sur le sort : tout cela est sur les **spell-levels**.
- **spellLevels** : tableau d’**IDs** de niveaux ; il faut appeler `/spell-levels?spellId=…` pour obtenir les vrais niveaux.

---

## 3. GET /spell-levels?spellId=… — Réponse niveaux (levels[])

Exemple pour un niveau (grade 1) :

```json
{
  "id": 82274,
  "spellId": 31158,
  "grade": 1,
  "apCost": 2,
  "minRange": 1,
  "range": 2,
  "maxCastPerTurn": 0,
  "maxCastPerTarget": 2,
  "minCastInterval": 0,
  "castTestLos": true,
  "effects": [
    {
      "effectId": 1042,
      "zoneDescr": { "cellIds": [], "shape": 80, "param1": 1, "param2": 0, … },
      "diceNum": 1,
      "diceSide": 0,
      "value": 0,
      "duration": 0,
      …
    },
    {
      "effectId": 97,
      "zoneDescr": { "shape": 80, "param1": 1, "param2": 0, … },
      "diceNum": 12,
      "diceSide": 14,
      …
    }
  ],
  "criticalEffect": [ … ]
}
```

### Champs importants pour le mapping

| Champ API | Type | Rôle | Chemin mapping |
|-----------|------|------|-----------------|
| **minRange** | number | Portée **minimale** (cases) ; 0 = soi, 1 = CAC | `levels.0.minRange` |
| **range** | number | Portée **maximale** (cases). **Pas** un objet `{ min, max }` | `levels.0.range` |
| **apCost** | number | Coût en PA | `levels.0.apCost` |
| **grade** | number | Degré du niveau (1, 2, 3…) | `levels.0.grade` |
| **maxCastPerTurn** | number | Lancers max par tour | `levels.0.maxCastPerTurn` |
| **maxCastPerTarget** | number | Lancers max par cible par tour | `levels.0.maxCastPerTarget` |
| **castTestLos** | boolean | Ligne de vue requise | `levels.0.castTestLos` |
| **minCastInterval** | number | Intervalle entre deux lancers (tours) | `levels.0.minCastInterval` |
| **effects[].zoneDescr** | object | Zone d’impact : `shape`, `param1`, `param2` ; **shape 80 = 1 case (CAC)** | `levels.0.effects.0.zoneDescr` |
| **effects[].effectId** | number | Référence `/effects/{id}` | — |
| **effects[].diceNum** | number | Nombre de dés (dégâts, etc.) | — |
| **effects[].diceSide** | number | Faces du dé | — |
| **effects[].value** | number | **Valeur fixe** lorsque l’effet n’utilise pas de dés (ex. +1 PA, durée fixe). Si `useDice` est false côté définition, c’est en général `value` qui porte la valeur. | — |
| **effects[].duration** | number | Durée (tours) | — |

**Valeur d’effet (dés vs fixe)** :  
- Si **diceNum** et **diceSide** > 0 → valeur aléatoire (dés) ; notation `XdY`, puis Phase 3 / dés.  
- Si **diceSide** = 0 (ou absent) et **diceNum** > 0 → **diceNum** porte la valeur fixe (ex. 10 = 10 % pour Bouclier, 50/200/500 pour résistances).  
- Sinon **value** est utilisé (ex. ajout de PA).  
La conversion gère ces trois cas. La **duration** (tours) est ajoutée dans `params.duration` pour affichage (ex. « 2 durée »).

**Attention** : la portée est donc **deux champs séparés** `minRange` et `range`, et non un objet `range.min` / `range.max`.

---

## 4. GET /effects/{id} — Définition d’un effet

Exemple (effet 754, retrait Fuite) :

```json
{
  "id": 754,
  "characteristic": 78,
  "category": 0,
  "description": { "fr": "-#1{{~1~2 à -}}#2 Fuite", … },
  "useDice": true,
  "elementId": -1,
  …
}
```

- **characteristic** : ID de caractéristique Dofus (même référentiel que `GET /characteristics`). Ex. **19 = Portée** (range), 1 = PA, 23 = PM.
- **category** : catégorie (dégâts, soins, etc.).
- **description** : libellé multilingue.

Utilisé par le **catalogue d’effets** et la conversion effet → sous-effet Krosmoz (slug, caractéristique).

---

### 4.1 Mapping DofusDB characteristic ID → clé Krosmoz (sorts)

Pour savoir quelle caractéristique Krosmoz est affectée par un effet, on s'appuie sur :

1. **Table `dofusdb_effect_mappings`** (prioritaire) : **effectId** → `characteristic_key` (ex. effet **116** → `po`). C'est le cas pour « -3 Portée » : effet 116, characteristic 19 en API, mappé en `po` → normalisé en `range_spell`.
2. **Fichier de référence (sort)** : `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz_spell.json` — mapping **id caractéristique DofusDB** → **clé courte** Krosmoz pour le groupe spell (ex. **19 → po**, 1 → pa, 23 → pm). Même logique que `dofusdb_characteristic_to_krosmoz.json` pour les objets. Permet de vérifier ou déduire la `characteristic_key` à partir de `GET /effects/{id}.characteristic`.

| DofusDB characteristic (ex. API) | Nom (keyword) | characteristic_key Krosmoz (spell) |
|----------------------------------|---------------|-------------------------------------|
| 1  | actionPoints   | pa → action_points_spell |
| 19 | range          | po → range_spell         |
| 23 | movementPoints | pm → movement_points_spell |
| 10 | strength       | strong                   |
| 18 | criticalHit    | critical                 |

Référence complète des IDs : [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](../Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md) (objets) ; pour les sorts, le JSON spell ci-dessus est la source.

---

## 5. Zone (zoneDescr) : triple paramètre shape, param1, param2

- **shape 80** = 1 case (CAC) → **`point`** (pas de 2ᵉ paramètre).
- **shape 67** = cercle : **param1** = rayon intérieur (où commence), **param2** = rayon extérieur (où finit) → **`circle-{param1}-{param2}`** (ex. `circle-0-2` plein, `circle-1-2` anneau sans centre, `circle-2-2` bordure seule).
- Le formatter **zoneDescrToNotation** transforme `zoneDescr` en notation Krosmoz ; voir [ZONE_NOTATION.md](../Spell-Effects/ZONE_NOTATION.md).

Voir [ZONE_NOTATION.md](../Spell-Effects/ZONE_NOTATION.md).

---

## 6. Paramètres globaux : spell_global et chemins (spell.json)

Pour simplifier le mapping et gérer la complexité des données sort (plusieurs niveaux, paramètres dans les effets), une **couche de normalisation** construit un objet unique **`spell_global`** avant conversion :

- **SpellGlobalNormalizer** (Orchestrator) fusionne : racine du sort (id, name, description, img, elementId, categoryId) + **premier spell-level** (apCost, minRange, range, grade, maxCastPerTurn, castTestLos, etc.) + **zone** = premier effet du premier niveau (`levels.0.effects.0.zoneDescr`).
- La conversion lit **uniquement** `spell_global.*` pour les paramètres globaux du sort. Les effets (sous-effets, dés, caractéristiques) restent gérés par SpellEffectsConversionService à partir de `raw['levels']`.

| Propriété Krosmoz | Chemin (après normalisation) |
|-------------------|------------------------------|
| dofusdb_id, name, description, image | `spell_global.id`, `spell_global.name`, etc. |
| spell_po_min | `spell_global.minRange` |
| spell_po_max | `spell_global.range` |
| pa | `spell_global.apCost` |
| level | `spell_global.grade` |
| cast_per_turn | `spell_global.maxCastPerTurn` |
| cast_per_target | `spell_global.maxCastPerTarget` |
| sight_line | `spell_global.castTestLos` |
| area | `spell_global.area` (objet zoneDescr du 1er effet → formatter zoneDescrToNotation) |
| po_editable | `spell_global.rangeCanBeBoosted` |

Les valeurs des sous-effets (dégâts, soins, etc.) viennent de **effects[].diceNum**, **diceSide**, **value** et du dictionnaire **/effects/{effectId}** pour la caractéristique et la catégorie.

---

## 7. Propriétés API non mappées (cette conversation)

Liste des champs présents dans les réponses API **spell** et **spell-levels** qui ne sont **pas** mappés vers le modèle `spells` ou les effets Krosmoz dans le cadre actuel du scrapping.

### 7.1 GET /spells — champs non mappés

| Champ API | Rôle probable |
|-----------|----------------|
| **typeId** | Type / classe du sort (ex. 3998 = race) |
| **order** | Ordre d’affichage |
| **scriptParams**, **scriptParamsCritical** | Paramètres script (jeu) |
| **scriptId**, **scriptIdCritical** | Référence script |
| **iconId** | ID icône (on utilise déjà `img` URL) |
| **boundScriptUsageData**, **criticalHitBoundScriptUsageData** | Données de script (ciblage, zones d’activation) |
| **basePreviewZoneDescr** | Zone de prévisualisation (shape, param1, param2) |
| **adminName** | Nom admin |
| **verboseCast** | Affichage verbeux du lancer |
| **bypassSummoningLimit** | Ignorer limite d’invocations |
| **canAlwaysTriggerSpells** | Peut toujours déclencher des sorts |
| **hideCastConditions** | Masquer conditions de lancer |
| **className**, **m_id**, **createdAt**, **updatedAt** | Métadonnées API |

### 7.2 GET /spell-levels — champs non mappés (niveau)

| Champ API | Rôle probable |
|-----------|----------------|
| **id** | ID du niveau (82274, etc.) |
| **spellId** | Déjà connu par le contexte |
| **initialCooldown** | Cooldown initial (tours) |
| **globalCooldown** | Cooldown global |
| **minPlayerLevel** | Niveau joueur minimum pour ce grade |
| **statesCriterion** | Critères d’états |
| **criticalHitProbability** | % de coup critique |
| **maxStack** | Pile max (buff) |
| **previewZones** | Zones de prévisualisation |
| **castInLine** | Lancer en ligne |
| **castInDiagonal** | Lancer en diagonale |
| **needFreeCell** | Case libre requise |
| **needTakenCell** | Case occupée requise |
| **needFreeTrapCell** | Case sans piège |
| **rangeCanBeBoosted** | Portée boostable → mappé vers **po_editable** (sort à PO modifiable) |
| **hideEffects** | Masquer les effets |
| **hidden** | Niveau caché |
| **playAnimation** | Jouer l’animation |
| **needVisibleEntity** | Entité visible requise |
| **needCellWithoutPortal** | Case sans portail |
| **portalProjectionForbidden** | Projection portail interdite |
| **minCastIntervalEditable** | Intervalle entre deux lancers éditable (en réserve dans `_mappingUnused`) |
| **rangeEditable** | Portée éditable (mappé en réserve → `po_editable`) |
| **isMagic** | Sort magique (en réserve → `is_magic`) |
| **powerful** | Puissant (en réserve → `powerful`) |

### 7.3 effects[] / criticalEffect[] — champs non mappés (instance d’effet)

| Champ API | Rôle probable |
|-----------|----------------|
| **effectUid**, **baseEffectId** | Identifiants techniques |
| **order** | Utilisé pour l’ordre des sous-effets |
| **targetId**, **targetMask** | Cible (allié, ennemi, etc.) |
| **random**, **group**, **modificator** | Modificateurs Dofus |
| **dispellable** | Dissipable |
| **delay** | Délai (tours) |
| **triggers** | Déclencheurs (P = piège, G = glyphe ; utilisé pour target_type) |
| **effectElement** | Élément (utilisé si charSource = element) |
| **effectTriggerDuration** | Durée du déclencheur |
| **displayZero**, **visibleInTooltip**, **visibleInBuffUi**, **visibleInFightLog**, **visibleOnTerrain** | Affichage |
| **forClientOnly**, **trigger** | Côté client / trigger |

### 7.4 GET /effects/{id} — champs non mappés (définition)

| Champ API | Rôle probable |
|-----------|----------------|
| **iconId** | Icône de l’effet |
| **characteristicOperator** | Opérateur (+ / -) |
| **showInTooltip**, **useDice**, **forceMinMax**, **boost**, **active** | Options d’affichage / jeu |
| **oppositeId** | Effet opposé |
| **theoreticalPattern**, **showInSet** | Patron / set |
| **bonusType**, **useInFight**, **effectPriority**, **effectPowerRate** | Gameplay |
| **isInPercent**, **hideValueInTooltip** | Affichage |
| **textIconReferenceId**, **effectTriggerDuration**, **actionFiltersId** | Références techniques |

---

Les entrées en **réserve** (`_mappingUnused` dans spell.json) sont mappées côté config mais pas encore intégrées au payload par défaut : `number_between_two_cast`, `number_between_two_cast_editable`, `po_editable`, `is_magic`, `powerful`. Le payload d’intégration les prend en compte s’ils sont présents dans les données converties.

---

## 8. Exemples réels (référence API)

| Sort / niveau | Données clés | Effet typique |
|---------------|--------------|----------------|
| **Gratin de gravier** (spellId 29108) | [spell-levels?spellId=29108](https://api.dofusdb.fr/spell-levels?$skip=0&spellId=29108&$sort=grade&lang=fr) | apCost 4, minRange/range 0, minCastInterval 4, globalCooldown 1, castTestLos false. Effet 1039 (Bouclier) : diceNum 10, diceSide 0 → 10 %, duration 2, zone shape 67 param1 5. |
| **Couche rocailleuse** (29082) | [spells/29082](https://api.dofusdb.fr/spells/29082?lang=fr), [spell-levels/77568](https://api.dofusdb.fr/spell-levels/77568?lang=fr) | 1 niveau, effet 111 : diceNum 1, diceSide 0, duration 5. |
| **Sort 29080** (77560) | [spell-levels/77560](https://api.dofusdb.fr/spell-levels/77560?lang=fr) | Plusieurs effets avec diceNum 50/200/500, diceSide 0 (valeurs fixes) ; un effet avec diceNum 29080, diceSide 2 (dés). |
| **Effet Bouclier** | [effects/1039](https://api.dofusdb.fr/effects/1039?lang=fr) | characteristic 0, description « Bouclier : #1{{~1~2 à }}#2% des PV max », useDice true. |
| **Craqueboulement** (spellId 29109) | [spell-levels?spellId=29109](https://api.dofusdb.fr/spell-levels?$skip=0&spellId=29109&$sort=grade&lang=fr), [effects/97](https://api.dofusdb.fr/effects/97?lang=fr) | Coût 3, portée 1, critique 10 %, maxCastPerTurn 3, maxCastPerTarget 1, maxStack 1. **Effets** : 97 (dommages Terre, diceNum 39, diceSide 45 → 289–333) ; 950 (état Indéplaçable, value 97, duration 1). Critiques : 97 (47d54 → 348–400), 950 (id.). |

Ces exemples permettent de valider la conversion (diceSide=0 → valeur dans diceNum, duration dans params, shape 67 → `shape-67`, effets avec dés + effets avec value/duration).

---

## 9. Dépannage : propriétés à 0, area = 97, description vide

### Solution rapide (beaucoup de 0 en converti)

Si la plupart des propriétés converties sont à 0 (pa, spell_po_min, spell_po_max, area, cast_per_turn, etc.) alors que le nom ou le level s’affichent, le **mapping en base est probablement obsolète** (mauvais `from_path`). Réexécuter le seeder pour réinjecter les bons chemins :

```bash
php artisan db:seed --class=ScrappingEntityMappingSeeder
```

---

- **pa, po_min, po_max, cast_per_turn, cast_per_target, level, etc. à 0**  
  Ces champs viennent de **spell-levels** (`levels.0.apCost`, `levels.0.minRange`, etc.). Si l’API `GET /spell-levels?spellId=XXX` ne renvoie **aucune donnée** pour l’ID du sort scrapé, `raw['levels']` est vide et tous ces chemins donnent 0. Vérifier que l’ID scrapé est bien celui du sort DofusDB (ex. **13156** pour Fureur, pas un autre ID). La requête spell-levels utilise maintenant `$sort=grade` pour que `levels.0` soit le premier grade.

- **area affiche 97 (ou un entier)**  
  Souvent dû à un mapping BDD qui pointe sur `levels.0.effects.0.effectId` au lieu de `levels.0.effects.0.zoneDescr`, ou à l’absence du formatter `zoneDescrToNotation`. Une protection en code évite d’écrire un entier type effectId (1–5000) dans `spells.area`. Pour corriger définitivement : **réexécuter le seeder** `ScrappingEntityMappingSeeder` (ou vérifier en BDD que la règle `area` a `from_path` = `levels.0.effects.0.zoneDescr` et les formatters `zoneDescrToNotation` + `toString`).

- **description convertie vide**  
  Si l’API renvoie `description` sous forme d’**id seul** (nombre ou `{ "id": "..." }` sans clé de langue), la conversion ne peut pas en tirer de texte. Comportement normal pour certains sorts ; pas de correctif côté mapping.

- **Mapping BDD vs JSON**  
  Si la table `scrapping_entity_mappings` est remplie, c’est elle qui fournit le mapping (pas le JSON `spell.json`). Pour retrouver le comportement du JSON (chemins et formatters à jour), réexécuter le seeder des mappings pour l’entité `spell`.
