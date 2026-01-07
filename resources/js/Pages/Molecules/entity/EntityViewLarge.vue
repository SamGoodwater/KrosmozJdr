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
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityConfig, normalizeEntityType } from "@/Entities/entity-registry";
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

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
const { downloadPdf, isDownloading } = useDownloadPdf(props.entityType);

const entityTypeKey = computed(() => normalizeEntityType(props.entityType));
const entityConfig = computed(() => getEntityConfig(entityTypeKey.value));

const entityCtx = computed(() => {
  // Le contexte sera géré par EntityActions via usePermissions
  return { meta: {} };
});

const entityDescriptors = computed(() => {
    const cfg = entityConfig.value;
    if (!cfg?.getDescriptors) return {};
    return cfg.getDescriptors(entityCtx.value) || {};
});

const rawEntity = computed(() => {
    if (props.entity && typeof props.entity.toRaw === "function") return props.entity.toRaw();
    if (props.entity && typeof props.entity._data !== "undefined") return props.entity._data;
    return props.entity || {};
});

const extendedFields = computed(() => {
    const cfg = entityConfig.value;
    const list = cfg?.viewFields?.extended || [];
    return (list || []).filter((key) => {
        const d = entityDescriptors.value?.[key];
        if (!d) return true;
        if (typeof d.visibleIf === "function") return Boolean(d.visibleIf(entityCtx.value));
        return true;
    });
});

const getExtendedViewCfg = (key) => {
    const d = entityDescriptors.value?.[key];
    return d?.display?.views?.extended || null;
};

const getExtendedSize = (key) => {
    const v = getExtendedViewCfg(key);
    const s = String(v?.size || "large");
    if (s === "small" || s === "normal" || s === "large") return s;
    return "large";
};

const showExtendedIcon = (key) => {
    const s = getExtendedSize(key);
    return s === "small" || s === "large";
};

const showExtendedLabel = (key) => {
    const s = getExtendedSize(key);
    return s === "normal" || s === "large";
};

const tooltipForResourceField = (key, cell) => {
    const d = entityDescriptors.value?.[key];
    const label = d?.label || key;

    let v = cell?.value;
    if (cell?.type === "icon") {
        const b = cell?.params?.booleanValue;
        const s = String(b ?? "").toLowerCase();
        if (s === "1" || s === "true") v = "Oui";
        else if (s === "0" || s === "false") v = "Non";
        else v = cell?.params?.alt || "—";
    }
    if (cell?.type === "image") {
        v = rawEntity.value?.name ? `Image de ${rawEntity.value.name}` : "Image";
    }
    const text = v === null || typeof v === "undefined" || v === "" ? "—" : String(v);
    return `${label} : ${text}`;
};

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

// Handlers pour les actions
const handleAction = async (actionKey, entity) => {
  const entityId = entity?.id ?? props.entity?.id ?? null;
  if (!entityId) return;

  const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;

  switch (actionKey) {
    case 'view':
      router.visit(route(`entities.${entityTypePlural}.show`, { [props.entityType]: entityId }));
      emit('view', entity);
      break;

    case 'quick-view':
      emit('quick-view', entity);
      break;

    case 'edit':
      router.visit(route(`entities.${entityTypePlural}.edit`, { [props.entityType]: entityId }));
      emit('edit', entity);
      break;

    case 'quick-edit':
      emit('quick-edit', entity);
      break;

    case 'copy-link': {
      const cfg = getEntityRouteConfig(props.entityType);
      const url = resolveEntityRouteUrl(props.entityType, 'show', entityId, cfg);
      if (url) {
        await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'entité copié !");
      }
      emit('copy-link', entity);
      break;
    }

    case 'download-pdf':
      await downloadPdf(entityId);
      emit('download-pdf', entity);
      break;

    case 'refresh':
      router.reload({ only: [entityTypePlural] });
      emit('refresh', entity);
      break;

    case 'delete':
      emit('delete', entity);
      break;

    case 'minimize':
      emit('minimize', entity);
      break;
  }
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
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            :entity-type="entityTypeKey"
                            :entity="entity"
                            format="buttons"
                            display="icon-text"
                            size="sm"
                            color="primary"
                            :context="{ inPanel: false }"
                            @action="handleAction"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Entités migrées (Option B via registry) -->
            <template v-if="entityConfig">
                <div
                    v-for="key in extendedFields"
                    :key="key"
                    class="p-3 bg-base-200 rounded-lg"
                >
                    <Tooltip
                        :content="tooltipForResourceField(key, entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'extended' }))"
                        placement="top"
                    >
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <Icon
                                    v-if="showExtendedIcon(key)"
                                    :source="entityDescriptors?.[key]?.icon || 'fa-solid fa-info-circle'"
                                    :alt="entityDescriptors?.[key]?.label || key"
                                    size="xs"
                                    class="text-primary-400"
                                />
                                <span class="text-xs text-primary-400 uppercase font-semibold">
                                    <span v-if="showExtendedLabel(key)">{{ entityDescriptors?.[key]?.label || key }}</span>
                                    <span v-else class="sr-only">{{ entityDescriptors?.[key]?.label || key }}</span>
                                </span>
                            </div>
                            <div class="text-primary-100 break-words">
                                <CellRenderer
                                    :cell="entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'extended' })"
                                    ui-color="primary"
                                />
                            </div>
                        </div>
                    </Tooltip>
                </div>
            </template>

            <!-- Fallback historique -->
            <template v-else v-for="(value, key) in entity" :key="key">
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

