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
import { ref } from 'vue';
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
    showColumnToggle: {
        type: Boolean,
        default: false
    },
    showSelection: {
        type: Boolean,
        default: false
    },
    showActionsMenu: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['sort', 'toggle-column']);

// Menu de sélection des colonnes
const showColumnMenu = ref(false);

const handleSort = (column) => {
    if (column.sortable) {
        emit('sort', column.key);
    }
};

const handleToggleColumn = (columnKey) => {
    emit('toggle-column', columnKey);
};
</script>

<template>
    <thead>
        <tr>
            <!-- Menu d'actions -->
            <th v-if="showActionsMenu" class="w-12"></th>
            <!-- Colonne de sélection -->
            <th v-if="showSelection" class="w-12"></th>
            <!-- Bouton de menu pour sélectionner les colonnes -->
            <th v-if="showColumnToggle" class="w-12">
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-sm">
                        <Icon source="fa-solid fa-columns" alt="Colonnes visibles" size="sm" />
                    </label>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg border border-base-300">
                        <li class="menu-title">
                            <span>Colonnes visibles</span>
                        </li>
                        <li v-for="column in columns" :key="column.key">
                            <label class="cursor-pointer flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    :checked="visibleColumns[column.key] !== false"
                                    @change="handleToggleColumn(column.key)"
                                    class="checkbox checkbox-sm"
                                />
                                <span>{{ column.label }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
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

