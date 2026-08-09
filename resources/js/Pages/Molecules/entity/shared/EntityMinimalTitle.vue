<script setup>
/**
 * EntityMinimalTitle — Titre cliquable des cartes Minimal (ouvre la modal, pas la page).
 *
 * @example
 * <EntityMinimalTitle :label="name" @open="openQuickView" />
 */
defineProps({
    label: { type: [String, Number], default: "—" },
});

defineEmits(["open"]);
</script>

<template>
    <button
        type="button"
        class="entity-minimal-title font-semibold truncate block text-sm text-left text-base-content w-full hover:text-primary"
        @click.stop="$emit('open')"
    >
        {{ label }}
    </button>
</template>
