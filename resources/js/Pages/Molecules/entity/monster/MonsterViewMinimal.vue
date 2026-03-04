<script setup>
/**
 * MonsterViewMinimal — Vue Minimal pour Monster
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Monster} monster - Instance du modèle Monster
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getEntityFieldShortLabel, shouldOmitLabelInMeta, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    monster: {
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
    /** Meta du tableau (ex. characteristics.creature.byDbColumn) pour la carte caractéristiques */
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
        viewAny: permissions.can('monsters', 'viewAny'),
        createAny: permissions.can('monsters', 'createAny'),
        updateAny: permissions.can('monsters', 'updateAny'),
        deleteAny: permissions.can('monsters', 'deleteAny'),
        manageAny: permissions.can('monsters', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getMonsterFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[MonsterViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['monster_race', 'size', 'is_boss', 'dofus_version'].filter(canShowField));

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
    const excluded = new Set(['creature_name', 'image', 'creature_characteristics']);
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
        entityType: 'monster',
    }).icon;
};

const getCell = (fieldKey) => {
    return props.monster.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const getFieldLabel = (fieldKey) => {
    return resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'monster',
    }).label;
};
const getFieldTooltip = (fieldKey) => resolveEntityFieldUi({
    fieldKey,
    descriptors: descriptors.value,
    tableMeta: props.tableMeta,
    entityType: 'monster',
}).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'monster',
    }).color;
    return color ? { color } : undefined;
};

const characteristicsByDbColumn = computed(() =>
    props.tableMeta?.characteristics?.creature?.byDbColumn || {}
);
const creatureData = computed(() => {
    const m = props.monster;
    return m?.creature ?? m?.data?.creature ?? null;
});
const creatureCharacteristicsGroups = computed(() =>
    buildCreatureCharacteristicGroups(creatureData.value, characteristicsByDbColumn.value)
);
const hasCreatureCharacteristics = computed(() => !!creatureData.value);

const tooltipForField = (fieldKey, cell) => {
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    if (shouldOmitLabelInMeta(fieldKey)) return String(value);
    const shortLabel = getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey));
    return `${shortLabel} : ${value}`;
};

const handleAction = async (actionKey) => {
    const monsterId = props.monster.id;
    if (!monsterId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.monsters.show', { monster: monsterId }));
            emit('view', props.monster);
            break;
        case 'edit':
            router.visit(route('entities.monsters.edit', { monster: monsterId }));
            emit('edit', props.monster);
            break;
        case 'delete':
            emit('delete', props.monster);
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
                <template #media>
                    <Icon source="fa-solid fa-dragon" :alt="monster.creature?.name || 'Monstre'" size="sm" class="shrink-0" />
                </template>

                <template #title>
                    <Tooltip :content="monster.creature?.name || 'Monstre'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">
                            <CellRenderer :cell="getCell('creature_name')" ui-color="primary" />
                        </span>
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
                            entity-type="monster"
                            :entity="monster"
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
                class="mt-2 pt-2 border-t border-base-300 space-y-2 text-xs text-primary-300 animate-fade-in">
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
                <!-- Carte caractéristiques (mode dense) -->
                <section v-if="hasCreatureCharacteristics" class="pt-2 border-t border-base-300">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-400 mb-1.5">Caractéristiques</h4>
                    <CharacteristicsCard
                        :entity="creatureData"
                        :groups="creatureCharacteristicsGroups"
                        :dense="true"
                    />
                </section>
            </div>
        </div>
    </div>
</template>
