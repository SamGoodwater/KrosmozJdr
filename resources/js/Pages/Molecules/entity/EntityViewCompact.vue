<script setup>
/**
 * EntityViewCompact Molecule
 * 
 * @description
 * Vue compacte d'une entité.
 *
 * - Default: itère sur les propriétés de l'entité (fallback historique).
 * - Resource (Option B): rendu piloté par `resource-descriptors` (par champ / par vue),
 *   avec tooltips systématiques "Label : Valeur".
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
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

// État pour la troncature
const expandedFields = ref(new Set());

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

const compactFields = computed(() => {
    const cfg = entityConfig.value;
    const list = cfg?.viewFields?.compact || [];
    return (list || []).filter((key) => {
        const d = entityDescriptors.value?.[key];
        if (!d) return true;
        if (typeof d.visibleIf === "function") return Boolean(d.visibleIf(entityCtx.value));
        return true;
    });
});

const getViewCfg = (key) => {
    const d = entityDescriptors.value?.[key];
    return d?.display?.views?.compact || null;
};

const getCompactSize = (key) => {
    const v = getViewCfg(key);
    const s = String(v?.size || "normal");
    if (s === "small" || s === "large") return s;
    return "normal";
};

const showCompactIcon = (key) => {
    // small + large affichent l'icône, normal non (règle produit)
    const s = getCompactSize(key);
    return s === "small" || s === "large";
};

const showCompactLabel = (key) => {
    // normal + large affichent le label, small non
    const s = getCompactSize(key);
    return s === "normal" || s === "large";
};

const tooltipForResourceField = (key, cell) => {
    const d = entityDescriptors.value?.[key];
    const label = d?.label || key;

    // valeur "humaine" pour tooltip
    let v = cell?.value;
    if (cell?.type === "icon") {
        // boolIcon: reconstituer "Oui/Non" si possible
        const b = cell?.params?.booleanValue;
        const s = String(b ?? "").toLowerCase();
        if (s === "1" || s === "true") v = "Oui";
        else if (s === "0" || s === "false") v = "Non";
        else v = cell?.params?.alt || "—";
    }
    if (cell?.type === "image") {
        v = rawEntity.value?.name ? `Image de ${rawEntity.value.name}` : "Image";
    }
    if (cell?.type === "route") {
        v = cell?.value;
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

const getFieldIcon = (key) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        rarity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        price: 'fa-solid fa-coins',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs',
        description: 'fa-solid fa-align-left',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock'
    };
    return icons[key] || 'fa-solid fa-info-circle';
};

const truncate = (text, maxLength = 30) => {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const toggleField = (key) => {
    if (expandedFields.value.has(key)) {
        expandedFields.value.delete(key);
    } else {
        expandedFields.value.add(key);
    }
};

const isFieldExpanded = (key) => {
    return expandedFields.value.has(key);
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
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact avec menu -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="md" class="flex-shrink-0" />
                <h3 class="text-lg font-semibold text-primary-100 truncate">{{ entity.name || entity.title }}</h3>
            </div>
            
            <!-- Actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    :entity-type="entityTypeKey"
                    :entity="entity"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false }"
                    @action="handleAction"
                />
            </div>
        </div>

        <!-- Informations en liste compacte avec icônes -->
        <div class="space-y-2 text-sm">
            <!-- Entités migrées (Option B via registry) -->
            <template v-if="entityConfig">
                <div
                    v-for="key in compactFields"
                    :key="key"
                    class="flex items-start gap-2 p-2 rounded hover:bg-base-200 transition-colors"
                >
                    <Tooltip
                        :content="tooltipForResourceField(key, entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'compact' }))"
                        placement="top"
                    >
                        <div class="flex items-start gap-2 w-full">
                            <Icon
                                v-if="showCompactIcon(key)"
                                :source="entityDescriptors?.[key]?.icon || getFieldIcon(key)"
                                size="xs"
                                class="text-primary-400 flex-shrink-0 mt-0.5"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        v-if="showCompactLabel(key)"
                                        class="text-primary-400 text-xs font-semibold uppercase"
                                    >
                                        {{ entityDescriptors?.[key]?.label || key }}
                                    </span>

                                    <div class="flex-1 text-right min-w-0 text-primary-200">
                                        <CellRenderer
                                            :cell="entityConfig.buildCell(key, rawEntity, entityCtx, { context: 'compact' })"
                                            ui-color="primary"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Tooltip>
                </div>
            </template>

            <!-- Fallback historique -->
            <template v-else v-for="(value, key) in entity" :key="key">
                <div v-if="!['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                     class="flex items-start gap-2 p-2 rounded hover:bg-base-200 transition-colors">
                    <!-- Icône du champ -->
                    <Icon :source="getFieldIcon(key)" size="xs" class="text-primary-400 flex-shrink-0 mt-0.5" />
                    
                    <!-- Label et valeur -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-primary-400 text-xs font-semibold uppercase">{{ key }}</span>
                            <div class="flex-1 text-right min-w-0">
                                <!-- Texte tronqué avec dépliage -->
                                <template v-if="typeof value === 'string' && value.length > 30">
                                    <div class="flex items-center gap-1 justify-end">
                                        <span class="text-primary-200 break-words" :class="{ 'line-clamp-2': !isFieldExpanded(key) }">
                                            {{ isFieldExpanded(key) ? value : truncate(value, 30) }}
                                        </span>
                                        <button @click="toggleField(key)" class="text-primary-400 hover:text-primary-200 flex-shrink-0" type="button">
                                            <Icon :source="isFieldExpanded(key) ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" size="xs" />
                                        </button>
                                    </div>
                                </template>
                                <!-- Badge pour booléen -->
                                <Badge v-else-if="typeof value === 'boolean'" 
                                       :color="value ? 'success' : 'error'" size="xs">
                                    {{ value ? 'Oui' : 'Non' }}
                                </Badge>
                                <!-- Tableau -->
                                <span v-else-if="Array.isArray(value)" class="text-primary-200">{{ value.length }} élément(s)</span>
                                <!-- Objet -->
                                <span v-else-if="typeof value === 'object'" class="text-primary-200 truncate block">{{ value.name || value.title || 'Objet' }}</span>
                                <!-- Autre -->
                                <span v-else class="text-primary-200 break-words">{{ value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

