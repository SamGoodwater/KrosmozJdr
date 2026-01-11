<script setup>
/**
 * ResourceViewLarge — Vue Large pour Resource
 * 
 * @description
 * Vue complète d'une ressource avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Resource} resource - Instance du modèle Resource
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @emit edit - Événement émis pour éditer la ressource
 * @emit copy-link - Événement émis pour copier le lien
 * @emit download-pdf - Événement émis pour télécharger le PDF
 * @emit refresh - Événement émis pour rafraîchir les données
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

const props = defineProps({
    resource: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits([
    'edit',
    'copy-link',
    'download-pdf',
    'refresh',
    'view',
    'quick-view',
    'quick-edit',
    'delete',
    'action',
]);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('resource');

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'rarity',
        'resource_type',
        'level',
        'usable',
        'price',
        'weight',
        'dofus_version',
        'is_visible',
        'auto_update',
        'dofusdb_id',
        'official_id',
    ];
    
    // Ajouter les champs conditionnels si permissions
    if (props.resource.canView) {
        fields.push('created_by', 'created_at', 'updated_at');
    }
    
    return fields;
});

// Handlers pour les actions
const handleAction = async (actionKey) => {
    const resourceId = props.resource.id;
    if (!resourceId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.resources.show', { resource: resourceId }));
            emit('view', props.resource);
            break;

        case 'quick-view':
            emit('quick-view', props.resource);
            break;

        case 'edit':
            router.visit(route('entities.resources.edit', { resource: resourceId }));
            emit('edit', props.resource);
            break;

        case 'quick-edit':
            emit('quick-edit', props.resource);
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('resource');
            const url = resolveEntityRouteUrl('resource', 'show', resourceId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la ressource copié !");
            }
            emit('copy-link', props.resource);
            break;
        }

        case 'download-pdf':
            await downloadPdf(resourceId);
            emit('download-pdf', props.resource);
            break;

        case 'refresh':
            router.reload({ only: ['resources'] });
            emit('refresh', props.resource);
            break;

        case 'delete':
            emit('delete', props.resource);
            break;
    }
};

// Helpers pour les labels et icônes
const getFieldLabel = (fieldKey) => {
    const labels = {
        rarity: 'Rareté',
        resource_type: 'Type',
        level: 'Niveau',
        usable: 'Utilisable',
        price: 'Prix',
        weight: 'Poids',
        dofus_version: 'Version Dofus',
        is_visible: 'Visibilité',
        auto_update: 'Mise à jour auto',
        dofusdb_id: 'ID DofusDB',
        official_id: 'ID Officiel',
        created_by: 'Créé par',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        rarity: 'fa-solid fa-star',
        resource_type: 'fa-solid fa-tag',
        level: 'fa-solid fa-level-up-alt',
        usable: 'fa-solid fa-check-circle',
        price: 'fa-solid fa-coins',
        weight: 'fa-solid fa-weight',
        dofus_version: 'fa-solid fa-gamepad',
        is_visible: 'fa-solid fa-eye',
        auto_update: 'fa-solid fa-sync',
        dofusdb_id: 'fa-solid fa-database',
        official_id: 'fa-solid fa-id-card',
        created_by: 'fa-solid fa-user',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

// Génère une cellule pour un champ
const getCell = (fieldKey) => {
    return props.resource.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image à gauche -->
            <div class="flex-shrink-0">
                <div v-if="resource.image" class="w-32 h-32 md:w-40 md:h-40">
                    <Image :source="resource.image" :alt="resource.name || 'Image'" size="lg" rounded="lg" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-32 h-32 md:w-40 md:h-40 flex items-center justify-center bg-base-200 rounded-lg">
                    <Icon source="fa-solid fa-gem" :alt="resource.name" size="xl" />
                </div>
            </div>
            
            <!-- Informations principales à droite -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ resource.name }}</h2>
                        <p v-if="resource.description" class="text-primary-300 mt-2 break-words">{{ resource.description }}</p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="resource"
                            :entity="resource"
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
