<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Btn Atom (DaisyUI)
 *
 * @description
 * Composant atomique Button conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <button> stylé DaisyUI
 * - Slot par défaut ou slot nommé 'content' : contenu du bouton (texte, icône, etc.)
 * - Prop content : texte simple du bouton (fallback si pas de slot)
 * - Props DaisyUI : color, variant, size, block, wide, square, circle, type, active, checked
 * - Hérite de commonProps (id, ariaLabel, role, tabindex, tooltip, etc.)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré (voir slot #tooltip)
 *
 * @see https://daisyui.com/components/button/
 * @version DaisyUI v5.x
 *
 * @example
 * <Btn color="primary" size="lg" content="Valider" />
 * <Btn variant="outline" color="error">Supprimer</Btn>
 * <Btn circle><i class="fa fa-plus"></i></Btn>
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} variant - Style DaisyUI ('', 'outline', 'ghost', 'link', 'soft', 'dash', 'glass')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {Boolean} block - Pleine largeur (btn-block)
 * @props {Boolean} wide - Largeur augmentée (btn-wide)
 * @props {Boolean} square - Carré (btn-square)
 * @props {Boolean} circle - Cercle (btn-circle)
 * @props {String} type - Type HTML du bouton ('button', 'submit', 'reset', 'radio', 'checkbox'), défaut 'button'
 * @props {Boolean} active - Ajoute la classe btn-active
 * @props {Boolean} checked - Pour usage avancé (inutile sur <button>, mais possible pour compatibilité API)
 * @props {String} content - Texte du bouton (optionnel, sinon slot)
 * @props {Boolean} disabled - Désactivé (btn-disabled + HTML, hérité de commonProps)
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot default|content - Contenu du bouton (texte, icône, etc.)
 * @slot tooltip - Contenu HTML complexe pour le tooltip (optionnel)
 *
 * @note Ce composant ne gère que <button>. Pour les liens ou autres éléments, utiliser un composant dédié (Route).
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import { colorList, variantList, sizeList } from "@/Pages/Atoms/atomMap";
import { typeList } from "./actionMap";

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    variant: {
        type: String,
        default: "",
        validator: (v) => [...variantList, "link"].includes(v),
    },
    size: {
        type: String,
        default: "",
        validator: (v) => sizeList.includes(v),
    },
    block: { type: Boolean, default: false },
    wide: { type: Boolean, default: false },
    square: { type: Boolean, default: false },
    circle: { type: Boolean, default: false },
    type: {
        type: String,
        default: "button",
        validator: (v) => typeList.includes(v),
    },
    checked: {
        type: Boolean,
        default: false,
    },
    active: {
        type: Boolean,
        default: false,
    },
    content: {
        type: String,
        default: "",
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            "btn",
            props.color === "neutral" && "btn-neutral",
            props.color === "primary" && "btn-primary",
            props.color === "secondary" && "btn-secondary",
            props.color === "accent" && "btn-accent",
            props.color === "info" && "btn-info",
            props.color === "success" && "btn-success",
            props.color === "warning" && "btn-warning",
            props.color === "error" && "btn-error",
            props.variant === "outline" && "btn-outline",
            props.variant === "ghost" && "btn-ghost",
            props.variant === "link" && "btn-link",
            props.variant === "soft" && "btn-soft",
            props.variant === "dash" && "btn-dash",
            props.variant === "glass" && "glass",
            props.size === "xs" && "btn-xs",
            props.size === "sm" && "btn-sm",
            props.size === "md" && "btn-md",
            props.size === "lg" && "btn-lg",
            props.size === "xl" && "btn-xl",
            props.block && "btn-block",
            props.wide && "btn-wide",
            props.square && "btn-square",
            props.circle && "btn-circle",
            props.active && "btn-active",
            props.disabled && "btn-disabled",
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    ),
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <button :type="type" :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <span v-if="content && !$slots.default">{{ content }}</span>
            <slot name="content" v-else />
            <slot v-if="!$slots.content && $slots.default" />
        </button>
        <template v-if="$slots.tooltip" #content>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped lang="scss">
// Primary
    $primary: var(--color-primary-400);
    $primary-light: var(--color-primary-50);
    $primary-dark: var(--color-primary-800);
    $primary-hover: var(--color-primary-300);
    $primary-outline-hover: var(--color-primary-950);
// Secondary
    $secondary: var(--color-secondary-400);
    $secondary-light: var(--color-secondary-50);
    $secondary-dark: var(--color-secondary-800);
    $secondary-hover: var(--color-secondary-300);
    $secondary-outline-hover: var(--color-secondary-950);
// Accent
    $accent: var(--color-accent-400);
    $accent-light: var(--color-accent-50);
    $accent-dark: var(--color-accent-800);
    $accent-hover: var(--color-accent-300);
    $accent-outline-hover: var(--color-accent-950);
// Info
    $info: var(--color-info-400);
    $info-light: var(--color-info-50);
    $info-dark: var(--color-info-800);
    $info-hover: var(--color-info-300);
    $info-outline-hover: var(--color-info-950);
// Success
    $success: var(--color-success-400);
    $success-light: var(--color-success-50);
    $success-dark: var(--color-success-800);
    $success-hover: var(--color-success-300);
    $success-outline-hover: var(--color-success-950);
// Warning
    $warning: var(--color-warning-400);
    $warning-light: var(--color-warning-50);
    $warning-dark: var(--color-warning-800);
    $warning-hover: var(--color-warning-300);
    $warning-outline-hover: var(--color-warning-950);
// Error
    $error: var(--color-error-400);
    $error-light: var(--color-error-50);
    $error-dark: var(--color-error-800);
    $error-hover: var(--color-error-300);
    $error-outline-hover: var(--color-error-950);
// Neutral
    $neutral: var(--color-neutral-300);
    $neutral-light: var(--color-neutral-50);
    $neutral-dark: var(--color-neutral-800);
    $neutral-hover: var(--color-neutral-200);
    $neutral-outline-hover: var(--color-neutral-950);

.btn-link {
    background-color: transparent;
    text-decoration: none;
    margin: 0;
    padding: 0;
    height: auto;
    min-height: auto;
    width: auto;
    min-width: auto;
    transition:
        scale 0.2s ease-in-out,
        color 0.2s ease-in-out,
        text-shadow 0.3s ease-in-out;
    scale: 1;

    &.btn-xs {
        font-size: 0.75rem;
    }

    &.btn-sm {
        font-size: 0.875rem;
    }

    &.btn-md {
        font-size: 1rem;
    }

    &.btn-lg {
        font-size: 1.25rem;
    }

    &:hover {
        text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.15);
        scale: 1.02;
    }

    &.btn-primary {
        color: $primary;
        &:hover {
            color: $primary-hover;
        }
    }
    &.btn-secondary {
        color: $secondary;
        &:hover {
            color: $secondary-hover;
        }
    }
    &.btn-accent {
        color: $accent;
        &:hover {
            color: $accent-hover;
        }
    }
    &.btn-info {
        color: $info;
        &:hover {
            color: $info-hover;
        }
    }
    &.btn-success {
        color: $success;
        &:hover {
            color: $success-hover;
        }
    }
    &.btn-warning {
        color: $warning;
        &:hover {
            color: $warning-hover;
        }
    }
    &.btn-error {
        color: $error;
        &:hover {
            color: $error-hover;
        }
    }
    &.btn-neutral {
        color: $neutral;
        &:hover {
            color: $neutral-hover;
        }
    }
}

.btn:not(.btn-link){
    transition:
        box-shadow 0.4s ease-in-out,
        border-color 0.2s ease-in-out,
        color 0.2s ease-in-out;

    position: relative;
    overflow: hidden;

    &:hover {
        box-shadow:
            0 0 1px 1px rgba(255, 255, 255, 0.15),
            0 0 3px 4px rgba(255, 255, 255, 0.05),
            0 0 5px 6px rgba(255, 255, 255, 0.02),
            inset 0 0 3px 4px rgba(255, 255, 255, 0.1),
            inset 0 0 5px 6px rgba(255, 255, 255, 0.05);
    }

    &:not(.btn-outline) {
        &.btn-primary {
            background-color: $primary-dark;
            color: $primary-light;
            &:hover {
                background-color: $primary;
                color: $primary-outline-hover;
            }
        }
        &.btn-secondary {
            background-color: $secondary-dark;
            color: $secondary-light;
            &:hover {
                background-color: $secondary;
                color: $secondary-outline-hover;
            }
        }
        &.btn-accent {
            background-color: $accent-dark; 
            color: $accent-light;
            &:hover {
                background-color: $accent;
                color: $accent-outline-hover;
            }
        }
        &.btn-info {
            background-color: $info-dark;
            color: $info-light;
            &:hover {
                background-color: $info;
                color: $info-outline-hover;
            }
        }
        &.btn-success {
            background-color: $success-dark;
            color: $success-light;
            &:hover {
                background-color: $success;
                color: $success-outline-hover;
            }
        }
        &.btn-warning {
            background-color: $warning-dark;
            color: $warning-light;
            &:hover {
                background-color: $warning;
                color: $warning-outline-hover;
            }
        }
        &.btn-error {
            background-color: $error-dark;
            color: $error-light;
            &:hover {
                background-color: $error;
                color: $error-outline-hover;
            }
        }
        &.btn-neutral {
            background-color: $neutral-dark;
            color: $neutral-light;
            &:hover {
                background-color: $neutral;
                color: $neutral-outline-hover;
            }
        }
        &::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.2) 48%,
                rgba(255, 255, 255, 0.35) 50%,
                rgba(255, 255, 255, 0.2) 52%
            );
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.5s ease;
        }

        &:hover::after {
            transform: translateX(100%) rotate(45deg);
        }
    }

    &.btn-outline {

        &.btn-primary {
            color: $primary;
            border-color: $primary;
            &:hover {
                color: $primary-outline-hover;
                border-color: $primary-outline-hover;
            }
        }
        &.btn-secondary {
                color: $secondary;
            border-color: $secondary;
            &:hover {
                color: $secondary-outline-hover;
                border-color: $secondary-outline-hover;
            }
        }
        &.btn-accent {
            color: $accent;
            border-color: $accent;
            &:hover {
                color: $accent-outline-hover;
                border-color: $accent-outline-hover;
            }
        }
        &.btn-info {
            color: $info;
            border-color: $info;
            &:hover {
                        color: $info-outline-hover;
                border-color: $info-outline-hover
            }
        }
        &.btn-success {
            color: $success;
            border-color: $success;
            &:hover {
                color: $success-outline-hover;
                border-color: $success-outline-hover;
            }
        }
        &.btn-warning {
            color: $warning;
            border-color: $warning;
            &:hover {
                color: $warning-outline-hover;
                border-color: $warning-outline-hover;
            }
        }
        &.btn-error {
            color: $error;
            border-color: $error;
            &:hover {
                color: $error-outline-hover;
                border-color: $error-outline-hover;
            }
        }
    }


}
</style>
