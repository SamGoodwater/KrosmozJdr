<script setup>
/**
 * Btn Atom (DaisyUI)
 *
 * @description
 * Composant atomique Button conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <button> stylé DaisyUI
 * - Slot par défaut ou slot nommé 'content' : contenu du bouton (texte, icône, etc.)
 * - Prop content : texte simple du bouton (fallback si pas de slot)
 * - Props DaisyUI : color, variant, size, block, wide, square, circle, type, active, checked
 * - Hérite de commonProps (id, ariaLabel, role, tabindex, etc.)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
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
 * @props {String} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default|content - Contenu du bouton (texte, icône, etc.)
 *
 * @note Ce composant ne gère que <button>. Pour les liens ou autres éléments, utiliser un composant dédié (Route).
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed, useAttrs } from "vue";
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
        default: "glass",
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
    animation: {
        type: String,
        default: "glass",
        validator: (v) => ["glass", "none"].includes(v),
    },
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

const $attrs = useAttrs();

const atomClasses = computed(() =>
    mergeClasses(
        [
            "btn",
            props.animation === "glass" && "btn-animation-glass",
            props.color === "primary" && "btn-custom-primary",
            props.color === "secondary" && "btn-custom-secondary",
            props.color === "accent" && "btn-custom-accent",
            props.color === "info" && "btn-custom-info",
            props.color === "success" && "btn-custom-success",
            props.color === "warning" && "btn-custom-warning",
            props.color === "error" && "btn-custom-error",
            props.color === "neutral" && "btn-custom-neutral",
            props.variant === "outline" && "btn-outline-custom border-glass-lg hover:border-glass-xl",
            props.variant === "ghost" && "btn-ghost-custom",
            props.variant === "link" && "btn-link",
            props.variant === "soft" && "btn-soft",
            props.variant === "dash" && "btn-dash",
            props.variant === "glass" && "btn-glass-custom box-glass-sm hover:box-glass-md",
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
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    ),
);

// Bindings pour le template (simplifié et approprié pour un bouton)
const buttonBindings = computed(() => ({
    ...getCommonAttrs(props),
    ...$attrs,
}));
</script>

<template>
    <button 
        :type="type" 
        :class="atomClasses" 
        v-bind="buttonBindings"
    >
        <span v-if="content && !$slots.default">{{ content }}</span>
        <slot name="content" v-else />
        <slot v-if="!$slots.content && $slots.default" />
    </button>
</template>

<style scoped lang="scss">
@use "sass:map";

    $color-map:(
        "primary": (
            "main": var(--color-primary-500),
            "semilight": var(--color-primary-300),
            "light": var(--color-primary-100),
            "semidark": var(--color-primary-700),
            "dark": var(--color-primary-900),
        ),
        "secondary": (
            "main": var(--color-secondary-500),
            "semilight": var(--color-secondary-300),
            "light": var(--color-secondary-100),
            "semidark": var(--color-secondary-700),
            "dark": var(--color-secondary-900),
        ),
        "accent": (
            "main": var(--color-accent-500),
            "semilight": var(--color-accent-300),
            "light": var(--color-accent-100),
            "semidark": var(--color-accent-700),
            "dark": var(--color-accent-900),
        ),
        "info": (
            "main": var(--color-info-500),
            "semilight": var(--color-info-300),
            "light": var(--color-info-100),
            "semidark": var(--color-info-700),
            "dark": var(--color-info-900),
        ),
        "success": (
            "main": var(--color-success-500),
            "semilight": var(--color-success-300),
            "light": var(--color-success-100),
            "semidark": var(--color-success-700),
            "dark": var(--color-success-900),
        ),
        "warning": (
            "main": var(--color-warning-500),
            "semilight": var(--color-warning-300),
            "light": var(--color-warning-100),
            "semidark": var(--color-warning-700),
            "dark": var(--color-warning-900),
        ),
        "error": (
            "main": var(--color-error-500),
            "semilight": var(--color-error-300),
            "light": var(--color-error-100),
            "semidark": var(--color-error-700),
            "dark": var(--color-error-900),
        ),
        "neutral": (
            "main": var(--color-neutral-500),
            "semilight": var(--color-neutral-300),
            "light": var(--color-neutral-100),
            "semidark": var(--color-neutral-700),
            "dark": var(--color-neutral-900),
        ),
    );

    .btn{
        outline-color: transparent;
        transition-property:
            box-shadow 0.4s ease-in-out,
            border-color 0.2s ease-in-out,
            color 0.2s ease-in-out,
            scale 0.2s ease-in-out;
        transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
        transition-duration: 0.2s;
        position: relative;
        overflow: hidden;
        background-color: transparent;
        border: none;
        text-shadow: none;
        box-shadow: none;
        cursor: pointer;
        text-decoration: none;
        

        // Size
        &-xs{ font-size: 0.75rem; }
        &-sm{ font-size: 0.875rem; }
        &-md{ font-size: 1rem; }
        &-lg{ font-size: 1.25rem; }
        &-xl{ font-size: 1.5rem; }

        // Ghost
        &-link, &-ghost-custom {
            background-color: transparent;
            margin: 0;
            padding: 0;
            height: auto;
            scale: 1;
            border: none;
        }

        // Ghost
        &-ghost-custom {
            transition: box-shadow 0.2s ease-in-out, scale 0.2s ease-in-out;
            scale: 1;
            &:hover {
                border: none;
                box-shadow: 0px 0px 3px 3px rgba(0,0,0,0.05), 
                            0px 0px 5px 5px rgba(0,0,0,0.02), 
                            0px 0px 2px 2px rgba(0,0,0,0.01) inset;
                scale: 1.05;
            }
        }

        // Link
        &-link {
            transition:
                scale 0.2s ease-in-out,
                color 0.2s ease-in-out,
                text-shadow 0.3s ease-in-out;   
            scale: 1;

  
            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    color: map.get($value, "main");
                }
            }

            &:hover {
                scale: 1.02;
                text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.15);
                @each $color, $value in $color-map {
                    &.btn-custom-#{$color} {
                        color: map.get($value, "semilight");
                    }
                }
            }
        }   

        &-outline-custom, &-soft, &-glass, &-dash {
            &:hover {
                box-shadow:
                    0 0 1px 1px rgba(255, 255, 255, 0.15),
                    0 0 3px 4px rgba(255, 255, 255, 0.05),
                    0 0 5px 6px rgba(255, 255, 255, 0.02),
                    inset 0 0 3px 4px rgba(255, 255, 255, 0.1),
                    inset 0 0 5px 6px rgba(255, 255, 255, 0.05);
            }
        }

        &-outline-custom, &-glass-custom {
            scale: 1;
            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    --color: var(--color-#{$color}-500);
                }
            }
            &:hover {
                scale: 1.02;
                @each $color, $value in $color-map {
                    &.btn-custom-#{$color} {
                        color: map.get($value, "semilight");
                    }
                }
            }
            &::after {
                content: "";
                position: absolute;
                inset: 0;
                z-index: -1; // Derrière le contenu
                border-radius: inherit;
            }
        }

        &-outline-custom {
            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    color: map.get($value, "semilight");
                }
            }
            &::after {
                background-color: transparent;
            }
            &:hover {
                &::after {
                    background-color: color-mix(in srgb, var(--color) 30%, transparent);
                }
                @each $color, $value in $color-map {
                    &.btn-custom-#{$color} {
                        color: map.get($value, "light");    
                    }
                }
            }
        }

        &-glass-custom {
            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    color: map.get($value, "light");
                }
            }
            &::after {
                background: linear-gradient(
                    90deg,
                    color-mix(in srgb, var(--color) 35%, transparent) 20%,
                    color-mix(in srgb, var(--color) 45%, transparent) 30%,
                    color-mix(in srgb, var(--color) 60%, transparent) 55%,
                    color-mix(in srgb, var(--color) 35%, transparent) 65%
                );
            }
            &:hover::after {
                filter: brightness(1.1);
            }
        }

        &-soft {
            padding: 0.25rem 0.5rem;
            border: none;

            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    color: map.get($value, "light");
                    background-color: color-mix(in srgb, map.get($value, "main") 30%, transparent);
                }
            }
            &:hover {
                @each $color, $value in $color-map {
                    &.btn-custom-#{$color} {
                        color: map.get($value, "semilight");
                        background-color: color-mix(in srgb, map.get($value, "main") 60%, transparent);
                    }
                }
            }
        }

        // Dash
        &-dash {
            border: none;
            @each $color, $value in $color-map {
                &.btn-custom-#{$color} {
                    color: map.get($value, "light");
                    background-color: map.get($value, "main");
                }
            }
            &:hover {
                @each $color, $value in $color-map {
                    &.btn-custom-#{$color} {
                        color: map.get($value, "semilight");
                        background-color: map.get($value, "main");
                    }
                }
            }
        }

        // Effet glass qui glisse via filter et gradient
        &-animation-glass:not(.btn-link):not(.btn-ghost-custom):not(.btn-outline-custom) {
            position: relative;
            overflow: hidden;
            
            // Effet de glass qui glisse via ::after avec filter
            &::after {
                content: '';
                position: absolute;
                inset: 0;
                background-size: 200% 200%;
                background-position: 200% -200%;
                transition: background-position 0.45s ease, backdrop-filter 0.2s ease;
                pointer-events: none;
                z-index: 1;
            }

            &:hover::after {
                background-position: 200% 200%;
                backdrop-filter: blur(24px);
                mix-blend-mode: overlay;
            }
        }

        &-disabled, &[disabled] {
            filter: grayscale(50%);
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
            &:hover {
                scale: 1;
                box-shadow: none;
                border: none;
            }
        }
    }
</style>
