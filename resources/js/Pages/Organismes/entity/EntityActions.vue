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
import { computed, ref, watch, nextTick } from "vue";
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
  "close", // Menu contextuel : fermeture (Échap ou parent)
  "view",
  "quick-view",
  "edit",
  "quick-edit",
  "expand",
  "copy-link",
  "download-pdf",
  "refresh",
  "minimize",
  "delete",
]);

/** @type {import('vue').Ref<HTMLElement | null>} */
const contextMenuListRef = ref(null);

function getContextMenuButtons() {
    const root = contextMenuListRef.value;
    if (!root) return [];
    return [...root.querySelectorAll("li > button")];
}

function focusFirstContextMenuItem() {
    nextTick(() => {
        getContextMenuButtons()[0]?.focus?.();
    });
}

watch(
    () => props.contextVisible && props.format === "context",
    (on) => {
        if (on) {
            focusFirstContextMenuItem();
        }
    },
);

/**
 * Navigation clavier dans le menu contextuel + Échap → fermeture.
 * @param {KeyboardEvent} e
 */
function onContextMenuKeydown(e) {
    if (props.format !== "context" || !props.contextVisible) return;
    const buttons = getContextMenuButtons();
    if (!buttons.length) return;

    const key = e.key;
    let idx = buttons.indexOf(/** @type {HTMLElement} */ (document.activeElement));

    if (key === "Escape") {
        e.preventDefault();
        e.stopPropagation();
        emit("close");
        return;
    }
    if (key === "ArrowDown") {
        e.preventDefault();
        const next = idx < 0 ? 0 : Math.min(buttons.length - 1, idx + 1);
        buttons[next]?.focus?.();
        return;
    }
    if (key === "ArrowUp") {
        e.preventDefault();
        const next = idx <= 0 ? buttons.length - 1 : idx - 1;
        buttons[next]?.focus?.();
        return;
    }
    if (key === "Home") {
        e.preventDefault();
        buttons[0]?.focus?.();
        return;
    }
    if (key === "End") {
        e.preventDefault();
        buttons[buttons.length - 1]?.focus?.();
    }
}

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
    :entity="entity"
    :display="display"
    :size="size"
    :color="color"
    :placement="placement"
    :icon-only-trigger="true"
    @action="handleAction"
  />

  <!-- Format : menu contextuel (clic droit) -->
  <Transition v-else-if="format === 'context'" name="entity-context-menu">
    <div
      v-if="contextVisible"
      :style="contextMenuStyle"
      class="dropdown dropdown-open"
    >
      <ul
        ref="contextMenuListRef"
        tabindex="-1"
        role="menu"
        class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-56 p-2 shadow-xl border border-base-300 outline-none origin-top-left"
        @keydown="onContextMenuKeydown"
      >
        <!-- Nom de l'entité en haut (discret mais visible) -->
        <li v-if="showEntityName" class="px-3 py-2 mb-1 border-b border-base-300" aria-hidden="true">
          <div class="text-xs text-base-content/60 font-medium truncate" :title="entityName">
            {{ entityName }}
          </div>
        </li>

        <template v-for="(groupActions, groupKey) in groupedActions" :key="groupKey">
          <li
            v-for="action in groupActions"
            :key="action.key"
            :class="{
              'text-error': action.variant === 'error',
            }"
          >
            <button
              type="button"
              role="menuitem"
              class="entity-actions-context-item flex items-center gap-2 w-full text-left px-2 py-1.5 rounded-lg transition-[transform,background-color,box-shadow] duration-150 ease-out outline-none hover:bg-base-200/90 focus-visible:bg-primary/12 focus-visible:ring-2 focus-visible:ring-primary/45 focus-visible:scale-[1.02] motion-reduce:transition-none motion-reduce:focus-visible:scale-100"
              :class="{ 'text-error': action.variant === 'error' }"
              @click="handleAction(action.key)"
              :title="action.tooltip || action.label"
            >
              <Icon
                v-if="display === 'icon-only' || display === 'icon-text'"
                :source="action.icon"
                :alt="action.label"
                :size="size"
              />
              <span v-if="display === 'icon-text' || display === 'text'">{{ action.label }}</span>
              <span v-else-if="display === 'icon-only' && !action.icon">{{ action.label }}</span>
              <span v-if="action.badge" class="badge badge-sm badge-primary ml-auto">{{ action.badge }}</span>
            </button>
          </li>

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

