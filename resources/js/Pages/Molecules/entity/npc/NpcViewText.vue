<script setup>
/**
 * NpcViewText — Vue Texte pour NPC
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → NpcViewMinimal).
 *
 * @props {Npc} npc - Instance du modèle NPC
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import NpcViewMinimal from "./NpcViewMinimal.vue";

defineProps({
  npc: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="npc"
    entity-prop="npc"
    :minimal-component="NpcViewMinimal"
    fallback-icon="fa-solid fa-user"
    name-field="creature_name"
  />
</template>
