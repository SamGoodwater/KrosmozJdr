# Vérification des fonctionnalités — UI Scrapping

**Référence :** [SPEC_UI_SCRAPPING.md](./SPEC_UI_SCRAPPING.md) (liste exhaustive F1–F28).  
**Contexte :** Après Phase 2 de la refonte (composants ScrappingFilters, ScrappingOptionsPanel, ScrappingResultsTable intégrés).

---

## Synthèse

| Statut | Nombre |
|--------|--------|
| ✅ Présente | 28 |
| ⚠️ Partielle / à compléter | 0 |

---

## Détail par fonctionnalité

| # | Fonctionnalité | Statut | Implémentation |
|---|----------------|--------|----------------|
| F1 | Choix du type d'entité | ✅ | `ScrappingFilters` : Select Entité (`entityOptions`), persisté via `useScrappingPreferences` (selectedEntityType, filterIds, filterName, perPage, options d'import). Types : class, monster, spell, equipment, consumable, resource, panoply. |
| F2 | Filtre IDs | ✅ | `ScrappingFilters` (filterIds) → `useScrappingSearch` avec `parseIdsFilter` (id / ids / idMin–idMax). |
| F3 | Filtre nom | ✅ | `ScrappingFilters` (filterName) → query `name`. |
| F4 | Filtre par types (items) | ✅ | `ScrappingFilters` : typeMode (all / allowed / selected), listes inclure/exclure (filterTypeIds, filterTypeIdsNot), `loadKnownTypes` (à la fermeture de la modale Gérer les types), bouton « Gérer les types ». |
| F5 | Filtre par races (monstres) | ✅ | `ScrappingFilters` : raceMode, filterRaceIds, « Gérer les races », liste « Races (inclure) ». `loadKnownRaces` (API `/api/types/monster-races?state=playable`) appelée au passage en type monster (watch selectedEntityTypeStr) et à la fermeture de la modale « Gérer les races ». |
| F6 | Filtres niveau / breedId | ✅ | `ScrappingFilters` : levelMin, levelMax, breedId selon `supports()`. |
| F7 | Pagination serveur | ✅ | `ScrappingFilters` : `TanStackTablePagination` (pageIndex, pageCount, perPage, totalRows, prev/next/first/last/go, set-page-size) → `useScrappingSearch` (page, per_page). |
| F8 | Bouton Rechercher | ✅ | `ScrappingFilters` @search → Dashboard `runSearchAndPreview()` (runSearch + applyStatusAndPreview → fetchConvertedBatch). |
| F9 | Tableau des résultats | ✅ | `ScrappingResultsTable` : lignes entité + sous-lignes relation (sorts, drops, etc.), colonnes checkbox, ID, déplier, État, Nom, Existe, Type, Race, Niveau selon supports. |
| F10 | Recherche dans le tableau | ✅ | Dashboard : `tableSearch`, filtre client dans `visibleRowsWithRelations` (ID + nom). |
| F11 | Données converties par ligne | ✅ | `useScrappingPreview` : fetchConvertedBatch, convertedByItemId ; affichage dans tableau et ligne dépliée. |
| F12 | Relations (sorts, drops) | ✅ | `lastBatchRelationsByKey` + `visibleRowsWithRelations` ; lignes relation dans `ScrappingResultsTable`. |
| F13 | Système d'état par ligne | ✅ | `useScrappingItemStatus` (statusConfig) : getStatusEntry, getStatusLabel, getStatusColor ; setStatusForEntities / setStatusFromBatchResults ; badges dans tableau. |
| F14 | Sélection multiple | ✅ | `ScrappingResultsTable` : checkbox par ligne, update:selectedIds (toggle-one / toggle-all) ; Dashboard « Tout cocher / Tout décocher ». |
| F15 | Périmètre Simuler / Importer | ✅ | Dashboard : batchScope (selection / all / pages), champ Pages si « Par pages », `runBatchOrByPages`. |
| F16 | Simuler | ✅ | Bouton Simuler → `batch.runBatchOrByPages('simulate')`. |
| F17 | Importer | ✅ | Bouton Importer → `batch.runBatchOrByPages('import')`. |
| F18 | Options d'import | ✅ | `ScrappingOptionsPanel` : includeRelations, whitelist/blacklist, replaceMode (never / draft_raw_only / always), persistées. |
| F19 | Ligne dépliable | ✅ | `ScrappingResultsTable` : expandedRowId, toggle-expand ; détail Brut/Converti/Krosmoz (comparisonRows) + bloc Effets (itemEffectsForRow, hasItemEffects). |
| F20 | Double-clic → Comparer | ✅ | `ScrappingResultsTable` @open-compare → Dashboard `openCompareModalForRow`. |
| F21 | Modale Comparaison | ✅ | `CompareModal` (entityType, dofusdbId, @imported → setStatusForEntities(…, 'importé')). |
| F22 | Lien « Existe » | ✅ | `ScrappingResultsTable` : existsLabel, existsTooltip, existsEntityHref ; @open-entity → Dashboard `openEntityModal` ; `EntityModal`. |
| F23 | Gestion des types / races | ✅ | `ScrappingFilters` @open-type-manager → typeManagerOpen = true ; Dashboard : `TypeManagerTable` (typeManagerConfig : listUrl, bulkUrl, mode decision/state). |
| F24 | Historique | ✅ | `ScrappingOptionsPanel` : historyLines, bouton Vider, @clear-history. |
| F25 | Erreurs du dernier batch | ✅ | `ScrappingOptionsPanel` : batchErrorResults, tableau + Export CSV (@export-errors-csv) et Fermer (@clear-errors). |
| F26 | Réinitialiser | ✅ | Dashboard : bouton Réinitialiser → `resetTable` (vide tableau, sélection, convertis, relations, statuts pour le type courant). |
| F27 | Analyse des effets (non mappés) | ✅ | Dashboard : bouton « Analyser effets (non mappés) », `analyzeEffects`, carte « Analyse des effets non mappés » (effets unmapped + Export optionnel). |
| F28 | Persistance des préférences | ✅ | `useScrappingPreferences` (loadScrappingPreferences, hydratePrefs, prefsRefs : selectedEntityType, filtres, perPage, options d'import). |

---

## Correctif appliqué pour F5 (filtre par races)

- `loadKnownRaces()` a été réintégrée dans le Dashboard.
- Elle est appelée : (1) au passage au type d’entité `monster` (watch sur `selectedEntityTypeStr` avec `immediate: true`) ; (2) à la fermeture de la modale Type Manager lorsque le type courant est `monster`.
- Ainsi, la liste « Races (inclure) » est remplie dès que l’utilisateur choisit Monstres ou après avoir fermé « Gérer les races ».
