<script setup>
/**
 * Multi-sélection des 5 éléments primaires (Neutre → Eau) avec icônes / couleurs.
 * `v-model` : `null` = aucun élément ; sinon masque 1–127 (combinaison de primaires).
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import {
  getElementPrimaries,
  primariesToElementValue,
  SPELL_PRIMARY_ELEMENT_OPTIONS,
  ELEMENT_PRIMARY_ICONS,
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

const labelText = computed(() => (typeof props.label === 'string' ? props.label : ''));
const helperText = computed(() => (typeof props.helper === 'string' ? props.helper : ''));

const validationMessage = computed(() => {
  const v = props.validation;
  if (!v || typeof v !== 'object') return '';
  return v.message || v.text || '';
});
const hasError = computed(() => {
  const v = props.validation;
  if (!v || typeof v !== 'object') return false;
  return v.state === 'error' || Boolean(v.message);
});

/** Valeur encodée ou `null` si aucun lien élémentaire. */
const encoded = computed(() => {
  const v = props.modelValue;
  if (v === null || v === undefined || v === '') return null;
  const mask = normalizeElementStorageValue(v);
  return mask === 0 ? null : mask;
});

const isNone = computed(() => encoded.value === null);

const selectedPrimaries = computed(() => getElementPrimaries(encoded.value));

function isSelected(primaryIndex) {
  return selectedPrimaries.value.includes(primaryIndex);
}

function noneChipClass() {
  const on = isNone.value;
  const base =
    'inline-flex min-w-0 items-center gap-2 rounded-lg border px-2.5 py-1.5 text-left text-sm transition-colors';
  const active = on
    ? 'border-base-content/40 bg-base-300/25 shadow-[inset_0_0_0_1px_rgba(255,255,255,0.12)]'
    : 'border-base-300/60 hover:border-base-300 hover:bg-base-200/30';
  return [base, active, props.disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'].join(' ');
}

/** États visuels (bordure + léger fond) par primaire. */
function chipClass(primaryIndex) {
  const on = isSelected(primaryIndex);
  const map = {
    0: on ? 'border-slate-400 bg-slate-500/20 shadow-[inset_0_0_0_1px_rgba(148,163,184,0.45)]' : 'border-base-300/70',
    1: on ? 'border-amber-600 bg-amber-900/30 shadow-[inset_0_0_0_1px_rgba(217,119,6,0.45)]' : 'border-base-300/70',
    2: on ? 'border-red-500 bg-red-900/25 shadow-[inset_0_0_0_1px_rgba(239,68,68,0.4)]' : 'border-base-300/70',
    3: on ? 'border-emerald-500 bg-emerald-900/25 shadow-[inset_0_0_0_1px_rgba(16,185,129,0.4)]' : 'border-base-300/70',
    4: on ? 'border-blue-500 bg-blue-900/25 shadow-[inset_0_0_0_1px_rgba(59,130,246,0.4)]' : 'border-base-300/70',
    5: on ? 'border-violet-500 bg-violet-900/25 shadow-[inset_0_0_0_1px_rgba(139,92,246,0.4)]' : 'border-base-300/70',
    6: on ? 'border-lime-500 bg-lime-900/20 shadow-[inset_0_0_0_1px_rgba(132,204,22,0.45)]' : 'border-base-300/70',
  };
  const base =
    'inline-flex min-w-0 items-center gap-2 rounded-lg border px-2.5 py-1.5 text-left text-sm transition-colors';
  const off = 'border-base-300/60 hover:border-base-300 hover:bg-base-200/30';
  return [base, on ? map[primaryIndex] ?? map[0] : off, props.disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'].join(
    ' ',
  );
}

function selectNone() {
  if (props.disabled) return;
  emit('update:modelValue', null);
}

function toggle(primaryIndex) {
  if (props.disabled) return;
  const set = new Set(getElementPrimaries(encoded.value));
  if (set.has(primaryIndex)) {
    set.delete(primaryIndex);
  } else {
    set.add(primaryIndex);
  }
  const next = [...set].sort((a, b) => a - b);
  if (next.length === 0) {
    emit('update:modelValue', null);
    return;
  }
  emit('update:modelValue', primariesToElementValue(next));
}
</script>

<template>
  <div class="w-full">
    <div v-if="labelText" class="label py-0">
      <span class="label-text text-sm font-medium">{{ labelText }}</span>
    </div>
    <p v-if="helperText" class="mt-0.5 text-xs leading-snug text-base-content/70">
      {{ helperText }}
    </p>
    <div
      class="mt-2 flex flex-wrap gap-2"
      role="group"
      :aria-label="labelText || 'Éléments du sort'"
    >
      <button
        type="button"
        :class="noneChipClass()"
        :disabled="disabled"
        :aria-pressed="isNone"
        @click="selectNone"
      >
        <Icon source="fa-solid fa-ban" alt="Aucun" size="sm" class="shrink-0 opacity-80" />
        <span class="truncate font-medium leading-tight">Aucun</span>
      </button>
      <button
        v-for="opt in SPELL_PRIMARY_ELEMENT_OPTIONS"
        :key="opt.value"
        type="button"
        :class="chipClass(opt.value)"
        :disabled="disabled"
        :aria-pressed="isSelected(opt.value)"
        @click="toggle(opt.value)"
      >
        <Icon :source="ELEMENT_PRIMARY_ICONS[opt.value]" :alt="opt.label" size="sm" class="shrink-0" />
        <span class="truncate font-medium leading-tight">{{ opt.label }}</span>
      </button>
    </div>
    <p v-if="hasError && validationMessage" class="mt-1.5 text-sm text-error">
      {{ validationMessage }}
    </p>
  </div>
</template>
