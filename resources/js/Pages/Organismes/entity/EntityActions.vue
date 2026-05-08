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
import EntityActionMenuList from "@/Pages/Molecules/entity/EntityActionMenuList.vue";

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
   * Clés d’actions affichées en raccourcis à côté du menu (si `showInlineShortcuts` et assez de place).
   * Ordre conservé. Par défaut : épingler, lien, page, édition rapide.
   */
  inlineActionKeys: {
    type: Array,
    default: () => ["pin", "favorite", "copy-link", "view", "edit", "quick-view", "quick-edit"],
  },
  /** Activer les raccourcis inline (désactiver ex. barre d’outils déjà chargée). */
  showInlineShortcuts: {
    type: Boolean,
    default: true,
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
  "close", // Menu contextuel : fermeture (Échap ou parent)
  "view",
  "quick-view",
  "edit",
  "quick-edit",
  "expand",
  "copy-link",
  "favorite",
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

/**
 * Récupère le nom de l'entité en gérant les modèles et objets bruts.
 */
const getEntityName = () => {
  if (!props.entity) return null;
  
  // Si c'est une instance de modèle, utiliser le getter name
  if (props.entity && typeof props.entity._data !== 'undefined') {
    return props.entity.name || props.entity.title || null;
  }
  // Sinon, accès direct
  return props.entity?.name || props.entity?.title || null;
};

const entityName = computed(() => getEntityName());
const showEntityName = computed(() => Boolean(entityName.value));

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
    :entity-type="entityType"
    :entity="entity"
    :actions="availableActions"
    :display="display"
    :size="size"
    :color="color"
    @action="handleAction"
  />

  <!-- Format : dropdown (colonne Actions) -->
  <EntityActionsDropdown
    v-else-if="format === 'dropdown'"
    :entity-type="entityType"
    :actions="availableActions"
    :grouped-actions="groupedActions"
    :entity="entity"
    :display="display"
    :size="size"
    :color="color"
    :placement="placement"
    :icon-only-trigger="true"
    :inline-action-keys="inlineActionKeys"
    :show-inline-shortcuts="showInlineShortcuts"
    @action="handleAction"
  />

  <!-- Format : menu contextuel (clic droit) -->
  <Transition v-else-if="format === 'context'" name="entity-context-menu">
    <div
      v-if="contextVisible"
      :style="contextMenuStyle"
      class="dropdown dropdown-open pointer-events-auto"
      @pointerdown.stop
      @mousedown.stop
      @mouseup.stop
      @click.stop
      @contextmenu.prevent.stop
    >
      <EntityActionMenuList
        :entity-type="entityType"
        :entity="entity"
        :actions="availableActions"
        :grouped-actions="groupedActions"
        :display="display"
        :size="size"
        :entity-name="showEntityName ? entityName : ''"
        :focus-on-mount="contextVisible"
        menu-class="dropdown-content menu bg-base-100 rounded-box pointer-events-auto z-[9999] w-56 p-2 shadow-xl border border-base-300 outline-none origin-top-left"
        @action="handleAction"
        @close="emit('close')"
      />
    </div>
  </Transition>
</template>

<style scoped>
.entity-context-menu-enter-active,
.entity-context-menu-leave-active {
  transition: opacity 0.16s ease, transform 0.16s ease;
}
.entity-context-menu-enter-from,
.entity-context-menu-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
.entity-context-menu-enter-to,
.entity-context-menu-leave-from {
  opacity: 1;
  transform: scale(1);
}
</style>

