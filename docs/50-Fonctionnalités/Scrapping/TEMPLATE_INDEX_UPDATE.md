# Template pour mettre à jour les pages Index.vue

## Pattern à suivre

1. Ajouter `onBeforeUnmount` dans les imports
2. Ajouter `filters` dans les props
3. Ajouter `search` et `filters` dans l'état
4. Remplacer `handleSort` pour utiliser `router.get` avec les paramètres
5. Ajouter `handleSearchUpdate`, `handleFiltersUpdate`, `handleFiltersReset`
6. Ajouter `filterableColumns` (selon les colonnes disponibles)
7. Mettre à jour le template `EntityTable` pour inclure les props et events

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

// Dans le template
<EntityTable
    ...
    :show-filters="true"
    :search="search"
    :filters="filters"
    :filterable-columns="filterableColumns"
    @update:search="handleSearchUpdate"
    @update:filters="handleFiltersUpdate"
/>
```

