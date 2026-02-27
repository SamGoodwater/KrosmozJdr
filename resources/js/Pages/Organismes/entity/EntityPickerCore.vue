<script setup>
/**
 * EntityPickerCore
 *
 * @description
 * Coeur UI du moteur de recherche d'entités.
 * - Consomme useEntitySearch (api.tables.{entityType} avec format=entities)
 * - Gère la sélection simple ou multiple
 * - Deux variantes d'UI : compact (popover) et étendue (bloc)
 *
 * Ce composant ne gère PAS les labels/erreurs de formulaire :
 * il est destiné à être enveloppé par un Field (EntityPickerField).
 */
import { computed, watch, onMounted } from 'vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import { useEntitySearch } from '@/Composables/entity/useEntitySearch';

const props = defineProps({
    modelValue: {
        type: [Number, String, Array, null],
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    entityType: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    whitelist: {
        type: Array,
        default: () => [],
    },
    blacklist: {
        type: Array,
        default: () => [],
    },
    sort: {
        type: String,
        default: 'id',
    },
    order: {
        type: String,
        default: 'asc',
    },
    limit: {
        type: Number,
        default: 20,
    },
    debounce: {
        type: Number,
        default: 250,
    },
    variant: {
        type: String,
        default: 'compact', // 'compact' | 'extended'
    },
    placeholder: {
        type: String,
        default: 'Choisir…',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md', // xs | sm | md | lg | xl
    },
});

const emit = defineEmits(['update:modelValue', 'update:selectedEntities']);

const {
    query,
    results,
    loading,
    error,
    currentSort,
    currentOrder,
    hasFilters,
    search,
    setFilters,
    setSort,
    setWhitelist,
    setBlacklist,
} = useEntitySearch({
    entityType: props.entityType,
    initialFilters: props.filters,
    initialSort: props.sort,
    initialOrder: props.order,
    limit: props.limit,
    debounce: props.debounce,
    whitelist: props.whitelist,
    blacklist: props.blacklist,
});

// Synchroniser quand les props changent (filtres / listes / tri)
watch(
    () => props.filters,
    (next) => {
        if (next) {
            setFilters(next);
        }
    },
    { deep: true }
);

watch(
    () => [props.sort, props.order],
    ([s, o]) => {
        setSort(s || 'id', o || 'asc');
    }
);

watch(
    () => props.whitelist,
    (ids) => {
        setWhitelist(ids || []);
    },
    { deep: true }
);

watch(
    () => props.blacklist,
    (ids) => {
        setBlacklist(ids || []);
    },
    { deep: true }
);

// Charger une première fois, en tenant compte d'une valeur initiale
onMounted(() => {
    const ids = selectedIds.value;
    if (ids.length > 0) {
        // Chargement ciblé pour récupérer au moins le libellé de l'entité sélectionnée.
        // Contrat commun: filters[id] supporté par les endpoints api.tables.{entityType}.
        search({
            'filters[id]': ids[0],
            search: '',
        });
    } else {
        search();
    }
});

// Normalisation de la valeur sélectionnée
const selectedIds = computed(() => {
    if (props.multiple) {
        if (Array.isArray(props.modelValue)) {
            return props.modelValue.map((v) => String(v));
        }
        return [];
    }
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
        return [];
    }
    return [String(props.modelValue)];
});

const isSelected = (id) => {
    const strId = String(id);
    return selectedIds.value.includes(strId);
};

const toggleSelect = (entity) => {
    if (!entity || entity.id === undefined || entity.id === null) return;
    const id = entity.id;
    const idStr = String(id);

    if (props.multiple) {
        const current = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
        const idx = current.findIndex((v) => String(v) === idStr);
        if (idx >= 0) {
            current.splice(idx, 1);
        } else {
            current.push(id);
        }
        emit('update:modelValue', current);
    } else {
        // En sélection simple, on remplace simplement la valeur
        emit('update:modelValue', id);
    }

    // Propage aussi les entités sélectionnées complètes si le parent en a besoin
    const byId = new Map(results.value.map((e) => [String(e.id), e]));
    const fullSelected = selectedIds.value
        .map((sid) => byId.get(sid))
        .filter((e) => !!e);
    emit('update:selectedEntities', fullSelected);
};

const selectedCount = computed(() => selectedIds.value.length);

const selectedLabel = computed(() => {
    if (!selectedCount.value) {
        return props.placeholder || 'Choisir…';
    }

    // Essayer de trouver le nom de la première entité sélectionnée
    const firstId = selectedIds.value[0];
    const firstEntity = results.value.find((e) => String(e.id) === firstId);
    const name = firstEntity?.name || firstEntity?.creature?.name || null;

    if (props.multiple) {
        if (name && selectedCount.value === 1) {
            return name;
        }
        return `${selectedCount.value} sélectionné(s)`;
    }

    return name || String(props.modelValue);
});

const sizeClass = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'select-xs';
        case 'sm':
            return 'select-sm';
        case 'lg':
            return 'select-lg';
        case 'xl':
            return 'select-xl';
        case 'md':
        default:
            return 'select-md';
    }
});

const showError = computed(() => !!error.value);
</script>

<template>
    <!-- Variante compacte : bouton + popover -->
    <div v-if="variant === 'compact'" class="w-full">
        <Dropdown
            placement="bottom-start"
            :close-on-content-click="false"
        >
            <template #trigger>
                <button
                    type="button"
                    class="select select-bordered w-full text-left select-variant-glass flex items-center justify-between"
                    :class="[sizeClass, disabled || readonly ? 'select-disabled opacity-60 cursor-not-allowed' : '']"
                    :disabled="disabled || readonly"
                >
                    <span :class="{ 'opacity-50': !selectedCount }">
                        {{ selectedLabel }}
                    </span>
                    <span class="ml-2 text-xs text-base-content/60">
                        <span v-if="loading">Chargement…</span>
                        <span v-else-if="showError">Erreur</span>
                    </span>
                </button>
            </template>

            <template #content>
                <div class="p-3 w-80 space-y-3">
                    <!-- Barre de recherche + actions icônes (filtres, tri) -->
                    <div class="flex items-center gap-2">
                        <InputCore
                            type="search"
                            variant="glass"
                            color="primary"
                            :size="size"
                            class="flex-1"
                            placeholder="Rechercher…"
                            :model-value="query"
                            @update:model-value="(v) => (query = v)"
                        />

                        <!-- Icône filtre (placeholder pour filtres avancés, tooltips) -->
                        <Btn
                            variant="ghost"
                            size="xs"
                            class="tooltip"
                            :data-tip="hasFilters ? 'Filtres actifs' : 'Filtres'"
                        >
                            <i class="fa-solid fa-filter" :class="hasFilters ? 'text-primary' : 'text-base-content/60'"></i>
                        </Btn>

                        <!-- Icône tri (inverse l'ordre) -->
                        <Btn
                            variant="ghost"
                            size="xs"
                            class="tooltip"
                            data-tip="Inverser le tri"
                            @click.stop="setSort(currentSort, currentOrder === 'asc' ? 'desc' : 'asc')"
                        >
                            <i
                                class="fa-solid fa-arrow-up-a-z"
                                :class="currentOrder === 'asc' ? 'rotate-0' : 'rotate-180'"
                            ></i>
                        </Btn>
                    </div>

                    <!-- Liste des résultats -->
                    <div class="max-h-72 overflow-y-auto space-y-1">
                        <button
                            v-for="entity in results"
                            :key="String(entity.id)"
                            type="button"
                            class="w-full flex items-center justify-between rounded px-2 py-1.5 text-sm cursor-pointer transition-colors"
                            :class="[
                                isSelected(entity.id)
                                    ? 'bg-primary/20 text-primary-content'
                                    : 'hover:bg-base-200',
                            ]"
                            @click.stop="toggleSelect(entity)"
                        >
                            <span class="truncate">
                                {{ entity.name || entity.creature?.name || `#${entity.id}` }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Badge
                                    v-if="entity.state"
                                    size="xs"
                                    variant="outline"
                                >
                                    {{ entity.state }}
                                </Badge>
                                <input
                                    v-if="multiple"
                                    type="checkbox"
                                    class="checkbox checkbox-xs"
                                    :checked="isSelected(entity.id)"
                                    @click.stop="toggleSelect(entity)"
                                />
                            </span>
                        </button>

                        <div
                            v-if="!loading && results.length === 0"
                            class="text-sm text-base-content/60 text-center py-2"
                        >
                            Aucun résultat
                        </div>
                    </div>
                </div>
            </template>
        </Dropdown>
    </div>

    <!-- Variante étendue : bloc complet -->
    <div v-else class="w-full space-y-3">
        <div class="flex items-center gap-2">
            <InputCore
                type="search"
                variant="glass"
                color="primary"
                :size="size"
                class="flex-1"
                placeholder="Rechercher…"
                :model-value="query"
                @update:model-value="(v) => (query = v)"
            />

            <Btn
                variant="ghost"
                size="sm"
                class="tooltip"
                :data-tip="hasFilters ? 'Filtres actifs' : 'Filtres'"
            >
                <i class="fa-solid fa-filter" :class="hasFilters ? 'text-primary' : 'text-base-content/60'"></i>
            </Btn>

            <Btn
                variant="ghost"
                size="sm"
                class="tooltip"
                data-tip="Inverser le tri"
                @click.stop="setSort(currentSort, currentOrder === 'asc' ? 'desc' : 'asc')"
            >
                <i
                    class="fa-solid fa-arrow-up-a-z"
                    :class="currentOrder === 'asc' ? 'rotate-0' : 'rotate-180'"
                ></i>
            </Btn>
        </div>

        <div class="border border-base-300 rounded-xl bg-base-100/60 max-h-80 overflow-y-auto divide-y divide-base-200">
            <button
                v-for="entity in results"
                :key="String(entity.id)"
                type="button"
                class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-base-200/80 transition-colors"
                :class="isSelected(entity.id) ? 'bg-primary/10' : ''"
                @click.stop="toggleSelect(entity)"
            >
                <div class="flex flex-col items-start gap-0.5">
                    <span class="font-medium">
                        {{ entity.name || entity.creature?.name || `#${entity.id}` }}
                    </span>
                    <span class="text-xs text-base-content/70">
                        <span v-if="entity.monsterRace?.name">{{ entity.monsterRace.name }}</span>
                        <span v-else-if="entity.description">{{ entity.description }}</span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <Badge
                        v-if="entity.state"
                        size="xs"
                        variant="outline"
                    >
                        {{ entity.state }}
                    </Badge>
                    <input
                        v-if="multiple"
                        type="checkbox"
                        class="checkbox checkbox-xs"
                        :checked="isSelected(entity.id)"
                        @click.stop="toggleSelect(entity)"
                    />
                </div>
            </button>

            <div
                v-if="!loading && results.length === 0"
                class="text-sm text-base-content/60 text-center py-3"
            >
                Aucun résultat
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.select-variant-glass {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
    transition: all 0.15s ease-in-out;

    &:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.3);
    }

    &:focus-visible {
        outline: 2px solid var(--color-primary, #3b82f6);
        outline-offset: 2px;
    }
}
</style>

