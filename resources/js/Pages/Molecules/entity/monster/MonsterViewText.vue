<script setup>
/**
 * MonsterViewText — Vue Texte pour Monster
 *
 * @description
* Délègue l’implémentation à `EntityViewTextLink` (click-first → MonsterViewMinimal).
 *
 * @props {Monster} monster - Instance du modèle Monster
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import MonsterViewMinimal from "./MonsterViewMinimal.vue";

const props = defineProps({
  monster: { type: Object, required: true },
  tableMeta: { type: Object, default: () => ({}) },
  characteristicRuntime: { type: Object, default: null },
});

const languages = computed(() => {
  const m = props.monster?._data ?? props.monster;
  const list = m?.languages;
  return Array.isArray(list) ? list : [];
});
</script>

<template>
  <div class="inline-flex flex-col items-start gap-1">
    <EntityViewTextLink
      :entity="monster"
      entity-prop="monster"
      :minimal-component="MonsterViewMinimal"
      fallback-icon="fa-solid fa-dragon"
      name-field="creature_name"
      :table-meta="tableMeta"
      :characteristic-runtime="characteristicRuntime"
    />
    <EntityLanguagesInline v-if="languages.length" :languages="languages" />
  </div>
</template>
