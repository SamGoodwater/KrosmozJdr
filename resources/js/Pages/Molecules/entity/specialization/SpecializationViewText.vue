<script setup>
/**
 * SpecializationViewText — Vue Texte pour Specialization
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → SpecializationViewMinimal).
 *
 * @props {Specialization} specialization - Instance du modèle Specialization
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import SpecializationViewMinimal from "./SpecializationViewMinimal.vue";

defineProps({
  specialization: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="specialization"
    entity-prop="specialization"
    :minimal-component="SpecializationViewMinimal"
    fallback-icon="fa-solid fa-graduation-cap"
  />
</template>
