<script setup>
/**
 * CampaignViewLarge — Vue Large pour Campaign
 * 
 * @description
 * Vue complète d'une campagne avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Campaign} campaign - Instance du modèle Campaign
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
import { getCampaignFieldDescriptors } from "@/Entities/campaign/campaign-descriptors";
import { getEntityFieldTooltip, getEntityFieldShortLabel, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    campaign: {
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
const { downloadPdf } = useDownloadPdf('campaign');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('campaigns', 'viewAny'),
        createAny: permissions.can('campaigns', 'createAny'),
        updateAny: permissions.can('campaigns', 'updateAny'),
        deleteAny: permissions.can('campaigns', 'deleteAny'),
        manageAny: permissions.can('campaigns', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCampaignFieldDescriptors(ctx.value));

const usableValue = computed(() => {
    const v = props.campaign?.usable ?? props.campaign?._data?.usable;
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
            console.warn('[CampaignViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ([
    'state',
    'is_public',
].filter(canShowField)));

const metaFields = computed(() => ([
    'keyword',
].filter(canShowField).filter((k) => !headlineFields.value.includes(k))));

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ([
    'is_visible',
].filter(canShowField)));

const technicalFields = computed(() => ([
    'slug',
    'created_by',
    'created_at',
    'updated_at',
].filter(canShowField)));

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getFieldTooltip = (fieldKey) => getEntityFieldTooltip(descriptors.value?.[fieldKey]);

const getCell = (fieldKey) => {
    return props.campaign.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const getBadgeColor = (fieldKey) => {
    const cell = getCell(fieldKey);
    if (cell?.params?.color) return cell.params.color;
    const colorMap = {
        state: 'info',
        is_public: 'secondary',
        keyword: 'secondary',
        is_visible: 'primary',
        slug: 'neutral',
        created_by: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
    };
    return colorMap[fieldKey] || 'neutral';
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return { type: 'text', value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v), params: cell?.params || {} };
};

const handleAction = async (actionKey) => {
    const campaignId = props.campaign.id;
    if (!campaignId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.campaigns.show', { campaign: props.campaign.slug || campaignId }));
            emit('view', props.campaign);
            break;
        case 'edit':
            router.visit(route('entities.campaigns.edit', { campaign: campaignId }));
            emit('edit', props.campaign);
            break;
        case 'quick-edit':
            emit('quick-edit', props.campaign);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('campaign');
            const url = resolveEntityRouteUrl('campaign', 'show', props.campaign.slug || campaignId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la campagne copié !");
            }
            emit('copy-link', props.campaign);
            break;
        }
        case 'download-pdf':
            await downloadPdf(campaignId);
            emit('download-pdf', props.campaign);
            break;
        case 'refresh':
            router.reload({ only: ['campaigns'] });
            emit('refresh', props.campaign);
            break;
        case 'delete':
            emit('delete', props.campaign);
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

                    <Image
                        v-if="campaign.image"
                        :src="campaign.image"
                        :alt="campaign.name || 'Campaign'"
                        size="xl"
                        rounded="lg"
                        fit="cover"
                        class="w-full h-full peer-hover:hidden pointer-events-none"
                    />
                    <Image
                        v-if="campaign.image"
                        :src="campaign.image"
                        :alt="campaign.name || 'Campaign'"
                        size="xl"
                        rounded="lg"
                        fit="contain"
                        class="w-full h-full hidden peer-hover:block pointer-events-none"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 rounded-lg">
                        <Icon source="fa-solid fa-flag" :alt="campaign.name || 'Campaign'" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">
                    <CellRenderer :cell="getCell('name')" ui-color="primary" />
                </h2>
            </template>

            <template #subtitle>
                <p v-if="campaign.description" class="text-primary-300 mt-2 break-words">
                    {{ campaign.description }}
                </p>
            </template>

            <template #mainInfos>
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

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="campaign"
                        :entity="campaign"
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

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
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
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
                                    <Badge :color="getBadgeColor(fieldKey)" size="sm">
                                        <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                    </Badge>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
