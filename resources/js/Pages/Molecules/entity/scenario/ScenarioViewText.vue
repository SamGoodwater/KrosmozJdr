<script setup>
/**
 * ScenarioViewText — Vue Texte pour Scenario
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ScenarioViewMinimal).
 *
 * @props {Scenario} scenario - Instance du modèle Scenario
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ScenarioViewMinimal from "./ScenarioViewMinimal.vue";

defineProps({
  scenario: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="scenario"
    entity-prop="scenario"
    :minimal-component="ScenarioViewMinimal"
    fallback-icon="fa-solid fa-scroll"
  />
</template>
