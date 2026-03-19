<script setup>
/**
 * ConsumableViewLarge — Vue Large pour Consumable
 *
 * @description
 * Vue complète d'un consommable avec toutes les informations affichées.
 * Layout aligné sur Resource : image + 3 lignes (nom+niveau+type / rareté+prix / description),
 * puis effet, ingrédients, bloc admin (write permission).
 *
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import EntityUsableDot from '@/Pages/Atoms/data-display/EntityUsableDot.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
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
const { downloadPdf } = useDownloadPdf('consumable');
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

const ingredients = computed(() => {
    const raw = props.consumable?.resources ?? props.consumable?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

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
    return color ? { color } : {};
};
const getFieldUnit = (fieldKey) => getFieldUi(fieldKey).characteristic?.unit ?? '';

const getCell = (fieldKey) =>
    props.consumable.toCell(fieldKey, { size: 'lg', context: 'extended' });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        consumable_type: 'info',
        level: 'warning',
        rarity: 'auto',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const levelDisplay = computed(() => {
    const v = props.consumable?.level ?? props.consumable?._data?.level;
    return v !== null && v !== undefined && v !== '' ? String(v) : '-';
});
const typeDisplay = computed(() => {
    const ct = props.consumable?.consumableType ?? props.consumable?._data?.consumableType;
    return ct?.name ?? ct?.label ?? '-';
});
const rarityDisplay = computed(() => {
    const r = props.consumable?.rarity ?? props.consumable?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.label ?? '-';
});
const rarityBadgeColor = computed(() => {
    const r = props.consumable?.rarity ?? props.consumable?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.color ?? 'neutral';
});

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
        case 'download-pdf':
            await downloadPdf(consumableId);
            emit('download-pdf', props.consumable);
            break;
        case 'refresh':
            router.reload({ only: ['consumable'] });
            emit('refresh', props.consumable);
            break;
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <EntityViewHeader mode="large">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div class="absolute top-2 left-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <EntityUsableDot :state="stateValue" />
                    </div>
                    <ImageViewer
                        v-if="consumable.image"
                        :src="consumable.image"
                        :alt="consumable.name || 'Consommable'"
                        :caption="consumable.name || ''"
                        preload="hover"
                        :image-props="{ size: 'xl', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-flask" :alt="consumable.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ consumable.name }}</h2>
                        <template v-if="canShowField('level')">
                            <Badge
                                :color="getBadgeColor('level')"
                                :auto-label="levelDisplay"
                                auto-scheme="level"
                                auto-tone="mid"
                                size="sm"
                            >
                                Nvx {{ levelDisplay }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('consumable_type')">
                            <Tooltip :content="getFieldTooltip('consumable_type')" placement="top">
                                <Badge
                                    color="auto"
                                    :auto-label="typeDisplay"
                                    auto-scheme="mixed"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    {{ typeDisplay }}
                                </Badge>
                            </Tooltip>
                        </template>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <template v-if="canShowField('rarity')">
                            <Badge
                                :color="rarityBadgeColor"
                                :auto-label="String(consumable.rarity ?? 0)"
                                auto-scheme="rarity"
                                auto-tone="mid"
                                size="sm"
                            >
                                {{ rarityDisplay }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('price')">
                            <Tooltip :content="getFieldTooltip('price')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('price')">
                                    <Icon :source="getFieldIcon('price')" :alt="getFieldLabel('price')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('price') }}</span><span> {{ consumable.price ?? '-' }}{{ getFieldUnit('price') ? ` ${getFieldUnit('price')}` : '' }}</span>
                                </span>
                            </Tooltip>
                        </template>
                    </div>
                </div>
            </template>

            <template #mainInfos />
            <template #subtitle>
                <p v-if="consumable.description" class="text-primary-300 mt-2 break-words">{{ consumable.description }}</p>
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
                                <button
                                    type="button"
                                    class="btn btn-sm btn-ghost gap-1.5 min-h-0 h-8 px-2 rounded-md hover:bg-base-300/50"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                >
                                    <Badge :color="stateBadgeColor" size="xs" variant="soft">
                                        {{ stateLabel }}
                                    </Badge>
                                    <Icon source="fa-solid fa-chevron-down" size="xs" class="opacity-70" aria-hidden="true" />
                                </button>
                            </template>
                            <template #content>
                                <ul class="dropdown-content dropdown-content-glass dropdown-content-sm py-1 min-w-[140px]" role="listbox">
                                    <li
                                        v-for="opt in stateOptions"
                                        :key="opt.value"
                                        role="option"
                                        :aria-selected="stateValue === opt.value"
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
                                                'bg-base-300': !['raw','draft','playable','archived'].includes(opt.value),
                                            }"
                                            aria-hidden="true"
                                        />
                                        {{ opt.label }}
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                        <Tooltip v-else :content="getFieldTooltip('state')" placement="top">
                            <Badge :color="stateBadgeColor" size="xs" variant="soft">
                                {{ stateLabel }}
                            </Badge>
                        </Tooltip>
                    </template>
                    <EntityActions
                        v-if="showActions"
                        entity-type="consumable"
                        :entity="consumable"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false, inPage: true }"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <!-- Effet -->
        <div v-if="canShowField('effect') && (consumable.effect || consumable._data?.effect)" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Effet</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <CellRenderer :cell="getCell('effect')" ui-color="primary" />
            </div>
        </div>

        <!-- Ingrédients -->
        <div v-if="ingredients.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Ingrédients</h3>
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
