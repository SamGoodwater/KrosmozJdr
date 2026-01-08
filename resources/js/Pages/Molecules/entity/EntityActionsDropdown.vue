<script setup>
/**
 * EntityActionsDropdown Molecule
 *
 * @description
 * Menu dropdown d'actions pour une entité.
 * Utilisé dans les tableaux (colonne Actions) et comme menu contextuel.
 * Réutilise le composant Dropdown existant pour la cohérence.
 *
 * @example
 * <EntityActionsDropdown
 *   :actions="availableActions"
 *   :grouped-actions="groupedActions"
 *   display="icon-text"
 *   size="sm"
 *   placement="bottom-end"
 *   @action="handleAction"
 * />
 */
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { computed } from "vue";

const props = defineProps({
  /**
   * Liste des actions disponibles (format plat).
   * @type {Array<Object>}
   */
  actions: {
    type: Array,
    required: true,
    default: () => [],
  },
  /**
   * Actions groupées par groupe (pour séparateurs).
   * @type {Object}
   */
  groupedActions: {
    type: Object,
    default: () => ({}),
  },
  /**
   * Entité (pour afficher le nom en haut du menu).
   * @type {Object|null}
   */
  entity: {
    type: Object,
    default: null,
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
   * Position du dropdown (bottom-end, top-start, etc.)
   */
  placement: {
    type: String,
    default: "bottom-end",
  },
  /**
   * Afficher uniquement l'icône dans le trigger (pour colonne Actions)
   */
  iconOnlyTrigger: {
    type: Boolean,
    default: true,
  },
  /**
   * Variant du trigger (ghost, outline, etc.)
   */
  triggerVariant: {
    type: String,
    default: "ghost",
  },
});

const emit = defineEmits(["action"]);

const showIcon = computed(() => props.display === "icon-only" || props.display === "icon-text");
const showText = computed(() => props.display === "icon-text");

const handleAction = (actionKey) => {
  emit("action", actionKey);
};

/**
 * Retourne les groupes d'actions dans l'ordre, avec les actions non groupées à la fin.
 */
const orderedGroups = computed(() => {
  const groups = props.groupedActions;
  const groupKeys = Object.keys(groups);
  
  // Si on a des groupes, on les utilise
  if (groupKeys.length > 0) {
    return groupKeys;
  }
  
  // Sinon, on retourne un groupe unique avec toutes les actions
  return ["all"];
});

/**
 * Retourne les actions d'un groupe.
 */
const getGroupActions = (groupKey) => {
  if (groupKey === "all") {
    return props.actions;
  }
  return props.groupedActions[groupKey] || [];
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
</script>

<template>
  <Dropdown :placement="placement" :close-on-content-click="true">
    <template #trigger>
      <Btn
        :size="size"
        :variant="triggerVariant"
        :color="color"
        :class="iconOnlyTrigger ? 'btn-square' : ''"
        :title="iconOnlyTrigger ? 'Actions' : null"
      >
        <Icon source="fa-solid fa-ellipsis-vertical" :size="size" />
        <span v-if="!iconOnlyTrigger" class="ml-2">Actions</span>
      </Btn>
    </template>
    <template #content>
      <ul class="menu bg-base-100 rounded-box z-[1] w-56 p-2 shadow-lg border border-base-300">
        <!-- Nom de l'entité en haut (discret mais visible) -->
        <li v-if="showEntityName" class="px-3 py-2 mb-1 border-b border-base-300">
          <div class="text-xs text-base-content/60 font-medium truncate" :title="entityName">
            {{ entityName }}
          </div>
        </li>
        
        <template v-for="groupKey in orderedGroups" :key="groupKey">
          <li
            v-for="action in getGroupActions(groupKey)"
            :key="action.key"
            :class="{
              'text-error': action.variant === 'error',
            }"
          >
            <button
              @click="handleAction(action.key)"
              class="flex items-center gap-2 w-full"
              :class="{
                'text-error': action.variant === 'error',
              }"
              :title="action.tooltip || action.label"
            >
              <Icon v-if="showIcon" :source="action.icon" :alt="action.label" :size="size" />
              <span v-if="showText">{{ action.label }}</span>
              <span v-else-if="!showIcon">{{ action.label }}</span>
            </button>
          </li>
          
          <!-- Séparateur entre les groupes (sauf pour le dernier) -->
          <li
            v-if="
              groupKey !== orderedGroups[orderedGroups.length - 1] &&
              getGroupActions(orderedGroups[orderedGroups.indexOf(groupKey) + 1]).length > 0
            "
          >
            <hr class="my-1" />
          </li>
        </template>
      </ul>
    </template>
  </Dropdown>
</template>

