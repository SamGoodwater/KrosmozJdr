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
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
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

const stateValue = computed(() => props.resource?.state ?? props.resource?._data?.state ?? null);

const autoUpdateValue = computed(() => {
    const v = props.resource?.auto_update ?? props.resource?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

// Champs à afficher dans la vue large sous forme de badges (principaux)
const primaryFields = computed(() => [
    'resource_type',
    'level',
    'price',
    'rarity',
    'weight',
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

const headlineFields = computed(() => ([
    'resource_type',
    'level',
].filter(canShowField)));

const metaFields = computed(() => (visiblePrimaryFields.value || []).filter((k) => !headlineFields.value.includes(k)));

const orderedMetaFields = computed(() => {
    const preferred = [];
    const rest = (metaFields.value || []).filter((k) => !preferred.includes(k));
    const head = preferred.filter((k) => (metaFields.value || []).includes(k));
    return [...head, ...rest];
});

const displayMetaFields = computed(() => [...headlineFields.value, ...orderedMetaFields.value]);

const userCanEditFields = computed(() => ([
    'dofus_version',
    'auto_update',
    'read_level',
    'write_level',
].filter(canShowField)));

// Champs secondaires (infos techniques)
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

const technicalFields = computed(() => (secondaryFields.value || []).filter(canShowField));

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

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldTooltip = (fieldKey) => {
    return getEntityFieldTooltip(descriptors.value?.[fieldKey]);
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
        state: 'neutral',
        'weight': 'secondary',
        'dofus_version': 'secondary',
        read_level: 'primary',
        write_level: 'secondary',
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
        <EntityViewHeader mode="large">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div class="absolute top-2 left-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <EntityUsableDot :state="stateValue" />
                    </div>

                    <div class="absolute top-2 right-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
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

                    <ImageViewer
                        v-if="resource.image"
                        :src="resource.image"
                        :alt="resource.name || 'Ressource'"
                        :caption="resource.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 rounded-lg">
                        <Icon source="fa-solid fa-gem" :alt="resource.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">{{ resource.name }}</h2>
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
                                        <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                    </template>
                                </Badge>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </template>

            <template #subtitle>
                <p v-if="resource.description" class="text-primary-300 mt-3 break-words">{{ resource.description }}</p>
            </template>

            <template #actions>
                <div v-if="showActions">
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
            </template>
        </EntityViewHeader>

        <!-- Infos techniques (texte, pas en badges) -->
        <div v-if="technicalFields.length > 0" class="pt-3 border-t border-base-300">
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
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
        
            <!-- UserCanEdit (paramètres) — sans séparation supplémentaire -->
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
    </div>
</template>
