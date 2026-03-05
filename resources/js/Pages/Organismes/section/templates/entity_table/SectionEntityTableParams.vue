<script setup>
/**
 * Vue paramètres dédiée au template entity_table.
 *
 * Utilisée dans SectionParamsModal (édition) et CreateSectionModal (création).
 * Émet update:settings avec { entity, filters, limit } pour que le parent fusionne dans formData.
 */
import { ref, watch, computed } from 'vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import { useTableFilterPresets } from '@/Composables/table/useTableFilterPresets';
import { TableConfig } from '@/Utils/Entity/Configs/TableConfig.js';
import { getEntityConfig } from '@/Entities/entity-registry';

const props = defineProps({
  /** Section existante (null en mode création) */
  section: { type: Object, default: null },
  /** Settings actuels (slice entity_table) */
  settings: {
    type: Object,
    default: () => ({ entity: 'spells', filters: {}, limit: 50 }),
  },
  /** create | edit */
  mode: { type: String, default: 'edit' },
});

const emit = defineEmits(['update:settings']);
const { listPresets } = useTableFilterPresets();

const entityOptions = [
  { value: 'spells', label: 'Sorts' },
  { value: 'monsters', label: 'Monstres' },
  { value: 'npcs', label: 'NPCs' },
  { value: 'campaigns', label: 'Campagnes' },
  { value: 'scenarios', label: 'Scénarios' },
  { value: 'shops', label: 'Boutiques' },
  { value: 'breeds', label: 'Classes' },
  { value: 'specializations', label: 'Spécialisations' },
  { value: 'attributes', label: 'Attributs' },
  { value: 'capabilities', label: 'Capacités' },
  { value: 'consumables', label: 'Consommables' },
  { value: 'items', label: 'Objets' },
  { value: 'resources', label: 'Ressources' },
  { value: 'panoplies', label: 'Panoplies' },
];

const availablePresets = ref([]);
const selectedPresetId = ref('');
const presetsLoading = ref(false);

function resolveTableIdForEntity(entityType) {
  const normalized = String(entityType || '').trim();
  if (!normalized) return null;

  const entityConfig = getEntityConfig(normalized);
  if (!entityConfig || typeof entityConfig.getDescriptors !== 'function') return null;

  const ctx = {
    capabilities: {
      viewAny: true,
      createAny: false,
      updateAny: false,
      deleteAny: false,
      manageAny: false,
    },
  };

  const descriptors = entityConfig.getDescriptors(ctx);
  const config = TableConfig.fromDescriptors(descriptors, ctx);
  const built = config.build(ctx);
  return built?.id ? String(built.id) : null;
}

function normSettings(s) {
  const raw = s ?? {};
  let filters = raw.filters;
  if (typeof filters === 'string') {
    try {
      filters = filters.trim() ? JSON.parse(filters) : {};
    } catch {
      filters = {};
    }
  } else if (typeof filters !== 'object' || filters === null) {
    filters = {};
  }
  const limit = Math.min(500, Math.max(1, Number(raw.limit) || 50));
  return {
    entity: raw.entity ?? 'spells',
    filters,
    limit,
  };
}

const local = ref({
  entity: normSettings(props.settings).entity,
  filtersJson: (() => {
    const f = normSettings(props.settings).filters;
    try {
      return JSON.stringify(f, null, 2);
    } catch {
      return '{}';
    }
  })(),
  limit: normSettings(props.settings).limit,
});

const presetOptions = computed(() => {
  const tableId = resolveTableIdForEntity(local.value.entity);
  return [
    { value: '', label: 'Aucun preset' },
    ...availablePresets.value.map((preset) => {
      const scopeLabel = tableId ? ` (${tableId})` : '';
      const defaultLabel = preset.isDefault ? ' ★' : '';
      return {
        value: String(preset.id),
        label: `${preset.name}${defaultLabel}${scopeLabel}`,
      };
    }),
  ];
});

const currentTableId = computed(() => resolveTableIdForEntity(local.value.entity) || '');

watch(
  () => props.settings,
  (s) => {
    const n = normSettings(s);
    local.value.entity = n.entity;
    local.value.limit = n.limit;
    try {
      local.value.filtersJson = JSON.stringify(n.filters, null, 2);
    } catch {
      local.value.filtersJson = '{}';
    }
  },
  { deep: true }
);

async function loadPresetsForEntity(entityType) {
  if (!entityType) {
    availablePresets.value = [];
    selectedPresetId.value = '';
    return;
  }
  const tableId = resolveTableIdForEntity(entityType);
  if (!tableId) {
    availablePresets.value = [];
    selectedPresetId.value = '';
    return;
  }

  presetsLoading.value = true;
  try {
    const presets = await listPresets({
      entityType,
      tableId,
      includeGlobal: false,
    });
    availablePresets.value = presets;
    if (presets.length > 0 && !selectedPresetId.value) {
      const defaultPreset = presets.find((p) => p.isDefault);
      if (defaultPreset) {
        selectedPresetId.value = String(defaultPreset.id);
        const filters = defaultPreset.filters || {};
        local.value.filtersJson = JSON.stringify(filters, null, 2);
        if (defaultPreset.limit && Number.isFinite(Number(defaultPreset.limit))) {
          local.value.limit = Number(defaultPreset.limit);
        }
        emitUpdate();
      }
    }
  } catch {
    availablePresets.value = [];
  } finally {
    presetsLoading.value = false;
  }
}

watch(
  () => local.value.entity,
  (nextEntity) => {
    loadPresetsForEntity(nextEntity);
  },
  { immediate: true }
);

function applySelectedPreset() {
  const preset = availablePresets.value.find((p) => String(p.id) === String(selectedPresetId.value));
  if (!preset) return;

  local.value.filtersJson = JSON.stringify(preset.filters || {}, null, 2);
  if (preset.limit && Number.isFinite(Number(preset.limit))) {
    local.value.limit = Number(preset.limit);
  }
  emitUpdate();
}

function emitUpdate() {
  let filters = {};
  if (local.value.filtersJson && local.value.filtersJson.trim()) {
    try {
      filters = JSON.parse(local.value.filtersJson);
    } catch {
      filters = {};
    }
  }
  const limit = Math.min(500, Math.max(1, Number(local.value.limit) || 50));
  emit('update:settings', {
    entity: local.value.entity,
    filters,
    limit,
  });
}

watch(
  () => [local.value.entity, local.value.limit],
  () => emitUpdate(),
  { deep: true }
);

function onFiltersBlur() {
  emitUpdate();
}
</script>

<template>
  <div class="section-entity-table-params space-y-4">
    <SelectField
      v-model="selectedPresetId"
      label="Preset de filtres"
      helper="Affiche tous les presets enregistrés pour cette table et permet de les réappliquer"
      :options="presetOptions"
      @update:model-value="applySelectedPreset"
    />
    <p v-if="currentTableId" class="text-xs text-base-content/60">
      Table ciblée: <code>{{ currentTableId }}</code>
    </p>
    <p v-if="presetsLoading" class="text-xs text-base-content/60">Chargement des presets...</p>
    <p
      v-else-if="currentTableId && availablePresets.length === 0"
      class="text-xs text-warning"
    >
      Aucun preset enregistré pour cette table.
    </p>
    <SelectField
      v-model="local.entity"
      label="Type d'entité"
      helper="Table d'entités à afficher (sorts, monstres, campagnes, etc.)"
      :options="entityOptions"
      @update:model-value="emitUpdate"
    />
    <TextareaField
      v-model="local.filtersJson"
      label="Filtres (JSON)"
      helper='Ex: {"level": "50", "state": "playable"}. Laisser vide ou {} pour aucun filtre.'
      placeholder="{}"
      :rows="4"
      @blur="onFiltersBlur"
    />
    <InputField
      v-model.number="local.limit"
      label="Nombre max d'entrées"
      type="number"
      :min="1"
      :max="500"
      helper="1 à 500"
      @blur="emitUpdate"
    />
  </div>
</template>
