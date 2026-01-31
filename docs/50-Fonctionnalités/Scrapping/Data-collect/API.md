## API DofusDB — Référence (collect)

### Objectif
Cette page documente **l’API DofusDB telle qu’on l’utilise dans KrosmozJDR** pour la phase **Collect** :
- endpoints réellement consommés,
- syntaxe de requête (style FeathersJS),
- pagination (dont le cap fréquent à 50),
- conventions (langue, structures de réponse).

### Base URL et langue
- **Base URL** : `https://api.dofusdb.fr`
- **Langue** : la plupart des endpoints acceptent `lang=fr` (ou `en/de/es/pt`)

### Format de réponse (Feathers)
Sur les endpoints listés, DofusDB renvoie généralement :

```json
{
  "total": 4900,
  "limit": 50,
  "skip": 0,
  "data": [ { /* ... */ } ]
}
```

Notes :
- `total` peut être absent selon les services/variantes ; on le traite en **best effort**.
- `limit` peut être **différent** du `$limit` demandé (voir “cap à 50”).

### Syntaxe de requête (FeathersJS)
Les listes se requêtent en combinant `lang=fr` et des paramètres “Feathers” :

- **Pagination**
  - `$limit` : taille de page (ex: 50)
  - `$skip` : offset (ex: 0, 50, 100…)
- **Recherche**
  - `name[$search]=Bouftou` : recherche texte (sur champ `name`)
- **Tri**
  - `$sort=-id` : tri décroissant (le format exact dépend des endpoints)
- **Opérateurs courants**
  - `$in` : inclusion (ex: `id[$in][]=1&id[$in][]=2`)
  - `$gte` / `$lte` : bornes (ex: `level[$gte]=50&level[$lte]=100`)
  - `$ne` : “différent de”

Exemples :
- Page 1 de monstres par nom :
  - `GET /monsters?lang=fr&name[$search]=Bouftou&$limit=50&$skip=0`
- Page 2 (offset) :
  - `GET /monsters?lang=fr&name[$search]=Bouftou&$limit=50&$skip=50`

### Important : cap fréquent à 50
En pratique, certains endpoints **capent** le `$limit` (souvent à **50**), même si on demande 100/200.

Conséquence :
- Pour paginer correctement, il faut avancer le `$skip` avec **le `limit` réellement renvoyé** par l’API (`resp.limit`), pas avec le `$limit` demandé.

C’est exactement ce que fait maintenant :
- `app/Services/Scrapping/DataCollect/ConfigDrivenDofusDbCollector.php`
- et l’UI “Tout récupérer (pagination)” s’aligne sur `meta.limit`.

### Endpoints DofusDB utilisés (KrosmozJDR)
Les chemins ci-dessous sont ceux consommés par nos configs JSON (`resources/scrapping/sources/dofusdb/entities/*.json`).

#### 1) Classes (Breeds)
- **Liste** : `GET /breeds?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /breeds/{id}?lang=fr`

#### 2) Monstres
- **Liste** : `GET /monsters?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /monsters/{id}?lang=fr`

Filtres courants :
- `name[$search]=...`
- `id[$in][]=...`
- `id[$gte]=...&id[$lte]=...`
- `raceId=...`
- `level[$gte]=...&level[$lte]=...`

#### 3) Items (objets / ressources / consommables)
- **Liste** : `GET /items?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /items/{id}?lang=fr`

Filtres courants :
- `name[$search]=...`
- `typeId=...`
- `id[$in][]=...`
- `id[$gte]=...&id[$lte]=...`

> Dans KrosmozJDR, un item DofusDB peut finir en `items`, `resources` ou `consumables` selon notre mapping + registry `resource_types`.

#### 4) Panoplies (Item sets)
- **Liste** : `GET /item-sets?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /item-sets/{id}?lang=fr`

#### 5) Sorts
- **Liste** : `GET /spells?lang=fr&$limit=…&$skip=…`
- **Note** : DofusDB ne fournit pas toujours un `GET /spells/{id}` fiable.
  - Notre collector sait **simuler** un fetchOne via une recherche (id + limit=1).

#### 6) Niveaux de sorts
- **Liste** : `GET /spell-levels?lang=fr&$limit=…&$skip=…`

#### 7) Effets
- **Liste** : `GET /effects?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /effects/{id}?lang=fr`

#### 8) Types d’items (item-types)
Utilisé pour la registry “resource types” (superTypeId=9) et la détection côté scrapping.
- **Liste** : `GET /item-types?lang=fr&$limit=…&$skip=…`
- **Unitaire** : `GET /item-types/{id}?lang=fr`

### Lien avec la collect KrosmozJDR
Dans le code, la collecte DofusDB est centralisée ici :
- `app/Services/Scrapping/Http/DofusDbClient.php` : cache/retry/timeout
- `app/Services/Scrapping/DataCollect/ConfigDrivenDofusDbCollector.php` : fetchOne/fetchMany paginé piloté par JSON
- `app/Services/Scrapping/DataCollect/DataCollectService.php` : façade legacy + fallback
- `GET /api/scrapping/search/{entity}` : endpoint UI “collect-only” (pagination + filtres)

