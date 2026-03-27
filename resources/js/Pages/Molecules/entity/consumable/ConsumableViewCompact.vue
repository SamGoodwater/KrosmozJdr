<script setup>
/**
 * ConsumableViewCompact — Vue Compact pour Consumable
 *
 * @description
 * Vue réduite d'un consommable avec informations essentielles.
 * Layout aligné sur ResourceViewCompact : EntityViewHeader mode compact,
 * badges niveau+type, rareté+prix, description, ingrédients.
 *
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import EntityUsableDot from '@/Pages/Atoms/data-display/EntityUsableDot.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getConsumableFieldDescriptors } from '@/Entities/consumable/consumable-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig, getEntityStateOptions } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';

const props = defineProps({
    consumable: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('consumables', 'viewAny'),
        createAny: permissions.can('consumables', 'createAny'),
        updateAny: permissions.can('consumables', 'updateAny'),
        deleteAny: permissions.can('consumables', 'deleteAny'),
        manageAny: permissions.can('consumables', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));
const stateValue = computed(() => props.consumable?.state ?? props.consumable?._data?.state ?? null);
const userCanEdit = computed(() => ctx.value.capabilities.updateAny ?? props.consumable?.can?.update ?? false);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'consumable' });
const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;
const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;
const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon || 'fa-solid fa-info-circle';
const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};
const getFieldUnit = (fieldKey) => getFieldUi(fieldKey).characteristic?.unit ?? '';

const getCell = (fieldKey) =>
    props.consumable.toCell(fieldKey, { size: 'md', context: 'compact' });

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v),
        params: cell?.params || {},
    };
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        consumable_type: 'info',
        level: 'warning',
        rarity: 'auto',
        price: 'success',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
    });
    return { autoLabel, autoScheme, autoTone };
};

const stateOptions = computed(() => getEntityStateOptions());
const stateColorMap = { raw: 'error', draft: 'warning', playable: 'success', archived: 'info' };
const stateBadgeColor = computed(() => stateColorMap[stateValue.value] ?? 'neutral');
const stateLabel = computed(() => {
    const opt = stateOptions.value.find((o) => o.value === stateValue.value);
    return opt?.label ?? '-';
});

const handleStateChange = (newState) => {
    if (!props.consumable?.id || !userCanEdit.value) return;
    router.patch(route('entities.consumables.update', { consumable: props.consumable.id }), { state: newState }, {
        preserveScroll: true,
        preserveState: true,
    });
};

const ingredients = computed(() => {
    const raw = props.consumable?.resources ?? props.consumable?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

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
        case 'quick-edit':
            emit('quick-edit', props.consumable);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', consumableId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.consumable);
            break;
        }
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader mode="compact">
            <template #media>
                <div class="group relative w-16 h-16">
                    <div class="absolute top-1 left-1 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <EntityUsableDot :state="stateValue" />
                    </div>
                    <ImageViewer
                        v-if="consumable.image"
                        :src="consumable.image"
                        :alt="consumable.name || 'Consommable'"
                        :caption="consumable.name || ''"
                        preload="hover"
                        :image-props="{ size: 'sm', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-flask" :alt="consumable.name" size="md" />
                    </div>
                </div>
            </template>

            <template #title>
                <h3 class="text-lg font-semibold text-primary-100 truncate leading-tight">{{ consumable.name }}</h3>
            </template>

            <template #actions>
                <div class="flex items-center gap-2">
                    <template v-if="canShowField('state')">
                        <Dropdown
                            v-if="userCanEdit"
                            placement="bottom-end"
                            :close-on-content-click="true"
                            aria-label="Changer l'état"
                        >
                            <template #trigger>
                                <Badge :color="stateBadgeColor" size="xs" variant="soft" class="cursor-pointer">
                                    {{ stateLabel }}
                                </Badge>
                            </template>
                            <template #content>
                                <ul class="dropdown-content dropdown-content-glass dropdown-content-sm py-1 min-w-[140px]" role="listbox">
                                    <li
                                        v-for="opt in stateOptions"
                                        :key="opt.value"
                                        role="option"
                                        class="cursor-pointer px-3 py-2 text-sm hover:bg-base-300/50 flex items-center gap-2"
                                        :class="{ 'bg-base-300/30': stateValue === opt.value }"
                                        @click="handleStateChange(opt.value)"
                                    >
                                        <span
                                            class="w-2 h-2 rounded-full shrink-0"
                                            :class="{
                                                'bg-error': opt.value === 'raw',
                                                'bg-warning': opt.value === 'draft',
                                                'bg-success': opt.value === 'playable',
                                                'bg-info': opt.value === 'archived',
                                            }"
                                        />
                                        {{ opt.label }}
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                        <Badge v-else :color="stateBadgeColor" size="xs" variant="soft">
                            {{ stateLabel }}
                        </Badge>
                    </template>
                    <EntityActions
                        v-if="showActions"
                        entity-type="consumable"
                        :entity="consumable"
                        format="dropdown"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </template>

            <template #mainInfos>
                <div class="space-y-1 mt-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <template v-if="canShowField('level')">
                            <Badge
                                :color="getBadgeColor('level')"
                                :auto-label="String(consumable.level ?? '')"
                                auto-scheme="level"
                                auto-tone="mid"
                                size="xs"
                            >
                                Nvx {{ consumable.level ?? '-' }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('consumable_type')">
                            <Badge
                                color="auto"
                                :auto-label="consumable.consumableType?.name ?? consumable.consumableType?.label ?? '-'"
                                auto-scheme="labelHash"
                                auto-tone="light"
                                size="xs"
                            >
                                {{ consumable.consumableType?.name ?? consumable.consumableType?.label ?? '-' }}
                            </Badge>
                        </template>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                        <template v-if="canShowField('rarity')">
                            <Badge
                                :color="(getRarityConfig(consumable.rarity ?? 0))?.color ?? 'neutral'"
                                size="xs"
                            >
                                {{ (getRarityConfig(consumable.rarity ?? 0))?.label ?? '-' }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('price')">
                            <Tooltip :content="getFieldTooltip('price')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('price')">
                                    <Icon :source="getFieldIcon('price')" :alt="getFieldLabel('price')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('price') }}</span><span> {{ consumable.price ?? '-' }}{{ getFieldUnit('price') ? ' ' + getFieldUnit('price') : '' }}</span>
                                </span>
                            </Tooltip>
                        </template>
                    </div>
                    <p v-if="consumable.description" class="text-primary-300 text-xs mt-1 line-clamp-2">{{ consumable.description }}</p>
                </div>
            </template>
        </EntityViewHeader>

        <ResourceIngredientsList
            v-if="ingredients.length > 0"
            :ingredients="ingredients"
            class="pt-3 border-t border-base-300"
        />
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
