<script setup>
/**
 * ClasseViewText — Vue Texte pour Classe
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → ClasseViewMinimal).
 *
 * @props {Classe} classe - Instance du modèle Classe
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ClasseViewMinimal from "./ClasseViewMinimal.vue";

defineProps({
  classe: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="classe"
    entity-prop="classe"
    :minimal-component="ClasseViewMinimal"
    fallback-icon="fa-solid fa-user-tie"
    image-fallback-field="icon"
  />
</template>
