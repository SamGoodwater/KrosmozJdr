<script setup>
/**
 * EntityTableRow Molecule
 * 
 * @description
 * Ligne de tableau pour une entité avec lien cliquable au centre
 * 
 * @props {Object} entity - Données de l'entité
 * @props {Array} columns - Configuration des colonnes
 * @props {String} entityType - Type d'entité (pour générer les routes)
 * @props {Function} formatCell - Fonction optionnelle pour formater les cellules
 * @emit view - Événement émis lors du clic sur le nom
 * @emit edit - Événement émis lors du clic sur éditer
 * @emit delete - Événement émis lors du clic sur supprimer
 */
import Route from '@/Pages/Atoms/action/Route.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    formatCell: {
        type: Function,
        default: null
    }
});

const emit = defineEmits(['view', 'edit', 'delete']);

const getCellValue = (column) => {
    const value = props.entity[column.key];
    
    // Format personnalisé depuis la colonne
    if (column.format && typeof column.format === 'function') {
        return column.format(value, props.entity);
    }
    
    // Format personnalisé depuis le prop
    if (props.formatCell) {
        return props.formatCell(column.key, value, props.entity);
    }
    
    // Formatage par défaut
    if (value === null || value === undefined) {
        return '-';
    }
    
    if (typeof value === 'boolean') {
        return value ? 'Oui' : 'Non';
    }
    
    if (Array.isArray(value)) {
        return value.length;
    }
    
    if (typeof value === 'object') {
        return value.name || value.title || JSON.stringify(value);
    }
    
    return value;
};

const handleView = () => {
    emit('view', props.entity);
};

const handleEdit = () => {
    emit('edit', props.entity);
};

const handleDelete = () => {
    emit('delete', props.entity);
};

const getEntityRoute = () => {
    return `entities.${props.entityType}.show`;
};

const getEntityRouteParams = () => {
    return { [props.entityType]: props.entity.id };
};
</script>

<template>
    <tr class="hover:bg-base-200 transition-colors">
        <td v-for="column in columns" :key="column.key" 
            :class="{ 'text-center font-semibold': column.isMain }">
            <!-- Colonne principale (nom avec lien) -->
            <template v-if="column.isMain">
                <button 
                    @click="handleView"
                    class="link link-primary link-hover font-semibold text-left">
                    {{ getCellValue(column) }}
                </button>
            </template>
            
            <!-- Colonnes d'actions -->
            <template v-else-if="column.key === 'actions'">
                <div class="flex gap-2 justify-end">
                    <Tooltip v-if="entity.can?.view" content="Voir" placement="top">
                        <Btn size="sm" variant="ghost" @click="handleView">
                            <i class="fa-solid fa-eye"></i>
                        </Btn>
                    </Tooltip>
                    <Tooltip v-if="entity.can?.update" content="Éditer" placement="top">
                        <Btn size="sm" variant="ghost" @click="handleEdit">
                            <i class="fa-solid fa-pen"></i>
                        </Btn>
                    </Tooltip>
                    <Tooltip v-if="entity.can?.delete" content="Supprimer" placement="top">
                        <Btn size="sm" variant="ghost" color="error" @click="handleDelete">
                            <i class="fa-solid fa-trash"></i>
                        </Btn>
                    </Tooltip>
                </div>
            </template>
            
            <!-- Colonnes normales -->
            <template v-else>
                <span v-if="column.type === 'badge'">
                    <Badge :color="column.badgeColor || 'primary'" size="sm">
                        {{ getCellValue(column) }}
                    </Badge>
                </span>
                <span v-else-if="column.type === 'truncate'" 
                      :title="getCellValue(column)"
                      class="block truncate max-w-xs">
                    {{ getCellValue(column) }}
                </span>
                <span v-else>
                    {{ getCellValue(column) }}
                </span>
            </template>
        </td>
    </tr>
</template>

