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

## Frontend

- **Composable** : `resources/js/Composables/entity/useGlobalEntitySearch.js`
- **UI** : `resources/js/Pages/Organismes/data-input/SearchInput.vue` (header)
- **Mapping icônes** : `resources/js/Utils/entity/globalSearchEntityLabel.js`

Au focus : overlay assombri + flou, champ élargi, filtres type entité (couleur = inclus, N&B = exclu), filtres d’état, résultats groupés par type avec badge `EntityLabel`, extrait (`subtitle`), bouton « Afficher plus » si `meta.hasMore`.

## Tests

- `tests/Feature/Api/GlobalSearchControllerTest.php`
- `tests/unit/entity/globalSearchEntityLabel.test.js`
