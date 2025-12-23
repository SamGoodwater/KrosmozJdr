<script setup>
/**
 * EntityViewLarge Molecule
 * 
 * @description
 * Vue grande d'une entité avec tout le contenu affiché
 * Utilisée dans les grandes modals ou directement dans le main
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @emit edit - Événement émis pour éditer l'entité
 * @emit copy-link - Événement émis pour copier le lien
 * @emit download-pdf - Événement émis pour télécharger le PDF
 * @emit refresh - Événement émis pour rafraîchir les données
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf, isDownloading } = useDownloadPdf(props.entityType);
const { isAdmin } = usePermissions();

// Permissions
const canUpdate = computed(() => {
    if (props.entity && typeof props.entity.canUpdate !== 'undefined') {
        return props.entity.canUpdate ?? false;
    }
    return props.entity?.can?.update ?? false;
});

const canView = computed(() => {
    if (props.entity && typeof props.entity.canView !== 'undefined') {
        return props.entity.canView ?? false;
    }
    return props.entity?.can?.view ?? false;
});

// Fonction pour obtenir l'icône selon le type d'entité
const getEntityIcon = (type) => {
    const icons = {
        attribute: 'fa-solid fa-list',
        campaign: 'fa-solid fa-book',
        capability: 'fa-solid fa-star',
        classe: 'fa-solid fa-user',
        consumable: 'fa-solid fa-flask',
        creature: 'fa-solid fa-paw',
        item: 'fa-solid fa-box',
        monster: 'fa-solid fa-dragon',
        npc: 'fa-solid fa-user-tie',
        panoply: 'fa-solid fa-layer-group',
        resource: 'fa-solid fa-gem',
        scenario: 'fa-solid fa-scroll',
        shop: 'fa-solid fa-store',
        specialization: 'fa-solid fa-graduation-cap',
        spell: 'fa-solid fa-wand-magic-sparkles'
    };
    return icons[type] || 'fa-solid fa-circle';
};

// Génère l'URL de l'entité
const getEntityUrl = () => {
    const entityId = props.entity?.id ?? null;
    if (!entityId) return '';
    
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    const routeName = `entities.${entityTypePlural}.show`;
    const routeParams = { [props.entityType]: entityId };
    return `${window.location.origin}${route(routeName, routeParams)}`;
};

// Handlers
const handleEdit = () => {
    const entityId = props.entity?.id ?? null;
    if (!entityId) return;
    
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    router.visit(route(`entities.${entityTypePlural}.edit`, { [props.entityType]: entityId }));
    emit('edit');
};

const handleCopyLink = async () => {
    const url = getEntityUrl();
    if (url) {
        await copyToClipboard(url, 'Lien de l\'entité copié !');
        emit('copy-link');
    }
};

const handleDownloadPdf = async () => {
    const entityId = props.entity?.id ?? null;
    if (entityId) {
        await downloadPdf(entityId);
        emit('download-pdf');
    }
};

const handleRefresh = () => {
    router.reload({ only: [props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`] });
    emit('refresh');
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image à gauche -->
            <div class="flex-shrink-0">
                <div v-if="entity.image" class="w-32 h-32 md:w-40 md:h-40">
                    <Image :source="entity.image" :alt="entity.name || 'Image'" size="lg" rounded="lg" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-32 h-32 md:w-40 md:h-40 flex items-center justify-center bg-base-200 rounded-lg">
                    <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="xl" />
                </div>
            </div>
            
            <!-- Informations principales à droite -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ entity.name || entity.title }}</h2>
                        <p v-if="entity.description" class="text-primary-300 mt-2 break-words">{{ entity.description }}</p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex gap-2 flex-shrink-0">
                        <Tooltip v-if="canUpdate" content="Modifier" placement="left">
                            <Btn size="sm" variant="ghost" @click="handleEdit" class="btn-square">
                                <Icon source="fa-solid fa-pen" size="sm" />
                            </Btn>
                        </Tooltip>
                        <Tooltip content="Copier le lien" placement="left">
                            <Btn size="sm" variant="ghost" @click="handleCopyLink" class="btn-square">
                                <Icon source="fa-solid fa-link" size="sm" />
                            </Btn>
                        </Tooltip>
                        <Tooltip content="Télécharger PDF" placement="left">
                            <Btn size="sm" variant="ghost" @click="handleDownloadPdf" :disabled="isDownloading" class="btn-square">
                                <Icon source="fa-solid fa-file-pdf" size="sm" :class="{ 'animate-spin': isDownloading }" />
                            </Btn>
                        </Tooltip>
                        <Tooltip v-if="isAdmin" content="Rafraîchir" placement="left">
                            <Btn size="sm" variant="ghost" @click="handleRefresh" class="btn-square">
                                <Icon source="fa-solid fa-arrows-rotate" size="sm" />
                            </Btn>
                        </Tooltip>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template v-for="(value, key) in entity" :key="key">
                <div v-if="!['id', 'name', 'title', 'description', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                     class="p-3 bg-base-200 rounded-lg">
                    <div class="flex flex-col">
                        <span class="text-xs text-primary-400 uppercase font-semibold mb-1">{{ key }}</span>
                        <span class="text-primary-100 break-words">
                            <Badge v-if="typeof value === 'boolean'" :color="value ? 'success' : 'error'" size="sm">
                                {{ value ? 'Oui' : 'Non' }}
                            </Badge>
                            <span v-else-if="Array.isArray(value)" class="text-sm">{{ value.length }} élément(s)</span>
                            <span v-else-if="typeof value === 'object' && value !== null" class="text-sm">{{ value.name || value.title || JSON.stringify(value) }}</span>
                            <span v-else class="text-sm">{{ value }}</span>
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

