<script setup>
/**
 * TanStackTableFilters Molecule
 *
 * @description
 * UI générique des filtres côté client (select/boolean/text) à partir de la config.
 * Les options peuvent venir de `filterOptions` (serveur) ou de la colonne (fallback).
 */

import ToggleCore from "@/Pages/Atoms/data-input/ToggleCore.vue";
import { computed, unref } from "vue";

const props = defineProps({
    columns: { type: Array, required: true },
    filterValues: { type: Object, default: () => ({}) }, // { [filterId]: any }
    filterOptions: { type: Object, default: () => ({}) }, // { [filterId]: [{value,label}] }
});

const emit = defineEmits(["update:filters", "reset"]);

const filterableColumns = () => (props.columns || []).filter((c) => c?.filter?.id && c?.filter?.type);

// Support: le parent peut passer soit un objet, soit un ref({}) (compat).
const values = computed(() => unref(props.filterValues) || {});

const getOptions = (col) => {
    const id = col?.filter?.id;
    if (id && Array.isArray(props.filterOptions?.[id])) return props.filterOptions[id];
    if (Array.isArray(col?.filter?.options)) return col.filter.options;
    return [];
};

const updateFilter = (filterId, value) => {
    emit("update:filters", { ...(values.value || {}), [filterId]: value });
};

const getRawFilterValue = (filterId) => String((values.value?.[filterId] ?? ""));

const booleanStateLabel = (raw) => {
    if (raw === "") return "Tous";
    return String(raw) === "1" ? "Oui" : "Non";
};

const isBooleanChecked = (raw) => String(raw) === "1";

const toggleBooleanFilter = (filterId, checked) => {
    const raw = getRawFilterValue(filterId);

    // Etat "Tous" (raw="") : un clic ON => Oui
    if (raw === "" && checked) {
        updateFilter(filterId, "1");
        return;
    }

    // Toggle classique Oui/Non
    updateFilter(filterId, checked ? "1" : "0");
};

const clearBooleanFilter = (filterId) => {
    updateFilter(filterId, "");
};

const hasBooleanFilter = (filterId) => getRawFilterValue(filterId) !== "";
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

                <!-- boolean (switch) -->
                <div
                    v-if="col.filter.type === 'boolean'"
                    class="flex items-center justify-between gap-3 w-full rounded-lg border border-base-300 px-2 py-1"
                >
                    <div class="flex items-center gap-3">
                        <!-- wrapper fixe pour éviter tout micro-shift de layout -->
                        <div class="w-10 flex items-center justify-center shrink-0">
                            <ToggleCore
                                :model-value="isBooleanChecked(getRawFilterValue(col.filter.id))"
                                size="sm"
                                color="primary"
                                @update:model-value="(v) => toggleBooleanFilter(col.filter.id, v)"
                            />
                        </div>

                        <span class="text-sm min-w-10" :class="{ 'opacity-70': !hasBooleanFilter(col.filter.id) }">
                            {{ booleanStateLabel(getRawFilterValue(col.filter.id)) }}
                        </span>
                    </div>

                    <button
                        type="button"
                        class="btn btn-ghost btn-xs w-7 h-7 min-h-0 px-0 flex items-center justify-center"
                        title="Retirer le filtre"
                        :class="{ 'opacity-40 cursor-default': !hasBooleanFilter(col.filter.id) }"
                        @click="hasBooleanFilter(col.filter.id) && clearBooleanFilter(col.filter.id)"
                    >
                        ✕
                    </button>
                </div>

                <!-- select -->
                <select
                    v-else-if="col.filter.type === 'select'"
                    class="select select-bordered select-sm w-full"
                    :value="String((values[col.filter.id] ?? ''))"
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


