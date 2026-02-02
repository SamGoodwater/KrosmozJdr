# Découverte API DofusDB — Refonte scrapping

Ce document consolide les informations sur l’API DofusDB nécessaires au scrapping : endpoints, paramètres, **structure des réponses** (déduite des configs et de la doc existante), pagination et filtres. Il sert de référence pour les configs « requêtes » et « mapping ».

Référence existante : [Data-collect/API.md](../Data-collect/API.md).

---

## 1. Base et conventions

| Élément | Valeur |
|--------|--------|
| **Base URL** | `https://api.dofusdb.fr` |
| **Langue** | Paramètre `lang=fr` (ou en/de/es/pt) sur la plupart des endpoints. |
| **Style** | FeathersJS (pagination `$limit`, `$skip`, filtres opérateurs). |

### Format de réponse (listes)

```json
{
  "total": 4900,
  "limit": 50,
  "skip": 0,
  "data": [ { /* objet */ } ]
}
```

- **total** : peut être absent (best effort).
- **limit** : limite **effective** renvoyée (souvent cap à 50 même si on demande 100).
- **skip** : offset de la page.
- **data** : tableau d’objets.

Pour paginer, avancer `skip` avec **la valeur `limit` renvoyée** par l’API, pas celle demandée.

---

## 2. Syntaxe de requête (Feathers)

| Paramètre | Rôle | Exemple |
|-----------|------|---------|
| **$limit** | Taille de page | 50, 100 |
| **$skip** | Offset | 0, 50, 100 |
| **$sort** | Tri | -id, name |
| **name[$search]** | Recherche texte | Bouftou |
| **id[$in][]** | IDs multiples | 1, 2, 3 |
| **id[$gte]** / **id[$lte]** | Bornes ID | idMin, idMax |
| **level[$gte]** / **level[$lte]** | Niveau | levelMin, levelMax |
| **raceId** | Filtre race (monstres) | 1 |
| **typeId** | Filtre type (items) | 15 |
| **breedId** | Filtre classe (spell-levels) | 1 |

La commande `scrapping` expose ces filtres de base : `--name`, `--level-min`, `--level-max`, `--id-min`, `--id-max`, `--race-id` (monstres), `--type-id` (objets). Ils sont convertis en paramètres Feathers dans le service de collecte.

---

## 3. Endpoints et structure des réponses

Structure déduite des chemins utilisés dans les configs d’entités (`from.path`). À **valider par des appels réels** (curl / Postman) pour confirmer les noms de champs et types.

### 3.1 Classes (Breeds)

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/breeds?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/breeds/{id}?lang=fr` | GET |

Champs attendus (à confirmer) : `id`, `name` (multilingue ?), `description`, `spellLevels` ou lien vers sorts, etc.

### 3.2 Monstres

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/monsters?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/monsters/{id}?lang=fr` | GET |

- **Total** : 5093 monstres (valeur `total` renvoyée par l’API).
- **Pagination** : par pas de **50** (`$limit=50`, `$skip=0`, puis `$skip=50`, `$skip=100`, … jusqu’à `$skip=5050` pour couvrir l’ensemble). Soit 102 pages (ceil(5093/50)).
- **Tri recommandé** : `$sort[id]=-1` si besoin d’un ordre stable.

Filtres : `name[$search]`, `id`, `id[$in][]`, `id[$gte]`, `id[$lte]`, `raceId`, `raceIds[]`, `level[$gte]`, `level[$lte]`.

#### Races de monstres

Il n’existe **pas** d’endpoint dédié `/monster-races` sur l’API DofusDB. Les IDs de race sont fournis dans le champ **`race`** de chaque objet monstre. Pour obtenir la liste de toutes les races référencées :

1. Paginer l’endpoint `/monsters` de 50 en 50 jusqu’à couvrir les 5093 entrées (`$skip=0`, puis +50 à chaque page).
2. Pour chaque page, extraire les valeurs distinctes du champ `race` de chaque monstre.
3. Constituer un registre (fichier de référence ou table `monster_races`) des IDs de race rencontrés, à enrichir au fur et à mesure (noms, etc.) si un autre mécanisme ou source existe.

**Structure de réponse (déduite des configs)** :

| Chemin | Type | Usage KrosmozJDR |
|--------|------|-------------------|
| `id` | number | dofusdb_id |
| `name` | object (lang) | name (pickLang) |
| `img` | string | image |
| `size` | string | size (mapSizeToKrosmoz) |
| `race` | number | monster_race_id |
| `grades[0].level` | number | creatures.level |
| `grades[0].lifePoints` | number | creatures.life |
| `grades[0].strength` | number | creatures.strength |
| `grades[0].intelligence` | number | creatures.intelligence |
| `grades[0].agility` | number | creatures.agility |
| `grades[0].wisdom` | number | creatures.wisdom |
| `grades[0].chance` | number | creatures.chance |
| `grades[0].actionPoints` | number | creatures.pa |
| `grades[0].movementPoints` | number | creatures.pm |
| `grades[0].kamas` | number | creatures.kamas |
| `grades[0].bonusRange` | number | creatures.po |
| `grades[0].paDodge` | number | creatures.dodge_pa |
| `grades[0].pmDodge` | number | creatures.dodge_pm |
| `grades[0].vitality` | number | creatures.vitality |
| `grades[0].neutralResistance` | number | creatures.res_neutre |
| `grades[0].earthResistance` | number | creatures.res_terre |
| `grades[0].fireResistance` | number | creatures.res_feu |
| `grades[0].airResistance` | number | creatures.res_air |
| `grades[0].waterResistance` | number | creatures.res_eau |
| `spells` | array | relation sorts |
| `drops` | array | relation drops (itemId, quantity) |

### 3.3 Items (objets / ressources / consommables)

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/items?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/items/{id}?lang=fr` | GET |

Filtres : `name[$search]`, `typeId`, `typeIds[]`, `id[$in][]`, `id[$gte]`, `id[$lte]`.

Champs attendus (déduits des configs) : `id`, `name`, `description`, `level`, `typeId`, `img`, `effects[]`, `recipe` (recette), etc. Un item peut être mappé vers `items`, `resources` ou `consumables` selon typeId / superType et registries KrosmozJDR.

### 3.4 Panoplies (Item sets)

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/item-sets?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/item-sets/{id}?lang=fr` | GET |

### 3.5 Sorts (Spells)

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/spells?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | Souvent **non fiable** → simuler avec liste `id=…&$limit=1`. | GET |

Champs attendus : `id`, `name`, `levels[].effects[]`, etc.

### 3.6 Spell-levels

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/spell-levels?lang=fr&$limit=…&$skip=…` | GET |

Filtres : `breedId`, `spellId`, etc. Utilisé pour lier classes ↔ sorts.

### 3.7 Effets (dictionnaire)

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/effects?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/effects/{id}?lang=fr` | GET |

Définitions d’effets (characteristic, elementId, etc.). Les instances sont dans `items.effects[]` et `spell-levels.effects[]`.

### 3.8 Item-types

| Requête | URL | Méthode |
|---------|-----|--------|
| Liste | `/item-types?lang=fr&$limit=…&$skip=…` | GET |
| Unitaire | `/item-types/{id}?lang=fr` | GET |

Utilisé pour les registries (resource_types, consumable_types, item_types) et le mapping superTypes/types. Total ~232 types.

### 3.9 Catalogues (races, types, superTypes) — collecte comme entités

Les **races de monstres**, **item-types** et **super-types** sont exposés comme entités collectables via la commande `scrapping`, au même titre que les monstres ou les items :

| Entité | Endpoint | Commande | Remarque |
|--------|----------|----------|----------|
| **monster-race** | `/monster-races?$limit=50&$skip=…` | `--collect=monster-race` | Pagination 50. Récupère toutes les races (id, name). |
| **item-type** | `/item-types?$limit=50&$skip=…` | `--collect=item-type` | Pagination 50, ~232 types. Chaque type a `superTypeId`, `name`, etc. |
| **item-super-type** | dérivé de `/item-types` | `--collect=item-super-type` | Pas d’endpoint dédié : la collecte pagine `/item-types` puis regroupe par `superTypeId` pour retourner la liste unique des superTypes (id, name). Par défaut tout est récupéré (`--limit=0`). |

Exemples :

```bash
# Toutes les races de monstres (limit=0 = tout par défaut)
php artisan scrapping --collect=monster-race --json

# Tous les item-types (232)
php artisan scrapping --collect=item-type --json

# Tous les superTypes uniques (dérivés des item-types)
php artisan scrapping --collect=item-super-type --json

# Limiter à 100 objets, ou commencer à l'offset 50
php artisan scrapping --collect=monster --limit=100 --offset=0 --json
```

Ces entités sont en **catalogue seul** (`meta.catalogOnly`) : pas d’intégration en base ; `--integrate` est ignoré.

---

## 4. Pagination

- Toujours utiliser **le `limit` renvoyé** dans la réponse pour calculer le `skip` de la requête suivante (pagination API en interne).
- Cap fréquent : **50** (certains endpoints acceptent 100).
- Options côté collecte : **limit** (nombre max d'objets à retourner, 0 = tout) et **offset** (objets à ignorer au début). Plus de notion de « page » côté utilisateur.
- **Monstres** : total 5093 → paginer de 50 en 50 (`$skip=0` à `5050`) pour tout récupérer ; les races sont à déduire du champ `race` de chaque monstre (voir § 3.2).

---

## 5. Relations dans les réponses

| Entité | Relation | Chemin / remarque |
|--------|----------|-------------------|
| Monstre | Sorts | `spells[]` (ids ou objets) |
| Monstre | Drops | `drops[]` (itemId, quantity) |
| Classe | Sorts | Via spell-levels (breedId) |
| Sort | Invocations | Référence vers monstres |
| Item | Recette | `recipe` ou équivalent (ressources) |
| Item | Effets | `effects[]` (effectId, valeurs) |

Ces relations déterminent l’**ordre de résolution** et l’option **include_relations** (voir VISION_ET_ARCHITECTURE).

---

## 6. À faire côté découverte

- [ ] Appels réels (curl / Postman) sur 1–2 entités (ex. monster, item) pour **valider** la structure JSON et les noms de champs.
- [ ] Noter le **limit** effectif renvoyé par chaque endpoint (50 vs 100).
- [ ] Vérifier la forme exacte de `name` (objet `{ fr: "...", en: "..." }` ou autre).
- [ ] Vérifier la forme de `drops` et `spells` (tableau d’ids vs tableau d’objets).

Une fois ces vérifications faites, mettre à jour ce document et les configs en conséquence.
