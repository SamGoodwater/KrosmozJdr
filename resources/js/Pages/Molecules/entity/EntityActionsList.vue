<script setup>
/**
 * EntityActionsList Molecule
 *
 * @description
 * Liste horizontale de boutons d'actions pour une entité.
 * Utilisé dans les vues entités (Compact, Minimal, Large).
 *
 * @example
 * <EntityActionsList
 *   :actions="availableActions"
 *   display="icon-only"
 *   size="sm"
 *   @action="handleAction"
 * />
 */
import EntityActionButton from "@/Pages/Atoms/action/EntityActionButton.vue";

const props = defineProps({
  /**
   * Liste des actions disponibles.
   * @type {Array<Object>}
   */
  actions: {
    type: Array,
    required: true,
    default: () => [],
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
   * Taille des boutons (xs, sm, md, lg)
   */
  size: {
    type: String,
    default: "sm",
  },
  /**
   * Couleur des boutons (primary, secondary, etc.)
   */
  color: {
    type: String,
    default: "primary",
  },
  /**
   * Variant par défaut des boutons (ghost, outline, etc.)
   */
  variant: {
    type: String,
    default: "ghost",
  },
});

const emit = defineEmits(["action"]);

const handleAction = (actionKey, event) => {
  // EntityActionButton émet (actionKey, event), on ne garde que actionKey
  emit("action", actionKey);
};
</script>

<template>
  <div class="flex items-center gap-2">
    <EntityActionButton
      v-for="action in actions"
      :key="action.key"
      :action="action"
      :display="display"
      :size="size"
      :color="color"
      :variant="variant"
      @click="handleAction"
    />
  </div>
</template>

