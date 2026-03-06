<script setup>
/**
 * SectionEntityTableEdit Template
 *
 * Édition des paramètres du tableau d'entités (type d'entité, filtres JSON, limite).
 * Les valeurs sont stockées dans section.settings et sauvegardées via useSectionSave.
 */
import { ref, watch, computed } from 'vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';
import { useTableFilterPresets } from '@/Composables/table/useTableFilterPresets';
import { TableConfig } from '@/Utils/Entity/Configs/TableConfig.js';
import { getEntityConfig } from '@/Entities/entity-registry';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();
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

const localSettings = ref({
  entity: props.settings?.entity ?? props.data?.entity ?? 'spells',
  filters: (() => {
    const raw = props.settings?.filters ?? props.data?.filters;
    if (typeof raw === 'string') return raw;
    if (typeof raw === 'object' && raw !== null) {
      try {
        return JSON.stringify(raw, null, 2);
      } catch {
        return '{}';
      }
    }
    return '{}';
  })(),
  limit: props.settings?.limit ?? props.data?.limit ?? 50,
});
const availablePresets = ref([]);
const selectedPresetId = ref('');
const presetsLoading = ref(false);
const filtersError = ref('');
const syncFromProps = ref(false);
const lastPersistSignature = ref('');
const saveState = ref('idle'); // idle | saving | saved | error
let saveStateTimer = null;

const setSaveState = (state) => {
  saveState.value = state;
  if (saveStateTimer) {
    clearTimeout(saveStateTimer);
    saveStateTimer = null;
  }
  if (state === 'saved') {
    saveStateTimer = setTimeout(() => {
      saveState.value = 'idle';
    }, 1600);
  }
};

const safeStringify = (value) => {
  try {
    return JSON.stringify(value);
  } catch {
    return '';
  }
};

const normalizeLimit = (value) => Math.min(500, Math.max(1, Number(value) || 50));

function normalizeSettingsForPersist(local = localSettings.value) {
  let filtersValue = local?.filters;
  let isValidJson = true;
  if (typeof filtersValue === 'string' && filtersValue.trim()) {
    try {
      filtersValue = JSON.parse(filtersValue);
    } catch {
      filtersValue = {};
      isValidJson = false;
    }
  } else {
    filtersValue = {};
  }

  return {
    normalized: {
      entity: String(local?.entity || 'spells'),
      filters: filtersValue,
      limit: normalizeLimit(local?.limit),
    },
    isValidJson,
  };
}

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

const currentTableId = computed(() => resolveTableIdForEntity(localSettings.value.entity) || '');

const presetOptions = computed(() => {
  const tableId = currentTableId.value;
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

watch(
  () => props.settings,
  (s) => {
    if (!s) return;
    syncFromProps.value = true;
    localSettings.value = {
      entity: s.entity ?? 'spells',
      filters: typeof s.filters === 'string' ? s.filters : (s.filters ? JSON.stringify(s.filters, null, 2) : '{}'),
      limit: s.limit ?? 50,
    };
    const { normalized } = normalizeSettingsForPersist(localSettings.value);
    lastPersistSignature.value = safeStringify(normalized);
    syncFromProps.value = false;
  },
  { deep: true, immediate: true }
);

function persist({ strictJson = false } = {}) {
  const sectionId = props.section?.id;
  if (!sectionId || syncFromProps.value) return;

  const { normalized, isValidJson } = normalizeSettingsForPersist(localSettings.value);
  if (!isValidJson) {
    filtersError.value = 'JSON invalide: corrige le format avant sauvegarde.';
    setSaveState('error');
    if (strictJson) return;
  } else {
    filtersError.value = '';
  }

  const signature = safeStringify(normalized);
  if (signature === lastPersistSignature.value) return;
  lastPersistSignature.value = signature;

  saveSection(sectionId, {
    settings: {
      ...props.settings,
      ...normalized,
    },
  }, {
    onQueued: () => setSaveState('saving'),
    onSuccess: () => setSaveState('saved'),
    onError: () => setSaveState('error'),
  });
  emit('data-updated');
}

async function loadPresetsForEntity(entityType) {
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

    if (!selectedPresetId.value) {
      const defaultPreset = presets.find((preset) => preset.isDefault);
      if (defaultPreset) {
        selectedPresetId.value = String(defaultPreset.id);
      }
    }
  } catch {
    availablePresets.value = [];
    selectedPresetId.value = '';
  } finally {
    presetsLoading.value = false;
  }
}

function applySelectedPreset() {
  const preset = availablePresets.value.find((p) => String(p.id) === String(selectedPresetId.value));
  if (!preset) return;

  try {
    localSettings.value.filters = JSON.stringify(preset.filters || {}, null, 2);
  } catch {
    localSettings.value.filters = '{}';
  }

  if (preset.limit && Number.isFinite(Number(preset.limit))) {
    localSettings.value.limit = Number(preset.limit);
  }

  persist({ strictJson: true });
}

watch(
  () => localSettings.value.entity,
  (nextEntity) => {
    if (syncFromProps.value) return;
    loadPresetsForEntity(nextEntity);
    persist({ strictJson: true });
  },
  { immediate: false }
);

watch(
  () => props.settings?.entity ?? props.data?.entity,
  (entityType) => {
    if (!entityType) return;
    loadPresetsForEntity(entityType);
  },
  { immediate: true }
);

function onFiltersBlur() {
  persist({ strictJson: true });
}

function onLimitBlur() {
  const n = normalizeLimit(localSettings.value.limit);
  localSettings.value.limit = n;
  persist({ strictJson: true });
}
</script>

<template>
  <div class="section-entity-table-edit space-y-4">
    <div class="flex justify-end">
      <InlineSaveStatus :state="saveState" />
    </div>
    <SelectField
      v-model="selectedPresetId"
      label="Preset de filtres"
      helper="Presets enregistrés pour cette table"
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
      v-model="localSettings.entity"
      label="Type d'entité"
      helper="Table d'entités à afficher"
      :options="entityOptions"
      @update:model-value="persist"
    />
    <TextareaField
      v-model="localSettings.filters"
      label="Filtres (JSON)"
      helper='Ex: {"level": "50", "state": "playable"}. Laisser vide ou {} pour aucun filtre.'
      placeholder="{}"
      :rows="4"
      @blur="onFiltersBlur"
    />
    <p v-if="filtersError" class="text-error text-xs">
      {{ filtersError }}
    </p>
    <InputField
      v-model.number="localSettings.limit"
      label="Nombre max d'entrées"
      type="number"
      :min="1"
      :max="500"
      helper="1 à 500"
      @blur="onLimitBlur"
    />
  </div>
</template>
