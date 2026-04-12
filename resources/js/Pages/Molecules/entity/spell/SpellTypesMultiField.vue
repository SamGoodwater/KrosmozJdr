<script setup>
/**
 * Multi-sélection des types de sort (pivot) — `v-model` : tableau d'IDs.
 * Utilise SelectSearchField en mode multiple pour un affichage compact.
 */
import { computed } from 'vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import { resolveSpellTypeVisual } from '@/Utils/Entity/spellTypeVisual.js';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  label: { type: [String, Object], default: '' },
  helper: { type: [String, Object], default: '' },
  /** @type {Array<{ value: number, label: string, color?: string|null, icon?: string|null }>} */
  options: { type: Array, default: () => [] },
  disabled: { type: Boolean, default: false },
  validation: { type: [String, Object, Boolean], default: undefined },
});

const emit = defineEmits(['update:modelValue']);

const ids = computed(() =>
  Array.isArray(props.modelValue) ? props.modelValue.map((n) => Number(n)).filter((n) => Number.isFinite(n)) : [],
);

/** Options enrichies avec hex + iconUrl résolus via le thème visuel. */
const enrichedOptions = computed(() =>
  props.options.map((opt) => {
    const visual = resolveSpellTypeVisual(opt.label, opt.color, opt.icon);
    return {
      ...opt,
      hex: visual.hex,
      iconUrl: visual.iconUrl,
    };
  })
);

function onUpdate(val) {
  const arr = (Array.isArray(val) ? val : [val])
    .map((n) => Number(n))
    .filter((n) => Number.isFinite(n))
    .sort((a, b) => a - b);
  emit('update:modelValue', arr);
}
</script>

<template>
  <SelectSearchField
    :model-value="ids"
    @update:model-value="onUpdate"
    :label="label"
    :helper="helper"
    :options="enrichedOptions"
    :disabled="disabled"
    :validation="validation"
    :searchable="options.length > 8"
    multiple
    placeholder="Sélectionner les types…"
  />
</template>
