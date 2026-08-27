<script setup>
/**
 * EntityStateBadge — pastille d'état entité (raw / draft / auto / playable / archived).
 *
 * @description
 * Badge coloré (variant soft par défaut), libellé métier optionnel, tooltip optionnel.
 * Pour un point seul + tooltip sans texte, préférer {@link EntityUsableDot}.
 *
 * @props {string|null} state
 * @props {string} size - xs | sm | md (Badge)
 * @props {string} variant - Badge (ex. soft)
 * @props {boolean} showLabel - Afficher le libellé dans le badge (défaut true)
 * @props {boolean|string} tooltip - false : pas de tooltip ; true : libellé état ; string : texte libre
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
  getEntityStateBadgeColor,
  getEntityStateDisplayLabel,
} from "@/Utils/Entity/SharedConstants";

const props = defineProps({
  state: { type: [String, null], default: null },
  size: {
    type: String,
    default: "xs",
    validator: (v) => ["xs", "sm", "md"].includes(v),
  },
  variant: { type: String, default: "soft" },
  showLabel: { type: Boolean, default: true },
  /** false | true (libellé état) | chaîne personnalisée */
  tooltip: { type: [Boolean, String], default: true },
});

const color = computed(() => getEntityStateBadgeColor(props.state));
const label = computed(() => getEntityStateDisplayLabel(props.state));

const tooltipContent = computed(() => {
  if (props.tooltip === false) {
    return "";
  }
  if (typeof props.tooltip === "string" && props.tooltip.length > 0) {
    return props.tooltip;
  }
  return label.value !== "—" ? label.value : "";
});
</script>

<template>
  <Tooltip
    :content="tooltipContent"
    :disabled="!tooltipContent"
    placement="top"
    class="inline-flex max-w-full min-w-0"
  >
    <Badge :color="color" :size="size" :variant="variant" class="inline-flex max-w-full min-w-0 font-medium">
      <span v-if="showLabel" class="truncate">{{ label }}</span>
      <span v-else class="sr-only">{{ label }}</span>
    </Badge>
  </Tooltip>
</template>
