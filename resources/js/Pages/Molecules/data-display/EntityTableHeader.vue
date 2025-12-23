<script setup>
/**
 * EntityTableHeader Molecule
 * 
 * @description
 * En-tête de tableau pour les entités avec colonnes configurables
 * 
 * @props {Array} columns - Configuration des colonnes [{ key, label, sortable }]
 * @props {String} sortBy - Colonne actuellement triée
 * @props {String} sortOrder - Ordre de tri ('asc' | 'desc')
 * @props {Object} visibleColumns - Objet avec les colonnes visibles { key: boolean }
 * @props {Boolean} showColumnToggle - Afficher le menu de sélection des colonnes
 * @emit sort - Événement émis lors du clic sur une colonne triable
 * @emit toggle-column - Événement émis lors du toggle d'une colonne
 */
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
        default: () => []
    },
    sortBy: {
        type: String,
        default: ''
    },
    sortOrder: {
        type: String,
        default: 'asc',
        validator: (v) => ['asc', 'desc'].includes(v)
    },
    visibleColumns: {
        type: Object,
        default: () => ({})
    },
    showSelection: {
        type: Boolean,
        default: false
    },
    /**
     * État du checkbox "tout sélectionner" (géré par le parent).
     */
    allSelected: {
        type: Boolean,
        default: false
    },
    /**
     * Indéterminé (une partie sélectionnée).
     */
    someSelected: {
        type: Boolean,
        default: false
    },
    showActionsMenu: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['sort', 'toggle-column', 'toggle-all']);

const handleSort = (column) => {
    if (column.sortable) {
        emit('sort', column.key);
    }
};

const handleToggleColumn = (columnKey) => {
    emit('toggle-column', columnKey);
};

const handleToggleAll = (event) => {
    emit('toggle-all', Boolean(event.target.checked));
};
</script>

<template>
    <thead>
        <tr>
            <!-- Menu d'actions -->
            <th v-if="showActionsMenu" class="w-12"></th>
            <!-- Colonne de sélection -->
            <th v-if="showSelection" class="w-12">
                <input
                    type="checkbox"
                    class="checkbox checkbox-sm"
                    :checked="allSelected"
                    :indeterminate.prop="someSelected && !allSelected"
                    @change="handleToggleAll"
                    title="Sélectionner/désélectionner tout"
                />
            </th>
            
            <!-- Colonnes du tableau -->
            <th 
                v-for="column in columns" 
                :key="column.key"
                v-show="visibleColumns[column.key] !== false"
                :class="{ 'cursor-pointer hover:bg-base-200': column.sortable }"
                @click="handleSort(column)">
                <div class="flex items-center gap-2">
                    <span>{{ column.label }}</span>
                    <span v-if="column.sortable && sortBy === column.key" class="text-xs">
                        {{ sortOrder === 'asc' ? '↑' : '↓' }}
                    </span>
                </div>
            </th>
        </tr>
    </thead>
</template>

