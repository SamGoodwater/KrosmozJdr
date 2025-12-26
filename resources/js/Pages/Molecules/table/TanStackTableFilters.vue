<script setup>
/**
 * TanStackTableFilters Molecule
 *
 * @description
 * UI générique des filtres côté client (select/boolean/text) à partir de la config.
 * Les options peuvent venir de `filterOptions` (serveur) ou de la colonne (fallback).
 */

const props = defineProps({
    columns: { type: Array, required: true },
    filterValues: { type: Object, default: () => ({}) }, // { [filterId]: any }
    filterOptions: { type: Object, default: () => ({}) }, // { [filterId]: [{value,label}] }
});

const emit = defineEmits(["update:filters", "reset"]);

const filterableColumns = () => (props.columns || []).filter((c) => c?.filter?.id && c?.filter?.type);

const getOptions = (col) => {
    const id = col?.filter?.id;
    if (id && Array.isArray(props.filterOptions?.[id])) return props.filterOptions[id];
    if (Array.isArray(col?.filter?.options)) return col.filter.options;
    return [];
};

const updateFilter = (filterId, value) => {
    emit("update:filters", { ...(props.filterValues || {}), [filterId]: value });
};
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold">Filtres</div>
            <button class="btn btn-ghost btn-xs" type="button" @click="emit('reset')">
                Réinitialiser
            </button>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="col in filterableColumns()" :key="col.id" class="space-y-1">
                <div class="text-xs opacity-70">{{ col.label }}</div>

                <!-- select / boolean -->
                <select
                    v-if="col.filter.type === 'select' || col.filter.type === 'boolean'"
                    class="select select-bordered select-sm w-full"
                    :value="String((filterValues[col.filter.id] ?? ''))"
                    @change="updateFilter(col.filter.id, $event.target.value)"
                >
                    <option value="">Tous</option>
                    <option
                        v-for="opt in getOptions(col)"
                        :key="String(opt.value)"
                        :value="String(opt.value)"
                    >
                        {{ opt.label }}
                    </option>
                </select>

                <!-- text -->
                <input
                    v-else-if="col.filter.type === 'text'"
                    class="input input-bordered input-sm w-full"
                    type="text"
                    :value="String(filterValues[col.filter.id] ?? '')"
                    @input="updateFilter(col.filter.id, $event.target.value)"
                />

                <!-- unsupported (Phase 1) -->
                <div v-else class="text-xs opacity-50">
                    Filtre non supporté ({{ col.filter.type }})
                </div>
            </div>
        </div>
    </div>
</template>


