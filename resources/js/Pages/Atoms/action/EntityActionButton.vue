<script setup>
/**
 * EntityActionButton Atom
 *
 * @description
 * Bouton d'action unique pour une entité (icône seule ou icône + texte).
 * Utilisé dans les listes d'actions et les menus.
 *
 * @example
 * <EntityActionButton
 *   :action="{ key: 'view', label: 'Ouvrir', icon: 'fa-solid fa-eye' }"
 *   display="icon-text"
 *   size="sm"
 *   @click="handleView"
 * />
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { computed } from "vue";

const props = defineProps({
  /**
   * Configuration de l'action.
   * @type {Object}
   * @property {string} key - Identifiant de l'action
   * @property {string} label - Label affiché
   * @property {string} icon - Icône Font Awesome
   * @property {string} [variant] - Variant du bouton (ex: 'error' pour delete)
   */
  action: {
    type: Object,
    required: true,
  },
  /**
   * Mode d'affichage : 'icon-only' ou 'icon-text'
   */
  display: {
    type: String,
    default: "icon-text",
    validator: (v) => ["icon-only", "icon-text"].includes(v),
  },
  /**
   * Taille du bouton (xs, sm, md, lg)
   */
  size: {
    type: String,
    default: "sm",
  },
  /**
   * Couleur du bouton (primary, secondary, error, etc.)
   */
  color: {
    type: String,
    default: "primary",
  },
  /**
   * Variant du bouton (ghost, outline, solid, etc.)
   */
  variant: {
    type: String,
    default: "ghost",
  },
  /**
   * Désactiver le bouton
   */
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["click"]);

const showIcon = computed(() => props.display === "icon-only" || props.display === "icon-text");
const showText = computed(() => props.display === "icon-text");

const buttonVariant = computed(() => props.action.variant || props.variant);
const buttonColor = computed(() => {
  if (buttonVariant.value === "error") return "error";
  return props.color;
});

const handleClick = (event) => {
  if (!props.disabled) {
    emit("click", props.action.key, event);
  }
};
</script>

<template>
  <Tooltip
    :content="action.tooltip || action.label"
    :disabled="showText"
    placement="top"
  >
    <Btn
      :size="size"
      :variant="buttonVariant"
      :color="buttonColor"
      :disabled="disabled"
      class="gap-2"
      @click="handleClick"
    >
      <Icon v-if="showIcon" :source="action.icon" :alt="action.label" :size="size" />
      <span v-if="showText">{{ action.label }}</span>
    </Btn>
  </Tooltip>
</template>

