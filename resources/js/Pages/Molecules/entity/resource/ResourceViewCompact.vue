<script setup>
/**
 * ResourceViewCompact — Vue Compact pour Resource
 * 
 * @description
 * Vue réduite d'une ressource avec informations essentielles affichées sous forme de badges.
 * Utilisée dans les modals compacts.
 * 
 * @props {Resource} resource - Instance du modèle Resource
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
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

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
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

// Obtenir les descriptors avec le contexte
const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));

// Champs à afficher dans la vue compacte sous forme de badges
const compactFields = computed(() => [
    'resource_type',
    'level',
    'price',
    'rarity',
    'usable',
    'dofus_version',
    'is_visible',
]);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ResourceViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const visibleCompactFields = computed(() => (compactFields.value || []).filter(canShowField));

// Utiliser les descriptors pour les icônes
const getFieldIcon = (fieldKey) => {
    return descriptors.value[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.resource.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
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
        'dofus_version': 'secondary',
        'is_visible': 'primary',
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

const handleAction = async (actionKey) => {
    const resourceId = props.resource.id;
    if (!resourceId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.resources.show', { resource: resourceId }));
            emit('view', props.resource);
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.resource);
            break;
        }
        case 'delete':
            emit('delete', props.resource);
            break;
    }
};
</script>

<template>
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex items-start gap-3">
            <!-- Image à gauche -->
            <div class="w-16 h-16 flex-shrink-0">
                <CellRenderer
                    :cell="getCell('image')"
                    ui-color="primary"
                    class="w-full h-full"
                />
            </div>
            
            <!-- Nom et actions -->
            <div class="flex-1 min-w-0 flex items-start justify-between gap-2">
                <h3 class="text-lg font-semibold text-primary-100 truncate leading-tight">
                    {{ resource.name }}
                </h3>
                
                <div v-if="showActions" class="flex-shrink-0">
                    <EntityActions
                        entity-type="resource"
                        :entity="resource"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </div>
        </div>

        <!-- Badges avec icônes -->
        <div class="flex flex-wrap gap-2">
            <template v-for="fieldKey in visibleCompactFields" :key="fieldKey">
                <Badge
                    :color="getBadgeColor(fieldKey)"
                    :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                    :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                    :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                    size="sm"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1"
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
