<script setup>
/**
 * EntityTableFilters Molecule
 * 
 * @description
 * Barre de filtres et recherche pour les tableaux d'entités
 * Utilise InputField pour la recherche et SelectField pour les filtres
 * 
 * @props {String} search - Valeur de recherche
 * @props {Object} filters - Objet avec les filtres par colonne
 * @props {Array} filterableColumns - Colonnes filtrables avec leurs options
 * @emit update:search - Événement émis lors du changement de recherche
 * @emit update:filters - Événement émis lors du changement de filtres
 * @emit reset - Événement émis lors de la réinitialisation
 */
import { computed } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    filterableColumns: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:search', 'update:filters', 'reset']);

const handleSearchUpdate = (value) => {
    emit('update:search', value);
};

const handleFilterUpdate = (columnKey, value) => {
    const newFilters = { ...props.filters, [columnKey]: value };
    if (!value || value === '') {
        delete newFilters[columnKey];
    }
    emit('update:filters', newFilters);
};

const handleReset = () => {
    emit('reset');
};

const hasActiveFilters = computed(() => {
    return props.search || Object.keys(props.filters).length > 0;
});
</script>

<template>
    <div class="space-y-4 p-4 bg-base-200 rounded-lg">
        <!-- Barre de recherche -->
        <div class="flex gap-2 items-end">
            <div class="flex-1">
                <InputField
                    :model-value="search"
                    @update:model-value="handleSearchUpdate"
                    placeholder="Rechercher..."
                    label="Recherche"
                    default-label-position="top"
                >
                    <template #labelInEnd>
                        <Icon source="fa-solid fa-magnifying-glass" alt="Rechercher" size="sm" />
                    </template>
                </InputField>
            </div>
            
            <!-- Bouton reset -->
            <Btn
                v-if="hasActiveFilters"
                variant="ghost"
                size="md"
                @click="handleReset"
                aria-label="Réinitialiser les filtres">
                <Icon source="fa-solid fa-xmark" alt="Réinitialiser" size="sm" />
            </Btn>
        </div>

        <!-- Filtres par colonne -->
        <div v-if="filterableColumns.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <SelectField
                v-for="column in filterableColumns"
                :key="column.key"
                :model-value="filters[column.key] || ''"
                @update:model-value="(value) => handleFilterUpdate(column.key, value)"
                :options="column.options || []"
                :label="column.label"
                default-label-position="top"
                placeholder="Tous"
            />
        </div>
    </div>
</template>

