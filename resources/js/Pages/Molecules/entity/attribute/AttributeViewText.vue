<script setup>
/**
 * AttributeViewText — Vue Texte pour Attribute
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → AttributeViewMinimal).
 *
 * @props {Attribute} attribute - Instance du modèle Attribute
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import AttributeViewMinimal from "./AttributeViewMinimal.vue";

defineProps({
  attribute: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="attribute"
    entity-prop="attribute"
    :minimal-component="AttributeViewMinimal"
    fallback-icon="fa-solid fa-star"
  />
</template>
