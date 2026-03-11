# Refonte TanStack Table — Analyse et plan

**Date :** 2026-03-10  
**Contexte :** Problèmes récurrents (recherche effacée au chargement, complexité mode serveur) et besoin de stabiliser l’architecture.

---

## 1. État des lieux

### Fichiers concernés
| Fichier | Lignes | Rôle |
|---------|--------|------|
| `TanStackTable.vue` | ~1940 | Organism principal : rendu, état UI, logique client/serveur |
| `EntityTanStackTable.vue` | ~340 | Wrapper : fetch API, permissions, adaptation |
| `useTanStackTablePreferences.js` | ~95 | Persistance colonnes visibles, pageSize |
| `useTableFilterPresets.js` | ~77 | API presets (base de données) |
| Molecules | ~5 | Toolbar, Filters, Header, Row, Pagination, Skeleton |

### Modes actuels
- **Client** : dataset local, filtres/recherche/tri/pagination côté client (getFilteredRowModel, getSortedRowModel, getPaginationRowModel).
- **Serveur** : dataset paginé par l’API, `serverParams` pilotés par les émissions du tableau, refetch à chaque changement.

---

## 2. Problèmes identifiés

### 2.1 Recherche effacée au chargement
- **Symptôme** : lors du chargement des résultats (loading → done), le texte de recherche disparaît.
- **Causes possibles** :
  1. **Double source de vérité** : `searchText` (local) et `serverParams.search` (parent) peuvent diverger.
  2. **Sync bidirectionnelle** : le watch qui restaure `searchText` depuis `serverParams` peut entrer en conflit avec l’input contrôlé.
  3. **Presets** : même avec l’exclusion en mode serveur, `reloadPresetsFromApi` peut interférer selon la chronologie.
  4. **Réactivité de l’input** : en mode contrôlé, si la prop change pendant le loading, l’affichage peut être réinitialisé.

### 2.2 Complexité du mode serveur
- **État dispersé** : `searchText`, `activeFilters`, `sortingState`, `paginationState` dans TanStackTable + `serverParams` dans EntityTanStackTable.
- **Sync multiple** : plusieurs watchers pour garder `paginationState` / `searchText` alignés avec `serverParams`.
- **Pagination TanStack** : `pageCount` non réactif en Vue → contournement avec émissions directes et `paginationCanPrev`/`paginationCanNext` calculés manuellement.
- **Comportements divergents** : pagination via `table.setPageIndex` (client) vs `emit('update:serverParams', { page })` (serveur).

### 2.3 Code mort / redondant
- **`getCellObject`** : alias de `getCellFor`, peu utilisé.
- **Compat kebab-case** : `update:selectedIds` et `update:selected-ids` émis en parallèle.
- **`resolveEntityRouteHref`** : importé mais usage limité.
- **`getEntityConfig`** : utilisé dans `getCellFor`, pourrait être injecté.
- **Watchers qui se chevauchent** : reset page client, sync serveur, etc.

### 2.4 Presets et mode serveur
- Presets pensés pour un dataset client complet.
- En mode serveur, l’auto-apply du preset par défaut est désactivé, mais le chargement des presets reste synchrone avec les recherches.
- Gestion de `isActivePresetDirty` potentiellement incohérente avec un état partagé serveur.

---

## 3. Pistes de refonte

### Option A — Montée de l’état en mode serveur (recommandé)
**Principe** : en mode `serverSide`, l’état de la requête (search, filters, sort, page) est uniquement dans `EntityTanStackTable`.

- `EntityTanStackTable` possède `serverParams` (source de vérité).
- `TanStackTable` en mode serveur : **contrôlé** — reçoit `serverParams` et l’utilise pour afficher recherche, filtres, tri, pagination.
- Plus de `searchText` local en mode serveur : l’input affiche `serverParams.search`.
- Changements utilisateur → `emit` → parent met à jour `serverParams` → refetch → nouvel affichage.
- **Avantage** : une seule source de vérité, pas de sync ni de boucles.
- **Impact** : refactor du flux de props/events en mode serveur.

### Option B — Composable `useTableQueryState`
**Principe** : extraire la logique d’état (recherche, filtres, tri, pagination) dans un composable réutilisable.

```js
// useTableQueryState.js
export function useTableQueryState(initial = {}, { serverSide, onParamsChange }) {
  const search = ref(initial.search ?? '')
  const filters = ref(initial.filters ?? {})
  const sort = ref(initial.sort ?? 'id')
  const order = ref(initial.order ?? 'desc')
  const page = ref(initial.page ?? 1)
  const pageSize = ref(initial.pageSize ?? 25)
  // ...
  return { search, filters, sort, order, page, pageSize, ... }
}
```

- Utilisé par TanStackTable et EntityTanStackTable.
- En mode serveur, le composable émet vers le parent ; en mode client, il pilote le filtrage local.
- **Avantage** : logique centralisée et testable.
- **Impact** : refactor important des deux composants.

### Option C — Séparation nette client / serveur
**Principe** : deux chemins de code distincts au lieu d’un hybride.

- `TanStackTableClient` : filtrage/tri/pagination locaux, state local.
- `TanStackTableServer` : tout en props + emits, pas de state local pour search/filters.
- `TanStackTable` : facade qui délègue à l’un ou l’autre selon `serverSide`.
- **Avantage** : comportements explicites, moins de `if (serverSide)`.
- **Inconvénient** : duplication de layout, toolbar, header, etc.

### Option D — Correction ciblée (minimal)
**Principe** : corriger le bug de recherche sans refonte.

- Utiliser `serverParams.search` comme unique source pour l’input en mode serveur (input 100 % contrôlé par le parent).
- Adapter le flux : input → emit immédiat (ou debounced) → parent met à jour `serverParams` → parent repasse en prop → input affiche la nouvelle valeur.
- **Avantage** : faible impact.
- **Risque** : ne règle pas les autres points de complexité.

---

## 4. Recommandations

### Priorité 1 — Corriger la recherche (court terme) ✅ fait
- Implémenter l’**input contrôlé** en mode serveur : valeur via `serverSearchDraft` (brouillon local), sync depuis `serverParams.search` au montage et après fetch.
- Émission au changement (debounce) : `emit('update:serverParams', { search })`.
- Suppression de la sync bidirectionnelle `serverParams` ↔ `searchText` dans le watcher (garder uniquement `paginationState`).
- Feedback immédiat à la saisie via `serverSearchDraft`.

### Priorité 2 — Nettoyer le code mort
- Supprimer ou fusionner `getCellObject` avec `getCellFor`.
- Simplifier les doubles emissions (selectedIds) si plus nécessaires.
- Documenter ou supprimer les chemins de code réellement inutilisés.

### Priorité 3 — Refonte architecturale (moyen terme)
- Appliquer **Option A** : montée de l’état en mode serveur, TanStackTable en mode contrôlé.
- Ou **Option B** si on veut une base réutilisable pour d’autres tableaux.
- Prévoir une phase de tests (client + serveur) avant mise en prod.

### Priorité 4 — Presets en mode serveur
- Désactiver les presets en mode serveur, ou les adapter pour qu’ils n’écrasent pas la recherche.
- Ou : charger les presets une seule fois au mount, sans auto-apply, et ne plus les recharger pendant les recherches.

---

## 5. Plan d’action proposé

| Phase | Action | Effort | Risque | Statut |
|-------|--------|--------|--------|--------|
| 1 | Input contrôlé pour la recherche (Option D) | Faible | Faible | ✅ 2026-03-10 |
| 2 | Nettoyage code mort + simplification emits | Faible | Faible | ✅ 2026-03-10 |
| 3 | useTableServerParams + useTableSearch (Option A) | Moyen | Moyen | ✅ 2026-03-10 |
| 4 | Virtualisation pour gros tableaux client | Moyen | Moyen | ✅ 2026-03-10 |
| 5 | Tests E2E sur sorts + une autre entité | Moyen | - | |
| 6 | Documentation TANSTACK_TABLE.md | Faible | - | ✅ |

---

## 6. Correctif input recherche (2026-03-10)

### Problème
L'input de recherche n'acceptait aucun caractère (reste vide malgré la saisie).

### Cause probable
Le composant `InputCore` utilise `hasVModelListener` (détection via `$attrs`) pour décider d'afficher `props.modelValue`. La chaîne Toolbar → InputCore avec `:model-value` + `@update:model-value` pouvait ne pas être correctement détectée par InputCore dans certains contextes (héritage d'attrs, réactivité).

### Solution
Remplacement de `InputCore` par un **input HTML natif** dans `TanStackTableToolbar.vue` :
- `:value="searchInputValue"` + `@input="onSearchInput"`
- Valeur locale `searchInputValue` mise à jour immédiatement à la saisie
- Watch pour sync depuis `props.searchValue` (preset, clear côté parent)
- Suppression de la dépendance à InputCore pour ce champ

Si le problème persistait, la refonte plus profonde (composable, découpage) resterait recommandée.

---

## 7. Autres optimisations possibles

### 7.1 Code mort et redondances (priorité faible)

| Élément | Action | Impact |
|---------|--------|--------|
| `getCellObject` | Supprimer, remplacer par `getCellFor` partout | -5 lignes, moins de confusion |
| Doubles emits `update:selectedIds` + `update:selected-ids` | Garder ou migrer vers une seule convention | Cohérence |
| Import `resolveEntityRouteHref` | Utilisé dans `getCellFor` ; OK | - |
| Logique `rowBool` dupliquée | Extraire `parseBooleanFilterValue(value)` dans `passesFilter` | Lisibilité |

### 7.2 Performance

| Optimisation | Description | Effort | Gain |
|--------------|-------------|--------|------|
| **Memoization getCellFor** | `getCellFor` appelé à chaque rendu de cellule. Option : cache par `(row.id, col.id)` avec invalidation lors du changement de config | Moyen | Limite les recalculs sur gros tableaux client |
| **Computed filteredRows** | En mode client, le filtrage parcourt toutes les lignes à chaque changement. Option : debounce sur searchText/filters si dataset > 1000 | Faible | Moins de recalculs pendant la saisie |
| **Virtualisation** | Pour 1000+ lignes visibles, envisager `@tanstack/vue-virtual` ou équivalent | Élevé | Réduction du DOM |
| **shallowRef pour rows** | `rows` peut être un `shallowRef` si on évite de muter en profondeur | Faible | Moins de réactivité inutile |
| **Watch paginationState.pageSize** | Persiste à chaque changement ; déjà debounced via `prefs` | OK | - |

### 7.3 Composables à extraire

| Composable | Contenu à extraire | Avantage |
|------------|-------------------|----------|
| `useTableSearch` | `searchText`, `searchDisplayValue`, `handleSearchInput`, `effectiveSearchDisplayValue`, `applySearchValue`, debounce | Réutilisable, testable |
| `useTableFilters` | `activeFilters`, `passesFilter`, `applyFilters`, `resetFilters`, `applyDefaultFilters` | Isolation logique filtres |
| `useTableServerParams` | Sync `serverParams` ↔ état local (page, sort, search, filters) | Unifie mode serveur |
| `useTableSelection` | `selectedIds`, `toggleRow`, `toggleAllOnPage`, `clearSelection`, `emitSelection` | Réutilisable (ex: bulk) |
| `useTableCellResolver` | `getCellFor`, `getFilterValueFor`, `getSearchValueFor`, `getSortValue` | Centralise la résolution des cellules |

### 7.4 Simplifications architecturales

| Piste | Description |
|-------|-------------|
| **Unification pagination UI** | `paginationCanPrev`/`paginationCanNext` calculés manuellement à cause du `pageCount` non réactif. Vérifier si une version plus récente de TanStack Table Vue corrige cela. |
| **Presets mode serveur** | Désactiver le panneau presets en `serverSide`, ou adapter pour n’émettre que des `serverParams` (sans écraser la recherche). |
| **EffectiveServerUrl** | Construction de l’URL dans EntityTanStackTable ; pourrait être une fonction pure testable. |
| **Colonnes TanStack** | `updateTanStackColumns` recrée tout le tableau de colonnes ; déjà en shallowRef. |

### 7.5 UX / Accessibilité

| Piste | Description |
|-------|-------------|
| **Aria-live pour pagination** | Annoncer « Page 2 sur 10 » à la navigation |
| **Focus trap** | En mode modal ou fullscreen, garder le focus dans le tableau |
| **Skeleton plus précis** | Adapter le nombre de lignes skeleton à la hauteur visible |
| **Loading state sur toolbar** | Désactiver recherche/filtres pendant un fetch serveur (optionnel) |

### 7.6 Priorisation suggérée

1. **Immédiat** : Suppression de `getCellObject` (remplacement par `getCellFor`).
2. **Court terme** : Extraction de `useTableSearch` si d’autres tableaux ont besoin de la même logique.
3. **Moyen terme** : Option A (montée de l’état serveur) + extraction de `useTableServerParams`.
4. **Long terme** : Virtualisation si des tableaux dépassent 500 lignes affichées en client.

---

## 8. Références

- [TANSTACK_TABLE.md](../../30-UI/TANSTACK_TABLE.md) — Spécification actuelle
- `TanStackTable.vue` — ~1940 lignes
- `EntityTanStackTable.vue` — ~340 lignes
- TanStack Table Vue : `pageCount` non réactif (limitation connue)
