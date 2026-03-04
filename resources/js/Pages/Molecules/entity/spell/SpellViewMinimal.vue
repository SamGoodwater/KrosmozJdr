<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Spell} spell - Instance du modèle Spell
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
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import { getEntityFieldShortLabel, shouldOmitLabelInMeta, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    spell: {
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
        viewAny: permissions.can('spells', 'viewAny'),
        createAny: permissions.can('spells', 'createAny'),
        updateAny: permissions.can('spells', 'updateAny'),
        deleteAny: permissions.can('spells', 'deleteAny'),
        manageAny: permissions.can('spells', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const stateValue = computed(() => props.spell?.state ?? props.spell?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[SpellViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['level', 'pa', 'po', 'element', 'category', 'state', 'read_level'].filter(canShowField));

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
        entityType: 'spell',
    }).icon;
};

const getCell = (fieldKey) => {
    return props.spell.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const getFieldLabel = (fieldKey) => resolveEntityFieldUi({
    fieldKey,
    descriptors: descriptors.value,
    tableMeta: props.tableMeta,
    entityType: 'spell',
}).label;

const getFieldIconStyle = (fieldKey) => {
    const color = resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'spell',
    }).color;
    return color ? { color } : undefined;
};

const tooltipForField = (fieldKey, cell) => {
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    const label = getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey));
    if (shouldOmitLabelInMeta(fieldKey)) return String(value);
    return `${label} : ${value}`;
};

const handleAction = async (actionKey) => {
    const spellId = props.spell.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', props.spell);
            break;
        case 'edit':
            router.visit(route('entities.spells.edit', { spell: spellId }));
            emit('edit', props.spell);
            break;
        case 'delete':
            emit('delete', props.spell);
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
                    <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="sm" class="flex-shrink-0" />
                </template>

                <template #title>
                    <Tooltip :content="spell.name" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ spell.name }}</span>
                    </Tooltip>
                </template>

                <template #actions>
                    <div v-if="showActions && isHovered">
                        <EntityActions
                            entity-type="spell"
                            :entity="spell"
                            format="buttons"
                            display="icon-only"
                            size="xs"
                            color="primary"
                            :context="{ inPanel: false }"
                            @action="handleAction"
                        />
                    </div>
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
                        :content="tooltipForField(key, getCell(key))"
                        placement="left"
                    >
                        <div class="flex items-start gap-2 w-full">
                            <Icon
                                :source="getFieldIcon(key)"
                                size="xs"
                                class="text-primary-400 flex-shrink-0 mt-0.5"
                                :style="getFieldIconStyle(key)"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-primary-400">
                                    {{ getFieldLabel(key) }}:
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
