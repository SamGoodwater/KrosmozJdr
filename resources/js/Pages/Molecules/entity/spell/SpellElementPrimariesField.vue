<script setup>
/**
 * Multi-sélection des éléments primaires (Neutre → Vitalité) avec SelectSearchField.
 * `v-model` : `null` = aucun élément ; sinon masque 1–127 (combinaison de primaires).
 */
import { computed } from 'vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import {
  getElementPrimaries,
  primariesToElementValue,
  SPELL_PRIMARY_ELEMENT_OPTIONS,
  ELEMENT_PRIMARY_ICONS,
  ELEMENT_PRIMARY_CSS_VARS,
  normalizeElementStorageValue,
} from '@/Utils/Entity/Elements';

const props = defineProps({
  modelValue: { type: [Number, String, null], default: null },
  label: { type: [String, Object], default: '' },
  helper: { type: [String, Object], default: '' },
  disabled: { type: Boolean, default: false },
  validation: { type: [String, Object, Boolean], default: undefined },
});

const emit = defineEmits(['update:modelValue']);

/** Options enrichies avec icône et couleur CSS pour chaque élément. */
const enrichedOptions = computed(() =>
  SPELL_PRIMARY_ELEMENT_OPTIONS.map((opt) => ({
    ...opt,
    iconUrl: ELEMENT_PRIMARY_ICONS[opt.value] || null,
    color: ELEMENT_PRIMARY_CSS_VARS[opt.value] || null,
  }))
);

/** Array of primary indices currently selected (e.g. [0, 2, 4]). */
const selectedPrimaries = computed(() => {
  const v = props.modelValue;
  if (v === null || v === undefined || v === '') return [];
  const mask = normalizeElementStorageValue(v);
  return mask === 0 ? [] : getElementPrimaries(mask);
});

function onUpdate(val) {
  const arr = (Array.isArray(val) ? val : [])
    .map((n) => Number(n))
    .filter((n) => Number.isFinite(n) && n >= 0 && n <= 6);
  if (arr.length === 0) {
    emit('update:modelValue', null);
    return;
  }
  emit('update:modelValue', primariesToElementValue(arr));
}
</script>

<template>
  <SelectSearchField
    :model-value="selectedPrimaries"
    @update:model-value="onUpdate"
    :label="label"
    :helper="helper"
    :options="enrichedOptions"
    :disabled="disabled"
    :validation="validation"
    :searchable="false"
    multiple
    placeholder="Sélectionner les éléments…"
  />
</template>
