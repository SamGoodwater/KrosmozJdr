<script setup>
/**
 * ResourceViewText — Vue Texte pour Resource
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ResourceViewMinimal).
 *
 * @props {Resource} resource - Instance du modèle Resource
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ResourceViewMinimal from "./ResourceViewMinimal.vue";

defineProps({
  resource: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="resource"
    entity-prop="resource"
    :minimal-component="ResourceViewMinimal"
    fallback-icon="fa-solid fa-gem"
  />
</template>
