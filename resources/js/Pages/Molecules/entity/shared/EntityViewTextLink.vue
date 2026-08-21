<script setup>
import { computed, markRaw } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import { fetchEntityModelById } from "@/Composables/entity/useEntityTableFetch";

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
  /**
   * Affiche le menu d’options sur la vue minimale ouverte (dropdown).
   * Défaut vrai : on ouvre déjà une fiche, les actions doivent rester accessibles.
   */
  showActionsOnHover: { type: Boolean, default: true },
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
  /**
   * Type d’entité API (`spells`, `items`…) : hydrate la fiche complète au clic
   * (payload tableau allégé → vue minimale avec effets).
   */
  hydrateType: { type: String, default: "" },
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

function entityAlreadyHasEffects(entity) {
  if (!entity) return false;
  const chips =
    entity.effectUsagesChips ??
    entity._data?.effect_usages_chips ??
    entity.effect_usages_chips;
  if (Array.isArray(chips) && chips.length > 0) return true;
  const defs =
    entity.effectsDefinitions ??
    entity._data?.effects_definitions ??
    entity.effects_definitions;
  return Array.isArray(defs) && defs.length > 0;
}

function buildMinimalProps(entity) {
  return {
    [props.entityProp]: entity,
    showActions: props.showActionsOnHover,
    displayMode: "extended",
    ...(Object.keys(props.tableMeta || {}).length > 0 ? { tableMeta: props.tableMeta } : {}),
    ...(props.characteristicRuntime != null ? { characteristicRuntime: props.characteristicRuntime } : {}),
    class: props.hoverCardClass,
  };
}

const overlayContent = computed(() => {
  const hydrateType = String(props.hydrateType || "").trim();
  const entityId = props.entity?.id;
  if (hydrateType && entityId && !entityAlreadyHasEffects(props.entity)) {
    return {
      key: `hydrate:${hydrateType}:${entityId}`,
      loader: async () => {
        let entity = props.entity;
        try {
          const full = await fetchEntityModelById(hydrateType, entityId);
          if (full) entity = full;
        } catch {
          /* payload déjà affiché / allégé en repli */
        }
        return {
          component: markRaw(props.minimalComponent),
          props: buildMinimalProps(entity),
        };
      },
    };
  }
  return {
    component: markRaw(props.minimalComponent),
    props: buildMinimalProps(props.entity),
  };
});
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
