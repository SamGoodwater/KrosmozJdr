# État des lieux : affichage scrapping et conversion

**Contexte :** Tester le flux scrapping → conversion des données. Le principal problème signalé est que **les données ne s'affichent pas très bien** lors du lancement d'un scrapping.

**Références :** [Architecture scrapping](Architecture/README.md), [Plan finalisation](PLAN_FINALISATION_SCRAPPING.md).

---

## 1. Ce qui existe

### 1.1 Pages et composants

| Élément | Rôle |
|--------|------|
| **Page `/scrapping`** | `Pages/scrapping/Index.vue` → wrapper avec titre, contient `ScrappingDashboard`. |
| **ScrappingDashboard** | Dashboard complet : choix d'entité, filtres (IDs, name, typeId, raceId, level…), recherche via `/api/scrapping/search/{entity}`, tableau de résultats, options d'import (skipCache, forceUpdate, dryRun, includeRelations), actions Simuler / Importer (single, plage, tout), historique type CLI, modale de gestion des types (item-types, resource-types, consumable-types, monster-races, spell-types). |
| **ScrappingSection** | Bloc réutilisable (page ou modal) : sélecteur de type d'entité, options d'import, `SearchPreviewSection`, `ScrappingSearchTableSection`, historique. Utilisé dans la page et dans `ScrappingModal`. |
| **SearchPreviewSection** | Recherche par ID (unique, plage, tout) + boutons Prévisualiser / Simuler / Importer. Appel à `GET /api/scrapping/preview/{entity}/{id}`. Affiche le **résultat de la prévisualisation**. |
| **ScrappingSearchTableSection** | Recherche par filtres (name, id, ids, typeId, raceId, levelMin/Max, pagination). Tableau TanStack avec colonnes id, name, typeId, raceId, level selon le type. Sélection multiple → Importer la sélection ou Prévisualiser (1er ID). |
| **EntityDiffTable** | Table de comparaison : flatten des objets existant vs importé, lignes « Champ | Base actuelle | Version importée » pour chaque clé différente. |
| **CompareModal** | Modal « Krosmoz vs DofusDB » : même principe de flatten, choix par propriété (garder Krosmoz ou DofusDB), puis import avec merge. |
| **ScrappingModal** | Modal simple : type verrouillé + ID prérempli, preview auto, import (utilisé depuis une fiche entité pour « Mettre à jour via scrapping »). |

### 1.2 API utilisée pour l’affichage

| Endpoint | Usage |
|----------|--------|
| `GET /api/scrapping/preview/{type}/{id}` | Prévisualisation d’une entité. Retourne `success`, `data.converted` (structure par modèle : creatures, monsters, items…), `data.existing` (record Krosmoz pour comparaison), `data.validation_errors`, `data.raw` (actuellement **toujours null**). |
| `POST /api/scrapping/preview/batch` | Prévisualisation en lot (jusqu’à 100 IDs). Retourne `data.items[]` avec pour chaque id : `converted`, `existing`, `error`. |
| `GET /api/scrapping/search/{entity}` | Recherche DofusDB (collect-only) avec filtres + pagination. Retourne `data.items` (liste d’objets bruts avec id, name, typeId, etc.) et `data.meta`. |
| `POST /api/scrapping/import/batch` | Import en lot. Payload `entities`, `skip_cache`, `force_update`, etc. |
| `POST /api/scrapping/import/{entity}/{id}` | Import d’une entité (route alternative). |

### 1.3 Données renvoyées par la prévisualisation

- **converted** : structure imbriquée par **modèle** (ex. `{ creatures: { name, ... }, monsters: { level, ... }, ... }`). C’est le payload après conversion (formatters, caractéristiques, limites).
- **existing** : `{ record: { ... } }` — même forme aplatie / par modèle selon `getExistingAttributesForComparison`.
- **validation_errors** : liste `[{ path, message }]` en cas d’échec de validation (limites, type list/boolean, etc.). **Non affichée** dans l’UI actuelle.
- **raw** : prévu côté front (Relations détectées) mais **toujours null** côté back → la section « Relations détectées » ne peut pas s’appuyer sur `raw`.

---

## 2. Problèmes d’affichage identifiés

### 2.1 Prévisualisation (SearchPreviewSection)

| Problème | Détail |
|----------|--------|
| **JSON brut dans des `<pre>`** | « Version DofusDB convertie » et « Version actuelle (base Krosmoz) » sont affichées avec `JSON.stringify(..., null, 2)` dans un `<pre>`. Pour des structures riches (effets, bonus, relations), c’est illisible et peu exploitable. |
| **Structure par modèle** | `converted` est un objet par modèle (`creatures`, `monsters`, `items`…). Le front affiche cet objet tel quel ; l’utilisateur ne voit pas une « fiche » métier (ex. un monstre avec nom, niveau, PV, sorts) mais des clés techniques. |
| **Pas de libellés métier** | Les champs sont des clés techniques (level, rarity, effects, …) sans traduction ni regroupement (caractéristiques, effets, relations). |
| **Relations détectées** | Le bloc s’appuie sur `previewData.raw` (spells, drops, recipe, summon). L’API renvoie `raw: null` → les relations ne sont jamais affichées (ou uniquement « Aucune relation détectée »). |
| **Erreurs de validation** | `data.validation_errors` est renvoyé par l’API mais **n’est pas affiché** dans SearchPreviewSection. En cas d’échec (hors limites, valeur hors liste), l’utilisateur ne voit pas pourquoi. |
| **Succès / échec** | Même si `success` est false (validation échouée), la réponse est parfois traitée comme succès et les données s’affichent sans message d’erreur clair. |

### 2.2 Table de comparaison (EntityDiffTable)

| Problème | Détail |
|----------|--------|
| **Clés techniques** | Après flatten, les clés sont du type `monsters.level`, `effects[0].effect_type_id`, `creatures.name`. Pas de libellés « Niveau », « Effet 1 – Type », « Nom ». |
| **Table très longue** | Beaucoup de champs → table avec beaucoup de lignes, peu lisible. |
| **Pas de regroupement** | Pas de sections (Identité, Caractéristiques, Effets, Relations). |
| **Objets / tableaux** | Affichés en `String(...)` (ex. `[object Object]`) quand ce n’est pas du flatten récursif partiel. |

### 2.3 CompareModal

- Même logique de flatten et clés techniques.
- Choix « Krosmoz / DofusDB » par champ : utile mais liste très longue et peu lisible.

### 2.4 Tableau de recherche (ScrappingSearchTableSection)

- Colonnes basiques (id, name, typeId, raceId, level) : correct pour une liste.
- Pas de libellés localisés (typeId → « Type », raceId → « Race »).
- Pas d’affichage des erreurs de validation ou de conversion par ligne si on avait un mode « prévisualiser tout le lot » avec erreurs.

### 2.5 Résultats d’import (batch / range)

- Message global du type « Import batch: X/Y (erreurs: Z) ».
- Pas de détail par ID (quel ID a échoué, quelle erreur). Difficile de corriger la config ou les données.

---

## 3. Pistes d’amélioration

### 3.1 Court terme (affichage prévisualisation)

1. **Afficher les erreurs de validation**  
   Dans `SearchPreviewSection`, si `previewData.validation_errors` est non vide, afficher un bloc Alert listant `path` et `message` pour chaque erreur.

2. **Ne pas afficher les données converties comme succès si `success === false`**  
   Vérifier `data.success` (et éventuellement `data.data.validation_errors`) avant de remplir `previewData` ; sinon afficher un message d’erreur et optionnellement la liste des erreurs.

3. **Enrichir l’API preview avec `raw` (optionnel)**  
   Pour que « Relations détectées » soit utile, renvoyer dans `data.raw` un extrait des données brutes (spells, drops, recipe, summon) depuis `$result->getRaw()` dans le contrôleur. Adapter le front pour afficher ces relations.

4. **Remplacer le double `<pre>` par une vue structurée**  
   Au lieu de `stringify(previewData.converted)` :
   - Soit un composant « résumé » par type d’entité (monster, item, spell…) qui affiche les champs principaux (nom, niveau, PV, rareté…) avec des libellés.
   - Soit une liste de paires (libellé, valeur) dérivée d’un mapping ou des descriptors existants (ex. `entity-actions-config`, descriptors par entité) pour réutiliser les libellés du reste de l’app.

### 3.2 Moyen terme (comparaison et lisibilité)

5. **Libellés dans EntityDiffTable / CompareModal**  
   - Introduire un mapping `clé technique → libellé` (ou réutiliser un descriptor existant) pour afficher « Niveau », « Rareté », « Nom », etc.
   - Grouper les champs par section (Identité, Stats, Effets, Relations) pour réduire la longueur de la table et améliorer la lecture.

6. **Affichage des tableaux (effets, bonus)**  
   - Pour les champs de type tableau (effects, bonus), ne pas les aplatir en `effects[0].…` dans une seule ligne ; afficher une sous-section ou un tableau par effet (type, min, max, etc.) avec des libellés.

7. **Vue « fiche » par type d’entité**  
   - Composant dédié par type (MonsterPreviewCard, ItemPreviewCard, SpellPreviewCard) qui affiche les champs importants avec la même logique que les vues entité existantes (descriptors, formatters), en lecture seule. Réutiliser les configs / descriptors du système d’entités si possible.

### 3.3 Plus long terme (batch et import)

8. **Détail des erreurs après import batch**  
   - Côté API : retourner une liste d’erreurs par ID (id, message ou validation_errors).
   - Côté front : afficher un résumé + liste dépliable ou tableau « ID | Statut | Erreur ».

9. **Prévisualisation batch dans l’UI**  
   - Pour une sélection multiple, appeler `preview/batch` et afficher un tableau avec pour chaque ligne : ID, nom, statut (OK / Erreur), message d’erreur éventuel. Permet de voir avant d’importer quels IDs posent problème.

10. **Indicateurs de conversion**  
    - Afficher clairement quelles caractéristiques viennent de la conversion (niveau, rareté, PV, etc.) et rappeler la source (DofusDB → formules / limites BDD) pour faciliter le debug et la confiance dans le flux.

---

## 4. Synthèse

| Où | Ce qui existe | Problème principal | Action prioritaire |
|----|----------------|--------------------|--------------------|
| **Prévisualisation (preview)** | Appel API, affichage converted + existing en JSON, diff table | JSON brut illisible ; pas d’erreurs de validation ; raw null | Afficher validation_errors ; optionnellement renvoyer raw ; remplacer `<pre>` par vue structurée ou résumé par type |
| **Comparaison (diff)** | EntityDiffTable, CompareModal | Clés techniques, liste longue, pas de regroupement | Libellés + regroupement par section ; sous-sections pour tableaux (effets) |
| **Recherche + tableau** | Filtres, TanStack, sélection, import batch | OK pour la liste ; pas de détail d’erreur par ligne | Garder tel quel en priorité ; détail des erreurs batch en phase 2 |
| **Import batch** | Payload + message résumé | Pas de détail par ID en cas d’erreur | Retourner et afficher erreurs par ID |

En résumé : **l’affichage actuel repose surtout sur du JSON brut et des clés techniques**, sans exploitation des **validation_errors** ni des **libellés métier**. Les premières améliorations les plus impactantes sont : **afficher les erreurs de validation**, **ne plus considérer comme succès un preview en échec**, et **remplacer le double `<pre>` par une vue structurée ou des résumés par type d’entité** (avec libellés et regroupements). Ensuite : libellés et regroupement dans la diff, puis détail des erreurs par ID sur l’import batch.
