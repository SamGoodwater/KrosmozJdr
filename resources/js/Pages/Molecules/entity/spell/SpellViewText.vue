<script setup>
/**
 * SpellViewText — Vue Texte pour Spell
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → SpellViewMinimal).
 *
 * @props {Spell} spell - Instance du modèle Spell
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import SpellViewMinimal from "./SpellViewMinimal.vue";

defineProps({
  spell: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="spell"
    entity-prop="spell"
    :minimal-component="SpellViewMinimal"
    fallback-icon="fa-solid fa-wand-magic-sparkles"
  />
</template>
