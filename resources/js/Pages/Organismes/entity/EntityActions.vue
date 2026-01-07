/**
 * EntityActions Organism
 *
 * @description
 * Composant flexible pour afficher les actions d'une entité.
 * Supporte différents formats : boutons, dropdown, menu contextuel.
 * Utilise useEntityActions pour la logique métier et les permissions.
 *
 * @example
 * Format buttons: entity-type="spells" :entity="entity" format="buttons" display="icon-only"
 * Format dropdown: entity-type="spells" :entity="row.entity" format="dropdown" display="icon-text"
 * Format context: entity-type="spells" :entity="contextEntity" format="context" display="icon-text" :context="{ inPanel: false }"
 */

<script setup>
import { computed } from "vue";
import { useEntityActions } from "@/Composables/entity/useEntityActions";
import EntityActionsList from "@/Pages/Molecules/entity/EntityActionsList.vue";
import EntityActionsDropdown from "@/Pages/Molecules/entity/EntityActionsDropdown.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
  /**
   * Type d'entité (ex: 'spells', 'items')
   */
  entityType: {
    type: String,
    required: true,
  },
  /**
   * Entité (peut être null pour certaines actions comme minimize)
   */
  entity: {
    type: Object,
    default: null,
  },
  /**
   * Format d'affichage : 'buttons' (liste horizontale), 'dropdown' (menu dropdown), 'context' (menu contextuel)
   */
  format: {
    type: String,
    default: "dropdown",
    validator: (v) => ["buttons", "dropdown", "context"].includes(v),
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
   * Whitelist d'actions à inclure uniquement
   */
  whitelist: {
    type: Array,
    default: null,
  },
  /**
   * Blacklist d'actions à exclure
   */
  blacklist: {
    type: Array,
    default: null,
  },
  /**
   * Contexte supplémentaire (ex: { inPanel: true } pour minimize)
   */
  context: {
    type: Object,
    default: () => ({}),
  },
  /**
   * Options UI
   */
  size: {
    type: String,
    default: "sm",
  },
  color: {
    type: String,
    default: "primary",
  },
  /**
   * Position du dropdown (pour format 'dropdown' ou 'context')
   */
  placement: {
    type: String,
    default: "bottom-end",
  },
  /**
   * Pour le format 'context' : position fixe (x, y)
   */
  contextPosition: {
    type: Object,
    default: null,
  },
  /**
   * Pour le format 'context' : visible ou non
   */
  contextVisible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "action", // Émis pour chaque action (actionKey, entity)
  "view",
  "quick-view",
  "edit",
  "quick-edit",
  "copy-link",
  "download-pdf",
  "refresh",
  "minimize",
  "delete",
]);

const { availableActions, groupedActions } = useEntityActions(
  props.entityType,
  props.entity,
  {
    whitelist: props.whitelist,
    blacklist: props.blacklist,
    context: props.context,
  }
);

const handleAction = (actionKey) => {
  emit("action", actionKey, props.entity);
  // Émettre aussi l'événement spécifique pour compatibilité
  emit(actionKey, props.entity);
};

// Pour le menu contextuel, on utilise un Dropdown positionné de manière absolue
const contextMenuStyle = computed(() => {
  if (props.format !== "context" || !props.contextPosition) {
    return {};
  }
  return {
    position: "fixed",
    left: `${props.contextPosition.x}px`,
    top: `${props.contextPosition.y}px`,
    zIndex: 9999,
  };
});
</script>

<template>
  <!-- Format : liste de boutons -->
  <EntityActionsList
    v-if="format === 'buttons'"
    :actions="availableActions"
    :display="display"
    :size="size"
    :color="color"
    @action="handleAction"
  />

  <!-- Format : dropdown (colonne Actions) -->
  <EntityActionsDropdown
    v-else-if="format === 'dropdown'"
    :actions="availableActions"
    :grouped-actions="groupedActions"
    :display="display"
    :size="size"
    :color="color"
    :placement="placement"
    :icon-only-trigger="true"
    @action="handleAction"
  />

  <!-- Format : menu contextuel (clic droit) -->
  <div v-else-if="format === 'context' && contextVisible" :style="contextMenuStyle" class="dropdown dropdown-open">
    <ul
      tabindex="0"
      class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-56 p-2 shadow-lg border border-base-300"
    >
      <template v-for="(groupActions, groupKey) in groupedActions" :key="groupKey">
        <li
          v-for="action in groupActions"
          :key="action.key"
          :class="{
            'text-error': action.variant === 'error',
          }"
        >
          <button
            @click="handleAction(action.key)"
            :class="[
              'flex items-center gap-2 w-full',
              { 'text-error': action.variant === 'error' }
            ]"
          >
            <Icon
              v-if="display === 'icon-only' || display === 'icon-text'"
              :source="action.icon"
              :alt="action.label"
              :size="size"
            />
            <span v-if="display === 'icon-text' || display === 'text'">{{ action.label }}</span>
            <span v-else-if="display === 'icon-only' && !action.icon">{{ action.label }}</span>
          </button>
        </li>

        <!-- Séparateur entre les groupes (sauf pour le dernier) -->
        <li
          v-if="
            groupKey !== Object.keys(groupedActions)[Object.keys(groupedActions).length - 1] &&
            groupedActions[Object.keys(groupedActions)[Object.keys(groupedActions).indexOf(groupKey) + 1]]?.length > 0
          "
        >
          <hr class="my-1" />
        </li>
      </template>
    </ul>
  </div>
</template>

