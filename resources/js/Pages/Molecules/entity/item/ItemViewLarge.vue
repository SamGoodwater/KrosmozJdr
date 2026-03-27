<script setup>
/**
 * ItemViewLarge — Vue Large pour Item (équipement)
 *
 * @description
 * Vue complète d'un équipement avec toutes les informations affichées.
 * Layout aligné sur Resource : image + 3 lignes (nom+niveau+type / rareté+poids+prix / description),
 * puis effet, bonus, ingrédients, bloc admin (write permission).
 *
 * @props {Item} item - Instance du modèle Item
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
import { getItemFieldDescriptors } from '@/Entities/item/item-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig, getRoleConfig, getEntityStateOptions } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';

const props = defineProps({
    item: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('item');
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('items', 'viewAny'),
        createAny: permissions.can('items', 'createAny'),
        updateAny: permissions.can('items', 'updateAny'),
        deleteAny: permissions.can('items', 'deleteAny'),
        manageAny: permissions.can('items', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getItemFieldDescriptors(ctx.value));
const stateValue = computed(() => props.item?.state ?? props.item?._data?.state ?? null);
const autoUpdateValue = computed(() => {
    const v = props.item?.auto_update ?? props.item?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const ingredients = computed(() => {
    const raw = props.item?.resources ?? props.item?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

const userCanEdit = computed(() => ctx.value.capabilities.updateAny ?? props.item?.can?.update ?? false);

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
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'item' });
const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;
const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;
const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon || 'fa-solid fa-info-circle';
const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : {};
};
const getFieldUnit = (fieldKey) => getFieldUi(fieldKey).characteristic?.unit ?? '';

const getCell = (fieldKey) =>
    props.item.toCell(fieldKey, { size: 'lg', context: 'extended' });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        item_type: 'info',
        level: 'warning',
        rarity: 'auto',
        read_level: 'primary',
        write_level: 'secondary',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const levelDisplay = computed(() => {
    const v = props.item?.level ?? props.item?._data?.level;
    return v !== null && v !== undefined && v !== '' ? String(v) : '-';
});
const typeDisplay = computed(() => {
    const it = props.item?.itemType ?? props.item?._data?.itemType;
    return it?.name ?? it?.label ?? '-';
});
const rarityDisplay = computed(() => {
    const r = props.item?.rarity ?? props.item?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.label ?? '-';
});
const rarityBadgeColor = computed(() => {
    const r = props.item?.rarity ?? props.item?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.color ?? 'neutral';
});
const readLevelLabel = computed(() => {
    const v = props.item?.read_level ?? props.item?._data?.read_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});
const writeLevelLabel = computed(() => {
    const v = props.item?.write_level ?? props.item?._data?.write_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});

const stateOptions = computed(() => getEntityStateOptions());
const stateColorMap = { raw: 'error', draft: 'warning', playable: 'success', archived: 'info' };
const stateBadgeColor = computed(() => stateColorMap[stateValue.value] ?? 'neutral');
const stateLabel = computed(() => {
    const opt = stateOptions.value.find((o) => o.value === stateValue.value);
    return opt?.label ?? '-';
});

const handleStateChange = (newState) => {
    if (!props.item?.id || !userCanEdit.value) return;
    router.patch(route('entities.items.update', { item: props.item.id }), { state: newState }, {
        preserveScroll: true,
        preserveState: true,
    });
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
        case 'quick-edit':
            emit('quick-edit', props.item);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('item');
            const url = resolveEntityRouteUrl('item', 'show', itemId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.item);
            break;
        }
        case 'download-pdf':
            await downloadPdf(itemId);
            emit('download-pdf', props.item);
            break;
        case 'refresh':
            router.reload({ only: ['item'] });
            emit('refresh', props.item);
            break;
        case 'delete':
            emit('delete', props.item);
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
                        v-if="item.image"
                        :src="item.image"
                        :alt="item.name || 'Équipement'"
                        :caption="item.name || ''"
                        preload="hover"
                        :image-props="{ size: 'xl', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-sword" :alt="item.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ item.name }}</h2>
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
                        <template v-if="canShowField('item_type')">
                            <Tooltip :content="getFieldTooltip('item_type')" placement="top">
                                <Badge
                                    color="auto"
                                    :auto-label="typeDisplay"
                                    auto-scheme="labelHash"
                                    auto-tone="light"
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
                                :auto-label="String(item.rarity ?? 0)"
                                auto-scheme="rarity"
                                auto-tone="mid"
                                size="sm"
                            >
                                {{ rarityDisplay }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('weight')">
                            <Tooltip :content="getFieldTooltip('weight')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('weight')">
                                    <Icon :source="getFieldIcon('weight')" :alt="getFieldLabel('weight')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('weight') }}</span><span> {{ item.weight ?? '-' }}{{ getFieldUnit('weight') ? ` ${getFieldUnit('weight')}` : '' }}</span>
                                </span>
                            </Tooltip>
                        </template>
                        <template v-if="canShowField('price')">
                            <Tooltip :content="getFieldTooltip('price')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('price')">
                                    <Icon :source="getFieldIcon('price')" :alt="getFieldLabel('price')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('price') }}</span><span> {{ item.price ?? '-' }}{{ getFieldUnit('price') ? ` ${getFieldUnit('price')}` : '' }}</span>
                                </span>
                            </Tooltip>
                        </template>
                    </div>
                </div>
            </template>

            <template #mainInfos />
            <template #subtitle>
                <p v-if="item.description" class="text-primary-300 mt-2 break-words">{{ item.description }}</p>
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
                        entity-type="item"
                        :entity="item"
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
        <div v-if="canShowField('effect') && (item.effect || item._data?.effect)" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Effet</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <CellRenderer :cell="getCell('effect')" ui-color="primary" />
            </div>
        </div>

        <!-- Bonus -->
        <div v-if="canShowField('bonus') && (item.bonus || item._data?.bonus)" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Bonus</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <CellRenderer :cell="getCell('bonus')" ui-color="primary" />
            </div>
        </div>

        <!-- Ingrédients -->
        <div v-if="ingredients.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Ingrédients</h3>
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>

        <!-- Bloc admin (write permission) -->
        <section
            v-if="userCanEdit && (canShowField('read_level') || canShowField('write_level') || canShowField('dofus_version') || canShowField('dofusdb_id') || canShowField('auto_update') || canShowField('created_by') || canShowField('created_at') || canShowField('updated_at'))"
            role="region"
            aria-label="Administration"
            class="rounded-box overflow-hidden border border-base-300 bg-base-200/50 border-glass-primary-md"
        >
            <div class="px-5 py-4 flex items-center gap-3 border-b border-base-300/80 bd-glass-xs">
                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-lg bg-primary-500/20 text-primary-400">
                    <Icon source="fa-solid fa-shield-halved" size="sm" aria-hidden="true" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-primary-200">Administration</h3>
                    <p class="text-xs text-base-content/60 mt-0.5">Paramètres techniques</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div v-if="canShowField('read_level') || canShowField('write_level')" class="space-y-3">
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60">Accès</h4>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-if="canShowField('read_level')"
                            :color="getBadgeColor('read_level')"
                            :auto-label="String(item.read_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-eye" size="xs" class="mr-1" />
                            {{ readLevelLabel }}
                        </Badge>
                        <Badge
                            v-if="canShowField('write_level')"
                            :color="getBadgeColor('write_level')"
                            :auto-label="String(item.write_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-pen-to-square" size="xs" class="mr-1" />
                            {{ writeLevelLabel }}
                        </Badge>
                    </div>
                </div>
                <div v-if="canShowField('dofus_version') || canShowField('dofusdb_id') || canShowField('auto_update')" class="space-y-3 pt-3 border-t border-base-300/60">
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60">Source</h4>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                        <div v-if="canShowField('dofus_version')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-gamepad" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">Dofus</span>
                            <span class="font-mono text-xs text-base-content">{{ item.dofus_version ?? '—' }}</span>
                        </div>
                        <div v-if="canShowField('dofusdb_id')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-link" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">DofusDB ID</span>
                            <span class="font-mono text-xs text-base-content">{{ item.dofusdb_id ?? '—' }}</span>
                        </div>
                        <div v-if="canShowField('auto_update')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-arrows-rotate" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">Auto-update</span>
                            <Icon
                                v-if="autoUpdateValue !== null"
                                :source="autoUpdateValue ? 'fa-solid fa-check-circle' : 'fa-solid fa-times-circle'"
                                size="sm"
                                :class="autoUpdateValue ? 'text-success' : 'text-error'"
                                :alt="autoUpdateValue ? 'Oui' : 'Non'"
                            />
                            <span v-else class="text-base-content/60">—</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
