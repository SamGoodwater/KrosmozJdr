<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Tooltip Atom (DaisyUI)
 *
 * @description
 * Composant atomique Tooltip conforme DaisyUI (v5.x) et Atomic Design.
 * - Slot par défaut : élément déclencheur (trigger)
 * - Slot #content : contenu complexe du tooltip (optionnel, sinon prop content)
 * - Prop content : string simple pour le tooltip (fallback si pas de slot)
 * - Props DaisyUI : placement, color, open, responsive
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props d'accessibilité et HTML natif héritées de commonProps (sauf tooltip/tooltip_placement)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres (aucune concaténation dynamique)
 * - Accessibilité renforcée (role, aria, etc.)
 * - Seul atome à ne pas intégrer Tooltip (pas de récursivité)
 *
 * @see https://daisyui.com/components/tooltip/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Tooltip content="Info-bulle simple">
 *   <button>Survois-moi</button>
 * </Tooltip>
 *
 * <Tooltip placement="right" color="primary" open>
 *   <template #default>
 *     <i class="fa fa-info-circle"></i>
 *   </template>
 *   <template #content>
 *     <div>
 *       <strong>Info-bulle riche</strong>
 *       <p>Texte détaillé, <a href="#">lien</a>, etc.</p>
 *     </div>
 *   </template>
 * </Tooltip>
 *
 * @props {String} content - Texte simple du tooltip (optionnel, sinon slot #content)
 * @props {String} placement - Position du tooltip ('top', 'right', 'bottom', 'left'), défaut 'top'
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {Boolean} open - Force l'ouverture du tooltip
 * @props {String} responsive - Breakpoint responsive DaisyUI ('sm', 'md', 'lg', 'xl', '2xl')
 * @props {String} shadow, backdrop, opacity - utilitaires custom ('' | 'xs' | ...)
 * @props {String} id, ariaLabel, role, tabindex, class, style, disabled - hérités de commonProps (sauf tooltip/tooltip_placement)
 * @slot default - Élément déclencheur (trigger)
 * @slot content - Contenu HTML complexe du tooltip (optionnel, prioritaire sur prop content)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : role="tooltip" sur .tooltip-content, aria-label, tabindex, etc. transmis.
 * @note Seul atome à ne pas intégrer Tooltip (pas de récursivité).
 */
import { computed } from "vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import { colorList } from "@/Pages/Atoms/atomMap";

const props = defineProps({
    ...getCommonProps({ exclude: ["tooltip", "tooltip_placement"] }),
    ...getCustomUtilityProps(),
    // Contenu du tooltip (string simple)
    content: {
        type: String,
        default: "",
    },
    // Placement DaisyUI : top, right, bottom, left
    placement: {
        type: String,
        default: "top",
        validator: (v) => ["top", "right", "bottom", "left"].includes(v),
    },
    // Couleur DaisyUI : neutral, primary, secondary, accent, info, success, warning, error
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    // Forcer l'ouverture
    open: {
        type: Boolean,
        default: false,
    },
    // Responsive (ex: lg)
    responsive: {
        type: String,
        default: "",
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            props.responsive === "sm" && "sm:tooltip",
            props.responsive === "md" && "md:tooltip",
            props.responsive === "lg" && "lg:tooltip",
            props.responsive === "xl" && "xl:tooltip",
            props.responsive === "2xl" && "2xl:tooltip",
            !props.responsive && "tooltip",
            props.placement === "top" && "tooltip-top",
            props.placement === "right" && "tooltip-right",
            props.placement === "bottom" && "tooltip-bottom",
            props.placement === "left" && "tooltip-left",
            props.color === "neutral" && "tooltip-neutral",
            props.color === "primary" && "tooltip-primary",
            props.color === "secondary" && "tooltip-secondary",
            props.color === "accent" && "tooltip-accent",
            props.color === "info" && "tooltip-info",
            props.color === "success" && "tooltip-success",
            props.color === "warning" && "tooltip-warning",
            props.color === "error" && "tooltip-error",
            props.open && "tooltip-open",
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    ),
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div
        :class="atomClasses"
        v-bind="attrs"
        v-on="$attrs"
        :data-tip="!$slots.content && content ? content : undefined"
    >
        <slot />
        <template v-if="$slots.content">
            <div class="tooltip-content" role="tooltip">
                <slot name="content" />
            </div>
        </template>
    </div>
</template>

<style scoped>
.tooltip-content {
    position: relative;
    z-index: 1;
}
</style>
