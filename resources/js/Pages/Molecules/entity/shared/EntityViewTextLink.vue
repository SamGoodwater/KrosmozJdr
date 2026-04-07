<script setup>
/**
 * EntityViewTextLink — Vue Texte générique (inline) avec hover vers une Vue Minimal
 *
 * @description
 * Molecule réutilisable pour implémenter la "Vue Texte" officielle :
 * - Affiche une miniature (image si dispo sinon icône) + le nom de l'entité.
 * - Au survol, affiche une Vue Minimal (via composant passé en prop).
 *
 * @props {Object} entity - Instance de modèle (ou objet) exposant idéalement `toCell()`
 * @props {string} entityProp - Nom de la prop attendue par la vue minimal (ex: 'resource', 'item')
 * @props {any} minimalComponent - Composant Vue de la Vue Minimal à afficher au hover
 * @props {string} fallbackIcon - Icône FontAwesome si pas d'image
 * @props {string} nameField - Champ pour le nom (default: 'name')
 * @props {string} imageField - Champ pour l'image (default: 'image')
 * @props {string} uiColor - Couleur UI pour le rendu du nom
 * @props {string} hoverWidthClass - Largeur du panneau hover (ex: 'w-64')
 * @props {string} hoverCardClass - Classes appliquées à la card hover (border/shadow)
 * @props {boolean} showActionsOnHover - Si la vue minimal doit afficher les actions
 *
 * @example
 * <EntityViewTextLink
 *   :entity="resource"
 *   entity-prop="resource"
 *   :minimal-component="ResourceViewMinimal"
 *   fallback-icon="fa-solid fa-gem"
 * />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";

const props = defineProps({
  entity: { type: Object, required: true },
  entityProp: { type: String, required: true },
  minimalComponent: { type: [Object, Function], required: true },
  fallbackIcon: { type: String, default: "fa-solid fa-circle" },
  nameField: { type: String, default: "name" },
  imageField: { type: String, default: "image" },
  imageFallbackField: { type: String, default: "" },
  uiColor: { type: String, default: "primary" },
  hoverWidthClass: { type: String, default: "w-64" },
  hoverCardClass: { type: String, default: "shadow-xl border-2 border-primary/20" },
  showActionsOnHover: { type: Boolean, default: false },
  minimalDisplayMode: {
    type: String,
    default: "extended",
    validator: (v) => ["compact", "hover", "extended"].includes(v),
  },
});

const entityName = computed(() => props.entity?.name || props.entity?.title || "");
const imageSrc = computed(() => {
  const primary = props.entity?.[props.imageField] || null;
  if (primary) return primary;
  if (!props.imageFallbackField) return null;
  return props.entity?.[props.imageFallbackField] || null;
});

const nameCell = computed(() => {
  if (props.entity && typeof props.entity.toCell === "function") {
    return props.entity.toCell(props.nameField, { size: "sm", context: "text" });
  }
  return {
    type: "text",
    value: props.entity?.[props.nameField] ?? "",
    params: {},
  };
});

const minimalBind = computed(() => ({
  [props.entityProp]: props.entity,
  showActions: props.showActionsOnHover,
  displayMode: props.minimalDisplayMode,
}));
</script>

<template>
  <div class="relative inline-flex min-w-0 items-center gap-2 cursor-pointer group">
    <!-- Miniature alignée sur le texte (éviter size="xs" sur Image = w-16 h-16 qui déborde sur le nom) -->
    <Image
      v-if="imageSrc"
      :src="imageSrc"
      :alt="entityName || 'Image'"
      fit="cover"
      rounded="sm"
      class="h-4 w-4 shrink-0 overflow-hidden"
    />
    <Icon
      v-else
      :source="fallbackIcon"
      :alt="entityName"
      size="sm"
      class="h-4 w-4 shrink-0 text-primary-300 group-hover:text-primary-100 transition-colors"
    />

    <!-- Nom (min-w-0 : troncature correcte en flex à côté de la vignette) -->
    <span class="min-w-0">
      <CellRenderer :cell="nameCell" :ui-color="uiColor" />
    </span>

    <!-- Hover : Vue minimal -->
    <div class="absolute left-0 top-full mt-2 z-50 hidden group-hover:block" :class="hoverWidthClass">
      <div class="pointer-events-auto">
        <component :is="minimalComponent" v-bind="minimalBind" :class="hoverCardClass" />
      </div>
    </div>
  </div>
</template>

