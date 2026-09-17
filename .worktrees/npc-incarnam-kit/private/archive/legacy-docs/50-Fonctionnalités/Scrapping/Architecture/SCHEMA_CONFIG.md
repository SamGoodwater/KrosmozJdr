# Schéma des configs scrapping

Ce document décrit la structure des fichiers de configuration : **source**, **requêtes** (endpoints, pagination, filtres) et **mapping** (propriété DofusDB → KrosmozJDR + formatter). Les configs sont dans `resources/scrapping/config/sources/dofusdb/`.

---

## 1. Arborescence

```
resources/scrapping/config/
├── sources/
│   └── dofusdb/
│       ├── source.json          # Conventions globales (baseUrl, langue, http)
│       └── entities/
│           ├── monster.json     # Entité pilote
│           ├── item.json         # (à venir)
│           └── ...
└── formatters/
    └── registry.json            # (à venir, ou réutiliser l’existant)
```

---

## 2. Source (source.json)

Décrit la source API : URL de base, langue par défaut, options HTTP.

| Champ | Type | Description |
|-------|------|-------------|
| `version` | int | Version du schéma. |
| `source` | string | Identifiant (ex. `dofusdb`). |
| `label` | string | Libellé affiché. |
| `baseUrl` | string | URL de base (ex. `https://api.dofusdb.fr`). |
| `defaultLanguage` | string | Langue par défaut (ex. `fr`). |
| `http` | object | (optionnel) timeout, retry, userAgent. |
| `security` | object | (optionnel) allowedHosts. |

---

## 3. Entité (entities/{entity}.json)

Chaque fichier décrit **requêtes** (collecte) et **mapping** (conversion) pour une entité.

### 3.1 Métadonnées

| Champ | Type | Description |
|-------|------|-------------|
| `version` | int | Version du fichier. |
| `source` | string | Doit correspondre à `source.json` (ex. `dofusdb`). |
| `entity` | string | Identifiant d’entité (ex. `monster`, `item`). |
| `label` | string | Libellé (ex. « Monstres »). |
| `meta.maxId` | int | (optionnel) ID max connu pour cette entité. |

### 3.2 Requêtes (endpoints)

| Champ | Type | Description |
|-------|------|-------------|
| `endpoints.fetchOne` | object | Requête pour un objet par ID. |
| `endpoints.fetchOne.pathTemplate` | string | Chemin avec `{id}` (ex. `/monsters/{id}`). |
| `endpoints.fetchOne.queryDefaults` | object | Paramètres par défaut (ex. `{ "lang": "{lang}" }`). |
| `endpoints.fetchMany` | object | Requête pour une liste. |
| `endpoints.fetchMany.path` | string | Chemin (ex. `/monsters`). |
| `endpoints.fetchMany.queryDefaults` | object | Ex. `{ "lang": "{lang}", "$limit": 50, "$skip": 0 }`. |

Si `fetchOne` est absent ou non fiable (ex. spells), le collecteur peut simuler avec fetchMany + filtre `id=…` + limit 1.

### 3.3 Filtres supportés

| Champ | Type | Description |
|-------|------|-------------|
| `filters.supported` | array | Liste des filtres autorisés pour fetchMany. |
| `filters.supported[].key` | string | Nom du filtre (ex. `id`, `name`, `raceId`, `levelMin`, `levelMax`). |
| `filters.supported[].type` | string | `number`, `string`, `number[]`. |
| `filters.supported[].max` | int | (optionnel) Taille max pour les listes (ex. ids). |

Ces clés sont mappées vers les paramètres Feathers (ex. `name` → `name[$search]`, `idMin`/`idMax` → `id[$gte]`/`id[$lte]`).

### 3.4 Cible KrosmozJDR

| Champ | Type | Description |
|-------|------|-------------|
| `target.krosmozEntity` | string | Entité cible (ex. `monster`). |
| `target.primaryKey` | object | `sourceField` (ex. `id`), `targetField` (ex. `dofusdb_id`). |
| `target.autoUpdateField` | string | (optionnel) Champ auto_update. |

### 3.5 Mapping (propriété source → cible + formatter)

| Champ | Type | Description |
|-------|------|-------------|
| `mapping` | array | Liste des règles de mapping. |
| `mapping[].key` | string | Clé logique (ex. `dofusdb_id`, `name`, `level`). |
| `mapping[].from` | object | Source DofusDB. |
| `mapping[].from.path` | string | Chemin dans l’objet (ex. `id`, `grades.0.level`, `name`). |
| `mapping[].from.langAware` | bool | (optionnel) Si vrai, la valeur est multilingue (objet { fr, en }). |
| `mapping[].to` | array | Cibles KrosmozJDR. |
| `mapping[].to[].model` | string | Table ou modèle (ex. `creatures`, `monsters`). |
| `mapping[].to[].field` | string | Champ cible. |
| `mapping[].formatters` | array | Liste de formatters (nom + args). |
| `mapping[].formatters[].name` | string | Nom du formatter (ex. `toString`, `pickLang`, `clampInt`). |
| `mapping[].formatters[].args` | object | Arguments (ex. `{ "lang": "{lang}", "min": 1, "max": 200 }`). |

L’argument `{lang}` est remplacé au moment de l’exécution par la langue courante.

### 3.6 Relations (optionnel)

| Champ | Type | Description |
|-------|------|-------------|
| `relations` | object | Définition des relations (sorts, drops, etc.). |
| `relations.{name}.enabledByDefault` | bool | Inclus par défaut si include_relations. |
| `relations.{name}.extract.path` | string | Chemin dans la réponse (ex. `spells`, `drops`). |
| `relations.{name}.extract.idPath` | string | Champ ID (ex. `id`, `itemId`). |
| `relations.{name}.targetEntity` | string | Entité cible (ex. `spell`, `resource`). |

Utilisé plus tard par l’orchestrateur et l’intégration pour résoudre les liens.

---

## 4. Entité pilote : monster

Le fichier `resources/scrapping/config/sources/dofusdb/entities/monster.json` sert d’exemple complet : endpoints, filtres, target, mapping (champs principaux creature + monster), et relations (spells, drops).

---

## 5. Évolution

- **Nouvelles entités** : ajouter un fichier `entities/{entity}.json` en respectant ce schéma.

---

## 6. Items et super types (ressource / équipement / consommable)

### 6.1 Rôle de chaque fichier

| Fichier | Rôle | À modifier pour… |
|---------|------|-------------------|
| **entities/item.json** | Décrit **l’API** items : endpoints `/items`, filtres supportés (`id`, `name`, `typeId`, `typeIds`, etc.). | Ajouter un filtre supporté par l’API, changer le mapping des champs. |
| **collect_aliases.json** | Lie l’alias CLI (`ressource`, `item`, `consumable`) à l’entité `item` + un **groupe** par défaut (`superTypeGroup`). | Changer quel alias pointe vers quel groupe (ex. renommer « item » en « équipement »). |
| **item-super-types.json** | Définit les **groupes** (`resource`, `equipment`, `consumable`) et les **superTypeIds** (DofusDB) par groupe, plus **excludedTypeIds**. Emplacement : `resources/scrapping/config/sources/dofusdb/item-super-types.json`. | Ajouter/retirer un super type dans un groupe, exclure des typeIds, créer un nouveau groupe. |

**Vous ne devez pas indiquer les super types dans l’entité item.json.**  
L’entité `item.json` décrit uniquement l’API (chemins, paramètres). Le filtrage par catégorie (ressource / équipement / consommable) est géré par les alias et par `item-super-types.json`.

### 6.2 Chaîne de résolution (exemple : `--collect=ressource`)

1. **collect_aliases.json** : l’alias `ressource` pointe vers `entity: "item"` et `defaultFilter.superTypeGroup: "resource"`.
2. **item-super-types.json** : le groupe `resource` contient `superTypeIds: [9]` (Ressource DofusDB).
3. Le code charge la liste des **typeIds** (item-types) dont le `superTypeId` est 9 (via le catalogue item-types).
4. La collecte appelle l’API `/items` avec le filtre `typeId[$in][]` = ces typeIds.

Résultat : on ne récupère que les items dont le type appartient au super type « Ressource ».

### 6.3 Où modifier quoi

- **Changer quels types sont des « ressources »** : éditer `resources/scrapping/config/sources/dofusdb/item-super-types.json` → section `groups.resource.superTypeIds`.
- **Changer quels types sont des « consommables » ou « équipements »** : même fichier, sections `groups.consumable` et `groups.equipment`.
- **Exclure des types de toute collecte item** (Songes obsolètes, La source, apparat, etc.) : éditer `item-super-types.json`  → tableau **`excludedTypeIds`**. Pas besoin d’« entity = none » : on garde entity = ressource / consumable / item et on exclut par typeId.
- **Ajouter un nouvel alias** (ex. `--collect=equipement`) : éditer `collect_aliases.json` et, si besoin, ajouter un groupe dans `item-super-types.json`.
- **Modifier les champs collectés ou convertis pour les items** : éditer `entities/item.json` (mapping, filtres supportés).

### 6.4 Mapping par super-type vs liste include par entity

Deux approches possibles pour définir « quels types collecter » pour ressource / consumable / item :

| Approche | Idée | Avantages | Inconvénients |
|----------|------|-----------|---------------|
| **Mapping par super-type** (actuel) | Chaque groupe (resource, consumable, equipment) définit des **superTypeIds** (DofusDB). Le code déduit les typeIds via le catalogue item-types, puis soustrait **excludedTypeIds**. | Aligné sur le modèle DofusDB ; un nouveau type ajouté par l’API dans le superType 9 est automatiquement inclus en « resource ». Moins de maintenance quand l’API évolue. | Pour exclure des types précis (Songes, apparat) on garde une liste **excludedTypeIds** en plus. |
| **Liste include par entity** | Chaque alias (ressource, consumable, item) aurait une liste explicite **typeIds** à collecter (ex. ressource → [15, 26, 34, …]). | Contrôle total ; pas de surprise si DofusDB ajoute un type dans un superType. Une seule liste par entity. | Liste longue à maintenir ; il faut rafraîchir quand l’API ajoute des types. |

**Recommandation** : garder le **mapping par super-type** + **excludedTypeIds**. On définit « resource = superType 9 », « consumable = superTypes 6 et 70 », « equipment = tout sauf … », et on soustrait les typeIds qu’on ne veut jamais (Songes, La source, apparat). Si tu préfères une liste include explicite par entity, on peut faire évoluer le schéma (ex. `groups.resource.typeIds` en remplacement de `superTypeIds` quand présent).
