<script setup>
/**
 * CreatureTraitViewText — Vue Texte pour CreatureTrait
 *
 * @description
* Délègue l’implémentation à `EntityViewTextLink` (click-first → CreatureTraitViewMinimal).
 *
 * @props {CreatureTrait} creatureTrait - Instance du modèle CreatureTrait
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import CreatureTraitViewMinimal from "./CreatureTraitViewMinimal.vue";

defineProps({
  creatureTrait: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="creatureTrait"
    entity-prop="creatureTrait"
    :minimal-component="CreatureTraitViewMinimal"
    fallback-icon="fa-solid fa-star"
  />
</template>
