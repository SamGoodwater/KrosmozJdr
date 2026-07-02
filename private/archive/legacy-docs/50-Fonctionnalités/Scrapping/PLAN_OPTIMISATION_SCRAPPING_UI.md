# Plan d’optimisation — UI Scrapping (existant)

**Contexte :** Idées issues des échanges sur l’affichage scrapping et l’enregistrement. Ce plan vise à **optimiser l’existant** sans refonte complète.

**Références :** [ETAT_LIEUX_AFFICHAGE_SCRAPPING.md](./ETAT_LIEUX_AFFICHAGE_SCRAPPING.md), [Architecture README](./Architecture/README.md).

---

## 1. Vue d’ensemble

| Phase | Objectif | Effort estimé |
|-------|----------|----------------|
| **A** | Persistance des préférences + export des erreurs | Faible |
| **B** | Vue structurée (remplacer le double `<pre>`) | Moyen |
| **C** | Libellés et regroupement (diff / comparaison) | Moyen |
| **D** | Optionnel : indicateurs de conversion, raw API | Faible à moyen |

---

## 2. Phase A — Préférences et export (priorité haute) *(réalisé)*

### A1. Persistance des options / préférences (localStorage) *(fait)*

**Objectif :** Ne pas perdre les réglages à chaque rechargement (type d’entité, options d’import, dernier filtre).

**Périmètre :**
- **Fichiers :** `ScrappingDashboard.vue`, éventuellement `ScrappingSearchTableSection.vue` si utilisé seul.
- **Données à persister (exemples) :**
  - `selectedEntityType`
  - Options d’import : `optSkipCache`, `optForceUpdate`, `optManualChoice`, `optIncludeRelations`, `optWithImages`
  - Dernier filtre de recherche (query params ou champs du formulaire) — optionnel
- **Clé localStorage :** ex. `krosmoz_scrapping_prefs` (objet JSON).

**Critères d’acceptation :**
- Au chargement de la page scrapping, lecture de `localStorage` et application des valeurs si présentes.
- À chaque changement pertinent, sauvegarde dans `localStorage` (debounce optionnel).
- Pas de persistance de données sensibles ni de résultats d’import.

**Notes :** Composable `useScrappingPreferences` dans `Composables/store/useScrappingPreferences.js` ; intégration dans `ScrappingDashboard` (hydrate au mount, persist au changement options + après recherche).

---

### A2. Export des erreurs d’import batch (CSV)

**Objectif :** Permettre d’archiver ou de traiter les erreurs en dehors de l’UI (Excel, scripts).

**Périmètre :**
- **Fichiers :** `ScrappingDashboard.vue`, `ScrappingSearchTableSection.vue`.
- **Comportement :** Quand le bloc « Erreurs import batch » est affiché (`lastBatchErrorResults.length > 0`), afficher un bouton **« Exporter les erreurs (CSV) »**.
- **Contenu CSV :** Colonnes au minimum : `type`, `id`, `statut`, `message` ; si `validation_errors` présent, une colonne supplémentaire (ex. `détails` avec path + message concaténés ou une ligne par erreur de validation selon le choix UX).

**Critères d’acceptation :**
- Clic sur le bouton → téléchargement d’un fichier `.csv` (nom du fichier avec date/heure ou timestamp).
- Encodage UTF-8 avec BOM pour Excel si besoin.
- Pas d’appel API : uniquement à partir des données déjà en mémoire (`lastBatchErrorResults`).

**Notes :** `Composables/utils/useCsvDownload.js` : `buildCsvFromErrorResults`, `downloadCsvFromRows`, `filenameForBatchErrors`. Bouton « Exporter (CSV) » dans Dashboard et ScrappingSearchTableSection.

---

### A3. Export de la prévisualisation batch (CSV) *(fait)*

**Objectif :** Exporter le tableau « Résultat prévisualisation » (ID, Nom, Statut, Message) pour partage ou analyse.

**Périmètre :** Même principe que A2 : bouton « Exporter (CSV) » à côté de « Fermer » sur la carte de résultat de la prévisualisation batch, dans `ScrappingDashboard.vue` et `ScrappingSearchTableSection.vue`. Contenu : `id`, `nom`, `statut`, `message`.

**Priorité :** Plus basse que A2 ; à traiter si le besoin est exprimé.

---

## 3. Phase B — Vue structurée (remplacer le double `<pre>`) *(réalisé)*

**Objectif :** Remplacer l’affichage `JSON.stringify(previewData.converted)` et `JSON.stringify(previewData.existing)` par une vue lisible (libellés, regroupement).

**Référence :** Piste 4 de [ETAT_LIEUX_AFFICHAGE_SCRAPPING.md](./ETAT_LIEUX_AFFICHAGE_SCRAPPING.md#31-court-terme-affichage-prévisualisation).

**Périmètre :**
- **Fichiers :** `SearchPreviewSection.vue`, éventuellement un nouveau composant (ex. `PreviewConvertedSummary.vue`) ou sous-composants par type d’entité.

**Options (à trancher) :**
1. **Résumé par type d’entité :** Blocs dédiés (ex. Monster : nom, niveau, PV, rareté ; Item : nom, type, niveau, rareté) en s’appuyant sur la structure `converted` (par modèle : `creatures`, `monsters`, `items`, etc.).
2. **Liste générique (libellé, valeur) :** Flatten de `converted` + mapping `clé technique → libellé` (fichier de mapping ou réutilisation des descriptors existants).

**Critères d’acceptation :**
- Les champs principaux (nom, niveau, etc.) sont affichés avec des libellés lisibles, pas uniquement du JSON.
- L’affichage « Version actuelle (base Krosmoz) » peut rester en JSON dans un premier temps ou suivre le même schéma.
- Pas de régression sur l’affichage des erreurs de validation ni sur le bloc « Relations détectées ».

**Notes :** Réutiliser les configs / descriptors du système d’entités si possible (voir piste 7 de l’état des lieux).

---

## 4. Phase C — Libellés et regroupement (EntityDiffTable / CompareModal)

**Objectif :** Rendre la comparaison Krosmoz vs DofusDB lisible : clés techniques → libellés, regroupement par section.

**Référence :** Piste 5 et 6 de [ETAT_LIEUX_AFFICHAGE_SCRAPPING.md](./ETAT_LIEUX_AFFICHAGE_SCRAPPING.md#32-moyen-terme-comparaison-et-lisibilité).

**Périmètre :**
- **Fichiers :** `EntityDiffTable.vue`, `CompareModal.vue` (ou composants qu’ils utilisent).
- **Mapping :** Fichier ou constante `clé technique → libellé` (ex. `monsters.level` → « Niveau », `creatures.name` → « Nom »). À partager avec la Phase B si liste générique.
- **Regroupement :** Grouper les lignes par section (ex. Identité, Caractéristiques, Effets, Relations) selon un préfixe de clé ou une liste de clés par section.

**Critères d’acceptation :**
- Les colonnes/lignes de la table de diff affichent un libellé lisible pour chaque clé.
- Les lignes sont regroupées sous des titres de section (collapsible optionnel).
- Pour les champs de type tableau (effets, bonus), éviter un seul flatten `effects[0].…` ; prévoir une sous-section ou un tableau par élément (piste 6).

**Priorité :** Après la Phase B pour réutiliser le même mapping de libellés.

**Réalisé :** Fichier partagé `Pages/scrapping/components/previewDiffLabels.js` (SECTION_LABELS, FIELD_LABELS, getSectionLabel, getFieldLabel, getSectionFromFlatKey). EntityDiffTable et CompareModal : colonne Propriété/Champ affiche le libellé ; lignes regroupées par section (Identité, Monstre, Objet, etc.) avec titre de section.

---

## 5. Phase D — Optionnel *(réalisé)*

### D1. Indicateurs de conversion *(fait)*

**Objectif :** Afficher clairement quelles données viennent de la conversion (niveau, rareté, PV, etc.) et la source (DofusDB → formules / limites BDD).

**Réalisé :** Ligne sous « Version DofusDB convertie » et sous « Propriétés : Brut / Converti / Krosmoz » indiquant la source (DofusDB → formules et limites BDD).

### D2. API preview et `raw` *(vérifié)*

**Objectif :** S’assurer que l’API preview renvoie bien `raw` (spells, drops, recipe, summon) pour que le bloc « Relations détectées » soit toujours alimenté lorsque les données existent.

**Réalisé :** L’API retourne déjà `raw` via `$result->getRaw()`. PHPDoc du contrôleur complété.

---

## 6. Ordre de réalisation recommandé

1. **A1** — Persistance des préférences (gain immédiat pour l’utilisateur).
2. **A2** — Export CSV des erreurs d’import batch (utile pour le suivi des échecs).
3. **B** — Vue structurée (impact fort sur la lisibilité de la prévisualisation).
4. **C** — Libellés et regroupement (diff / CompareModal).
5. **A3** — Export CSV prévisualisation batch (si besoin).
6. **D1 / D2** — Selon priorité métier.

---

## 7. Récapitulatif des fichiers impactés

| Fichier | A1 | A2 | A3 | B | C | D |
|---------|----|----|----|----|----|---|
| `ScrappingDashboard.vue` | ✓ | ✓ | ✓ | — | — | — |
| `ScrappingSearchTableSection.vue` | (✓) | ✓ | ✓ | — | — | — |
| `SearchPreviewSection.vue` | — | — | — | ✓ | — | ✓ (D1) |
| `EntityDiffTable.vue` | — | — | — | — | ✓ | — |
| `CompareModal.vue` | — | — | — | — | ✓ | — |
| `previewDiffLabels.js` | — | — | — | ✓ | ✓ | — |
| Composable `useScrappingPreferences` | ✓ | — | — | — | — | — |
| Composable `useCsvDownload` | — | ✓ | ✓ | — | — | — |
| Backend `ScrappingController` (preview) | — | — | — | — | — | ✓ (D2) |

---

*Document créé à partir des idées de la conversation sur l’optimisation de l’existant (préférences, export, vue structurée, libellés).*

---

## 8. Révision finale (vérification des phases)

| Phase | Élément | Statut | Fichiers / preuve |
|-------|---------|--------|-------------------|
| **A1** | Persistance préférences | OK | `useScrappingPreferences.js` (clé `krosmoz_scrapping_prefs`) ; Dashboard : `hydratePrefs()` au mount, `persistPrefs()` sur changement type/options et après recherche ; validation type dans `metaEntityTypes`. |
| **A2** | Export CSV erreurs batch | OK | `useCsvDownload.js` (BOM, `buildCsvFromErrorResults`, `filenameForBatchErrors`) ; bouton « Exporter (CSV) » sur carte Erreurs dans Dashboard + ScrappingSearchTableSection. |
| **A3** | Export CSV prévisualisation batch | OK | Même utilitaire ; bouton « Exporter (CSV) » sur carte Prévisualisation sélection dans les deux composants. |
| **B** | Vue structurée + libellés | OK | `SearchPreviewSection.vue` : `buildStructuredSummary` avec `getSectionLabel` / `getFieldLabel` ; affichage `block.sectionLabel` et `row.label`. Pas de `<pre>` JSON. |
| **C** | Libellés + regroupement diff | OK | `previewDiffLabels.js` partagé ; `EntityDiffTable.vue` et `CompareModal.vue` : `rowsBySection`, colonne Propriété/Champ = libellé, titres de section (Identité, Monstre, Objet, etc.). |
| **D1** | Indicateurs de conversion | OK | Lignes sous « Version DofusDB convertie » et « Propriétés : Brut / Converti / Krosmoz » (source DofusDB → formules et limites BDD). |
| **D2** | API preview `raw` | OK | `ScrappingController::preview()` retourne `raw` ; PHPDoc complété. Orchestrator transmet les données brutes au résultat. |

Lint : aucun diagnostic sur les fichiers concernés. Build Vite (HMR) : pas d’erreur constatée dans les terminaux.
