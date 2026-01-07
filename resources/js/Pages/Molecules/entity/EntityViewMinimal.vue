<script setup>
/**
 * EntityViewMinimal Molecule
 * 
 * @description
 * Vue minimale d'une entité avec seulement les infos importantes
 * Affichées sous forme d'icônes avec tooltips
 * Peut s'agrandir au hover pour afficher plus de choses
 * Utilisée dans des grilles, petites modals ou hovers
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {Array} importantFields - Liste des champs importants à afficher
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @emit edit - Événement émis pour éditer l'entité
 * @emit copy-link - Événement émis pour copier le lien
 * @emit download-pdf - Événement émis pour télécharger le PDF
 * @emit refresh - Événement émis pour rafraîchir les données
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
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
    importantFields: {
        type: Array,
        default: () => ['level', 'rarity', 'usable', 'is_visible']
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

const isHovered = ref(false);
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

const minimalFields = computed(() => {
    const cfg = entityConfig.value;
    const list = Array.isArray(props.importantFields) && props.importantFields.length
        ? props.importantFields
        : (cfg?.defaults?.minimalImportantFields || (Array.isArray(props.importantFields) ? props.importantFields : []));
    return (list || []).filter((key) => {
        const d = entityDescriptors.value?.[key];
        if (!d) return true;
        if (typeof d.visibleIf === "function") return Boolean(d.visibleIf(entityCtx.value));
        return true;
    });
});

const expandedFields = computed(() => {
    const cfg = entityConfig.value;
    const all = cfg?.viewFields?.extended || [];
    const important = new Set(minimalFields.value || []);
    return all
        .filter((k) => !important.has(k))
        .filter((k) => {
            const d = entityDescriptors.value?.[k];
            if (!d) return true;
            if (typeof d.visibleIf === "function") return Boolean(d.visibleIf(entityCtx.value));
            return true;
        });
});

const tooltipForField = (key, cell) => {
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

const getFieldIcon = (field) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        rarity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        price: 'fa-solid fa-coins',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs'
    };
    return icons[field] || 'fa-solid fa-info-circle';
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
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false">
        
        <div class="p-3">
            <!-- En-tête avec nom, icône et menu -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="sm" class="flex-shrink-0" />
                    <Tooltip :content="entity.name || entity.title || 'Entité'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ entity.name || entity.title }}</span>
                    </Tooltip>
                </div>
                
                <!-- Actions en haut à droite -->
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <EntityActions
                        :entity-type="entityTypeKey"
                        :entity="entity"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </div>

            <!-- Infos importantes en icônes avec tooltips -->
            <div class="flex gap-2 flex-wrap">
                <!-- Entités migrées (Option B via registry) : small = icône + valeur -->
                <template v-if="entityConfig">
                    <template v-for="field in minimalFields" :key="field">
                        <Tooltip
                            :content="tooltipForField(field, entityConfig.buildCell(field, rawEntity, entityCtx, { context: 'minimal' }))"
                            placement="top"
                        >
                            <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                                <Icon
                                    :source="entityDescriptors?.[field]?.icon || getFieldIcon(field)"
                                    :alt="entityDescriptors?.[field]?.label || field"
                                    size="xs"
                                    class="text-primary-400"
                                />
                                <span class="text-xs text-primary-300 font-medium">
                                    <CellRenderer
                                        :cell="entityConfig.buildCell(field, rawEntity, entityCtx, { context: 'minimal' })"
                                        ui-color="primary"
                                    />
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </template>

                <!-- Fallback historique -->
                <template v-else>
                <template v-for="field in importantFields" :key="field">
                    <Tooltip 
                        v-if="entity[field] !== null && entity[field] !== undefined"
                        :content="`${field}: ${entity[field]}`"
                        placement="top">
                        <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                            <Icon :source="getFieldIcon(field)" :alt="field" size="xs" class="text-primary-400" />
                            <span class="text-xs text-primary-300 font-medium">{{ entity[field] }}</span>
                        </div>
                    </Tooltip>
                    </template>
                </template>
            </div>

            <!-- Contenu supplémentaire au hover avec animation -->
            <div 
                v-if="isHovered" 
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <!-- Entités migrées (Option B via registry) : dépliage détaillé (extended) -->
                <template v-if="entityConfig">
                    <div
                        v-for="key in expandedFields"
                        :key="key"
                        class="flex items-start gap-2"
                    >
                        <Tooltip
                            :content="tooltipForField(key, entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'extended' }))"
                            placement="left"
                        >
                            <div class="flex items-start gap-2 w-full">
                                <Icon
                                    :source="entityDescriptors?.[key]?.icon || getFieldIcon(key)"
                                    :alt="entityDescriptors?.[key]?.label || key"
                                    size="xs"
                                    class="text-primary-400 flex-shrink-0 mt-0.5"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-primary-400">
                                        {{ entityDescriptors?.[key]?.label || key }}:
                                    </div>
                                    <div class="text-primary-200 truncate">
                                        <CellRenderer
                                            :cell="entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'extended' })"
                                            ui-color="primary"
                                        />
                                    </div>
                                </div>
                            </div>
                        </Tooltip>
                    </div>
                </template>

                <!-- Fallback historique -->
                <template v-else>
                <template v-for="(value, key) in entity" :key="key">
                    <div v-if="!importantFields.includes(key) && !['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                         class="flex items-start gap-1">
                        <Tooltip :content="`${key}: ${typeof value === 'object' ? JSON.stringify(value) : value}`" placement="left">
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-primary-400">{{ key }}:</span> 
                                <span v-if="Array.isArray(value)" class="text-primary-200">{{ value.length }} élément(s)</span>
                                <span v-else-if="typeof value === 'object' && value !== null" class="text-primary-200 truncate block">{{ value.name || value.title || 'Objet' }}</span>
                                <span v-else class="text-primary-200 truncate block">{{ value }}</span>
                            </div>
                        </Tooltip>
                    </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>

