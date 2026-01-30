<script setup>
/**
 * CampaignViewMinimal — Vue Minimal pour Campaign
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Campaign} campaign - Instance du modèle Campaign
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCampaignFieldDescriptors } from "@/Entities/campaign/campaign-descriptors";
import { getEntityFieldShortLabel, getEntityFieldTooltip, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    campaign: {
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

const stateValue = computed(() => props.campaign?.state ?? props.campaign?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[CampaignViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['name', 'state', 'is_public'].filter(canShowField));

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'slug',
    'state',
    'read_level',
].filter(canShowField));

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.campaign.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const tooltipForField = (fieldKey, cell) => {
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    const label = getEntityFieldShortLabel(fieldKey, descriptors.value?.[fieldKey]?.general?.label || fieldKey);
    if (shouldOmitLabelInMeta(fieldKey)) return String(value);
    return `${label} : ${value}`;
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
        case 'delete':
            emit('delete', props.campaign);
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
                    <div v-if="campaign.image" class="w-8 h-8">
                        <Image
                            :src="campaign.image"
                            :alt="campaign.name || 'Campaign'"
                            size="xs"
                            class="w-full h-full rounded object-cover"
                        />
                    </div>
                    <Icon v-else source="fa-solid fa-flag" :alt="campaign.name || 'Campaign'" size="sm" class="flex-shrink-0" />
                </template>

                <template #title>
                    <Tooltip :content="campaign.name || 'Campaign'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">
                            <CellRenderer :cell="getCell('name')" ui-color="primary" />
                        </span>
                    </Tooltip>
                </template>

                <template #actions>
                    <div v-if="showActions && isHovered">
                        <EntityActions
                            entity-type="campaign"
                            :entity="campaign"
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
                                <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
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
