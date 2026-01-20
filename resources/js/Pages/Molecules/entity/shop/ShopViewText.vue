<script setup>
/**
 * ShopViewText — Vue Texte pour Shop
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ShopViewMinimal).
 *
 * @props {Shop} shop - Instance du modèle Shop
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ShopViewMinimal from "./ShopViewMinimal.vue";

defineProps({
  shop: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="shop"
    entity-prop="shop"
    :minimal-component="ShopViewMinimal"
    fallback-icon="fa-solid fa-store"
  />
</template>
