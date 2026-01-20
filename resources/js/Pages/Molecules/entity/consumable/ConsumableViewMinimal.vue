<script setup>
/**
 * ConsumableViewMinimal — Vue Minimal pour Consumable
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";

const props = defineProps({
    consumable: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    displayMode: {
        type: String,
        default: 'hover',
        validator: (v) => ['compact', 'hover', 'extended'].includes(v),
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const isHovered = ref(props.displayMode === 'extended');
const canHoverExpand = computed(() => props.displayMode === 'hover');
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('consumable');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('consumable', 'viewAny'),
        createAny: permissions.can('consumable', 'createAny'),
        updateAny: permissions.can('consumable', 'updateAny'),
        deleteAny: permissions.can('consumable', 'deleteAny'),
        manageAny: permissions.can('consumable', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ConsumableViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['level', 'rarity', 'is_visible'].filter(canShowField));

const usableValue = computed(() => {
    const v = props.consumable?.usable ?? props.consumable?._data?.usable;
    return typeof v === 'boolean' ? v : null;
});

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'price',
    'dofus_version',
    'auto_update',
].filter(canShowField));

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.consumable.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const tooltipForField = (fieldKey, cell) => {
    const label = descriptors.value?.[fieldKey]?.general?.label || fieldKey;
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    return `${label} : ${value}`;
};

const handleAction = async (actionKey) => {
    const consumableId = props.consumable.id;
    if (!consumableId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.consumables.show', { consumable: consumableId }));
            emit('view', props.consumable);
            break;
        case 'edit':
            router.visit(route('entities.consumables.edit', { consumable: consumableId }));
            emit('edit', props.consumable);
            break;
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div 
        class="relative rounded-lg border border-base-300 transition-all duration-300 overflow-hidden"
        :class="{ 
            'bg-base-200 shadow-lg': isHovered,
            'bg-base-100': !isHovered
        }"
        :style="{ 
            width: isHovered ? 'auto' : '150px',
            minWidth: '150px',
            maxWidth: isHovered ? '300px' : '200px',
            height: isHovered ? 'auto' : '100px',
            minHeight: '80px'
        }"
        @mouseenter="canHoverExpand && (isHovered = true)"
        @mouseleave="canHoverExpand && (isHovered = false)">
        <div class="absolute top-1 left-1 z-20">
            <EntityUsableDot :usable="usableValue" />
        </div>
        
        <div class="p-3">
            <!-- En-tête avec nom et actions -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <Icon source="fa-solid fa-flask" :alt="consumable.name" size="sm" class="flex-shrink-0" />
                    <Tooltip :content="consumable.name" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ consumable.name }}</span>
                    </Tooltip>
                </div>
                
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <EntityActions
                        entity-type="consumable"
                        :entity="consumable"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </div>

            <!-- Infos importantes en icônes avec tooltips -->
            <div class="flex gap-2 flex-wrap">
                <template v-for="field in importantFields" :key="field">
                    <Tooltip
                        :content="tooltipForField(field, getCell(field))"
                        placement="top"
                    >
                        <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                            <Icon
                                :source="getFieldIcon(field)"
                                size="xs"
                                class="text-primary-400"
                            />
                            <span class="text-xs text-primary-300 font-medium">
                                <CellRenderer
                                    :cell="getCell(field)"
                                    ui-color="primary"
                                />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <!-- Contenu supplémentaire au hover -->
            <div 
                v-if="isHovered" 
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <div
                    v-for="key in expandedFields"
                    :key="key"
                    class="flex items-start gap-2"
                >
                    <Tooltip
                        :content="tooltipForField(key, getCell(key))"
                        placement="left"
                    >
                        <div class="flex items-start gap-2 w-full">
                            <Icon
                                :source="getFieldIcon(key)"
                                size="xs"
                                class="text-primary-400 flex-shrink-0 mt-0.5"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-primary-400">
                                    {{ descriptors?.[key]?.general?.label || key }}:
                                </div>
                                <div class="text-primary-200 truncate">
                                    <CellRenderer
                                        :cell="getCell(key)"
                                        ui-color="primary"
                                    />
                                </div>
                            </div>
                        </div>
                    </Tooltip>
                </div>
            </div>
        </div>
    </div>
</template>
