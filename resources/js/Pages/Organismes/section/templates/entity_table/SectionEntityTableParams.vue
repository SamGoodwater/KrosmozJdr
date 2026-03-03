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
