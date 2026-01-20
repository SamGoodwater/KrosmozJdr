<script setup>
/**
 * CreatureViewText — Vue Texte pour Creature
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → CreatureViewMinimal).
 *
 * @props {Creature} creature - Instance du modèle Creature
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import CreatureViewMinimal from "./CreatureViewMinimal.vue";

defineProps({
  creature: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="creature"
    entity-prop="creature"
    :minimal-component="CreatureViewMinimal"
    fallback-icon="fa-solid fa-dragon"
  />
</template>
