# Plan de refonte totale — UI Scrapping

**Objectifs :** Simplicité et robustesse, en conservant **toutes** les fonctionnalités décrites dans [SPEC_UI_SCRAPPING.md](./SPEC_UI_SCRAPPING.md).

**Principes directeurs :**
- **Simplicité** : un fichier = une responsabilité claire ; peu d’état partagé ; noms explicites ; pas de logique dupliquée.
- **Robustesse** : pas de throw dans le chemin critique (affichage, preview, batch) ; parsing et API toujours protégés ; valeurs par défaut sûres ; contrats explicites (props, events, retour des composables).

---

## 1. Architecture cible

### 1.1 Arborescence proposée

```
resources/js/
├── Composables/
│   └── scrapping/
│       ├── useScrappingSearch.js      # Recherche + pagination + rawItems
│       ├── useScrappingPreview.js     # Preview batch + convertis + relations
│       ├── useScrappingBatch.js       # Simuler / Importer (payload, runBatch, runByPages)
│       ├── useScrappingItemStatus.js  # Statuts par ligne (key, label, color, set)
│       ├── useScrappingCompare.js     # Helpers comparaison (triple, flatten, comparisonRows)
│       └── index.js                   # Réexport si besoin
├── config/
│   └── scrapping/
│       ├── statusConfig.js            # STATUS_LABELS, STATUS_COLORS, TERMINAL_STATUSES
│       ├── relationConfig.js          # RELATION_EXTRACT_CONFIG, RELATION_TYPE_LABELS
│       └── entityLabels.js            # labelOverrides par type (optionnel, ou depuis API)
├── utils/
│   └── scrapping/
│       ├── parseIdsFilter.js          # Texte → { id } | { ids } | { idMin, idMax }
│       ├── parsePageRange.js          # "1-6,4" → [1,2,3,4,5,6]
│       └── api.js                     # fetch wrappers (getJson, postJson) avec erreur unifiée
└── Pages/
    └── Organismes/
        └── scrapping/
            ├── ScrappingDashboard.vue       # Orchestrateur (~400–600 lignes)
            ├── ScrappingFilters.vue         # Bloc filtres (IDs, nom, types, races, niveau, pagination)
            ├── ScrappingResultsTable.vue    # Tableau + barre d’actions (périmètre, Simuler, Importer)
            ├── ScrappingTableRow.vue        # Une ligne entité + détail dépliable (ou inclus dans ResultsTable)
            ├── ScrappingOptionsPanel.vue    # Options d’import + Historique + Erreurs batch
            ├── CompareModal.vue             # Inchangé (déjà isolé)
            └── (TypeManagerTable / EntityModal restent où ils sont, utilisés par le Dashboard)
```

Le store de préférences existant (`useScrappingPreferences`) reste utilisé ; les constantes actuellement dans le Dashboard migrent vers `config/scrapping/`.

---

## 2. Contrats et responsabilités

### 2.1 Config (fichiers plats, pas de Vue)

| Fichier | Exports | Rôle |
|---------|--------|------|
| `statusConfig.js` | `STATUS_LABELS`, `STATUS_COLORS`, `TERMINAL_STATUSES` | Une seule source de vérité pour les statuts affichés. |
| `relationConfig.js` | `RELATION_EXTRACT_CONFIG`, `RELATION_TYPE_LABELS`, `extractRelationsFromRaw(raw, entityType)` | Règles d’extraction des relations par type ; fonction pure, ne lance jamais. |

**Robustesse :** `extractRelationsFromRaw` est entourée d’un try/catch interne et retourne toujours un tableau (éventuellement vide).

---

### 2.2 Utilitaires (purs, testables)

| Fichier | Exports | Rôle |
|---------|--------|------|
| `parseIdsFilter.js` | `parseIdsFilter(text)` → `{}` ou `{ id }` ou `{ ids }` ou `{ idMin, idMax }` | Parsing du champ IDs. |
| `parsePageRange.js` | `parsePageRange(text)` → `number[]` (numéros de page 1-based, triés) | Parsing de la plage de pages (ex. "1-6,4"). |
| `api.js` | `getJson(url, options?)`, `postJson(url, body, options?)` | Fetch + `res.text()` puis `JSON.parse` ; en cas d’erreur HTTP ou parse, retourne `{ ok: false, error: string }` au lieu de throw. Optionnel : passer un `onError` pour toast. |

**Simplicité :** Les composables appellent ces utils ; pas de fetch direct dans le Dashboard.

---

### 2.3 Composables (état + logique, pas de template)

Chaque composable retourne un objet clair. Pas de ref/computed inutile exposé.

#### `useScrappingSearch(options)`

**Options :** `{ entityTypeRef, configRef, notifyError }` (refs et fonction pour toasts).

**Retourne :**
- `rawItems`, `lastMeta`, `searching`, `pageNumber`, `perPage`, `totalPages`, `totalRows`, `canPrev`, `canNext`
- `buildSearchQuery()` → string (query string)
- `runSearch()` → Promise (lance la recherche, met à jour rawItems/lastMeta, appelle optionalement `onSearchDone(ids)` pour chaînage preview)
- `goPrev()`, `goNext()`, `goToPage(n)`, `setPageSize(n)`

**Responsabilité unique :** Tout ce qui concerne la requête GET search et la pagination. Ne gère pas les statuts ni le preview (le parent enchaîne).

**Robustesse :** En cas d’échec API, `notifyError` est appelé ; `rawItems` et `lastMeta` ne sont pas mis à jour (ou mis à vide selon le choix).

---

#### `useScrappingPreview(options)`

**Options :** `{ entityTypeRef, rawItemsRef, notifyError, onSuccess? }`.

**Retourne :**
- `convertedByItemId` (ref : `Record<id, { raw, converted, existing, error }>`)
- `lastBatchRelationsByKey` (ref : `Record<string, Array<{ type, id }>>`)
- `loadingConverted`
- `fetchConvertedBatch()` → Promise (lit les IDs depuis rawItemsRef, appelle POST preview/batch, met à jour convertedByItemId et relations via `relationConfig`)

**Responsabilité unique :** Charger les données converties et relations pour les IDs courants. N’écrit pas les statuts (le parent peut mettre « converti » après succès).

**Robustesse :** Parsing JSON sécurisé ; si la réponse n’est pas success ou items invalide, pas de crash, `notifyError` appelé ; les relations sont extraites dans un try/catch (retour [] en erreur).

---

#### `useScrappingBatch(options)`

**Options :** `{ entityTypeRef, rawItemsRef, visibleItemsRef, selectedIdsRef, batchScopeRef, pageRangeRef, buildPayloadOptionsRef, notifyError, notifySuccess, setStatusForEntities, setStatusFromBatchResults }`.

**Retourne :**
- `importing`, `lastBatchResults`, `lastBatchErrorResults` (computed)
- `buildBatchPayload(simulate, scope)` → `{ entities, dry_run, include_relations, replace_mode, ... }`
- `runBatch(mode, scope)` → Promise (mode: 'simulate' | 'import', scope: 'selection' | 'all')
- `runImportByPages(simulate)` → Promise (boucle pages, search + batch)
- `runBatchOrByPages(mode)` → Promise (délègue selon batchScope)
- `clearBatchErrors()` pour fermer la carte d’erreurs

**Responsabilité unique :** Construire le payload et exécuter simulate/import (batch ou par pages). Met à jour les statuts via les callbacks fournis et `lastBatchResults` / relations si la réponse les contient.

**Robustesse :** Vérification CSRF avant envoi ; en cas d’erreur HTTP ou body invalide, statut « erreur » pour les entités concernées et `notifyError`.

---

#### `useScrappingItemStatus(options)`

**Options :** `{ entityTypeRef }`.

**Retourne :**
- `itemStatusByKey` (ref)
- `statusKey(item)` → string
- `getStatusEntry(item)` → `{ status, error? } | null`
- `getStatusLabel(item)` → string | null
- `getStatusColor(item)` → string
- `setStatusForEntities(entities, status, error?)`
- `setStatusFromBatchResults(results, isSimulate)`
- `clearStatusForEntityType(entityType)` (pour reset tableau)

Utilise `statusConfig.js` en interne.

**Responsabilité unique :** Stocker et dériver l’affichage des statuts par ligne. Pas d’appel API.

---

#### `useScrappingCompare(options)`

**Options :** `{ convertedByItemIdRef, configRef }` (config pour comparisonKeys / entityType).

**Retourne :**
- `existingRecord(item)` → record | null
- `cellTriple(item, getExisting, getConverted, getRaw)` → `{ existant, converti, brut }`
- `tripleName(item)`, `tripleLevel(item)`, `tripleType(item)` (prédéfinis pour Nom, Niveau, Type)
- `comparisonRows(item)` → `Array<{ key, brut, converti, existant, differs }>`
- Helpers si besoin : `flattenForCompareShallow`, `findInFlat`, `convertedName`, `convertedLevel`, `extractFirstBlock`

**Responsabilité unique :** Dériver les valeurs d’affichage pour une ligne (existant / converti / brut) et les lignes du tableau de comparaison. Fonctions pures à partir des refs.

---

### 2.4 Composants Vue

#### `ScrappingDashboard.vue`

- **Rôle :** Orchestrateur. Charge meta + config au mount ; compose Filtres, Tableau, Options ; gère les modales (Compare, Entity, TypeManager) et les callbacks (ex. onCompareImported → setStatusForEntities(…, 'importé')).
- **État local minimal :** `compareModalOpen`, `compareEntityType`, `compareDofusdbId`, `entityModalOpen`, `typeManagerOpen`, `showOptionsAndHistory`, `expandedRowId`, `tableSearch`, `historyLines`, `selectedIds`. Le reste vient des composables.
- **Simplicité :** Le fichier ne contient ni parsing ni construction de payload ni extraction de relations ; il appelle les composables et passe les refs/callbacks aux enfants.

#### `ScrappingFilters.vue`

- **Props :** `entityType`, `config` (supported filters, knownTypes, knownRaces, etc.), `modelValue` (objet filtres : ids, name, typeMode, typeIds, typeIdsNot, raceMode, raceIds, levelMin, levelMax, breedId, page, perPage).
- **Events :** `update:modelValue`, `search` (clic Rechercher).
- **Rôle :** Afficher les champs de filtre selon le type d’entité et la config ; bouton « Gérer les types/races » (emit ou callback) ; pagination (TanStackTablePagination) ; bouton Rechercher. Pas de logique de build de query (ça reste dans useScrappingSearch, le parent appelle runSearch quand l’utilisateur clique).

#### `ScrappingResultsTable.vue`

- **Props :** `rows` (visibleRowsWithRelations), `selectedIds`, `expandedRowId`, `loadingConverted`, `entityType`, `config` (comparisonKeys, supports), `getStatusLabel`, `getStatusColor`, `tripleName`, `tripleLevel`, `tripleType`, `comparisonRows`, `relationTypeLabel`, etc. (tout ce qui est nécessaire pour rendre les cellules).
- **Events :** `update:selectedIds`, `toggle-expand`, `open-compare`, `open-entity`.
- **Rôle :** Tableau (checkbox, ID, déplier, État, Nom, Existe, Type, Race, Niveau) + lignes de relation + ligne dépliée (comparaison + effets). Émet les actions (sélection, déplier, double-clic comparaison, clic Existe).

#### `ScrappingOptionsPanel.vue`

- **Props :** `open` (repliable), `importOptions` (refs ou objet : includeRelations, replaceMode, whitelist, blacklist), `historyLines`, `batchErrorResults`, `entityType`.
- **Events :** `update:open`, `update:importOptions`, `clear-history`, `clear-errors`, `export-errors-csv`.
- **Rôle :** Bloc « Options & historique » + carte « Erreurs import batch » avec tableau et Export CSV. Pas de logique batch (uniquement affichage et emit).

---

## 3. Flux de données (simplifié)

```
[ScrappingDashboard]
  │
  ├─ useScrappingPreferences (existant) → prefs + persist
  ├─ useScrappingSearch     → rawItems, lastMeta, runSearch, pagination
  ├─ useScrappingPreview    → convertedByItemId, relations, fetchConvertedBatch
  ├─ useScrappingBatch      → runBatch, runBatchOrByPages, lastBatchResults
  ├─ useScrappingItemStatus → itemStatusByKey, setStatus*, getStatus*
  └─ useScrappingCompare    → triple*, comparisonRows, existingRecord
  │
  ├─ ScrappingFilters       (v-model filtres, @search → runSearch puis fetchConvertedBatch)
  ├─ ScrappingResultsTable  (rows = f(visibleItems, relations), statuts, triples)
  └─ ScrappingOptionsPanel  (options, historique, erreurs batch)
```

- **Recherche :** Clic Rechercher → Dashboard appelle `runSearch()` puis `fetchConvertedBatch()` et met les statuts « recherché » / « converti » (via setStatusForEntities).
- **Simuler / Importer :** Clic Simuler ou Importer → `runBatchOrByPages(mode)` ; le composable batch met à jour les statuts et lastBatchResults via les callbacks fournis.
- **Comparaison :** Double-clic ligne → ouverture CompareModal ; à l’import réussi, Dashboard reçoit `@imported` et appelle `setStatusForEntities([{ type, id }], 'importé')`.

---

## 4. Plan d’exécution par phases

Refonte **incrémentale** pour limiter les régressions : chaque phase livre un état fonctionnel.

### Phase 0 — Préparation (sans casser l’existant)

1. Créer les dossiers `resources/js/config/scrapping/`, `resources/js/utils/scrapping/`, `resources/js/Composables/scrapping/`.
2. Extraire **config** : `statusConfig.js`, `relationConfig.js` (avec `extractRelationsFromRaw` déplacée depuis le Dashboard). Le Dashboard importe ces fichiers et supprime les constantes locales.
3. Extraire **utils** : `parseIdsFilter.js`, `parsePageRange.js`. Créer `api.js` (getJson/postJson sécurisés). Le Dashboard les utilise à la place du code inline.
4. **Vérifier** : recherche, preview, simuler, importer, comparaison, statuts, erreurs batch. Aucun changement de comportement.

### Phase 1 — Composables

5. **useScrappingItemStatus** : extraire statuts (itemStatusByKey, setStatus*, getStatus*, clearStatusForEntityType). Dashboard utilise le composable, supprime le code correspondant.
6. **useScrappingSearch** : extraire buildSearchQuery, runSearch, rawItems, lastMeta, pagination. Dashboard utilise le composable.
7. **useScrappingPreview** : extraire fetchConvertedBatch, convertedByItemId, lastBatchRelationsByKey (avec relationConfig). Dashboard utilise le composable et enchaîne après runSearch.
8. **useScrappingCompare** : extraire cellTriple, tripleName/Level/Type, comparisonRows, existingRecord, flatten*, convertedName/Level, extractFirstBlock. Dashboard et tableau utilisent le composable.
9. **useScrappingBatch** : extraire buildBatchPayload, runBatch, runImportByPages, runBatchOrByPages, lastBatchResults. Dashboard fournit les refs et callbacks (setStatusForEntities, setStatusFromBatchResults, rawItems, selectedIds, etc.) et utilise le composable.
10. **Vérifier** : tous les scénarios (recherche, sélection, tous, par pages, simuler, importer, comparaison, reset, export CSV erreurs).

### Phase 2 — Découpage du template

11. **ScrappingFilters** : créer le composant, y déplacer le bloc filtres (IDs, nom, types, races, niveau, pagination, bouton Rechercher, Gérer types/races). Dashboard passe les refs (filtres, config, entityType) et écoute @search.
12. **ScrappingOptionsPanel** : créer le composant (options d’import, historique, erreurs batch). Dashboard passe les refs et écoute les events.
13. **ScrappingResultsTable** : créer le composant ; y déplacer le tableau (thead, tbody, lignes entité + relation + détail). Dashboard passe rows, selectedIds, expandedRowId, et les getters (statut, triple*, comparisonRows). Événements : update:selectedIds, toggle-expand, open-compare, open-entity.
14. (Optionnel) **ScrappingTableRow** : extraire une ligne + détail en sous-composant si le fichier ResultsTable reste trop long.
15. **Vérifier** : même comportement qu’avant ; aucun régression visuelle ou fonctionnelle.

### Phase 3 — Nettoyage et robustesse

16. Remplacer tous les `fetch` + `res.json()` directs par `api.getJson` / `api.postJson` (ou équivalent) avec gestion d’erreur centralisée.
17. S’assurer qu’aucun chemin critique (preview, batch, affichage) ne peut throw : try/catch dans les composables et retour de valeurs par défaut ou appel à notifyError.
18. Nettoyer les imports inutilisés, les variables mortes, et les doublons. Documenter (JSDoc) les composables et les props/events des composants.
19. Vérification finale manuelle + mise à jour de la spec si un détail a changé.

---

## 5. Règles de robustesse à respecter

- **API :** Toujours parser la réponse avec un try/catch (ou via `api.getJson`/`postJson`) ; en cas d’échec, notifier l’utilisateur et ne pas écraser les données affichées par des valeurs invalides.
- **Preview batch :** Si la réponse n’est pas `success && items`, ne pas modifier `convertedByItemId` ; afficher un message d’erreur.
- **Extraction des relations :** Toujours dans un try/catch ; retourner `[]` en cas d’exception.
- **Statuts :** Ne jamais laisser une clé avec `undefined` ou un statut inconnu ; utiliser un libellé par défaut (ex. « — ») et une couleur neutre.
- **Payload batch :** Valider que `entities.length > 0` avant envoi ; vérifier le token CSRF.

---

## 6. Récapitulatif

| Avant | Après |
|-------|--------|
| 1 fichier ~2 700 lignes | 1 Dashboard ~500 lignes + 5 composables + 3 config/utils + 3–4 composants Vue |
| Constantes et logique mélangées | Config et utils dans des modules dédiés ; logique dans des composables à responsabilité unique |
| Fetch et parsing dispersés | API centralisée (getJson/postJson) avec erreur gérée |
| Difficile à tester | Utils et composables testables unitairement ; composants testables en isolation avec des props mockées |

Les **fonctionnalités** restent celles de [SPEC_UI_SCRAPPING.md](./SPEC_UI_SCRAPPING.md) ; seuls l’**organisation du code** et la **robustesse** (gestion d’erreurs, pas de throw en chemin critique) changent. La refonte peut être réalisée phase par phase en validant à chaque étape que le comportement reste identique.
