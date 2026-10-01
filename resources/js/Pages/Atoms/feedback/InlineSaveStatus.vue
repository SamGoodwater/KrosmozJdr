<script setup>
/**
 * InlineSaveStatus Atom
 *
 * @description
 * Petit indicateur de statut de sauvegarde inline.
 * États supportés:
 * - idle: masqué
 * - saving: "Enregistrement…"
 * - saved: "Enregistré"
 * - error: "Échec de l’enregistrement"
 */
import { computed } from 'vue';
import { ACTION } from '@/Utils/atomic-design/actionLabels';

const props = defineProps({
  state: {
    type: String,
    default: 'idle',
    validator: (v) => ['idle', 'saving', 'saved', 'error'].includes(v),
  },
});

const label = computed(() => {
  if (props.state === 'saving') return ACTION.save.processing;
  if (props.state === 'saved') return 'Enregistré';
  if (props.state === 'error') return 'Échec de l’enregistrement';
  return '';
});

const classes = computed(() => {
  if (props.state === 'saving') return 'text-xs text-base-content/60';
  if (props.state === 'saved') return 'text-xs text-success';
  if (props.state === 'error') return 'text-xs text-error';
  return 'hidden';
});
</script>

<template>
  <span v-if="state !== 'idle'" :class="classes">{{ label }}</span>
</template>
