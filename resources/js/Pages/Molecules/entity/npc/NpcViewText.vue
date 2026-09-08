<script setup>
/**
 * NpcViewText — Vue Texte pour NPC
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (click-first → NpcViewMinimal).
 * Langues en ligne sous le nom, comme pour les monstres.
 *
 * @props {Npc} npc - Instance du modèle NPC
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import NpcViewMinimal from "./NpcViewMinimal.vue";

const props = defineProps({
  npc: { type: Object, required: true },
  tableMeta: { type: Object, default: () => ({}) },
  characteristicRuntime: { type: Object, default: null },
});

const languages = computed(() => {
  const m = props.npc?._data ?? props.npc;
  const list = m?.languages;
  return Array.isArray(list) ? list : [];
});
</script>

<template>
  <div class="inline-flex flex-col items-start gap-1">
    <EntityViewTextLink
      :entity="npc"
      entity-prop="npc"
      :minimal-component="NpcViewMinimal"
      fallback-icon="fa-solid fa-user"
      name-field="creature_name"
      :table-meta="tableMeta"
      :characteristic-runtime="characteristicRuntime"
    />
    <EntityLanguagesInline v-if="languages.length" :languages="languages" />
  </div>
</template>
