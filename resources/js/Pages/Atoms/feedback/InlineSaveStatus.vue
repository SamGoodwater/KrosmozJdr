<script setup>
/**
 * InlineSaveStatus Atom
 *
 * @description
 * Petit indicateur de statut de sauvegarde inline.
 * États supportés:
 * - idle: masqué
 * - saving: "Enregistrement..."
 * - saved: "Sauvegardé"
 * - error: "Erreur de sauvegarde"
 */
import { computed } from 'vue';

const props = defineProps({
  state: {
    type: String,
    default: 'idle',
    validator: (v) => ['idle', 'saving', 'saved', 'error'].includes(v),
  },
});

const label = computed(() => {
  if (props.state === 'saving') return 'Enregistrement...';
  if (props.state === 'saved') return 'Sauvegardé';
  if (props.state === 'error') return 'Erreur de sauvegarde';
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
