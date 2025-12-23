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
import EntityActionsMenu from '@/Pages/Organismes/entity/EntityActionsMenu.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';

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
    },
    /**
     * Active la sélection au clic sur la ligne (même si les checkboxes sont masquées).
     */
    enableSelection: {
        type: Boolean,
        default: false
    },
    showSelection: {
        type: Boolean,
        default: false
    },
    isSelected: {
        type: Boolean,
        default: false
    },
    showActionsMenu: {
        type: Boolean,
        default: false
    },
    disableQuickActions: {
        type: Boolean,
        default: false
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['view', 'edit', 'delete', 'select', 'deselect', 'quick-view', 'quick-edit', 'copy-link', 'download-pdf', 'refresh', 'cell-update']);

// Copie d'URL
const { copyToClipboard } = useCopyToClipboard();

/**
 * Récupère la valeur d'une cellule en gérant les instances de modèles et les objets bruts
 */
const getRawCellValue = (column) => {
    let value;
    
    // Si l'entité est une instance de modèle (BaseModel), utiliser les getters
    if (props.entity && typeof props.entity._data !== 'undefined') {
        // C'est une instance de modèle, accéder via les getters ou _data
        const getterName = column.key;
        // Essayer d'abord le getter direct (pour les propriétés comme name, id, etc.)
        if (typeof props.entity[getterName] !== 'undefined') {
            value = props.entity[getterName];
        } else {
            // Sinon, accéder via _data
            value = props.entity._data?.[column.key];
        }
    } else {
        // Objet brut, accès direct
        value = props.entity[column.key];
    }

    return value;
};

const getCellValue = (column) => {
    const value = getRawCellValue(column);
    
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

/**
 * Copie l'URL de l'entité dans le presse-papier
 */
const handleCopyLink = async () => {
    const entityId = props.entity?.id ?? props.entity?.id ?? null;
    if (!entityId) return;
    
    // Générer l'URL selon le type d'entité.
    // IMPORTANT: on passe l'ID en paramètre scalaire pour éviter les soucis de nom de paramètre (item vs items, resourceType vs resource-types, etc.)
    try {
        const routeName = `entities.${props.entityType}.show`;
        const url = `${window.location.origin}${route(routeName, entityId)}`;
        await copyToClipboard(url, `Lien de l'entité copié !`);
    } catch (e) {
        // Route inexistante (ex: entité sans page show) => on ignore sans casser l'UI.
        console.warn('Impossible de copier le lien (route show manquante ?)', e);
    }
};

const handleSelectionChange = (event) => {
    if (event.target.checked) {
        emit('select', props.entity);
    } else {
        emit('deselect', props.entity);
    }
};

const isInteractiveTarget = (event) => {
    const el = event?.target;
    if (!el || typeof el.closest !== 'function') return false;
    return Boolean(el.closest('a,button,input,select,textarea,[role="button"],[data-no-row-select]'));
};

const handleRowClick = (event) => {
    if (!props.enableSelection) return;
    if (isInteractiveTarget(event)) return;

    // Toggle sélection
    if (props.isSelected) {
        emit('deselect', props.entity);
    } else {
        emit('select', props.entity);
    }
};

const handleRowDoubleClick = (event) => {
    if (isInteractiveTarget(event)) return;

    // Ouvre "le modal" (côté parent: quick-edit/quick-view)
    // Par défaut: on privilégie quick-edit (souvent modal d'édition), sinon quick-view.
    emit('quick-edit', props.entity);
};

const handleInlineSelectChange = (column, event) => {
    const value = event?.target?.value ?? null;
    emit('cell-update', {
        entity: props.entity,
        key: column.key,
        value,
    });
};

const handleQuickView = () => {
    emit('quick-view', props.entity);
};

const handleQuickEdit = () => {
    emit('quick-edit', props.entity);
};

const handleRefresh = () => {
    emit('refresh', props.entity);
};

const handleDownloadPdf = () => {
    emit('download-pdf', props.entity);
};

const getEntityRoute = () => {
    return `entities.${props.entityType}.show`;
};

const getEntityRouteParams = () => {
    // Gérer les instances de modèles et les objets bruts
    const entityId = props.entity?.id ?? props.entity?.id ?? null;
    return { [props.entityType]: entityId };
};

/**
 * Récupère les permissions en gérant les modèles et objets bruts
 * Les modèles BaseModel ont des getters canView, canUpdate, canDelete
 */
const getCanView = () => {
    if (props.entity && typeof props.entity.canView !== 'undefined') {
        // C'est un getter (modèle) ou une propriété
        return props.entity.canView ?? false;
    }
    return props.entity?.can?.view ?? false;
};

const getCanUpdate = () => {
    if (props.entity && typeof props.entity.canUpdate !== 'undefined') {
        // C'est un getter (modèle) ou une propriété
        return props.entity.canUpdate ?? false;
    }
    return props.entity?.can?.update ?? false;
};

const getCanDelete = () => {
    if (props.entity && typeof props.entity.canDelete !== 'undefined') {
        // C'est un getter (modèle) ou une propriété
        return props.entity.canDelete ?? false;
    }
    return props.entity?.can?.delete ?? false;
};
</script>

<template>
    <tr
        class="hover:bg-base-200 transition-colors"
        :class="{ 'bg-primary/10': isSelected }"
        @click="handleRowClick"
        @dblclick="handleRowDoubleClick"
    >
        <!-- Checkbox de sélection -->
        <td v-if="showSelection" class="w-12">
            <input
                type="checkbox"
                :checked="isSelected"
                @change="handleSelectionChange"
                @click.stop
                class="checkbox checkbox-sm"
            />
        </td>
        <td v-for="column in columns" :key="column.key" 
            :class="{ 'text-center font-semibold': column.isMain }">
            <!-- Colonne principale (nom avec lien) -->
            <template v-if="column.isMain">
                <button 
                    @click.stop="handleView"
                    @dblclick.stop
                    class="link link-primary link-hover font-semibold text-left">
                    {{ getCellValue(column) }}
                </button>
            </template>
            
            <!-- Colonnes d'actions -->
            <template v-else-if="column.key === 'actions'">
                <div class="flex gap-2 justify-end">
                    <!-- Gérer les permissions pour les modèles et objets bruts -->
                    <Tooltip v-if="getCanView()" content="Voir" placement="top">
                        <Btn size="sm" variant="ghost" @click.stop="handleView" @dblclick.stop>
                            <i class="fa-solid fa-eye"></i>
                        </Btn>
                    </Tooltip>
                    <Tooltip v-if="getCanUpdate()" content="Éditer" placement="top">
                        <Btn size="sm" variant="ghost" @click.stop="handleEdit" @dblclick.stop>
                            <i class="fa-solid fa-pen"></i>
                        </Btn>
                    </Tooltip>
                    <Tooltip content="Copier le lien" placement="top">
                        <Btn size="sm" variant="ghost" @click.stop="handleCopyLink" @dblclick.stop>
                            <i class="fa-solid fa-link"></i>
                        </Btn>
                    </Tooltip>
                    <Tooltip v-if="getCanDelete()" content="Supprimer" placement="top">
                        <Btn size="sm" variant="ghost" color="error" @click.stop="handleDelete" @dblclick.stop>
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
                <span v-else-if="column.type === 'inline-select'">
                    <select
                        class="select select-bordered select-sm"
                        :value="String(getRawCellValue(column) ?? '')"
                        :disabled="column.disabled === true"
                        @change="(e) => handleInlineSelectChange(column, e)"
                        @click.stop
                        @dblclick.stop
                        title="Modifier"
                    >
                        <option
                            v-for="opt in (column.options || [])"
                            :key="String(opt.value)"
                            :value="String(opt.value)"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                </span>
                <span v-else-if="column.type === 'image'">
                    <div class="flex items-center justify-center">
                        <img
                            v-if="getRawCellValue(column)"
                            :src="getRawCellValue(column)"
                            :alt="`${entityType} #${props.entity?.id ?? ''}`"
                            class="h-8 w-8 rounded object-contain bg-base-200"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="h-8 w-8 rounded bg-base-200 flex items-center justify-center text-base-content/40"
                            title="Aucune image"
                        >
                            <i class="fa-regular fa-image"></i>
                        </div>
                    </div>
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

