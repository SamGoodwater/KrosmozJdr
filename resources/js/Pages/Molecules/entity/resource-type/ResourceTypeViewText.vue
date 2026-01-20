<script setup>
/**
 * ResourceTypeViewText — Vue Texte pour ResourceType
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ResourceTypeViewMinimal).
 *
 * @props {ResourceType} resourceType - Instance du modèle ResourceType
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ResourceTypeViewMinimal from "./ResourceTypeViewMinimal.vue";

defineProps({
  resourceType: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="resourceType"
    entity-prop="resourceType"
    :minimal-component="ResourceTypeViewMinimal"
    fallback-icon="fa-solid fa-tag"
  />
</template>
