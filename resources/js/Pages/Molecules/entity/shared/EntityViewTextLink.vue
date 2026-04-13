<script setup>
/**
 * EntityViewTextLink — Vue Texte générique (inline) avec hover vers une Vue Minimal
 *
 * @description
 * Molecule réutilisable pour implémenter la "Vue Texte" officielle :
 * - Affiche une miniature (image si dispo sinon icône) + le nom de l'entité.
 * - La Vue Minimal ne s’affiche qu’au survol (ou focus) sur le **texte du nom** (vue texte),
 *   pas sur l’icône / la vignette. L’ouverture est pilotée en **état Vue** (pas `group-hover` Tailwind)
 *   pour éviter les conflits avec un ancêtre `.group` (ligne tableau, carte, etc.).
 * - L’aperçu est positionné comme un **tooltip** (Floating UI, `strategy: fixed`) **sans**
 *   `Teleport` vers `body` : le panneau reste dans l’arbre DOM sous la ligne / carte, ce qui
 *   **conserve le survol** (`:hover`) sur l’item parent quand la souris est sur l’aperçu.
 * - Les `params.tooltip` / troncature issus de `toCell()` sont retirés pour éviter le **Tooltip**
 *   atomique de `CellRenderer` (redondant avec la vue minimal).
 *
 * @props {Object} entity - Instance de modèle (ou objet) exposant idéalement `toCell()`
 * @props {string} entityProp - Nom de la prop attendue par la vue minimal (ex: 'resource', 'item')
 * @props {any} minimalComponent - Composant Vue de la Vue Minimal à afficher au hover
 * @props {string} fallbackIcon - Icône FontAwesome si pas d'image
 * @props {string} nameField - Champ pour le nom (default: 'name')
 * @props {string} imageField - Champ pour l'image (default: 'image')
 * @props {string} uiColor - Couleur UI pour le rendu du nom
 * @props {string} hoverWidthClass - Largeur max du panneau (ex. `max-w-[min(92vw,22rem)]`)
 * @props {string} hoverCardClass - Classes appliquées à la vue minimal
 * @props {string} placement - Placement Floating UI (défaut: `bottom-start`)
 * @props {boolean} showActionsOnHover - Si la vue minimal doit afficher les actions
 */
import { computed, onUnmounted, ref, watch } from "vue";
import { useFloating, offset, flip, shift, autoUpdate } from "@floating-ui/vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import { allocateTooltipZIndex } from "@/Composables/ui/allocateTooltipZIndex";

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
  return { ...cell, params };
});

const minimalBind = computed(() => ({
  [props.entityProp]: props.entity,
  showActions: props.showActionsOnHover,
  /** Toujours extended : le tooltip remplace le « survol sur la carte minimal » du contexte liste. */
  displayMode: "extended",
  ...(Object.keys(props.tableMeta || {}).length > 0 ? { tableMeta: props.tableMeta } : {}),
  ...(props.characteristicRuntime != null
    ? { characteristicRuntime: props.characteristicRuntime }
    : {}),
}));

const previewOpen = ref(false);
const triggerRef = ref(null);
const floatingRef = ref(null);

const floatingPlacement = computed(() => props.placement);

const { floatingStyles } = useFloating(triggerRef, floatingRef, {
  open: previewOpen,
  placement: floatingPlacement,
  strategy: "fixed",
  middleware: [offset(10), flip({ padding: 8 }), shift({ padding: 8 })],
  whileElementsMounted: autoUpdate,
});

const stackZIndex = ref(1100);
watch(
  () => previewOpen.value,
  (open, wasOpen) => {
    if (open && wasOpen !== true) {
      stackZIndex.value = allocateTooltipZIndex();
    }
  },
  { immediate: true },
);

const floatingStylesWithZ = computed(() => ({
  ...floatingStyles.value,
  zIndex: stackZIndex.value,
}));

/** Ouverture quasi immédiate ; fermeture retardée pour rejoindre le panneau sans clignoter. */
const OPEN_DELAY_MS = 0;
const CLOSE_DELAY_MS = 180;

let openTimer = null;
let closeTimer = null;

function clearTimers() {
  if (openTimer) {
    clearTimeout(openTimer);
    openTimer = null;
  }
  if (closeTimer) {
    clearTimeout(closeTimer);
    closeTimer = null;
  }
}

function scheduleOpen() {
  clearTimers();
  openTimer = setTimeout(() => {
    openTimer = null;
    previewOpen.value = true;
  }, OPEN_DELAY_MS);
}

function scheduleClose() {
  clearTimers();
  closeTimer = setTimeout(() => {
    closeTimer = null;
    previewOpen.value = false;
  }, CLOSE_DELAY_MS);
}

function onTriggerEnter() {
  clearTimers();
  scheduleOpen();
}

function onFloatingEnter() {
  clearTimers();
  previewOpen.value = true;
}

function onFloatingLeave() {
  scheduleClose();
}

/** Sortie du bloc texte + tooltip : fermeture retardée (souvent annulée si entrée dans le panneau). */
function onPreviewShellLeave() {
  scheduleClose();
}

/** @param {FocusEvent} e */
function onTriggerFocusIn() {
  clearTimers();
  previewOpen.value = true;
}

/** @param {FocusEvent} e */
function onTriggerFocusOut(e) {
  const next = e.relatedTarget;
  if (
    floatingRef.value &&
    next instanceof Node &&
    floatingRef.value.contains(next)
  ) {
    return;
  }
  scheduleClose();
}

onUnmounted(clearTimers);
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

    <!-- Enveloppe texte + tooltip : un seul mouseleave → survol parent conservé tant qu’on est sur l’un ou l’autre -->
    <div
      class="relative inline-block min-w-0 pb-2 text-left"
      @mouseleave="onPreviewShellLeave"
    >
      <span
        ref="triggerRef"
        class="relative z-0 inline-block min-w-0 cursor-pointer rounded-sm transition-colors"
        @mouseenter="onTriggerEnter"
        @focusin="onTriggerFocusIn"
        @focusout="onTriggerFocusOut"
      >
        <CellRenderer :cell="nameCell" :ui-color="uiColor" />
      </span>

      <div
        v-show="previewOpen"
        ref="floatingRef"
        role="tooltip"
        class="tooltip-floating-surface color-neutral pointer-events-auto flex min-w-[12rem] flex-col overflow-hidden p-0 shadow-2xl"
        :class="hoverWidthClass"
        :style="floatingStylesWithZ"
        @mouseenter="onFloatingEnter"
        @mouseleave="onFloatingLeave"
      >
        <div class="max-h-[min(70vh,520px)] overflow-y-auto overflow-x-hidden p-1">
          <component :is="minimalComponent" v-bind="minimalBind" :class="hoverCardClass" />
        </div>
      </div>
    </div>
  </div>
</template>
