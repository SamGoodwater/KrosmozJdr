<script setup>
/**
 * Grille responsive pour les containers d’édition d’entité.
 *
 * @example
 * <EntityEditGrid>
 *   <EntityEditContainer title="…" />
 * </EntityEditGrid>
 */
defineProps({
    /** Classes additionnelles sur la grille. */
    rootClass: { type: String, default: '' },
});
</script>

<template>
    <div
        class="entity-edit-grid grid gap-3 grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3"
        :class="rootClass"
        data-cy="entity-edit-grid"
    >
        <slot />
    </div>
</template>
