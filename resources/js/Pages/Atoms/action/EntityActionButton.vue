<script setup>
/**
 * EntityActionButton Atom
 *
 * @description
 * Bouton d'action unique pour une entité (icône seule ou icône + texte).
 * Pour `view-dofusdb`, ouvre aussi le store Pinia directement (chemin court).
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { computed } from "vue";
import { colorList, variantList } from "@/Pages/Atoms/atomMap";
import { useDofusDbReferenceStore } from "@/Composables/store/useDofusDbReferenceStore";

const BTN_COLOR_TOKENS = new Set(colorList.filter(Boolean));
const BTN_VARIANT_TOKENS = new Set([...variantList, "link"]);

const props = defineProps({
  action: {
    type: Object,
    required: true,
  },
  /**
   * Type d’entité (pluriel) — requis pour ouvrir DofusDB depuis le bouton.
   */
  entityType: {
    type: String,
    default: "",
  },
  entity: {
    type: Object,
    default: null,
  },
  display: {
    type: String,
    default: "icon-text",
    validator: (v) => ["icon-only", "icon-text"].includes(v),
  },
  size: {
    type: String,
    default: "sm",
  },
  color: {
    type: String,
    default: "primary",
  },
  variant: {
    type: String,
    default: "ghost",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["click"]);

const dofusDbStore = useDofusDbReferenceStore();

const showIcon = computed(() => props.display === "icon-only" || props.display === "icon-text");
const showText = computed(() => props.display === "icon-text");
const isDofusDbAction = computed(() => props.action?.key === "view-dofusdb");

/**
 * Style du bouton : ignore une couleur passée à tort dans `action.variant`.
 *
 * @returns {string}
 */
const buttonVariant = computed(() => {
  const raw = props.action?.variant || props.variant;
  if (BTN_COLOR_TOKENS.has(raw) && !BTN_VARIANT_TOKENS.has(raw)) {
    return props.variant;
  }
  return raw;
});

/**
 * Couleur : `action.color` prioritaire, puis legacy `action.variant` couleur, puis prop.
 *
 * @returns {string}
 */
const buttonColor = computed(() => {
  if (props.action?.color && BTN_COLOR_TOKENS.has(props.action.color)) {
    return props.action.color;
  }
  const rawVariant = props.action?.variant;
  if (rawVariant && BTN_COLOR_TOKENS.has(rawVariant) && !BTN_VARIANT_TOKENS.has(rawVariant)) {
    return rawVariant;
  }
  return props.color;
});

const handleClick = (event) => {
  if (props.disabled) return;

  // Chemin court : ne dépend pas de la remontée d’events EntityActions.
  if (isDofusDbAction.value) {
    dofusDbStore.openPanel(props.entityType, props.entity);
  }

  emit("click", props.action.key, event);
};
</script>

<template>
  <!-- Pas de Tooltip sur DofusDB : évite OverlayTrigger autour du bouton. -->
  <Btn
    v-if="isDofusDbAction"
    type="button"
    :size="size"
    :variant="buttonVariant"
    :color="buttonColor"
    :disabled="disabled"
    class="gap-2"
    :aria-label="action.tooltip || action.label"
    data-testid="entity-action-view-dofusdb"
    @click="handleClick"
  >
    <Icon
      v-if="showIcon"
      :source="action.icon"
      :alt="action.label || 'DofusDB'"
      :size="size"
    />
    <span v-if="showText">{{ action.label }}</span>
  </Btn>

  <Tooltip
    v-else
    :content="action.tooltip || action.label"
    :disabled="showText"
    placement="top"
  >
    <Btn
      type="button"
      :size="size"
      :variant="buttonVariant"
      :color="buttonColor"
      :disabled="disabled"
      class="gap-2"
      :class="{ 'entity-action-button--active text-primary!': action.active }"
      @click="handleClick"
    >
      <Icon
        v-if="showIcon"
        :source="action.icon"
        :alt="action.label || 'Action'"
        :size="size"
        :class="{ 'entity-action-button__icon--active': action.active }"
      />
      <span v-if="showText">{{ action.label }}</span>
      <span v-if="action.badge" class="badge badge-sm badge-primary">{{ action.badge }}</span>
    </Btn>
  </Tooltip>
</template>

<style scoped>
.entity-action-button--active {
  text-shadow: 0 0 8px color-mix(in srgb, var(--color-primary, #60a5fa) 70%, transparent);
}

.entity-action-button__icon--active {
  filter: drop-shadow(0 0 6px color-mix(in srgb, var(--color-primary, #60a5fa) 78%, transparent));
}
</style>
