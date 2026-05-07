<script setup>
/**
 * ConditionViewText — Vue Texte pour Condition
 *
 * @description
* Délègue l’implémentation à `EntityViewTextLink` (click-first → ConditionViewMinimal).
 *
 * @props {Condition} condition - Instance du modèle Condition
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ConditionViewMinimal from "./ConditionViewMinimal.vue";

defineProps({
  condition: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="condition"
    entity-prop="condition"
    :minimal-component="ConditionViewMinimal"
    fallback-icon="fa-solid fa-star"
  />
</template>
