<script setup>
/**
 * Phrases de rappel de règles sous une fiche d’entité.
 *
 * @example
 * <EntityRuleNotes :notes="['Même type d’effet : pas de cumul.']" />
 */
defineProps({
    notes: { type: Array, default: () => [] },
    notesClass: {
        type: String,
        default: "mt-1 text-xs leading-snug text-base-content/70",
    },
});
</script>

<template>
    <div
        v-if="notes.length"
        data-cy="entity-rule-notes"
        class="entity-rule-notes"
    >
        <p
            v-for="note in notes"
            :key="note"
            :class="notesClass"
        >
            {{ note }}
        </p>
    </div>
</template>
