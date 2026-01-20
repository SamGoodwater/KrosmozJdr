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
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getEntityFieldShortLabel, getEntityFieldTooltip, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

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

const usableValue = computed(() => {
    const v = props.resource?.usable ?? props.resource?._data?.usable;
    return typeof v === 'boolean' ? v : null;
});

const autoUpdateValue = computed(() => {
    const v = props.resource?.auto_update ?? props.resource?._data?.auto_update;
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
            console.warn('[ResourceViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ([
    'resource_type',
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

// Utiliser les descriptors pour les icônes
const getFieldIcon = (fieldKey) => {
    return descriptors.value[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldTooltip = (fieldKey) => {
    return getEntityFieldTooltip(descriptors.value?.[fieldKey]);
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
    <div class="space-y-3">
        <EntityViewHeader mode="compact">
            <template #media>
                <div class="relative w-16 h-16">
                    <div class="peer absolute inset-x-0 bottom-0 h-[80%] z-10"></div>
                    <div class="absolute top-1 left-1 z-20 transition-opacity duration-150 peer-hover:opacity-0">
                        <EntityUsableDot :usable="usableValue" />
                    </div>
                    <div class="absolute top-1 right-1 z-20 transition-opacity duration-150 peer-hover:opacity-0">
                        <Badge
                            :color="getBadgeColor('level')"
                            :auto-label="getBadgeAutoParams('level').autoLabel"
                            :auto-scheme="getBadgeAutoParams('level').autoScheme"
                            :auto-tone="getBadgeAutoParams('level').autoTone"
                            size="xs"
                        >
                            <CellRenderer :cell="asTextCell(getCell('level'))" ui-color="primary" />
                        </Badge>
                    </div>

                    <Image
                        v-if="resource.image"
                        :src="resource.image"
                        :alt="resource.name || 'Ressource'"
                        size="sm"
                        rounded="lg"
                        fit="cover"
                        class="w-full h-full peer-hover:hidden pointer-events-none"
                    />
                    <Image
                        v-if="resource.image"
                        :src="resource.image"
                        :alt="resource.name || 'Ressource'"
                        size="sm"
                        rounded="lg"
                        fit="contain"
                        class="w-full h-full hidden peer-hover:block pointer-events-none"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 rounded-lg">
                        <Icon source="fa-solid fa-gem" :alt="resource.name" size="md" />
                    </div>
                </div>
            </template>

            <template #title>
                <h3 class="text-lg font-semibold text-primary-100 truncate leading-tight">
                    {{ resource.name }}
                </h3>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="resource"
                        :entity="resource"
                        format="dropdown"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
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
                                    class="max-w-[14rem] whitespace-normal break-words"
                                >
                                    <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                </Badge>
                            </div>
                        </Tooltip>
                    </template>
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
                                        <Badge :color="getBadgeColor(fieldKey)" size="sm">
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
    </div>
</template>
