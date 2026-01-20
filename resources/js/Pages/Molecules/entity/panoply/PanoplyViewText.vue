<script setup>
/**
 * PanoplyViewText — Vue Texte pour Panoply
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → PanoplyViewMinimal).
 *
 * @props {Panoply} panoply - Instance du modèle Panoply
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import PanoplyViewMinimal from "./PanoplyViewMinimal.vue";

defineProps({
  panoply: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="panoply"
    entity-prop="panoply"
    :minimal-component="PanoplyViewMinimal"
    fallback-icon="fa-solid fa-boxes"
  />
</template>
