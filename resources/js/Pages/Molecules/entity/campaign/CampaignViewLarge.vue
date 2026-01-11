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
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

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

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'name',
        'slug',
        'description',
        'keyword',
        'state',
        'is_public',
        'usable',
        'is_visible',
    ];
    
    if (props.campaign.canView) {
        fields.push('created_by', 'created_at', 'updated_at');
    }
    
    return fields;
});

const getFieldLabel = (fieldKey) => {
    const labels = {
        name: 'Nom',
        slug: 'Slug',
        description: 'Description',
        keyword: 'Mot-clé',
        state: 'État',
        is_public: 'Public',
        usable: 'Utilisable',
        is_visible: 'Visible',
        created_by: 'Créé par',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        slug: 'fa-solid fa-link',
        description: 'fa-solid fa-align-left',
        keyword: 'fa-solid fa-tag',
        state: 'fa-solid fa-info-circle',
        is_public: 'fa-solid fa-globe',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        created_by: 'fa-solid fa-user',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.campaign.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
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
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image -->
            <div v-if="campaign.image" class="flex-shrink-0">
                <Image
                    :src="campaign.image"
                    :alt="campaign.name || 'Campaign'"
                    size="lg"
                    class="rounded-lg"
                />
            </div>
            
            <!-- Informations principales -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">
                            <CellRenderer
                                :cell="getCell('name')"
                                ui-color="primary"
                            />
                        </h2>
                        <p v-if="campaign.description" class="text-primary-300 mt-2 break-words">
                            {{ campaign.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
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
                </div>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="fieldKey in extendedFields"
                :key="fieldKey"
                class="p-3 bg-base-200 rounded-lg"
            >
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Icon
                            :source="getFieldIcon(fieldKey)"
                            :alt="getFieldLabel(fieldKey)"
                            size="xs"
                            class="text-primary-400"
                        />
                        <span class="text-xs text-primary-400 uppercase font-semibold">
                            {{ getFieldLabel(fieldKey) }}
                        </span>
                    </div>
                    <div class="text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell(fieldKey)"
                            ui-color="primary"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
