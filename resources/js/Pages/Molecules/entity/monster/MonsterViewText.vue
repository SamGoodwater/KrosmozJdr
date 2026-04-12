<script setup>
/**
 * MonsterViewText — Vue Texte pour Monster
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → MonsterViewMinimal).
 *
 * @props {Monster} monster - Instance du modèle Monster
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import MonsterViewMinimal from "./MonsterViewMinimal.vue";

defineProps({
  monster: { type: Object, required: true },
  tableMeta: { type: Object, default: () => ({}) },
  characteristicRuntime: { type: Object, default: null },
});
</script>

<template>
  <EntityViewTextLink
    :entity="monster"
    entity-prop="monster"
    :minimal-component="MonsterViewMinimal"
    fallback-icon="fa-solid fa-dragon"
    name-field="creature_name"
    :table-meta="tableMeta"
    :characteristic-runtime="characteristicRuntime"
  />
</template>
