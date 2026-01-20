<script setup>
/**
 * ConsumableViewText — Vue Texte pour Consumable
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ConsumableViewMinimal).
 *
 * @props {Consumable} consumable - Instance du modèle Consumable
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ConsumableViewMinimal from "./ConsumableViewMinimal.vue";

defineProps({
  consumable: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="consumable"
    entity-prop="consumable"
    :minimal-component="ConsumableViewMinimal"
    fallback-icon="fa-solid fa-flask"
  />
</template>
