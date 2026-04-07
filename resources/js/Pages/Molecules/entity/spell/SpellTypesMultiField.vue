<script setup>
/**
 * Multi-sélection des types de sort (pivot) — `v-model` : tableau d’IDs.
 */
import { computed } from 'vue';
import SpellTypeBadge from '@/Pages/Molecules/entity/spell/SpellTypeBadge.vue';

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

const ids = computed(() =>
  Array.isArray(props.modelValue) ? props.modelValue.map((n) => Number(n)).filter((n) => Number.isFinite(n)) : [],
);

function isOn(id) {
  return ids.value.includes(Number(id));
}

function toggle(id) {
  if (props.disabled) return;
  const n = Number(id);
  const set = new Set(ids.value);
  if (set.has(n)) set.delete(n);
  else set.add(n);
  emit('update:modelValue', [...set].sort((a, b) => a - b));
}

function chipClass(opt) {
  const on = isOn(opt.value);
  const base =
    'inline-flex min-w-0 items-center text-left transition-opacity';
  if (on) {
    return `${base} opacity-100`;
  }
  return `${base} opacity-55 hover:opacity-80`;
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
    <div v-if="!options.length" class="mt-2 text-xs text-base-content/60">
      Aucun type de sort disponible.
    </div>
    <div v-else class="mt-2 flex flex-wrap gap-2" role="group" :aria-label="labelText || 'Types de sort'">
      <button
        v-for="opt in options"
        :key="String(opt.value)"
        type="button"
        :class="[chipClass(opt), disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer']"
        :disabled="disabled"
        :aria-pressed="isOn(opt.value)"
        @click="toggle(opt.value)"
      >
        <SpellTypeBadge
          :name="String(opt.label)"
          :color="opt.color || null"
          :icon-hint="opt.icon ?? null"
          size="md"
        />
      </button>
    </div>
    <p v-if="hasError && validationMessage" class="mt-1.5 text-sm text-error">
      {{ validationMessage }}
    </p>
  </div>
</template>
