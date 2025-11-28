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
 * @emit sort - Événement émis lors du clic sur une colonne triable
 */
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
    }
});

const emit = defineEmits(['sort']);

const handleSort = (column) => {
    if (column.sortable) {
        emit('sort', column.key);
    }
};
</script>

<template>
    <thead>
        <tr>
            <th v-for="column in columns" :key="column.key" 
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

