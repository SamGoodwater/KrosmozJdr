<script setup>
/**
 * BreedViewText — Vue Texte pour Breed
 *
 * @description
* Délègue l’implémentation à `EntityViewTextLink` (click-first → BreedViewMinimal).
 *
 * @props {Breed} breed - Instance du modèle Breed
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import BreedViewMinimal from "./BreedViewMinimal.vue";

const props = defineProps({
  breed: { type: Object, required: true },
});

const languages = computed(() => {
  const b = props.breed?._data ?? props.breed;
  const list = b?.languages;
  return Array.isArray(list) ? list : [];
});
</script>

<template>
  <div class="inline-flex flex-col items-start gap-1">
    <EntityViewTextLink
      :entity="breed"
      entity-prop="breed"
      :minimal-component="BreedViewMinimal"
      fallback-icon="fa-solid fa-user-tie"
      image-fallback-field="icon"
    />
    <EntityLanguagesInline v-if="languages.length" :languages="languages" />
  </div>
</template>
