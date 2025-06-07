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
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'outline', 'ghost', 'link', 'soft', 'dash', 'glass'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    block: { type: Boolean, default: false },
    wide: { type: Boolean, default: false },
    square: { type: Boolean, default: false },
    circle: { type: Boolean, default: false },
    type: {
        type: String,
        default: 'button',
        validator: v => ['button', 'submit', 'reset', 'radio', 'checkbox'].includes(v),
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
        default: '',
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'btn',
            props.color === 'neutral' && 'btn-neutral',
            props.color === 'primary' && 'btn-primary',
            props.color === 'secondary' && 'btn-secondary',
            props.color === 'accent' && 'btn-accent',
            props.color === 'info' && 'btn-info',
            props.color === 'success' && 'btn-success',
            props.color === 'warning' && 'btn-warning',
            props.color === 'error' && 'btn-error',
            props.variant === 'outline' && 'btn-outline',
            props.variant === 'ghost' && 'btn-ghost',
            props.variant === 'link' && 'btn-link',
            props.variant === 'soft' && 'btn-soft',
            props.variant === 'dash' && 'btn-dash',
            props.variant === 'glass' && 'glass',
            props.size === 'xs' && 'btn-xs',
            props.size === 'sm' && 'btn-sm',
            props.size === 'md' && 'btn-md',
            props.size === 'lg' && 'btn-lg',
            props.size === 'xl' && 'btn-xl',
            props.block && 'btn-block',
            props.wide && 'btn-wide',
            props.square && 'btn-square',
            props.circle && 'btn-circle',
            props.active && 'btn-active',
            props.disabled && 'btn-disabled',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
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
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped lang="scss">
.btn-link {
    background-color: transparent;
    text-decoration: none;
    margin: 0;
    padding: 0;
    height: auto;
    min-height: auto;
    width: auto;
    min-width: auto;
    transition: filter 0.2s ease-in-out, backdrop-filter 0.2s ease-in-out,
        text-shadow 0.3s ease-in-out;

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
        filter: brightness(1.1);
        backdrop-filter: blur(4px);
        text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.6);
    }
}

.btn:not(.btn-link) {
    transition: filter 0.2s ease-in-out, backdrop-filter 0.3s ease-in-out,
        box-shadow 0.4s ease-in-out, text-shadow 0.3s ease-in-out;

    position: relative;
    overflow: hidden;

    &:hover {
        filter: brightness(1.1);
        backdrop-filter: blur(4px);
        text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.6);
        box-shadow:
            0 0 1px 1px rgba(255, 255, 255, 0.50),
            0 0 3px 4px rgba(255, 255, 255, 0.10),
            0 0 5px 6px rgba(255, 255, 255, 0.05),
            inset 0 0 3px 4px rgba(255, 255, 255, 0.10),
            inset 0 0 5px 6px rgba(255, 255, 255, 0.05);
    }

    &:not(.btn-outline) {
        &::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    rgba(255, 255, 255, 0.2) 48%,
                    rgba(255, 255, 255, 0.35) 50%,
                    rgba(255, 255, 255, 0.2) 52%,
                );
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.5s ease;
        }
    }

    &.btn-outline {
        &::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    rgba(255, 255, 255, 0.05) 48%,
                    rgba(255, 255, 255, 0.15) 50%,
                    rgba(255, 255, 255, 0.05) 52%,
                );
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.5s ease;
        }
    }

    &:hover::after {
        transform: translateX(100%) rotate(45deg);
    }
}
</style>
