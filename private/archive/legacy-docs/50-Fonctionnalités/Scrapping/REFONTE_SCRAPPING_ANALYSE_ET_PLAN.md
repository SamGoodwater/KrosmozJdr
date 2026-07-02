# Analyse et plan de refonte — Module Scrapping

**Contexte :** Le système de scrapping a évolué par ajouts successifs. Ce document fait le point sur la **qualité du code**, la **logique** et l’**optimisation**, puis propose une **refonte progressive** pour simplifier et rendre le tout plus robuste.

**Références :** [DIVISION_TACHES_SCRAPPING.md](./DIVISION_TACHES_SCRAPPING.md), [OPTIMISATION_ARCHITECTURE.md](./OPTIMISATION_ARCHITECTURE.md), [PLAN_OPTIMISATION_SCRAPPING_UI.md](./PLAN_OPTIMISATION_SCRAPPING_UI.md).

---

## 1. État actuel

### 1.1 Frontend — ScrappingDashboard.vue

| Critère | Constat |
|--------|--------|
| **Taille** | ~2 700 lignes, ~170 déclarations (ref, computed, function). Fichier monolithique. |
| **Brouillon** | Oui. Mélange de : état UI, appels API, configs en dur (STATUS_LABELS, RELATION_EXTRACT_CONFIG, DEFAULT_CHARACTERISTIC_LABELS), helpers d’affichage (cellTriple, tripleName, flattenForCompareShallow), logique métier légère (parseIdsFilter, buildSearchQuery). Difficile à parcourir et à tester. |
| **Logique** | Globalement correcte. Le flux (recherche → preview batch → tableau → simuler / importer) est cohérent. Quelques redondances (typeManagerConfig en if/else, loadKnownTypes / loadKnownRaces similaires). |
| **Optimisation** | Correcte pour l’usage actuel. Pas de sur-rendu évident. Les computed (visibleItems, visibleRowsWithRelations) sont raisonnables. La partie fragile est la **maintenabilité** plutôt que la perfs. |

**Points douloureux :**

- Une seule grosse page : tout changement touche un fichier énorme.
- Configs et constantes (statuts, relations, caractéristiques) noyées dans le script.
- Fonctions d’affichage (triple, flatten, comparisonRows) difficiles à réutiliser ou tester.
- Duplication de patterns (fetch + res.json() + showError) dans plusieurs fonctions.

### 1.2 Backend — Contrôleurs et services

| Critère | Constat |
|--------|--------|
| **Structure** | Bonne séparation : Contrôleurs (HTTP) → Services (Orchestrator, Collect, Integration, Relation). Aligné avec [DIVISION_TACHES_SCRAPPING.md](./DIVISION_TACHES_SCRAPPING.md). |
| **Brouillon** | Modéré. ScrappingController grossit (meta, preview, previewBatch, import*, resolveEntityForImport, entityTypeForComparison, getMaxIdForType…). Plusieurs contrôleurs (ScrappingController, ScrappingV2Controller, ScrappingImportController, ScrappingSearchController) : à clarifier qui sert quoi. |
| **Logique** | Bonne. Pipeline Collect → Conversion → Validation → Intégration bien défini. Relations et config centralisées. |
| **Optimisation** | Suffisante. Preview batch en séquentiel par ID ; une évolution possible serait du parallélisme limité (par lots) si le volume augmente. |

**Points d’attention :**

- Unifier ou documenter clairement les rôles des contrôleurs scrapping (API unique vs v2 vs import).
- Réduire la duplication de résolution d’entité / comparaison entre endpoints.

---

## 2. Faut-il faire une refonte ?

**Oui, une refonte progressive est recommandée**, pour :

1. **Simplifier** : extraire composables, configs et petits composants pour que le dashboard ne fasse qu’orchestrer.
2. **Rendre robuste** : une seule source de vérité pour statuts, relations, libellés ; moins de code dupliqué ; tests unitaires possibles sur la logique extraite.
3. **Stabiliser** : maintenant que le périmètre fonctionnel est clair (recherche, preview, simuler, importer, relations, comparaison), figer les contrats (API, props) et refactorer sans ajouter de features.

Une **refonte big-bang** (tout réécrire d’un coup) est à éviter : risque de régressions et de délai. Mieux vaut **découper en étapes** et garder le comportement métier inchangé.

---

## 3. Plan de refonte proposé

### 3.1 Priorité 1 — Frontend : extraire la logique et les configs

**Objectif :** Réduire ScrappingDashboard à une page de composition (layout + filtres + tableau + modales) en déplaçant la logique dans des composables et des fichiers de config.

| Étape | Action | Fichiers concernés | Bénéfice |
|-------|--------|--------------------|----------|
| 1.1 | **Composable `useScrappingSearch`** : buildSearchQuery, parseIdsFilter, runSearch, rawItems, lastMeta, pagination, loading. | Nouveau `composables/scrapping/useScrappingSearch.js`, Dashboard l’utilise | Recherche testable, réutilisable. |
| 1.2 | **Composable `useScrappingPreview`** : fetchConvertedBatch, convertedByItemId, loadingConverted, extraction des relations (extractRelationsFromRaw + RELATION_EXTRACT_CONFIG). | Nouveau `composables/scrapping/useScrappingPreview.js`, déplacer config relations dans `composables/scrapping/scrappingRelationConfig.js` ou équivalent | Preview et relations centralisés, moins de risque d’erreur dans le dashboard. |
| 1.3 | **Composable `useScrappingBatch`** : runBatch (simuler / importer), lastBatchResults, lastBatchRelationsByKey, setStatusFromBatchResults, options d’import (optIncludeRelations, etc.). | Nouveau `composables/scrapping/useScrappingBatch.js` | Logique batch et statuts au même endroit. |
| 1.4 | **Composable `useScrappingItemStatus`** : itemStatusByKey, STATUS_LABELS, STATUS_COLORS, TERMINAL_STATUSES, setStatusForEntities, getItemStatusLabel, getItemStatusColor, statusKey. | Nouveau `composables/scrapping/useScrappingItemStatus.js` | Statuts cohérents et réutilisables (ex. autre vue liste). |
| 1.5 | **Config / constantes** : Déplacer DEFAULT_CHARACTERISTIC_LABELS (ou les charger via API uniquement), RELATION_TYPE_LABELS, et les libellés d’entités (labelOverrides) dans un module dédié ou les dériver de la config API. | Ex. `composables/scrapping/scrappingConstants.js` ou garder côté config backend | Moins de “magic strings” dans le dashboard. |
| 1.6 | **Helpers d’affichage (comparaison)** : cellTriple, tripleName, tripleLevel, tripleType, comparisonRows, flattenForCompareShallow, flattenRawForCompare, findInFlat, convertedName, convertedLevel, existingRecord. | Nouveau `composables/scrapping/useScrappingCompare.js` ou `utils/scrappingCompareUtils.js` | Tableau et CompareModal peuvent partager la même logique ; tests unitaires possibles. |

Après 1.1–1.6, le dashboard devrait surtout : utiliser ces composables, afficher les champs (filtres, tableau, boutons), et ouvrir les modales. L’objectif est de passer sous la barre des ~1 000 lignes pour le fichier Vue.

### 3.2 Priorité 2 — Frontend : découper le template

**Objectif :** Remplacer de gros blocs du template par des composants ciblés (sans tout casser d’un coup).

| Étape | Action | Bénéfice |
|-------|--------|----------|
| 2.1 | **Bloc filtres** : Extraire les champs (IDs, nom, types, races, niveau, etc.) dans un composant `ScrappingFilters.vue` (props: modelValue / filtres, entityType, config; emit: update). | Dashboard plus lisible ; filtres réutilisables ou testables. |
| 2.2 | **Ligne de tableau (avec détail)** : Une ligne = item + ligne dépliée (comparaison + effets). Déjà partiellement fait avec visibleRowsWithRelations. Possibilité d’extraire une `ScrappingTableRow.vue` qui reçoit row, columnsConfig, expandedId, etc. | Template du dashboard allégé. |
| 2.3 | **Bloc options d’import + historique** : Déjà dans un panneau repliable ; l’extraire en `ScrappingImportOptions.vue` + `ScrappingHistory.vue` si besoin. | Clarté et réutilisation. |

On peut faire 2.1 puis 2.2/2.3 par itération.

### 3.3 Priorité 3 — Backend : clarifier et alléger les contrôleurs

| Étape | Action | Bénéfice |
|-------|--------|----------|
| 3.1 | **Documenter (ou unifier) les contrôleurs** : Qui utilise ScrappingController vs ScrappingV2Controller vs ScrappingImportController ? Si possible, une seule façade API “scrapping” (routes + un contrôleur principal qui délègue à des actions ou sous-services). | Moins de confusion, maintenance plus simple. |
| 3.2 | **Extraire la logique “réponse preview”** : Factoriser la construction de la réponse (raw, converted, existing, error) dans une méthode ou un DTO (ex. `PreviewItemResult`) utilisée par preview() et previewBatch(). | Moins de duplication, réponses homogènes. |
| 3.3 | **Optionnel — Paralléliser le preview batch** : Traiter les IDs par lots (ex. 10) en parallèle au lieu d’une boucle séquentielle, avec une limite de concurrence. | Meilleur temps de réponse sur grosses plages. |

### 3.4 Ce qu’on ne change pas (ou peu)

- **Pipeline métier** (Collect → Conversion → Validation → Intégration) : déjà sain.
- **Config JSON** (sources, entités, mapping) : déjà source de vérité.
- **CompareModal, EntityDiffTable** : les adapter progressivement pour consommer les nouveaux composables (ex. useScrappingCompare) plutôt que de tout réécrire.

---

## 4. Ordre d’exécution recommandé

1. **Phase 1 (composables + config)** : 1.1 → 1.2 → 1.4 → 1.5 → 1.3 → 1.6. Introduire un composable à la fois, en gardant le comportement identique (tests manuels ou existants).
2. **Phase 2 (template)** : 2.1 (filtres) puis 2.2 (ligne de tableau) puis 2.3 si besoin.
3. **Phase 3 (backend)** : 3.1 (doc / unification) puis 3.2 (factorisation réponse preview). 3.3 à traiter seulement si les perfs du batch deviennent un problème.

---

## 5. Résumé

| Question | Réponse |
|----------|---------|
| Le code est-il trop brouillon ? | **Oui** côté frontend (dashboard monolithique, configs et helpers mélangés). Backend acceptable avec quelques duplications. |
| La logique est-elle bonne ? | **Oui** : flux clair, séparation Contrôleurs / Services respectée, config-driven. |
| Est-ce optimisé ? | **Assez** pour l’usage actuel ; le gain principal sera en **maintenabilité** et **testabilité**, pas seulement en perfs. |
| Faut-il refondre ? | **Oui**, de façon **progressive** : composables + découpage du dashboard d’abord, puis clarification backend, sans refonte big-bang. |

En appliquant ce plan par petits pas, le module scrapping devient plus simple à faire évoluer et plus robuste, tout en conservant le comportement actuel.
