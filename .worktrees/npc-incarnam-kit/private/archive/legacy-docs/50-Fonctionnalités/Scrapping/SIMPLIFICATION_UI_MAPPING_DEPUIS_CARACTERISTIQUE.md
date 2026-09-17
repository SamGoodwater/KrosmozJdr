# Simplification de l’UI mapping : lier depuis la caractéristique

Ce document décrit une **évolution de l’interface** pour faire le lien entre une caractéristique KrosmozJDR et les règles de mapping DofusDB **directement depuis la fiche caractéristique**, sans obliger à passer par l’écran « Mappings » pour associer une règle à une caractéristique.

---

## 1. Avis sur l’idée

**Oui, c’est une très bonne approche** pour plusieurs raisons :

1. **Point d’entrée naturel** : On édite déjà la caractéristique (formules, limites, conversion). C’est là qu’on a en tête « d’où vient cette donnée en scrapping ? ». Faire le lien au même endroit évite d’aller chercher la règle dans un autre écran.
2. **Tableau par entité** : Une ligne par entité DofusDB (monster, item, spell, breed) avec un bouton « Lier » rend explicite : « pour cette caractéristique, d’où je prends la valeur pour monster ? pour item ? ».
3. **Modal avec les variables DofusDB** : En ouvrant un modal au clic sur « Lier », on affiche les **variables récupérées pour un objet DofusDB** (par ex. les `from_path` existants ou proposés pour cette entité). L’utilisateur choisit la source (ex. `grades.0.lifePoints` pour monster), enregistre, et le lien (règle de mapping → `characteristic_id`) est fait. Pas besoin de saisir from_path à la main ni de retenir la structure de l’API.
4. **Moins de va-et-vient** : Aujourd’hui il faut aller sur « Mappings », choisir l’entité, trouver ou créer la règle, puis sélectionner la caractéristique dans un select. En partant de la caractéristique, on réduit les étapes et on garde un seul endroit pour tout ce qui concerne cette caractéristique (limites, conversion, **et** source scrapping).

On peut garder l’écran Mappings pour la gestion globale (création de nouvelles règles, formatters, cibles, ordre), tout en permettant **depuis la caractéristique** de lier/délier rapidement une règle existante à cette caractéristique.

---

## 2. UX proposée

### 2.1 Tableau dans le panneau 3 (Mapping)

- **Titre** : « Lien avec le scrapping (par entité DofusDB) ».
- **Colonnes** : Entité DofusDB | Règle actuelle (clé / from_path) | Cibles | Action.
- **Lignes** : Une ligne par **entité DofusDB pertinente** pour le groupe de la caractéristique :
  - Groupe **creature** → entités : `monster`, `breed` (event. autres si ajoutées).
  - Groupe **object** → entités : `item`, `resource`, `consumable`, `panoply` (selon config).
  - Groupe **spell** → entité : `spell`.

Pour chaque ligne :

- Si une règle de mapping existe déjà pour cette entité **et** a `characteristic_id` = cette caractéristique : afficher la règle (mapping_key, from_path, cibles) et un bouton **Modifier** / **Délier**.
- Sinon : afficher « — » et un bouton **Lier**.

### 2.2 Modal « Lier à une source DofusDB »

- **Déclencheur** : Clic sur **Lier** pour une entité (ex. monster).
- **Contenu** :
  - Entité affichée en lecture seule (ex. « monster »).
  - **Liste des variables/sources disponibles** pour cette entité :
    - Option A : **Règles existantes** pour cette entité (source = dofusdb) : afficher mapping_key, from_path, et indiquer si déjà liées à une autre caractéristique. L’utilisateur en choisit une → on met à jour `characteristic_id` de cette règle.
    - Option B : **Liste dérivée de la config** (from_path connus pour monster dans les JSON ou en BDD) : l’utilisateur choisit un from_path → on crée une règle minimale (from_path, entity, mapping_key dérivé) + une cible par défaut (ex. monster.life) et on lie `characteristic_id`.
  - Bouton **Enregistrer** : appelle l’API (lier la règle à la caractéristique ou créer la règle puis lier). Fermeture du modal + rafraîchissement du panneau 3 (ou rechargement Inertia).

**Recommandation** : commencer par **Option A** (choisir parmi les règles existantes pour cette entité). C’est plus simple (pas de création de règle depuis la caractéristique) et couvre le besoin « écrire le lien directement » : on voit les variables déjà définies pour un objet DofusDB (monster, item, etc.) et on attache la caractéristique à l’une d’elles. Si besoin, on pourra ajouter plus tard l’Option B (création d’une règle depuis un from_path suggéré).

### 2.3 Délier

- Depuis le tableau : bouton **Délier** sur une ligne qui a déjà une règle. Action : mettre `characteristic_id` à `null` pour cette règle. Pas de suppression de la règle.

---

## 3. Backend à prévoir

1. **Endpoint « règles disponibles pour lier »** (ex. `GET /admin/characteristics/{id}/scrapping-mapping-options` ou inclus dans le payload de la fiche) :
   - Entrée : `characteristic_id` (et donc groupe connu).
   - Sortie : pour chaque entité DofusDB pertinente pour ce groupe, liste des règles (source = dofusdb, entity = X) avec : `id`, `mapping_key`, `from_path`, `characteristic_id` (pour afficher « déjà liée à … » ou « libre »). Permet au front d’afficher dans le modal « Choisir la source pour monster » la liste des from_path disponibles.

2. **Endpoint « lier une règle à la caractéristique »** (ex. `POST /admin/characteristics/{id}/link-scrapping-mapping`) :
   - Body : `mapping_id` (id de la règle).
   - Action : `ScrappingEntityMapping::whereId($mapping_id)->update(['characteristic_id' => $characteristic->id])`.
   - Contrôle : la règle doit être pour une entité cohérente avec le groupe de la caractéristique (optionnel mais recommandé).

3. **Endpoint « délier »** (ex. `POST /admin/characteristics/{id}/unlink-scrapping-mapping`) :
   - Body : `mapping_id`.
   - Action : mettre `characteristic_id` à `null` pour cette règle.

(Alternativement, un seul endpoint `PATCH` avec `link_mapping_id` / `unlink_mapping_id` selon le cas.)

---

## 4. Frontend à prévoir

1. **Panneau 3 (MappingPanel.vue)** :
   - Recevoir en props (ou charger) : `scrappingMappingsUsingThis` (déjà en place) + **entités DofusDB pertinentes** + **règles disponibles par entité** (pour le modal).
   - Tableau : une ligne par entité ; colonnes Entité | Règle actuelle | Cibles | Action (Lier / Modifier / Délier).
   - Au clic sur **Lier** : ouvrir un modal avec la liste des règles disponibles pour cette entité (from_path, mapping_key, éventuellement « déjà liée à X »). Au choix + Enregistrer, appeler l’API de liaison puis recharger les données du panneau.

2. **Modal** : composant dédié ou bloc dans MappingPanel ; liste (ou select) des règles pour l’entité sélectionnée ; bouton Enregistrer ; gestion du chargement et des erreurs.

3. **Écran Mappings** : inchangé pour la création complète de règles (formatters, cibles, from_path manuel). On y garde le select « Caractéristique » pour lier une règle à une caractéristique ; la simplification « depuis la caractéristique » vient en complément.

---

## 5. Résumé

| Élément | Description |
|--------|-------------|
| **Objectif** | Pouvoir lier une caractéristique à une règle de mapping DofusDB **depuis la fiche caractéristique**, sans passer obligatoirement par l’écran Mappings. |
| **Tableau** | Une ligne par entité DofusDB (monster, item, spell, breed…) avec règle actuelle (si liée) et bouton Lier / Délier. |
| **Modal** | Au clic sur Lier : afficher les variables/sources disponibles pour cette entité (règles existantes avec from_path) ; l’utilisateur en choisit une, enregistre → le lien est fait. |
| **Backend** | Endpoints (ou payload enrichi) : règles disponibles par entité pour ce groupe ; lier (characteristic_id) ; délier. |
| **Écran Mappings** | Conservé pour la gestion complète des règles ; la liaison depuis la caractéristique reste une simplification ciblée. |

Cette approche simplifie bien le flux « je veux que cette caractéristique soit alimentée par cette donnée DofusDB » en le faisant directement depuis la caractéristique, avec un tableau par entité et un modal qui liste les variables récupérées pour l’objet DofusDB (via les règles existantes).

---

## 6. Implémenté (février 2026) — Création depuis la modal, plus de page Mappings

- **ConfigLoader** : `getEntityMappingEntriesFromFile($source, $entity)` — lit le mapping depuis le JSON d’entité (sans BDD) et retourne les entrées avec `path`, `key`, `langAware`, `targets`, `formatters`. `getEntityLabel($source, $entity)` pour les libellés.
- **CharacteristicController** :  
  - `scrappingMappingOptions` : sans `?entity` → `{ entities: [{ id, label }] }` pour le groupe ; avec `?entity=X` → `{ paths: [...] }` depuis le JSON.  
  - `storeScrappingMapping` : body `{ entity, from_path }` — crée (ou met à jour) la règle à partir de l’entrée correspondante dans le JSON (targets, formatters) et lie à la caractéristique.  
  - `unlinkScrappingMapping` : inchangé (mapping_id → characteristic_id = null).  
  Routes : `scrapping-mapping-options`, `store-scrapping-mapping`, `unlink-scrapping-mapping`.
- **Entités par groupe (modal)** : creature → monster, breed ; object → item, panoply ; spell → spell (`DOFUSDB_ENTITIES_BY_GROUP`).
- **MappingPanel.vue** : tableau une ligne par entité (scrappingMappingEntities) ; **Lier** ouvre un modal qui charge les **chemins** (paths) pour cette entité ; champ **recherche** pour filtrer le tableau des chemins ; sélection d’un chemin → **Enregistrer** appelle `store-scrapping-mapping` (création de la règle + liaison). Plus de lien vers l’écran Mappings.
- **Page admin « Mapping scrapping »** : retirée du menu (LoggedHeaderContainer). Les routes et le contrôleur existent encore (export seeder, éventuels usages internes) mais ne sont plus proposés dans l’UI.
- **Tests** : `test_admin_can_get_scrapping_mapping_entities`, `test_admin_can_get_scrapping_mapping_paths`, `test_admin_can_store_scrapping_mapping`, `test_admin_can_unlink_scrapping_mapping`.
