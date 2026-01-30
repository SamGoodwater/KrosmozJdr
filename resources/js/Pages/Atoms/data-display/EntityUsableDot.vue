<script setup>
/**
 * EntityUsableDot — Indicateur discret d'état (`state`)
 *
 * @description
 * Petit point coloré (success/warning/neutral) avec tooltip.
 * Utilisable dans les tableaux, headers de vues, listes, etc.
 *
 * @props {string|null} state - raw|draft|playable|archived (ou null)
 * @props {string} playableLabel - Texte tooltip si state=playable
 * @props {string} draftLabel - Texte tooltip si state=draft
 * @props {string} rawLabel - Texte tooltip si state=raw
 * @props {string} archivedLabel - Texte tooltip si state=archived
 * @note
 * Le positionnement (absolute, offsets, etc.) se fait via un wrapper dans le parent
 * (ex: slot `dot` de `EntityViewHeader`).
 *
 * @example
 * <EntityUsableDot :state="entity._data.state" class="absolute -top-2 -left-2" />
 */
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { computed } from "vue";

const props = defineProps({
  state: { type: [String, null], default: null },
  playableLabel: { type: String, default: "Jouable" },
  draftLabel: { type: String, default: "Brouillon" },
  rawLabel: { type: String, default: "Brut" },
  archivedLabel: { type: String, default: "Archivé" },
});

const tooltip = computed(() => {
  switch (props.state) {
    case "playable":
      return props.playableLabel;
    case "draft":
      return props.draftLabel;
    case "raw":
      return props.rawLabel;
    case "archived":
      return props.archivedLabel;
    default:
      return null;
  }
});

const dotClass = computed(() => {
  switch (props.state) {
    case "playable":
      return "bg-success";
    case "draft":
      return "bg-warning";
    case "raw":
      return "bg-error";
    case "archived":
      return "bg-info";
    default:
      return "bg-base-300";
  }
});
</script>

<template>
  <Tooltip v-if="tooltip" :content="tooltip">
    <div class="w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90" :class="dotClass" />
  </Tooltip>
</template>

