<script setup>
/**
 * CapabilityViewText — Vue Texte pour Capability
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → CapabilityViewMinimal).
 *
 * @props {Capability} capability - Instance du modèle Capability
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import CapabilityViewMinimal from "./CapabilityViewMinimal.vue";

defineProps({
  capability: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="capability"
    entity-prop="capability"
    :minimal-component="CapabilityViewMinimal"
    fallback-icon="fa-solid fa-magic"
  />
</template>
