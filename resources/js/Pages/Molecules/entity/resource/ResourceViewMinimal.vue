<script setup>
/**
 * ResourceViewMinimal — Vue Minimal pour Resource
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Resource} resource - Instance du modèle Resource
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getEntityFieldShortLabel, getEntityFieldTooltip, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    resource: {
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
const { downloadPdf } = useDownloadPdf('resource');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('resources', 'viewAny'),
        createAny: permissions.can('resources', 'createAny'),
        updateAny: permissions.can('resources', 'updateAny'),
        deleteAny: permissions.can('resources', 'deleteAny'),
        manageAny: permissions.can('resources', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));

const stateValue = computed(() => props.resource?.state ?? props.resource?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ResourceViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['resource_type', 'level', 'rarity', 'state', 'read_level'].filter(canShowField));

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'price',
    'weight',
    'dofus_version',
    'auto_update',
].filter(canShowField));

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.resource.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const getFieldLabel = (fieldKey) => descriptors.value?.[fieldKey]?.general?.label || fieldKey;
const getFieldTooltip = (fieldKey) => getEntityFieldTooltip(descriptors.value?.[fieldKey]);

const tooltipForField = (fieldKey, cell) => {
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    if (shouldOmitLabelInMeta(fieldKey)) return String(value);
    const shortLabel = getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey));
    return `${shortLabel} : ${value}`;
};

const handleAction = async (actionKey) => {
    const resourceId = props.resource.id;
    if (!resourceId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.resources.show', { resource: resourceId }));
            emit('view', props.resource);
            break;
        case 'edit':
            router.visit(route('entities.resources.edit', { resource: resourceId }));
            emit('edit', props.resource);
            break;
        case 'delete':
            emit('delete', props.resource);
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
        
        <div class="p-3">
            <EntityViewHeader mode="minimal">
                <template #dot>
                    <EntityUsableDot :state="stateValue" />
                </template>
                <template #media>
                    <div class="w-8 h-8">
                        <CellRenderer :cell="getCell('image')" ui-color="primary" class="w-full h-full" />
                    </div>
                </template>

                <template #title>
                    <Tooltip :content="resource.name" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ resource.name }}</span>
                    </Tooltip>
                </template>

                <template #mainInfosRight>
                    <div class="flex items-center gap-2">
                        <template v-for="field in importantFields" :key="field">
                            <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
                            </Tooltip>
                        </template>
                    </div>
                </template>

                <template #actions>
                    <div v-if="showActions">
                        <EntityActions
                            entity-type="resource"
                            :entity="resource"
                            format="dropdown"
                            display="icon-only"
                            size="xs"
                            color="primary"
                            :context="{ inPanel: false }"
                            @action="handleAction"
                        />
                    </div>
                </template>
            </EntityViewHeader>

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
                        :content="getFieldTooltip(key) || tooltipForField(key, getCell(key))"
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
