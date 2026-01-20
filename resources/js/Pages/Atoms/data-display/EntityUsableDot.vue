<script setup>
/**
 * EntityUsableDot — Indicateur discret "adapté au JDR"
 *
 * @description
 * Petit point coloré (success/error) avec tooltip.
 * Utilisable dans les tableaux, headers de vues, listes, etc.
 *
 * @props {boolean|null} usable - true/false si connu, null pour masquer
 * @props {string} yesLabel - Texte tooltip si usable=true
 * @props {string} noLabel - Texte tooltip si usable=false
 * @note
 * Le positionnement (absolute, offsets, etc.) se fait via un wrapper dans le parent
 * (ex: slot `dot` de `EntityViewHeader`).
 *
 * @example
 * <EntityUsableDot :usable="entity._data.usable" class="absolute -top-2 -left-2" />
 */
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { computed } from "vue";

const props = defineProps({
  usable: { type: [Boolean, null], default: null },
  yesLabel: { type: String, default: "Adapté au JDR" },
  noLabel: { type: String, default: "Non adapté au JDR" },
});

const tooltip = computed(() => {
  if (props.usable === true) return props.yesLabel;
  if (props.usable === false) return props.noLabel;
  return null;
});

const dotClass = computed(() => {
  if (props.usable === true) return "bg-success";
  if (props.usable === false) return "bg-error";
  return "bg-base-300";
});
</script>

<template>
  <Tooltip v-if="tooltip" :content="tooltip">
    <div class="w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90" :class="dotClass" />
  </Tooltip>
</template>

