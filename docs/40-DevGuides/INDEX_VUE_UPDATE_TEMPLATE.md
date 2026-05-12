Ce document a été déplacé depuis `docs/50-Fonctionnalités/Scrapping/` car il ne concerne pas spécifiquement le scrapping.

---

# Template pour mettre à jour les pages Index.vue

## Pattern à suivre

1. Ajouter `onBeforeUnmount` dans les imports
2. Ajouter `filters` dans les props
3. Ajouter `search` et `filters` dans l'état
4. Remplacer `handleSort` pour utiliser `router.get` avec les paramètres
5. Ajouter `handleSearchUpdate`, `handleFiltersUpdate`, `handleFiltersReset`
6. Déclarer **filtres, tri et colonnes** dans le **`TanStackTableConfig`** de l’entité (généré via `TableConfig` / descripteurs dans `entity-registry`, pas via l’ancienne prop `filterableColumns` d’`EntityTable` v1). Chaque filtre expose un `filter.id` aligné sur les query params lus par `useTableServerParams` — voir [TANSTACK_TABLE.md](../30-UI/TANSTACK_TABLE.md).
7. Mettre à jour le template : utiliser **`EntityTanStackTable`** (TanStack Table v2), pas l’ancien `EntityTable`. Même doc pour `config`, mode serveur (`serverSide` + `serverBaseUrl`) et barre d’outils.

## Exemple de code à ajouter

```javascript
// Dans les imports
import { ref, computed, onBeforeUnmount } from "vue";

// Dans les props
filters: {
    type: Object,
    default: () => ({})
}

// Dans l'état
const search = ref(props.filters.search || '');
const filters = ref(props.filters || {});

// Handlers
const handleSort = ({ column, order }) => {
    router.get(route('entities.XXX.index'), {
        sort: column,
        order: order,
        search: search.value,
        ...filters.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

let searchTimeout = null;

const handleSearchUpdate = (value) => {
    search.value = value;
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(route('entities.XXX.index'), {
            search: value,
            ...filters.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }, 300);
};

onBeforeUnmount(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

const handleFiltersUpdate = (newFilters) => {
    filters.value = newFilters;
    router.get(route('entities.XXX.index'), {
        search: search.value,
        ...newFilters
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleFiltersReset = () => {
    search.value = '';
    filters.value = {};
    router.get(route('entities.XXX.index'), {}, {
        preserveState: true,
        preserveScroll: true
    });
};
```

```vue
<!-- Exemple minimal — adapter entity-type, tableConfig et route API -->
<EntityTanStackTable
    entity-type="XXX"
    :config="tableConfig"
    server-side
    :server-base-url="route('api.tables.XXX')"
/>
```

Les handlers `handleSearchUpdate` / `handleFiltersUpdate` ci-dessus restent utiles si la page synchronise recherche et filtres avec l’URL (`router.get`) : l’URL est alors la source de vérité lue par `EntityTanStackTable` via les query params (détail dans [TANSTACK_TABLE.md](../30-UI/TANSTACK_TABLE.md)).