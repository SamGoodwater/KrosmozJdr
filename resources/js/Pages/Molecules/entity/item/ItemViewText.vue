<script setup>
/**
 * ItemViewText — Vue Texte pour Item
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ItemViewMinimal).
 *
 * @props {Item} item - Instance du modèle Item
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ItemViewMinimal from "./ItemViewMinimal.vue";

defineProps({
  item: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="item"
    entity-prop="item"
    :minimal-component="ItemViewMinimal"
    fallback-icon="fa-solid fa-box"
  />
</template>
