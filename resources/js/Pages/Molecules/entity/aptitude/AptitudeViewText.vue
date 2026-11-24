<script setup>
/**
 * AptitudeViewText — Vue Texte pour Aptitude
 *
 * @description
* Délègue l’implémentation à `EntityViewTextLink` (click-first → AptitudeViewMinimal).
 *
 * @props {Aptitude} aptitude - Instance du modèle Aptitude
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import AptitudeViewMinimal from "./AptitudeViewMinimal.vue";

defineProps({
  aptitude: { type: Object, default: null },
  entity: { type: Object, default: null },
});
</script>

<template>
  <EntityViewTextLink
    :entity="aptitude || entity"
    entity-prop="aptitude"
    :minimal-component="AptitudeViewMinimal"
    fallback-icon="fa-solid fa-sparkles"
  />
</template>
