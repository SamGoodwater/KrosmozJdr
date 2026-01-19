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
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { usePermissions } from "@/Composables/permissions/usePermissions";

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
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('resources', 'viewAny'),
        createAny: permissions.can('resources', 'createAny'),
        updateAny: permissions.can('resources', 'updateAny'),
        deleteAny: permissions.can('resources', 'deleteAny'),
        manageAny: permissions.can('resources', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

// Obtenir les descriptors avec le contexte (permissions/options)
const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));

// Champs à afficher dans la vue large sous forme de badges (principaux)
const primaryFields = computed(() => [
    'resource_type',
    'level',
    'price',
    'rarity',
    'usable',
    'weight',
    'dofus_version',
    'is_visible',
    'auto_update',
]);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ResourceViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const visiblePrimaryFields = computed(() => (primaryFields.value || []).filter(canShowField));

// Champs secondaires (affichés après les principaux)
const secondaryFields = computed(() => {
    const fields = [
        'dofusdb_id',
        'official_id',
    ];
    // Ajouter les champs conditionnels si permissions (utilise visibleIf du descriptor)
    ['created_by', 'created_at', 'updated_at'].forEach((fieldKey) => {
        if (canShowField(fieldKey)) fields.push(fieldKey);
    });

    return fields.filter(canShowField);
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

// Utiliser les descriptors pour les icônes
const getFieldIcon = (fieldKey) => {
    return descriptors.value[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

// Génère une cellule pour un champ
const getCell = (fieldKey) => {
    return props.resource.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

/**
 * Convertit une cellule en "texte" pour éviter d'imbriquer Badge dans Badge
 * (ex: CellRenderer(type=badge) à l'intérieur d'un <Badge> de la vue).
 */
const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v),
        params: cell?.params || {},
    };
};

// Obtenir la couleur du badge selon le champ
const getBadgeColor = (fieldKey) => {
    const cell = getCell(fieldKey);
    // Si la cellule a déjà une couleur définie, l'utiliser
    if (cell?.params?.color) {
        return cell.params.color;
    }
    // Sinon, utiliser des couleurs par défaut selon le champ
    const colorMap = {
        'resource_type': 'info',
        'level': 'warning',
        'price': 'success',
        'rarity': 'auto', // Utilise auto-color avec autoScheme="rarity"
        'usable': 'success',
        'weight': 'secondary',
        'dofus_version': 'secondary',
        'is_visible': 'primary',
        'auto_update': 'warning',
        'dofusdb_id': 'neutral',
        'official_id': 'neutral',
        'created_by': 'neutral',
        'created_at': 'neutral',
        'updated_at': 'neutral',
    };
    return colorMap[fieldKey] || 'neutral';
};

// Obtenir les paramètres auto-color pour les badges
const getBadgeAutoParams = (fieldKey) => {
    const cell = getCell(fieldKey);
    if (fieldKey === 'rarity' && cell?.value) {
        return {
            autoLabel: String(cell.value),
            autoScheme: 'rarity',
            autoTone: 'mid',
        };
    }
    if (fieldKey === 'level' && cell?.value) {
        return {
            autoLabel: String(cell.value),
            autoScheme: 'level',
            autoTone: 'mid',
        };
    }
    return {};
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image à gauche -->
            <div class="flex-shrink-0">
                <div class="w-32 h-32 md:w-40 md:h-40">
                    <CellRenderer
                        :cell="getCell('image')"
                        ui-color="primary"
                        class="w-full h-full"
                    />
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

        <!-- Badges principaux avec icônes -->
        <div class="flex flex-wrap gap-2">
            <template v-for="fieldKey in visiblePrimaryFields" :key="fieldKey">
                <Badge
                    :color="getBadgeColor(fieldKey)"
                    :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                    :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                    :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                    size="sm"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5"
                >
                    <Icon
                        :source="getFieldIcon(fieldKey)"
                        size="xs"
                        class="flex-shrink-0"
                    />
                    <span class="font-medium">
                        <CellRenderer
                            :cell="asTextCell(getCell(fieldKey))"
                            ui-color="primary"
                        />
                    </span>
                </Badge>
            </template>
        </div>

        <!-- Badges secondaires (si présents) -->
        <div v-if="secondaryFields.length > 0" class="flex flex-wrap gap-2 pt-2 border-t border-base-300">
            <template v-for="fieldKey in secondaryFields" :key="fieldKey">
                <Badge
                    :color="getBadgeColor(fieldKey)"
                    :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                    :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                    :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                    size="sm"
                    variant="outline"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5"
                >
                    <Icon
                        :source="getFieldIcon(fieldKey)"
                        size="xs"
                        class="flex-shrink-0"
                    />
                    <span class="font-medium">
                        <CellRenderer
                            :cell="asTextCell(getCell(fieldKey))"
                            ui-color="primary"
                        />
                    </span>
                </Badge>
            </template>
        </div>
    </div>
</template>
