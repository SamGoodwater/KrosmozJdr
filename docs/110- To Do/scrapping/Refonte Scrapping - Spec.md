## Refonte Scrapping — Spécification (base de travail)

### Contexte
Le scrapping actuel (collect → conversion → intégration pilotés par un orchestrateur) est jugé ancien, peu optimisé et peu testé. L’objectif est de **refondre** cette fonctionnalité pour la rendre :
- **configurable** (pilotée par fichiers de configuration),
- **réutilisable** (appelable depuis différents endroits du site, en **page** ou en **modal**),
- **multi-sources** (DofusDB aujourd’hui, mais extensible à d’autres APIs),
- **interactive** (prévisualisation, validation, comparaison avant écriture),
- **filtrable** (par ids, noms, types, relations, etc),
- **safe** (validation, sécurité, auto_update, audit).

---

### Objectifs
- **Définir une architecture de scrapping pilotée par config** (entités récupérables, mapping champs source → champs KrosmozJDR, fonctions de formatage).
- **Exposer une UI** permettant :
  - de sélectionner une *source* (ex: DofusDB),
  - de sélectionner une *entité* (ex: monster, spell…),
  - de choisir *quels champs* récupérer,
  - de choisir *le mode* (preview seulement / enregistrer),
  - d’activer/désactiver *conversion* et *intégration*,
  - de **prévisualiser** (avant validation),
  - de **comparer** avec l’existant (si même ID présent en DB) et de choisir champ par champ quoi garder/modifier,
  - de gérer les relations (inclure/exclure, profondeur, stratégie anti-boucle),
  - d’appliquer des **filtres** (ids, noms, type, etc. + filtres basés sur la DB comme valeurs par défaut).
- **Gérer l’état `auto_update`** des entités pour savoir si le scrapping peut mettre à jour automatiquement ou si l’entité est “verrouillée/manuelle”.

### Non-objectifs (pour cadrer)
- Refaire toute l’UX du back-office (on réutilise au maximum les composants existants, notamment TanStack Table + toolbar/filters).
- Implémenter toutes les sources externes dès le départ (on prépare l’architecture pour en ajouter, mais on commence par DofusDB).

---

### Concept clé : un modèle de “Section Scrapping”
Créer une **Section** (au sens du projet) qui encapsule :
- la configuration (source + entité + mapping),
- l’état UI (filtres, sélection de colonnes/champs, results, sélection, diff),
- les actions (search/preview, convert, validate, integrate, batch validate),
- l’utilisation en **page** ou en **modal**.

> Idée : cette section doit être “headless-friendly” (logique centrale réutilisable) et branchable à différentes vues.

---

## Architecture fonctionnelle proposée

### Pipeline (conceptuel)
Le pipeline est piloté par options, et peut s’arrêter à différentes étapes :
1) **Collect**: appeler la source, récupérer des données “brutes” (raw).
2) **Normalize (optionnel)**: uniformiser la forme des réponses (pagination, champs multi-langues…).
3) **Convert (optionnel)**: appliquer les “formatters” + règles de conversion (ex: clamp, map types…).
4) **Preview/Validate (optionnel)**: présenter à l’UI, valider les champs, marquer warnings/erreurs.
5) **Integrate (optionnel)**: écrire en DB (create/update), gérer `auto_update`, gérer les relations.

### Multi-sources (architecture)
Chaque source doit exposer une interface stable :
- **capabilities** (support du fetch by id, par filtre, pagination, endpoints, etc.)
- **fetchOne / fetchMany** (selon les capabilities)
- **normalizers** (par source)
- **security constraints** (hosts allowlist, rate limit, etc.)

---

## Configuration : fichiers de config “scrapping”

### Emplacement & format
À définir (exemples) :
- `config/scrapping/sources/*.php` ou `resources/scrapping/*.json`
- Idéal : un format **lisible, versionnable**, et simple à valider.

### Objectif du fichier de config
Un fichier doit permettre de décrire :
- **Sources disponibles** (ex: DofusDB)
- **Entités scrappables** par source
- **Mapping** : source.entity.field → KrosmozJDR.model.field
- **Endpoint** + paramètres autorisés
- **Formatters** par champ (et éventuellement par entité)
- **Relations** (ex: monster → spells, drops ; spell → summon)
- **Filtres** supportés (avec valeurs par défaut basées sur la DB si pertinent)

### Exemple de “schema” de config (proposition JSON)

```json
{
  "version": 1,
  "source": "dofusdb",
  "baseUrl": "https://api.dofusdb.fr",
  "entities": {
    "monster": {
      "source": {
        "endpoint": "/monsters/{id}",
        "supports": {
          "fetchOne": true,
          "fetchMany": true,
          "filters": ["id", "name", "raceId", "levelMin", "levelMax"]
        }
      },
      "target": {
        "krosmozEntity": "monster",
        "primaryKey": { "sourceField": "id", "targetField": "dofusdb_id" },
        "autoUpdateField": "auto_update"
      },
      "mapping": [
        {
          "key": "name",
          "from": { "path": "name", "langAware": true },
          "to": [{ "model": "creatures", "field": "name" }],
          "formatters": [{ "name": "pickLang", "args": { "lang": "{lang}", "fallback": "fr" } }]
        }
      ],
      "relations": {
        "spells": {
          "enabledByDefault": true,
          "strategy": "import_and_link",
          "maxDepth": 1,
          "extract": { "path": "spells", "idPath": "id" },
          "targetEntity": "spell"
        }
      }
    }
  }
}
```

Notes :
- `from` accepte un “path” (dot-notation) avec variables (ex: `{lang}`).
- `to` pointe un modèle/structure KrosmozJDR (ex: `creatures.name`).
- `formatter` référence une fonction connue (liste blanche) + paramètres.

---

## UI : table + filtres + validation

### Écran / modal “Scrapping”
- **Toolbar**:
  - Source (select)
  - Entité (select)
  - Mode : *Preview* / *Enregistrer*
  - Toggles : *Convert* / *Integrate*
  - Relations: include / depth / stratégie (anti-boucle)
  - Filtres:
    - ids (liste)
    - range d’ids
    - noms (contains/startsWith/exact)
    - types (items/resources/spells…) + types DB cochés par défaut
    - options spécifiques à l’entité
  - Bouton : **Lancer la recherche**

### Table des résultats
Chaque ligne représente une entité “candidate” avec états :
- **raw** (résumé)
- **converted** (résumé)
- **existing** (si trouvé en DB)
- **diff** (si existing)
- **status** : ok / warning / error
- **actions** :
  - voir le détail
  - valider la ligne
  - ignorer la ligne
  - (si existing) choisir champ par champ quoi garder

### Détail d’une ligne (drawer/modal)
- Tabs :
  - Raw
  - Converted
  - Existing (DB)
  - Diff (champ par champ)
- Possibilité de **cocher/décocher** les propriétés à intégrer.
- Boutons :
  - “Valider cette entité”
  - “Valider et intégrer”
  - “Ignorer”

### Validation en masse
- Boutons :
  - “Tout valider”
  - “Tout intégrer” (avec confirmation)
  - “Intégrer la sélection”

---

## Comparaison & stratégie de mise à jour

### Comparaison
Si une entité existe en DB avec le même ID externe (ex: `dofusdb_id`) :
- générer une structure `diff` (par champ)
- proposer 3 modes :
  - **keep_db** (conserver la valeur DB)
  - **use_scrapped** (remplacer)
  - **custom** (choix champ par champ)

### Auto-update
Chaque entité KrosmozJDR a un flag (ou équivalent) :
- si `auto_update=false`, l’intégration :
  - n’écrase pas les champs sensibles (ou n’écrase rien),
  - marque l’entité comme “skipped/locked”,
  - laisse l’UI proposer une action manuelle (override explicite).

---

## Relations & risques de récursion
Les entités sont fortement liées :
- spell → invocation → monster
- monster → drops/resources/items
- item → recipe → resources

Exigences :
- **max_depth** configurable
- **anti-boucle** (visited set par `(source, entity_type, id)`)
- **stratégies** :
  - fetch_only (juste afficher les relations)
  - import_and_link (import + pivot)
  - link_only (si déjà existant en DB)
- **filtrage** possible par type de relation (ex: “ignorer drops”, “ignorer invocations”).

---

## Filtres avancés (incluant valeurs par défaut DB)
Cas typiques :
- Tout importer / seulement certains IDs / seulement un range
- Filtrer par noms (exact, contains, startsWith)
- Filtrer par types (ex: typeId DofusDB → types KrosmozJDR)
- Filtrer par types déjà présents en DB (cases cochées par défaut), puis possibilité d’ajustement manuel rapide

---

## Sécurité & robustesse
- Validation stricte des inputs UI (IDs, filtres).
- Allowlist de hosts pour images et endpoints.
- Rate limiting côté source.
- Journalisation (correlation id pour un run).
- Gestion des erreurs : par entité, sans casser tout le batch.

---

## Tests & critères d’acceptation (MVP)

### MVP (première itération)
- Une source : **DofusDB**
- 2 entités supportées bout en bout : **monster** et **spell**
- UI :
  - filtre par ID / liste d’IDs
  - preview + conversion optionnelle
  - comparaison si existant (diff simple)
  - intégration optionnelle
  - include_relations + max_depth (au moins 1)
- Respect de `auto_update`

### Critères d’acceptation
- On peut lancer un preview sans écrire en DB.
- On peut intégrer une entité et choisir champ par champ quoi mettre à jour si elle existe.
- Les relations ne créent pas de boucle infinie (max_depth + visited set).
- Les filtres par type (au moins pour “resources”) sont utilisables et initialisés par défaut depuis la DB si possible.
- Le système est extensible : ajouter une nouvelle entité ou une nouvelle source se fait principalement par config + un adaptateur source.

---

## Questions ouvertes (à trancher)
- Où stocker les configs : `config/` (PHP) vs `resources/` (JSON) ?
- Format des “formatters” :
  - noms de fonctions + params (liste blanche) ?
  - ou expressions plus libres (risque sécurité) ?
- Stratégie d’intégration :
  - mapping vers une structure “DTO” intermédiaire unique ?
  - ou mapping direct vers Eloquent + services ?
- Comment représenter proprement le mapping multi-modèles (ex: monster touche `creatures` + `monsters`) ?

---

## Proposition concrète : structure des configs (recommandée)

### Choix recommandé
- **Fichiers de config “métier” versionnés** en **JSON** dans `resources/` (lisible, portable, multi-sources).
- **Config technique** (timeouts, allowlists, rate-limit, etc.) reste dans `config/scrapping.php` (+ `.env`) comme aujourd’hui.
- Un **loader/validator** côté backend charge les JSON, valide un schéma (liste blanche de formatters, champs autorisés, etc.), puis expose une représentation normalisée.

> Raison : on sépare la config “d’infrastructure” (env) de la config “de mapping” (versionnable, lisible, extensible).

### Arborescence proposée
- `config/scrapping.php` : réglages globaux (http, cache, images, rate limit, etc.)
- `resources/scrapping/`
  - `README.md` (format, conventions)
  - `formatters/` (doc + liste blanche “fonctionnelle”)
  - `sources/`
    - `dofusdb/`
      - `source.json` (base_url, auth si besoin, conventions)
      - `entities/`
        - `monster.json`
        - `spell.json`
        - `item.json`
        - `resource.json`
        - `panoply.json`
    - `...` (autres sources futures)

### Conventions de formatters (sécurité)
- **Liste blanche** côté backend (registry) : un formatter = un nom + params typés.
- Un formatter doit être **pur** (transforme une valeur) ou **safe-side-effect** (ex: `storeScrappedImage`) explicitement marqué.
- Interdire toute exécution arbitraire (pas d’expression libre).

### Exemple minimal de config d’entité (proposition)
- Décrire explicitement :
  - endpoints (`fetch_one`, `fetch_many`)
  - filtres supportés
  - mapping “target” multi-modèles (ex: `creatures.*` + `monsters.*`)
  - relations (entité cible + extraction d’IDs + stratégie)

---

## Backlog (découpage pour une implémentation maîtrisée)

### Epic A — “Engine” backend piloté par config
- **A1. Loader & validation des configs**
  - Charger les configs source/entité depuis `resources/scrapping/sources/**`
  - Valider : structure, chemins `from`, cibles `to`, formatters autorisés, relations, filtres
  - Produire une représentation normalisée (DTO interne)
- **A2. Registry de formatters**
  - Catalogue + signatures (params) + documentation
  - Formatters de base : `toString`, `toInt`, `clampInt`, `pickLang`, `mapEnum`, `nullable`, etc.
  - Formatter image : `storeScrappedImage(entityFolder)` (sécurisé via allowlist/size)
- **A3. Source adapter DofusDB**
  - `fetchOne(type,id,options)` + `fetchMany(type,filters,options)`
  - Normalisation : pagination Feathers, multi-langue, champs inconsistants
  - Rate limiting + retry + cache (configurable)
- **A4. Orchestration “par options”**
  - Exécuter : collect → normalize → convert → diff → integrate selon options
  - Anti-boucle relations : visited set `(source, entity, id)` + `max_depth`
  - Modes : preview-only / integrate
- **A5. Diff & merge plan**
  - Calculer un diff champ par champ (raw/converted vs existing DB)
  - Construire un “plan d’intégration” (keep_db / use_scrapped / custom)
- **A6. Respect de `auto_update`**
  - Bloquer update si `auto_update=false` (sauf override explicite)
  - Remonter l’état à l’UI (locked/skipped)

### Epic B — API backend (pour UI page/modal)
- **B1. Endpoints**
  - `GET /scrapping/config` (sources + entités + filtres + champs disponibles)
  - `POST /scrapping/preview` (résultats + diff + statuses)
  - `POST /scrapping/integrate` (intégration batch, avec plan)
  - (optionnel) `POST /scrapping/validate` (si validation séparée)
- **B2. Sécurité**
  - FormRequest (validation filtres/ids/options)
  - Authorization (policy/permission)
  - Logs corrélés par run

### Epic C — UI “Section Scrapping” (table + filtres + validation)
- **C1. Section “headless”**
  - State : source, entité, options (convert/integrate), relations (depth), filtres, colonnes/champs
  - Actions : search/preview, open detail, select, apply merge plan, integrate
- **C2. Vue Page + Vue Modal**
  - Même section, 2 wrappers (page et modal)
- **C3. Table TanStack**
  - Toolbar (filtres) + columns dynamiques (selon config)
  - Row detail (raw/converted/existing/diff)
  - Bulk actions (validate/integrate)

### Epic D — Types, filtres “par défaut DB”, et ressources
- **D1. Filtres par type**
  - Lire les types existants en DB (cases cochées par défaut)
  - Permettre override rapide (UI)
- **D2. Registry DB des types “ressource”**
  - Garder le principe `resource_types` (allowed/blocked/pending)
  - L’intégrer dans l’UI de filtres et dans la config (default_from_db)

### Epic E — Tests (minimum utile)
- **E1. Unit**
  - Loader/validator de config
  - Formatters registry
  - Diff/merge
- **E2. Integration**
  - Preview → integrate (happy path) sur monster/spell
  - Relations + max_depth + anti-boucle
- **E3. Front**
  - Tests de la section (state transitions) + affichage diff

---

## Plan de livraison (itérations)

### Itération 1 (MVP utilisable)
- DofusDB uniquement
- Entités : `monster` + `spell`
- Preview + conversion + diff simple + intégration
- Relations : include + `max_depth=1` + anti-boucle
- Respect `auto_update`

### Itération 2 (production-ready)
- Batch `fetchMany` + filtres avancés (range ids, name filters)
- Amélioration diff (champ par champ + sélection fine)
- Filtres “types” pré-cochés depuis DB
- Observabilité (logs corrélés, erreurs par ligne)

### Itération 3 (multi-sources)
- Ajout d’une 2e source “exemple” (même simple) pour valider l’architecture
- Harmonisation des normalizers et capabilities

