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
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

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

watch(
  () => props.settings,
  (s) => {
    if (!s) return;
    localSettings.value = {
      entity: s.entity ?? 'spells',
      filters: typeof s.filters === 'string' ? s.filters : (s.filters ? JSON.stringify(s.filters, null, 2) : '{}'),
      limit: s.limit ?? 50,
    };
  },
  { deep: true }
);

function persist() {
  let filtersValue = localSettings.value.filters;
  if (typeof filtersValue === 'string' && filtersValue.trim()) {
    try {
      filtersValue = JSON.parse(filtersValue);
    } catch {
      filtersValue = {};
    }
  } else {
    filtersValue = {};
  }
  const limit = Math.min(500, Math.max(1, Number(localSettings.value.limit) || 50));
  saveSection(props.section.id, {
    settings: {
      ...props.settings,
      entity: localSettings.value.entity,
      filters: filtersValue,
      limit,
    },
  });
  emit('data-updated');
}

watch(
  () => localSettings.value.entity,
  () => persist(),
  { immediate: false }
);

function onFiltersBlur() {
  persist();
}

function onLimitBlur() {
  const n = Math.min(500, Math.max(1, Number(localSettings.value.limit) || 50));
  localSettings.value.limit = n;
  persist();
}
</script>

<template>
  <div class="section-entity-table-edit space-y-4">
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
