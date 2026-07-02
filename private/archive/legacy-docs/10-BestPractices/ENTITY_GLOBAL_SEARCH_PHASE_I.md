# Phase I — Recherche globale (header)

## API

- **Route** : `GET /api/global-search` (`api.global-search`)
- **Service** : `App\Services\Search\GlobalSearchService`
- **Contrôleur** : `App\Http\Controllers\Api\GlobalSearchController`

Paramètres : `q` (min. 2 caractères), `types[]`, `states[]`, `limit` (défaut 40, max 80).

Réponse :

```json
{
  "results": [{ "id", "entityType", "group", "title", "subtitle", "href", "icon", "iconUrl" }],
  "meta": { "limit": 40, "hasMore": false }
}
```

Chaque ligne est filtrée par le Gate `view` sur le modèle concerné.

### Ordre des types (résultats et requêtes par défaut)

`classes → spécialisations → monstres → sorts → capacités → traits → états → équipements → consommables → ressources → panoplies → campagnes → scénarios → PNJ → boutiques → pages → sections`

Les types suivants sont **recherchés** mais **sans bouton filtre** dans le header (résultats uniquement) :

- `item-types`, `consumable-types`, `spell-types`, `monster-races`, `resource-types`

Le type `creatures` (classe parente technique) n’est **pas** indexé.

### Redirections (`href`)

| `entityType` | Destination |
|--------------|-------------|
| Entités classiques (`spells`, `items`, …) | `entities.{type}.show` (fiche) |
| `resource-types` | `/entities/resources?resource_type_id={id}` |
| `item-types` | `/entities/items?item_type_id={id}` |
| `consumable-types` | `/entities/consumables?consumable_type_id={id}` |
| `monster-races` | `/entities/monsters?monster_race_id={id}` |
| `spell-types` | `/entities/spells?spell_type_id={id}` |
| `pages` / `sections` | Page CMS (+ ancre section) |

Titres spéciaux :

- **Monstres** : nom de la créature liée (`creature.name`), sinon race, sinon `#id`.
- **Groupes** : « Équipements » (items), « États » (conditions).

## Frontend

- **Composable** : `resources/js/Composables/entity/useGlobalEntitySearch.js`
  - `GLOBAL_SEARCH_TYPE_ORDER` — ordre des groupes
  - `GLOBAL_SEARCH_TYPE_FILTERS` — boutons visibles dans le header (sous-ensemble ordonné)
- **UI** : `resources/js/Pages/Organismes/data-input/SearchInput.vue` (header)
- **Mapping icônes** : `resources/js/Utils/entity/globalSearchEntityLabel.js`

Au focus : overlay assombri + flou via `<dialog showModal>` (top layer) ; panneau de recherche au-dessus du flou ; fermeture backdrop uniquement si pointerdown **et** pointerup sur le dialog (sélection de texte dans l’input sans fermer).

Champ compact : raccourci `ALT + K` en superposition dans l’input ; bouton effacer (`×`) si texte saisi.

### Miniatures des résultats

Chaque ligne affiche à gauche du titre :

- `iconUrl` — image métier (`image` / `creature.image` pour les monstres), URL absolue ou `storage/` normalisée côté API (aucune requête média Spatie en plus) ;
- sinon initiales du `title` via le composant `EntityThumb` (wrapper `Avatar`), comme l’avatar utilisateur ;
- si l’URL image échoue, même fallback initiales.

Taille `search` sur `EntityThumb` (36×36 px, `rounded-box`).

Filtres type (couleur = inclus, N&B = exclu), filtres d’état, résultats groupés par type avec `EntityLabel`, extrait (`subtitle`), bouton « Afficher plus » si `meta.hasMore`.

Couleur **Capacité** : token `capability` (pink) dans `EntityLabel` et thème entités.

## Fiches Show

- Équipement : `entities.items.show` → `Pages/entity/item/Show`
- Consommable : `entities.consumables.show` → `Pages/entity/consumable/Show`

## Tests

- `tests/Feature/Api/GlobalSearchControllerTest.php`
- `tests/unit/entity/globalSearchEntityLabel.test.js`
