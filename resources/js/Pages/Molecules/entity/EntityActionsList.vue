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
import EntityStateAction from "@/Pages/Molecules/entity/EntityStateAction.vue";
import { computed } from "vue";
import { useResolvedEntityActionState } from "@/Composables/entity/useResolvedEntityActionState";

const props = defineProps({
  entityType: {
    type: String,
    default: "",
  },
  entity: {
    type: Object,
    default: null,
  },
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
const sourceActions = computed(() => props.actions);
const { resolvedActions, runLocalAction } = useResolvedEntityActionState(
  computed(() => props.entityType),
  computed(() => props.entity),
  sourceActions,
);

const handleAction = (actionKey, _event) => {
  if (runLocalAction(actionKey)) {
    emit("action", actionKey);
    return;
  }
  // EntityActionButton émet (actionKey, event), on ne garde que actionKey
  emit("action", actionKey);
};
</script>

<template>
  <div class="flex items-center gap-2">
    <EntityStateAction
      v-for="action in resolvedActions.filter((item) => item?.key === 'state')"
      :key="action.key"
      :entity-type="entityType"
      :entity="entity"
      :action="action"
      :display="display"
      :size="size"
      @action="handleAction"
    />
    <EntityActionButton
      v-for="action in resolvedActions.filter((item) => item?.key !== 'state')"
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

