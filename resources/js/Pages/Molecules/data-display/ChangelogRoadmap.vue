<script setup>
/**
 * Frise des versions du journal : déjà là, prochaine version, ensuite.
 *
 * @param {{ version: string, label: string, state: string, text: string }[]} steps
 */
defineProps({
  steps: { type: Array, default: () => [] },
});
</script>

<template>
  <section v-if="steps.length" aria-label="Où va le jeu">
    <h2 class="mb-4 text-xl font-semibold">La suite</h2>
    <ol class="m-0 flex list-none flex-col gap-6 p-0 md:flex-row">
      <li v-for="step in steps" :key="step.version" class="min-w-0 flex-1">
        <div v-if="step.state === 'done'" class="h-1 rounded-full bg-primary" aria-hidden="true" />
        <div v-else-if="step.state === 'next'" class="h-1 rounded-full bg-secondary" aria-hidden="true" />
        <div v-else class="h-1 rounded-full bg-base-content/25" aria-hidden="true" />

        <p class="mt-3 text-sm font-semibold">{{ step.version }}</p>
        <p v-if="step.state === 'done'" class="text-xs font-medium uppercase tracking-wide text-primary">
          {{ step.label }}
        </p>
        <p v-else-if="step.state === 'next'" class="text-xs font-medium uppercase tracking-wide text-secondary">
          {{ step.label }}
        </p>
        <p v-else class="text-xs font-medium uppercase tracking-wide text-base-content/60">
          {{ step.label }}
        </p>
        <p class="mt-2 text-sm leading-snug">{{ step.text }}</p>
      </li>
    </ol>
  </section>
</template>
