# Spécification fonctionnelle — UI Scrapping

**Objectif :** Définir de façon exhaustive ce que doit faire l’interface de scrapping (fonctionnalités, composants, flux). Cette spec sert de référence pour la refonte du code frontend : le comportement décrit ici doit être conservé ; seuls l’organisation du code et les composants internes changent.

**Références :** [REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md](./REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md), [DIVISION_TACHES_SCRAPPING.md](./DIVISION_TACHES_SCRAPPING.md).

---

## 1. Vue d’ensemble

L’UI scrapping permet de :

1. **Choisir un type d’entité** (Monstres, Sorts, Classes, Équipements, Consommables, Ressources, Panoplies).
2. **Rechercher** des entités côté DofusDB via des filtres (IDs, nom, types, races, niveau, pagination).
3. **Afficher les résultats** dans un tableau avec : état par ligne, nom (converti), existence en base, type/race/niveau, et détail dépliable (brut / converti / Krosmoz).
4. **Charger les données converties** (preview batch) après chaque recherche pour afficher converti/existant par ligne.
5. **Simuler** ou **importer** en lot, avec choix du périmètre (sélection, tous les résultats visibles, ou par plage de pages), et options d’import (relations, remplacement, whitelist/blacklist).
6. **Comparer** une entité (double-clic) dans une modale Brut / Converti / Krosmoz et importer avec choix propriété par propriété.
7. **Gérer les types ou races** (selon l’entité) via une modale dédiée (décision allowed/blocked ou état playable/archived).
8. **Consulter l’historique** des actions et les erreurs du dernier import batch (avec export CSV).

Tout le flux repose sur les API existantes (search, preview, preview/batch, import/batch, import-with-merge, config, meta, registres de types/races). Aucune nouvelle API n’est requise pour respecter cette spec.

---

## 2. Liste exhaustive des fonctionnalités

| # | Fonctionnalité | Description courte |
|---|----------------|--------------------|
| F1 | Choix du type d’entité | Select en header : class, monster, spell, equipment, consumable, resource, panoply. Persisté (localStorage). |
| F2 | Filtre IDs | Champ texte : un ID, liste (1,2,3) ou plage (1-50). Envoyé en query (id, ids, idMin/idMax). |
| F3 | Filtre nom | Champ texte, optionnel. Envoyé en `name`. |
| F4 | Filtre par types (items) | Mode : tout / validés uniquement / sélection UI. Si sélection : listes « inclure » / « exclure » de typeIds. Visible pour resource, consumable, equipment. |
| F5 | Filtre par races (monstres) | Mode : toutes / validées uniquement / sélection UI. Si sélection : liste de raceIds. Visible pour monster. |
| F6 | Filtres niveau / breedId | levelMin, levelMax (optionnels). breedId pour les classes. Visibilité selon config (supports). |
| F7 | Pagination serveur | Page courante, taille de page (50/100/200), total. Requête search avec page + per_page. |
| F8 | Bouton Rechercher | Lance la recherche avec les filtres courants ; remplit le tableau ; déclenche le chargement des convertis (preview batch). |
| F9 | Tableau des résultats | Lignes = entités + sous-lignes relations (sorts, drops, etc.). Colonnes : checkbox, ID, déplier, État, Nom, Existe, Type (si support), Race (si monster), Niveau (si support). |
| F10 | Recherche dans le tableau | Champ « Recherche dans le tableau » : filtre côté client sur ID et nom (sans rappel API). |
| F11 | Données converties par ligne | Après recherche, appel preview/batch pour les IDs de la page ; affichage converti / existant dans les colonnes et dans la ligne dépliée. |
| F12 | Relations (sorts, drops, etc.) | Affichées sous l’entité dans le tableau (lignes « Sort (id) », « Drop (id) »). Remplies dès la recherche (depuis raw du preview) ou après import batch. |
| F13 | Système d’état par ligne | Badge par ligne : Recherché, Conversion…, Converti, Simulé, Simulation…, Simulation erreur, Import en cours, Importé, Erreur. Couleurs et libellés définis. Statuts terminaux non écrasés par une nouvelle recherche. |
| F14 | Sélection multiple | Checkbox par ligne + « Tout cocher / Tout décocher ». Sélection utilisée pour le périmètre « Sélection » (simuler / importer). |
| F15 | Périmètre Simuler / Importer | 3 choix : **Sélection** (entités cochées), **Tous** (toutes les lignes actuellement chargées pour la page), **Par pages** (plage de pages : ex. 1-6 ou 4,5 ; pour chaque page : search puis batch). |
| F16 | Simuler | Bouton « Simuler » : appelle import/batch avec dry_run, met à jour les états (simulé / simulation erreur), affiche les erreurs éventuelles. |
| F17 | Importer | Bouton « Importer » : appelle import/batch sans dry_run, met à jour les états (importé / erreur), remplit les relations si retournées. |
| F18 | Options d’import | Inclure les relations ; whitelist / blacklist de propriétés ; mode de remplacement (jamais / draft_raw_only / toujours). Persistées. |
| F19 | Ligne dépliable | Clic sur une ligne (ou bouton déplier) : affiche tableau Brut / Converti / Krosmoz (comparisonKeys) + bloc Effets (si équipement/consommable/ressource/sort). |
| F20 | Double-clic → Comparer | Ouvre la modale de comparaison (CompareModal) pour cette entité. |
| F21 | Modale Comparaison | Affiche Brut, Converti, Krosmoz par propriété ; choix par propriété (existant vs converti) ; bouton Importer avec merge (import-with-merge). |
| F22 | Lien « Existe » | Si l’entité existe en base : bouton ouvrant la fiche entité (EntityModal ou navigation). |
| F23 | Gestion des types / races | Bouton « Gérer les types » ou « Gérer les races » selon l’entité : ouvre TypeManagerTable (modale) avec listUrl/bulkUrl et mode (decision ou state). |
| F24 | Historique | Bloc « Options & historique » (masqué par défaut) : log des actions (recherche, simulation, import) avec horodatage ; bouton Vider. |
| F25 | Erreurs du dernier batch | Si des erreurs après import batch : carte avec tableau ID / Type / Statut / Message (et validation_errors) ; bouton Exporter (CSV) et Fermer. |
| F26 | Réinitialiser | Bouton « Réinitialiser » : vide le tableau, la sélection, les convertis, les relations, et les statuts pour le type d’entité courant ; garde les filtres. |
| F27 | Analyse des effets (non mappés) | Bouton « Analyser effets » (si 1 entité sélectionnée et type equipment/consumable/resource/spell) : prévisualisation des bonus/effets non mappés. |
| F28 | Persistance des préférences | Type d’entité, options d’import, filtres (IDs, nom), perPage, etc. dans localStorage (clé dédiée). |

---

## 3. Description détaillée des composants et de la logique

### 3.1 Dashboard (page Scrapping)

**Rôle :** Conteneur principal. Affiche le header (titre + choix d’entité), les filtres, la pagination, le bloc Options & historique (repliable), le tableau des résultats, les boutons d’action (Réinitialiser, Simuler, Importer, Analyser effets), et orchestre les modales.

**Contenu :**

- **Header** : Titre « Scrapping », sous-titre, Select « Entité » (options chargées depuis meta + config).
- **Carte Filtres** : Grille avec champs selon `supports(...)` (IDs, Nom, Types, Races, breedId, levelMin, levelMax), mode type/race, bouton « Gérer les types/races », pagination (TanStackTablePagination), bouton « Rechercher », indicateur de résultats (retournés / total).
- **Carte Options & historique** : Repliable (masqué par défaut). À l’intérieur : options d’import (toggles + radios + champs whitelist/blacklist), historique (lignes de log), et éventuellement la carte « Erreurs import batch » si `lastBatchErrorResults.length > 0`.
- **Carte Résultats** : Titre « Résultats » + badge nombre + recherche dans le tableau. Puis barre d’actions : Réinitialiser, Périmètre (Sélection / Tous / Par pages), champ Pages si « Par pages », Simuler, Importer, Analyser effets, Tout cocher/décocher. Puis tableau (voir 3.5).
- **Modales** : TypeManagerTable (types/races), CompareModal (comparaison), EntityModal (fiche entité existante), éventuellement modale Analyse effets.

**Responsabilités du Dashboard (logique à garder ou extraire en composables) :**

- Charger meta et config au mount ; hydrater les préférences ; appliquer la première entité autorisée si la courante n’est plus valide.
- Exposer `selectedEntityType` / `selectedEntityTypeStr` et les refs nécessaires aux enfants (filtres, tableau, batch).
- Déclencher la recherche (`runSearch`), le chargement des convertis (`fetchConvertedBatch`), la simulation/import (`runBatch` / `runImportByPages`), la réinitialisation (`resetTable`).
- Gérer l’ouverture des modales (compare, entity, type manager) et les callbacks (ex. `onCompareImported` → mise à jour statut « importé »).

Le Dashboard ne doit pas contenir la logique métier détaillée (parsing des filtres, construction du payload batch, extraction des relations, calcul des statuts) : celle-ci doit vivre dans des composables ou modules dédiés.

---

### 3.2 Filtres de recherche

**Rôle :** Saisie des critères envoyés à l’API search. Visibilité des champs selon le type d’entité (config `comparisonKeys` / `filters.supported`).

**Champs toujours présents (ou selon config) :**

- **IDs** : Texte. Interprétation : un nombre → `id` ; liste `a,b,c` → `ids` ; plage `a-b` → `idMin`, `idMax`. Persisté.
- **Nom** : Texte, optionnel. Envoyé en `name`. Persisté.

**Champs conditionnels :**

- **Types (resource, consumable, equipment)** :  
  - Mode : `all` | `allowed` | `selected`.  
  - Si `selected` : listes « Types à inclure » et « Types à exclure » (typeIds), chargées depuis les API de types (resource-types, consumable-types, item-types) avec `decision=allowed`.  
  - Query : `type_mode`, et si selected `typeIds` / `typeIdsNot`.  
  - Bouton « Gérer les types » → modale TypeManagerTable (listUrl/bulkUrl, mode decision).
- **Races (monster)** :  
  - Mode : `all` | `allowed` | `selected`.  
  - Si `selected` : liste de raceIds (inclure), chargée depuis `/api/types/monster-races?state=playable`.  
  - Query : `race_mode`, et si selected `raceIds`.  
  - Bouton « Gérer les races » → modale TypeManagerTable (monster-races, mode state).  
  - Optionnel : champ manuel raceId (debug).
- **breedId** : Si `supports('breedId')` (ex. class).
- **Niveau min / max** : Si `supports('levelMin')` / `supports('levelMax')`.

**Pagination :**

- Page courante (1-based), taille de page (50, 100, 200). Envoyée en `page` et `per_page`. Total et nombre de pages dérivés de la réponse search (`meta.total`, `meta.total_pages` ou calcul).

**Logique à isoler (composable « search ») :**

- `parseIdsFilter()` : texte → `{ id? } | { ids? } | { idMin?, idMax? }`.
- `buildSearchQuery()` : agrège tous les filtres + pagination en `URLSearchParams` ou objet query.
- Déclenchement : un seul point d’entrée `runSearch()` qui appelle `GET /api/scrapping/search/{entity}?{query}`, puis met à jour `rawItems`, `lastMeta`, les statuts « recherché », et appelle le chargement des convertis (preview batch).

---

### 3.3 Logique de recherche

**Flux :**

1. L’utilisateur modifie les filtres et/ou la page, puis clique « Rechercher » (ou la pagination déclenche la recherche).
2. Construction de la query (IDs, nom, type_mode, typeIds, race_mode, raceIds, levelMin, levelMax, breedId, page, per_page, skip_cache si option).
3. `GET /api/scrapping/search/{entityType}?{query}`.
4. Réponse : `data.items`, `data.meta` (total, total_pages, returned, etc.).
5. Mise à jour de `rawItems` et `lastMeta`. Pour chaque ID retourné, mise à jour du statut en « recherché » (sauf si statut terminal : simulé, simulation erreur, importé, erreur).
6. Appel immédiat à **preview batch** pour les IDs de la page courante (voir 3.4).

**Contraintes :**

- Une seule requête search à la fois (loading pendant l’appel).
- Après une recherche, le tableau affiche les lignes ; le chargement des convertis peut être asynchrone (indicateur « Valeurs converties… »).

---

### 3.4 Données converties et preview batch

**Rôle :** Obtenir pour chaque ligne du tableau les données **brut**, **converti** et **existant** (Krosmoz) pour affichage et comparaison.

**Flux :**

1. Après chaque recherche réussie, avec la liste d’IDs de la page courante (`rawItems`), appel `POST /api/scrapping/preview/batch` avec `{ type: entityType, ids: [...] }`.
2. Réponse : `data.items` ou `data.data.items`, chaque item : `id`, `raw`, `converted`, `existing` (avec `record`), `error`.
3. Stockage par ID : `convertedByItemId[id] = { raw, converted, existing, error }`.
4. Mise à jour des statuts en « converti » pour les IDs traités (sauf statuts terminaux).
5. Extraction des **relations** (sorts, drops, summon, recipe, etc.) depuis `raw` selon le type d’entité (config côté frontend), stockage dans `lastBatchRelationsByKey` pour affichage sous chaque entité dans le tableau.

**Affichage :**

- Colonnes Nom, Type, Niveau : affichage « converti » (et optionnellement existant/brut selon le design actuel).
- Ligne dépliée : tableau Propriété / Brut / Converti / Krosmoz (comparisonKeys).
- Les relations apparaissent comme sous-lignes (Sort (id), Drop (id), Invoqué (id), etc.).

**Logique à isoler (composable « preview ») :**

- `fetchConvertedBatch()` : appel API, parsing sécurisé, mise à jour de `convertedByItemId`, des statuts et de `lastBatchRelationsByKey`.
- Config d’extraction des relations par type d’entité (RELATION_EXTRACT_CONFIG) et fonction `extractRelationsFromRaw(entityType, raw)`.

---

### 3.5 Tableau des résultats

**Structure des lignes :**

- Chaque entité = une ligne principale (checkbox, ID, déplier, État, Nom, Existe, Type, Race, Niveau selon supports).
- Sous l’entité, éventuellement des **lignes de relation** (une par relation : type + id), affichées en style secondaire (ex. italique, indentation).

**Colonnes :**

- Checkbox de sélection.
- ID (DofusDB).
- Bouton ou cellule « déplier » pour afficher le détail.
- **État** : badge (recherché, converti, simulé, importé, erreur, etc.) — voir 3.6.
- **Nom** : nom converti (ou nom brut si pas de converti).
- **Existe** : indicateur « Existe » / « N’existe pas » + lien vers fiche entité si existant.
- **Type** (si entité avec types) : existant / converti / brut (DofusDB).
- **Race** (si monster) : nom de race + id.
- **Niveau** (si support) : converti (brut).

**Ligne dépliée (détail) :**

- Tableau : Propriété | Brut (DofusDB) | Converti | Krosmoz (existant). Clés = comparisonKeys (backend). Surlignage si converti ≠ existant.
- Pour equipment/consumable/resource/spell : bloc Effets (brut) et Bonus convertis.

**Interactions :**

- Clic checkbox : toggle sélection.
- Clic déplier : affiche/masque la ligne de détail.
- Double-clic ligne : ouvre CompareModal pour cet ID.

**Données :**

- Lignes = `visibleRowsWithRelations` : à partir de `visibleItems` (filtrage client par recherche dans le tableau) + expansion avec `lastBatchRelationsByKey` pour les sous-lignes relations.
- Pour chaque cellule « triple » (nom, type, niveau), lecture dans `convertedByItemId[id]` (converted, existing) et dans l’item brut.

---

### 3.6 Système d’état par ligne

**Rôle :** Afficher où en est chaque entité (recherche, conversion, simulation, import, erreur) sans tout recharger.

**Statuts possibles :**

- `recherché` : après une recherche (l’entité est dans les résultats).
- `conversion en cours` : (optionnel, si on affiche un loading dédié).
- `converti` : preview batch a renvoyé des données converties pour cet ID.
- `simulation en cours` : batch simulate en cours pour cet ID.
- `simulé` : simulation terminée avec succès.
- `simulation erreur` : simulation terminée en erreur.
- `importation en cours` : batch import en cours.
- `importé` : import terminé avec succès.
- `erreur` : import terminé en erreur.

**Règles :**

- Clé d’état : `{entityType}-{id}` (ex. `monster-123`).
- Lors d’une **nouvelle recherche** : on met « recherché » uniquement pour les IDs qui n’ont pas déjà un **statut terminal** (simulé, simulation erreur, importé, erreur). Les terminaux restent affichés.
- Mise à jour : après preview batch → « converti » ; après batch simulate → « simulé » ou « simulation erreur » ; après batch import → « importé » ou « erreur ». En cas d’import depuis CompareModal → « importé » pour l’ID concerné.

**Libellés et couleurs :** Définis dans une config (STATUS_LABELS, STATUS_COLORS) utilisée par le composant Badge. À sortir dans un module ou composable dédié.

---

### 3.7 Simulation et import (lot)

**Options d’import (persistées) :**

- **Inclure les relations** : oui/non (sorts, drops, recettes, invocations, etc.).
- **Whitelist / Blacklist de propriétés** : listes séparées par des virgules (optionnel). Envoyées dans le payload batch.
- **Mode de remplacement** : `never` | `draft_raw_only` | `always` (ne pas remplacer / remplacer si brouillon ou brut / toujours remplacer).
- **Skip cache / Force update** : options techniques (cache DofusDB, mise à jour forcée).
- **Validate only (manual choice)** : pour forcer un mode « choix manuel » côté backend si applicable.

**Périmètre (batch scope) :**

1. **Sélection** : uniquement les entités dont la checkbox est cochée. Si aucune cochée, comportement possible : « rien » ou fallback sur « tous les résultats visibles » (à trancher : actuellement fallback sur visibleItems).
2. **Tous** : toutes les entités actuellement chargées dans le tableau (toute la page courante de résultats).
3. **Par pages** : l’utilisateur saisit une plage de pages (ex. `1-6`, `4,5`, `1-3,7`). Pour chaque numéro de page :  
   - on met la page courante à ce numéro ;  
   - on lance une recherche ;  
   - on récupère les IDs de la page ;  
   - on envoie un batch (simulate ou import) sur ces IDs ;  
   - on met à jour les statuts et les erreurs.  
   À la fin, on restaure la page courante et on relance une recherche pour réafficher.

**Flux Simuler :**

1. Construction du payload : `entities: [{ type, id }, ...]`, `dry_run: true`, options (include_relations, replace_mode, whitelist, blacklist, etc.).
2. `POST /api/scrapping/import/batch` avec ce payload.
3. Réponse : `results[]` (par entité : success, error, validation_errors, relations si import), `summary` (success, errors, total).
4. Mise à jour des statuts : `simulé` ou `simulation erreur` selon `result.success`. Stockage des relations si présentes.
5. Si des erreurs : affichage dans la carte « Erreurs import batch » (et `lastBatchResults` pour export CSV).

**Flux Importer :**

- Même chose avec `dry_run: false`. Statuts finaux : `importé` ou `erreur`. Relations mises à jour depuis la réponse.

**Logique à isoler (composable « batch ») :**

- `buildBatchPayload(simulate, scope)` : à partir de `rawItems` / `selectedIds` / `visibleItems` et du scope (selection / all / pages), produit la liste `entities` et les options.
- `runBatch(mode, scope)` : pour scope selection ou all, un seul appel batch.
- `runImportByPages(simulate)` : boucle sur les pages, search + batch par page.
- `runBatchOrByPages(mode)` : selon `batchScope` (selection | all | pages), appelle `runBatch` ou `runImportByPages`.

---

### 3.8 Modale Comparaison (CompareModal)

**Rôle :** Comparer pour **une** entité les trois sources (Brut DofusDB, Converti, Krosmoz existant) et importer en choisissant pour chaque propriété la valeur à garder (existant ou converti).

**Ouverture :** Double-clic sur une ligne du tableau. Props : `entityType`, `dofusdbId`, `open`.

**Comportement :**

1. À l’ouverture (watch sur `open` + entityType + dofusdbId), appel `GET /api/scrapping/preview/{entityType}/{dofusdbId}`.
2. Affichage : tableau des propriétés (clés aplaties, 2 niveaux). Pour chaque ligne : clé, valeur Brut, valeur Converti, valeur Krosmoz (existant). Choix par propriété : radio « Krosmoz » ou « Converti » (défaut : Krosmoz si existant, sinon Converti).
3. Bouton « Importer avec choix » : `POST /api/scrapping/import-with-merge` avec `{ type, dofusdb_id, choices: { "field.path": "krosmoz" | "dofusdb" } }`.
4. Événement `imported` émis au succès ; le parent (Dashboard) met à jour le statut « importé » pour cet ID et peut fermer la modale.

**Données :** Preview = raw, converted, existing (record). Même structure que pour une ligne du tableau, mais pour une seule entité et avec choix par propriété.

---

### 3.9 Autres modales

- **EntityModal** : Affichage en lecture (ou lien) de l’entité existante en base (ouverture depuis le bouton « Existe » du tableau). Données chargées depuis l’API entité (ex. `/api/entities/monsters/{id}` selon le type).
- **TypeManagerTable (modale)** : Liste des types (resource-types, consumable-types, item-types) ou des races (monster-races) / types de sorts (spell-types). Mode « decision » (allowed/blocked) ou « state » (playable/archived). Actions bulk. Config passée par le parent (title, listUrl, bulkUrl, mode).

---

### 3.10 Options & historique

- **Options d’import** : Voir 3.7. Affichées dans un bloc repliable « Options & historique » (masqué par défaut).
- **Historique** : Liste de lignes de log (recherche, simulation, import par pages, etc.) avec horodatage. Bouton « Vider ». Pas d’export requis par la spec (optionnel).
- **Erreurs du dernier batch** : Carte affichée si `lastBatchErrorResults.length > 0`. Tableau : Type, ID, Statut, Message (et validation_errors). Boutons « Exporter (CSV) » et « Fermer ». Export = fichier CSV avec colonnes id, type, statut, message (et détails erreurs).

---

## 4. Synthèse des responsabilités par couche

| Couche | Responsabilité |
|--------|----------------|
| **Dashboard** | Layout, orchestration (recherche, preview, batch, reset), ouverture/fermeture des modales, persistance des préférences. Pas de logique métier fine (parsing, construction payload, extraction relations). |
| **Filtres** | Saisie des critères (IDs, nom, types, races, niveau, pagination). Émission des valeurs vers le parent ou utilisation d’un composable partagé. |
| **Composable Search** | buildSearchQuery, parseIdsFilter, runSearch, rawItems, lastMeta, pagination. |
| **Composable Preview** | fetchConvertedBatch, convertedByItemId, extraction des relations, lastBatchRelationsByKey. |
| **Composable Batch** | buildBatchPayload, runBatch, runImportByPages, runBatchOrByPages, lastBatchResults, options d’import. |
| **Composable ItemStatus** | itemStatusByKey, statuts terminaux, setStatusForEntities, setStatusFromBatchResults, getItemStatusLabel, getItemStatusColor. |
| **Composable Compare (helpers)** | cellTriple, tripleName, tripleLevel, tripleType, comparisonRows, flatten, convertedName, convertedLevel, existingRecord. |
| **Tableau** | Affichage des lignes (visibleRowsWithRelations), colonnes, ligne dépliée, sélection, double-clic → Compare. |
| **CompareModal** | Preview unitaire, affichage Brut/Converti/Krosmoz, choix par propriété, import-with-merge. |
| **TypeManagerTable** | Gestion des types ou races (liste, bulk). Config injectée (urls, mode). |

---

## 5. Référence pour la refonte

Lors de la refonte du code :

1. **Ne pas changer** le comportement décrit dans les sections 2 et 3 (fonctionnalités et flux).
2. **Extraire** la logique listée en « Logique à isoler » dans des composables ou modules (voir [REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md](./REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md)).
3. **Découper** le template du Dashboard en composants (Filtres, Tableau, Options, etc.) en gardant les mêmes props/events et la même UX.
4. **Tester** manuellement ou par tests E2E les scénarios : recherche → preview → simuler → importer (sélection, tous, par pages) ; comparaison ; gestion des types/races ; erreurs batch et export CSV.

Ce document peut être mis à jour si de nouvelles fonctionnalités sont ajoutées (ex. filtre avancé, export des résultats, etc.) ; la liste en section 2 reste la source de vérité du périmètre.
