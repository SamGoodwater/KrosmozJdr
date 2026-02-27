# Moteur de recherche d’entités — Besoins et conception

**Date** : 2026-02-27  
**Statut** : Conception (avant implémentation)

---

## 1. Objectifs

- **Recherche unifiée** : pouvoir rechercher toutes les entités (monstres, sorts, effets, ressources, etc.) avec des **filtres** et **options** communs (texte, type, état, etc.).
- **Intégration facile** : réutilisable dans différents contextes (formulaire de sort pour invocation = monstre, panoplie → objets, scénario → monstres, admin effets, etc.) sans dupliquer la logique.
- **Sécurité** : respect des droits par type d’entité (ex. effets/sous-effets accessibles en recherche uniquement pour `game_master` ou `admin`).

---

## 2. Cas d’usage

| Contexte | Entités recherchables | Rôle minimal | Remarque |
|----------|------------------------|--------------|----------|
| Édition sort → choix invocation | Monstres | lecture sorts/monstres | Liste potentiellement longue → recherche serveur |
| Édition sort → types de sort | Spell types | idem | Liste courte, optionnel |
| Édition panoplie → objets | Items | lecture items | Filtres par type, niveau |
| EntityRelationsManager (générique) | Variable (breeds, resources, etc.) | selon entité | Aujourd’hui `availableItems` en prop → passer en mode “search API” optionnel |
| Admin effets → choix effet / sous-effet | Effects, SubEffects | **game_master** ou admin | Restriction explicite |
| Scénario → monstres / PNJ | Monstres, NPCs | selon campagne/scénario | Filtres métier (race, niveau) |

---

## 3. Besoins fonctionnels

### 3.1 Backend (service de base)

- **Un point d’entrée ou un pattern commun** pour “rechercher des entités d’un type donné” avec :
  - **Query texte** : recherche sur nom, description, etc. (selon l’entité).
  - **Filtres** : par état, type, niveau, etc. (définis par entité).
  - **Options** : `limit`, `offset` ou `page`, `sort`, `order`.
- **Sécurité** :
  - Chaque type d’entité est protégé par la **Policy** Laravel existante (`viewAny` / `view`).
  - Pour **Effect** et **SubEffect** : n’autoriser la liste/recherche qu’aux rôles **game_master** ou **admin** (aligné avec [PERMISSIONS_SOURCE_OF_TRUTH.md](../../10-BestPractices/PERMISSIONS_SOURCE_OF_TRUTH.md)).
- **Réutilisation** : le même service (ou contrat) peut être utilisé par une API “search” dédiée et par les contrôleurs existants (ex. tables) pour éviter la duplication.

### 3.2 API — Option B (retenue)

- **Choix** : réutiliser les endpoints tables existants `GET /api/tables/{entity}?format=entities&search=...&limit=...&filters[...]=...&sort=...&order=...`.
- Les contrôleurs `*TableController` exposent déjà (ou doivent exposer) : `search`, `filters`, `limit`, `sort`, `order`, `format=entities`.
- **Endpoints à créer** si absents (nécessaires pour le moteur de recherche) :
  - `api.tables.effects` (modèle `Effect`) — liste/recherche protégée game_master+.
  - `api.tables.sub-effects` (modèle `SubEffect`) — idem.
  - Autres entités utiles selon besoins : `spell-types`, `monster-races`, `consumable-types`, etc.
- Chaque endpoint doit accepter les paramètres communs du contrat (voir § 3.4) et retourner en `format=entities` un tableau d’entités avec métadonnées (filterOptions, capabilities, etc.).

### 3.3 Propriétés du moteur (backend + frontend)

- **Sélection** : **simple** (une entité) ou **multiple** (plusieurs entités), avec l’UI adaptée (liste à choix unique vs liste à cases à cocher / tags des sélectionnés).
- **Filtrage des résultats reçus** :
  - **Whitelist** : n’afficher / n’accepter que les entités dont l’id (ou une clé) est dans une liste fournie (ex. `ids[]=1&ids[]=2` ou `whitelist=1,2,3`). Réduire côté backend ou filtrer côté front.
  - **Blacklist** : exclure les entités dont l’id est dans une liste fournie (ex. `exclude[]=...` ou `blacklist=...`). Utile pour masquer des entités déjà liées ou interdites dans le contexte.
- **Filtres par propriétés d’entité** : les entités n’ont pas les mêmes champs filtrables. Chaque endpoint `api.tables.{entity}` expose ses propres `filters` (ex. monstres : `size`, `is_boss`, `monster_race_id` ; sorts : `element`, `level` ; items : `item_type_id`, `rarity`). Le front envoie `filters[prop]=value` selon le type d’entité ; la doc ou le `filterOptions` retourné par l’API décrit les filtres disponibles par entité.
- **Tri (sort)** : l’output doit pouvoir être trié :
  - **Alphabétique** : `sort=name` (ou champ équivalent selon l’entité).
  - **Par propriétés** : ex. `sort=state`, `sort=level`, `sort=created_at`, etc. selon l’entité.
  - Paramètre `order=asc|desc` commun.
  - Chaque `*TableController` déclare les colonnes triables (`allowedSort`) et les applique.

### 3.4 Contrat API commun (Option B)

Paramètres attendus par tous les endpoints `api.tables.{entity}` :

| Paramètre | Type | Description |
|-----------|------|--------------|
| `format` | string | `entities` pour le picker/search (réponse : `entities[]`, `filterOptions`, etc.). |
| `search` | string | Recherche texte (nom, description, etc. selon l’entité). |
| `limit` | int | Nombre max de résultats (1–20000, défaut 5000). |
| `sort` | string | Colonne de tri (dépend de l’entité, ex. `name`, `state`, `id`, `created_at`). |
| `order` | string | `asc` ou `desc`. |
| `filters` | object | Filtres spécifiques à l’entité (ex. `filters[state]=playable`, `filters[size]=1`). |
| `whitelist` / `ids[]` | array | (Optionnel) Ids des entités à inclure uniquement. |
| `blacklist` / `exclude[]` | array | (Optionnel) Ids des entités à exclure. |

Réponse `format=entities` : `{ entities: [...], filterOptions: {...}, capabilities: {...} }` (voir réponses existantes des `*TableController`).

### 3.5 Frontend (intégration)

- **Composable** `useEntitySearch(entityType, options)` :
  - Paramètres : type d’entité, `filters` initiaux, `limit`, `debounce`, `whitelist`, `blacklist`, `sort`, `order`.
  - Retour : `query`, `results`, `loading`, `error`, `search()`, `reset()`, `filterOptions` (si renvoyé par l’API).
  - Appelle `api.tables.{entity}` avec `format=entities` et les paramètres ci-dessus.
- **Composant “Entity Picker”** (molécule ou organisme) :
  - **Sélection** : prop `multiple` (bool) — UI simple (liste radio / dropdown une seule valeur) ou multiple (checkboxes / tags des sélectionnés).
  - Champ de recherche + liste de résultats ; filtres et tri selon le type d’entité.
  - Props : `entityType`, `modelValue` (id, objet, ou tableau si multiple), `filters`, `whitelist`, `blacklist`, `placeholder`, `limit`, `multiple`, `sort`, `order`.
  - Émet `update:modelValue` avec l’entité (ou les entités) choisie(s).
  - Réutilisable dans Spell/Edit (invocation), EntityRelationsManager (mode API), admin effets, etc.
- **EntityRelationsManager** : mode “remote” optionnel (search via API, blacklist = ids déjà liés).

### 3.6 Deux variantes d’UI

- **Compact** :
  - Peu d’espace : déclencheur (bouton ou champ) ouvre un **popover** contenant la recherche, les filtres et la liste des résultats.
  - **Filtres** : regroupés dans le popover (ex. accordéon ou section repliable) ; **icônes sans label** pour les actions et filtres, avec **tooltips** pour les libellés.
  - Idéal pour barres d’outils, formulaires denses, cellules de tableau.
- **Étendu** :
  - Plus de place : zone dédiée avec recherche, filtres visibles (champs en ligne ou bloc), liste de résultats plus grande.
  - Labels affichés pour les filtres et actions.
  - Idéal pour pages dédiées (ex. admin effets), modales larges, panneaux latéraux.
- Le composant Entity Picker (ou un wrapper) accepte une prop **variant** : `compact` | `extended` pour basculer entre les deux.

---

## 4. Sécurité (détail)

- **Entités “standard”** (monstres, sorts, items, ressources, breeds, etc.) : déjà protégées par les Policies existantes (`viewAny` sur le modèle). La recherche n’est que une variante de liste (filtres + search).
- **Effets et sous-effets** :
  - Aujourd’hui : API GET effects sans auth (documenté ainsi pour l’affichage public).
  - Pour la **recherche / liste dans un picker** (ex. admin ou édition de sort) : restreindre à **game_master** et **admin**.
  - Proposition : soit une route dédiée `GET /api/effects/search` (ou `api/tables/effects`) protégée par un middleware ou une Policy `viewAny` sur `Effect` réservée aux rôles GM+ ; soit garder la route actuelle pour l’affichage en lecture et ajouter une route “search” protégée pour les pickers. À trancher avec l’existant (routes API effects).
- **Source de vérité** : [PERMISSIONS_SOURCE_OF_TRUTH.md](../../10-BestPractices/PERMISSIONS_SOURCE_OF_TRUTH.md) — toute décision côté backend (Policies/Gates).

---

## 5. Faut-il un plugin ?

### 5.1 Laravel (backend)

- **Scout (Laravel)** : recherche full-text avec moteurs (Algolia, Meilisearch, database driver). Utile si on veut recherche floue, typo-tolérance, ranking. Pour un “search” simple (LIKE + filtres), les requêtes Eloquent existantes (comme dans les `*TableController`) suffisent.
- **Pas de plugin obligatoire** pour la “recherche de base” : un **service** `EntitySearchService` (ou utilisation directe des requêtes dans les contrôleurs tables) avec un contrat commun (paramètres `q`, `filters`, `limit`, `sort`) suffit.
- **Plugin utile plus tard** si besoin de recherche full-text avancée (ex. Meilisearch) sur de gros volumes.

### 5.2 Frontend (Vue)

- **Composable + composant maison** : suffisant pour un picker “recherche par type d’entité” (appel API, debounce, affichage liste). Pas besoin de librairie lourde.
- **Librairies type “vue-select” ou “multiselect”** : peuvent être utiles pour l’UX (dropdown, async search). Le projet a déjà `SelectSearchField` (recherche locale). On peut soit étendre le pattern (source “remote” en plus des options en prop), soit introduire un composant dédié “EntityPicker” qui appelle l’API.

**Conclusion** : pas de plugin indispensable pour démarrer. Un **service backend** + **réutilisation des API tables** + **composable + Entity Picker** en front suffisent. Plugin (Scout/Meilisearch ou select async) peut être envisagé en phase 2 si les besoins évoluent (performance, UX avancée).

---

## 6. Endpoints tables existants et à créer

- **Déjà en place** (routes `api/tables.*`) : resources, **resource-types**, items, **item-types**, **consumable-types**, **monster-races**, **spell-categories**, spells, monsters, npcs, campaigns, scenarios, attributes, capabilities, breeds, specializations, creatures, consumables, panoplies, shops.
- **Entités de référence (types / catégories)** — utilisables dans les pickers pour filtrer ou afficher des listes de choix :
  - **resource-types** — types de ressources (`ResourceTypeTableController`).
  - **item-types** — types d’équipements (`ItemTypeTableController`) ; filtres : `state`, `decision`.
  - **consumable-types** — types de consommables (`ConsumableTypeTableController`) ; filtres : `state`, `decision`.
  - **monster-races** — races de monstres (`MonsterRaceTableController`) ; filtres : `state`, `id_super_race`.
  - **spell-categories** — catégories de sorts, liste statique (Inconnu, Offensif, Défensif, Utilitaire) (`SpellCategoryTableController`) ; pas de Policy, liste fixe.
- **À créer** pour couvrir tous les usages du moteur de recherche :
  - **effects** — `EffectTableController` + route `api.tables.effects` (Policy viewAny réservée player+).
  - **sub-effects** — `SubEffectTableController` + route `api.tables.sub-effects` (idem).

Chaque contrôleur table respecte le contrat commun : `search`, `filters`, `limit`, `sort`, `order`, `format=entities`, et optionnellement `whitelist` / `blacklist` (voir § 3.4).

---

## 7. Architecture proposée (résumé)

1. **Backend**
   - Conserver / généraliser le pattern des `*TableController` : `search`, `filters`, `limit`, `sort`, `order`, `format=entities`, et support **whitelist** / **blacklist** où pertinent.
   - Créer les endpoints manquants (effects, sub-effects, puis autres si besoin).
   - Policies : **Effect** et **SubEffect** — liste/recherche réservée à **game_master** et **admin**.
   - Optionnel : extraire un **EntitySearchService** (ou trait) pour centraliser la logique (whitelist/blacklist, tri, filtres) et éviter la duplication.

2. **API**
   - Contrat commun : paramètres listés en § 3.4 ; réponse `format=entities` avec `entities[]`, `filterOptions`, `capabilities`.
   - Documenter les filtres et colonnes triables par entité (dans le code ou une doc dédiée).

3. **Frontend**
   - **Composable** `useEntitySearch` : appelle `api.tables[entityType]` avec tous les paramètres (filters, whitelist, blacklist, sort, order), debounce, état loading/error/results.
   - **Composant EntityPicker** : sélection **simple ou multiple** (prop `multiple`), recherche + filtres + tri ; **deux variantes UI** : **compact** (popover, icônes + tooltips) et **étendu** (zone dédiée, labels visibles). Prop `variant: 'compact' | 'extended'`.
   - **EntityRelationsManager** : mode “remote” optionnel (search API, blacklist = ids déjà liés).

4. **Sécurité**
   - Policies existantes pour les entités métier ; **Effect** / **SubEffect** : accès liste/recherche limité à game_master et admin.

---

## 8. Prochaines étapes suggérées

1. **Créer les endpoints manquants** : `EffectTableController` + route `api.tables.effects`, `SubEffectTableController` + route `api.tables.sub-effects` ; Policies Effect/SubEffect (viewAny réservé game_master+). Ajouter si besoin spell-types, monster-races, consumable-types.
2. **Étendre le contrat commun** : dans les `*TableController` existants, ajouter le support de **whitelist** / **blacklist** (query params) et s’assurer que **sort** inclut au minimum `name` (ou équivalent) et `state` quand l’entité le possède.
3. **Implémenter** le composable `useEntitySearch` (params : filters, whitelist, blacklist, sort, order) et le composant **EntityPicker** (sélection simple/multiple, variant compact | extended).
4. **Intégrer** l’EntityPicker : d’abord dans la page d’édition de sort (invocation = monstre), puis dans l’admin effets et ailleurs selon besoin.
5. **Documenter** le contrat API (params communs, filtres et tri par entité) et les deux variantes UI (compact / étendu).

---

## 9. Références

- [PERMISSIONS_SOURCE_OF_TRUTH.md](../../10-BestPractices/PERMISSIONS_SOURCE_OF_TRUTH.md)
- [ROLES_AND_RIGHTS.md](../../20-Content/ROLES_AND_RIGHTS.md)
- [EntityRelationsManager](../EntityRelationsManager/README.md)
- [ScrappingSearchController](https://github.com/...)/app/Http/Controllers/Scrapping/ScrappingSearchController.php (recherche DofusDB, pas BDD Krosmoz)
- Contrôleurs `App\Http\Controllers\Api\Table\*TableController` (search, filters, format=entities)
