<script setup>
/**
 * Compat : l’ancien panneau DofusDB ouvre le modal unique Sources (DofusDB | IA).
 */
import EntitySourceModal from "@/Pages/Molecules/entity/EntitySourceModal.vue";

defineOptions({ inheritAttrs: false });
</script>

<template>
    <EntitySourceModal v-bind="$attrs" />
</template>
