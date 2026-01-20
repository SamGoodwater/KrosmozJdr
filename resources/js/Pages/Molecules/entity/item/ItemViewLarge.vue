<script setup>
/**
 * ItemViewLarge — Vue Large pour Item
 * 
 * @description
 * Vue complète d'un item avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Item} item - Instance du modèle Item
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { getEntityFieldTooltip, getEntityFieldShortLabel, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('item');
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

const usableValue = computed(() => {
    const v = props.item?.usable ?? props.item?._data?.usable;
    return typeof v === 'boolean' ? v : null;
});

const autoUpdateValue = computed(() => {
    const v = props.item?.auto_update ?? props.item?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ItemViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ([
    'item_type',
    'level',
].filter(canShowField)));

const metaFields = computed(() => ([
    'rarity',
    'price',
    'weight',
].filter(canShowField).filter((k) => !headlineFields.value.includes(k))));

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ([
    'dofus_version',
    'auto_update',
    'is_visible',
].filter(canShowField)));

const technicalFields = computed(() => ([
    'dofusdb_id',
    'official_id',
    'created_by',
    'created_at',
    'updated_at',
].filter(canShowField)));

const bodyFields = computed(() => ([
    'effect',
    'bonus',
    'recipe',
].filter(canShowField)));

const getBadgeColor = (fieldKey) => {
    const cell = getCell(fieldKey);
    if (cell?.params?.color) return cell.params.color;
    const colorMap = {
        item_type: 'info',
        level: 'warning',
        price: 'success',
        rarity: 'auto',
        weight: 'secondary',
        dofus_version: 'secondary',
        is_visible: 'primary',
        auto_update: 'warning',
        dofusdb_id: 'neutral',
        official_id: 'neutral',
        created_by: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
    };
    return colorMap[fieldKey] || 'neutral';
};

const getBadgeAutoParams = (fieldKey) => {
    const cell = getCell(fieldKey);
    if (fieldKey === 'rarity' && cell?.value) {
        return { autoLabel: String(cell.value), autoScheme: 'rarity', autoTone: 'mid' };
    }
    if (fieldKey === 'level' && cell?.value) {
        return { autoLabel: String(cell.value), autoScheme: 'level', autoTone: 'mid' };
    }
    return {};
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v),
        params: cell?.params || {},
    };
};

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldTooltip = (fieldKey) => getEntityFieldTooltip(descriptors.value?.[fieldKey]);

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.item.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'item copié !");
            }
            emit('copy-link', props.item);
            break;
        }
        case 'download-pdf':
            await downloadPdf(itemId);
            emit('download-pdf', props.item);
            break;
        case 'refresh':
            router.reload({ only: ['items'] });
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
                <div class="relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div class="peer absolute inset-x-0 bottom-0 h-[80%] z-10"></div>

                    <div class="absolute top-2 left-2 z-20 transition-opacity duration-150 peer-hover:opacity-0">
                        <EntityUsableDot :usable="usableValue" />
                    </div>

                    <div class="absolute top-2 right-2 z-20 transition-opacity duration-150 peer-hover:opacity-0">
                        <Badge
                            :color="getBadgeColor('level')"
                            :auto-label="getBadgeAutoParams('level').autoLabel"
                            :auto-scheme="getBadgeAutoParams('level').autoScheme"
                            :auto-tone="getBadgeAutoParams('level').autoTone"
                            size="sm"
                        >
                            <CellRenderer :cell="asTextCell(getCell('level'))" ui-color="primary" />
                        </Badge>
                    </div>

                    <Image
                        v-if="item.image"
                        :source="item.image"
                        :alt="item.name || 'Item'"
                        size="xl"
                        rounded="lg"
                        fit="cover"
                        class="w-full h-full peer-hover:hidden pointer-events-none"
                    />
                    <Image
                        v-if="item.image"
                        :source="item.image"
                        :alt="item.name || 'Item'"
                        size="xl"
                        rounded="lg"
                        fit="contain"
                        class="w-full h-full hidden peer-hover:block pointer-events-none"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 rounded-lg">
                        <Icon source="fa-solid fa-box" :alt="item.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">{{ item.name }}</h2>
            </template>

            <template #mainInfos>
                <!-- Metas: icône + label inline, valeur seule en badge -->
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <template v-for="fieldKey in displayMetaFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex items-start justify-between gap-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                                    <span
                                        v-if="!shouldOmitLabelInMeta(fieldKey)"
                                        class="text-xs uppercase font-semibold text-primary-300 truncate"
                                    >
                                        {{ getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey)) }}
                                    </span>
                                </div>

                                <Badge
                                    :color="getBadgeColor(fieldKey)"
                                    :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                    :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                    :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                    size="sm"
                                    :truncate="false"
                                    class="max-w-[18rem] whitespace-normal break-words"
                                >
                                    <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                </Badge>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </template>

            <template #subtitle>
                <p v-if="item.description" class="text-primary-300 mt-2 break-words">{{ item.description }}</p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
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

        <!-- Infos techniques (texte) + UserCanEdit (paramètres) -->
        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                            <span class="uppercase tracking-wide text-primary-300">
                                {{ getFieldLabel(fieldKey) }}
                            </span>
                            <span class="min-w-0 break-words">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                                <span class="uppercase tracking-wide text-primary-300">
                                    {{ getFieldLabel(fieldKey) }}
                                </span>
                                <span class="min-w-0 break-words">
                                    <template v-if="fieldKey === 'auto_update'">
                                        <Icon
                                            v-if="autoUpdateValue !== null"
                                            :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                                            :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                            size="sm"
                                            :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                                        />
                                        <span v-else>—</span>
                                    </template>
                                    <template v-else>
                                        <Badge
                                            :color="getBadgeColor(fieldKey)"
                                            :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                            :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                            :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                            size="sm"
                                        >
                                            <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                        </Badge>
                                    </template>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
        </div>

        <!-- Contenu (body) -->
        <div v-if="bodyFields.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="fieldKey in bodyFields" :key="fieldKey" class="p-3 bg-base-200 rounded-lg">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex items-center gap-2">
                                <Icon :source="getFieldIcon(fieldKey)" :alt="getFieldLabel(fieldKey)" size="xs" class="text-primary-400" />
                                <span class="text-xs text-primary-400 uppercase font-semibold">
                                    {{ getFieldLabel(fieldKey) }}
                                </span>
                            </div>
                        </Tooltip>
                    </div>
                    <div class="text-primary-100 break-words">
                        <CellRenderer :cell="getCell(fieldKey)" ui-color="primary" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
