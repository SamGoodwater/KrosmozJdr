<script setup>
/**
 * ItemViewMinimal — Vue Minimal pour Item
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Item} item - Instance du modèle Item
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
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { getEntityFieldShortLabel, shouldOmitLabelInMeta, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    item: {
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
    },
    tableMeta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const isHovered = ref(props.displayMode === 'extended');
const canHoverExpand = computed(() => props.displayMode === 'hover');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('items', 'viewAny'),
        createAny: permissions.can('items', 'createAny'),
        updateAny: permissions.can('items', 'updateAny'),
        deleteAny: permissions.can('items', 'deleteAny'),
        manageAny: permissions.can('items', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getItemFieldDescriptors(ctx.value));

const stateValue = computed(() => props.item?.state ?? props.item?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ItemViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['item_type', 'level', 'rarity', 'state', 'read_level'].filter(canShowField));

const technicalFieldsOrder = ['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at'];
const technicalFieldRank = new Map(technicalFieldsOrder.map((key, index) => [key, index]));
const sortExtendedFields = (fields) => {
    return [...fields].sort((a, b) => {
        const rankA = technicalFieldRank.has(a) ? technicalFieldRank.get(a) : -1;
        const rankB = technicalFieldRank.has(b) ? technicalFieldRank.get(b) : -1;

        if (rankA === -1 && rankB === -1) return 0;
        if (rankA === -1) return -1;
        if (rankB === -1) return 1;
        return rankA - rankB;
    });
};

// En mode étendu, afficher toutes les propriétés visibles non principales.
const expandedFields = computed(() => {
    const excluded = new Set(['name', 'image']);
    const fields = Object.keys(descriptors.value || {}).filter((key) => {
        return canShowField(key) && !importantFields.value.includes(key) && !excluded.has(key);
    });
    return sortExtendedFields(fields);
});

const getFieldIcon = (fieldKey) => {
    return resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'item',
    }).icon;
};

const getCell = (fieldKey) => {
    return props.item.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const getFieldLabel = (fieldKey) => resolveEntityFieldUi({
    fieldKey,
    descriptors: descriptors.value,
    tableMeta: props.tableMeta,
    entityType: 'item',
}).label;
const getFieldTooltip = (fieldKey) => resolveEntityFieldUi({
    fieldKey,
    descriptors: descriptors.value,
    tableMeta: props.tableMeta,
    entityType: 'item',
}).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'item',
    }).color;
    return color ? { color } : undefined;
};

const tooltipForField = (fieldKey, cell) => {
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    if (shouldOmitLabelInMeta(fieldKey)) return String(value);
    const shortLabel = getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey));
    return `${shortLabel} : ${value}`;
};

const handleAction = async (actionKey) => {
    const itemId = props.item.id;
    if (!itemId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.items.show', { item: itemId }));
            emit('view', props.item);
            break;
        case 'edit':
            router.visit(route('entities.items.edit', { item: itemId }));
            emit('edit', props.item);
            break;
        case 'delete':
            emit('delete', props.item);
            break;
    }
};
</script>

<template>
    <div
        data-cy="entity-minimal-card"
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
            minHeight: '80px',
            borderRadius: 'var(--radius-box, 0.1rem)'
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
                    <Tooltip :content="item.name" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ item.name }}</span>
                    </Tooltip>
                </template>

                <template #mainInfosRight>
                    <div class="flex items-center gap-2">
                        <template v-for="field in importantFields" :key="field">
                            <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" :style="getFieldIconStyle(field)" />
                            </Tooltip>
                        </template>
                    </div>
                </template>

                <template #actions>
                    <div v-if="showActions">
                        <EntityActions
                            entity-type="item"
                            :entity="item"
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
                data-cy="entity-minimal-expanded"
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <div
                    v-for="key in expandedFields"
                    :key="key"
                    :data-field-key="key"
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
                                class="text-primary-400 shrink-0 mt-0.5"
                                :style="getFieldIconStyle(key)"
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
