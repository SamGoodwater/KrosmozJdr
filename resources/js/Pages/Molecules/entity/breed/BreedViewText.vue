<script setup>
/**
 * BreedViewText — Vue Texte pour Breed
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → BreedViewMinimal).
 *
 * @props {Breed} breed - Instance du modèle Breed
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import BreedViewMinimal from "./BreedViewMinimal.vue";

defineProps({
  breed: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="breed"
    entity-prop="breed"
    :minimal-component="BreedViewMinimal"
    fallback-icon="fa-solid fa-user-tie"
    image-fallback-field="icon"
  />
</template>
