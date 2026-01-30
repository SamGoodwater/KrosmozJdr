<script setup>
/**
 * ResourceTypeViewLarge — Vue Large pour ResourceType
 * 
 * @description
 * Vue complète d'un type de ressource avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {ResourceType} resourceType - Instance du modèle ResourceType
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @emit edit - Événement émis pour éditer le type de ressource
 * @emit copy-link - Événement émis pour copier le lien
 * @emit refresh - Événement émis pour rafraîchir les données
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";

const props = defineProps({
    resourceType: {
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
    'refresh',
    'view',
    'quick-view',
    'quick-edit',
    'delete',
    'action',
]);

const { copyToClipboard } = useCopyToClipboard();
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('resourceType', 'viewAny'),
        createAny: permissions.can('resourceType', 'createAny'),
        updateAny: permissions.can('resourceType', 'updateAny'),
        deleteAny: permissions.can('resourceType', 'deleteAny'),
        manageAny: permissions.can('resourceType', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getResourceTypeFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ResourceTypeViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'decision',
        'state',
        'read_level',
        'write_level',
        'resources_count',
        'dofusdb_type_id',
        'seen_count',
        'last_seen_at',
    ];

    ['created_by', 'created_at', 'updated_at'].forEach((k) => fields.push(k));
    return fields.filter(canShowField);
});

// Handlers pour les actions
const handleAction = async (actionKey) => {
    const resourceTypeId = props.resourceType.id;
    if (!resourceTypeId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.resource-types.show', { resourceType: resourceTypeId }));
            emit('view', props.resourceType);
            break;

        case 'quick-view':
            emit('quick-view', props.resourceType);
            break;

        case 'edit':
            router.visit(route('entities.resource-types.edit', { resourceType: resourceTypeId }));
            emit('edit', props.resourceType);
            break;

        case 'quick-edit':
            emit('quick-edit', props.resourceType);
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('resource-type');
            const url = resolveEntityRouteUrl('resource-type', 'show', resourceTypeId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du type de ressource copié !");
            }
            emit('copy-link', props.resourceType);
            break;
        }

        case 'refresh':
            router.reload({ only: ['resourceTypes'] });
            emit('refresh', props.resourceType);
            break;

        case 'delete':
            emit('delete', props.resourceType);
            break;
    }
};

// Helpers pour les labels et icônes
const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

// Génère une cellule pour un champ
const getCell = (fieldKey) => {
    return props.resourceType.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Informations principales -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ resourceType.name }}</h2>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="resource-type"
                            :entity="resourceType"
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
