<script setup>
import { computed, markRaw } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";

const props = defineProps({
  entity: { type: Object, required: true },
  entityProp: { type: String, required: true },
  minimalComponent: { type: [Object, Function], required: true },
  fallbackIcon: { type: String, default: "fa-solid fa-circle" },
  nameField: { type: String, default: "name" },
  imageField: { type: String, default: "image" },
  imageFallbackField: { type: String, default: "" },
  uiColor: { type: String, default: "primary" },
  hoverWidthClass: { type: String, default: "max-w-[min(92vw,22rem)]" },
  hoverCardClass: { type: String, default: "" },
  /** Placement du « tooltip » (Floating UI). */
  placement: { type: String, default: "bottom-start" },
  showActionsOnHover: { type: Boolean, default: false },
  /**
   * Conservé pour compatibilité ; ignoré pour le rendu : l’aperçu minimal utilise toujours
   * `displayMode: 'extended'` sur la vue cible. Le survol sur le **nom** ouvre déjà le tooltip ;
   * évite d’exiger un second survol sur la carte (`hover`) pour voir le bloc « étendu ».
   * @deprecated Ne plus se fier à cette prop.
   */
  minimalDisplayMode: {
    type: String,
    default: "extended",
    validator: (v) => ["compact", "hover", "extended"].includes(v),
  },
  tableMeta: { type: Object, default: () => ({}) },
  characteristicRuntime: { type: Object, default: null },
});

const entityName = computed(() => props.entity?.name || props.entity?.title || "");
const imageSrc = computed(() => {
  const primary = props.entity?.[props.imageField] || null;
  if (primary) return primary;
  if (!props.imageFallbackField) return null;
  return props.entity?.[props.imageFallbackField] || null;
});

const nameCell = computed(() => {
  let cell;
  if (props.entity && typeof props.entity.toCell === "function") {
    cell = props.entity.toCell(props.nameField, { size: "sm", context: "text" });
  } else {
    cell = {
      type: "text",
      value: props.entity?.[props.nameField] ?? "",
      params: {},
    };
  }
  const params = { ...(cell.params || {}) };
  delete params.tooltip;
  delete params.truncate;
  delete params.truncateClass;
  // En mode overlay click-first, le trigger texte ne doit plus naviguer.
  delete params.href;
  delete params.target;
  return { ...cell, params };
});

const altClickHref = computed(() => {
  if (!props.entity || typeof props.entity.toCell !== "function") return "";
  const rawCell = props.entity.toCell(props.nameField, { size: "sm", context: "text" });
  const href = rawCell?.params?.href;
  return href ? String(href) : "";
});

function handleAltNavigation(event) {
  if (!event.altKey || !altClickHref.value) return;
  event.preventDefault();
  event.stopPropagation();
  window.location.assign(altClickHref.value);
}

const overlayContent = computed(() => ({
  component: markRaw(props.minimalComponent),
  props: {
    [props.entityProp]: props.entity,
    showActions: props.showActionsOnHover,
    displayMode: "extended",
    ...(Object.keys(props.tableMeta || {}).length > 0 ? { tableMeta: props.tableMeta } : {}),
    ...(props.characteristicRuntime != null ? { characteristicRuntime: props.characteristicRuntime } : {}),
    class: props.hoverCardClass,
  },
}));
</script>

<template>
  <div class="inline-flex min-w-0 items-center gap-2">
    <Image
      v-if="imageSrc"
      :src="imageSrc"
      :alt="entityName || 'Image'"
      fit="cover"
      rounded="sm"
      class="pointer-events-none h-4 w-4 shrink-0 overflow-hidden"
    />
    <Icon
      v-else
      :source="fallbackIcon"
      :alt="entityName"
      size="sm"
      class="pointer-events-none h-4 w-4 shrink-0 text-primary-300"
    />

    <OverlayTrigger
      :content="overlayContent"
      trigger="click"
      :placement="placement"
      max-width="auto"
      :interactive="true"
      :close-on-outside="true"
      :close-on-escape="true"
      :panel-class="hoverWidthClass"
      :focus-trap="false"
    >
      <span
        class="relative z-0 inline-block min-w-0 cursor-pointer rounded-sm transition-colors"
        tabindex="0"
        @mousedown.capture="handleAltNavigation"
        @click.capture="handleAltNavigation"
      >
        <CellRenderer :cell="nameCell" :ui-color="uiColor" />
      </span>
    </OverlayTrigger>
  </div>
</template>
