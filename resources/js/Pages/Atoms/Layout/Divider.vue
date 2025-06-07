<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Divider Atom (DaisyUI + Glassmorphism)
 *
 * @description
 * Composant atomique Divider conforme DaisyUI (v5.x) et Atomic Design, avec extension glassmorphism.
 * - Rend un <div class="divider"> stylé DaisyUI, vertical ou horizontal
 * - Slot par défaut : contenu du divider (texte, "OU", etc.)
 * - Props DaisyUI : color (neutral, primary, secondary, accent, info, success, warning, error), direction (vertical/horizontal), placement (start/end)
 * - Prop glass (true par défaut) : active le style glassmorphism (dégradé, flou, couleur custom)
 * - Fallback DaisyUI natif si glass=false
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityClasses)
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @see https://daisyui.com/components/divider/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Divider glass color="white" direction="horizontal">OU</Divider>
 * <Divider glass color="primary" direction="vertical" />
 * <Divider :glass="false" color="primary">OU</Divider> <!-- DaisyUI fallback -->
 *
 * @props {Boolean} glass - Active le style glassmorphism (défaut: true, extension KrosmozJDR)
 * @props {String} color - Couleur DaisyUI ('neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error', 'white', 'black')
 * @props {String} direction - Direction DaisyUI ('vertical' [défaut], 'horizontal')
 * @props {String} placement - Placement DaisyUI ('', 'start', 'end')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot default - Contenu du divider (texte, "OU", etc.)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 * @note Extension glassmorphism : applique un dégradé flou et une couleur custom, fallback DaisyUI natif si glass=false.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    glass: { type: Boolean, default: true },
    color: {
        type: String,
        default: 'white',
        validator: v => ['white', 'black', 'primary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    direction: {
        type: String,
        default: '',
        validator: v => ['', 'horizontal', 'vertical'].includes(v),
    },
    placement: {
        type: String,
        default: '',
        validator: v => ['', 'start', 'end'].includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'divider',
            props.glass && 'divider-glass',
            props.glass && props.color === 'white' && 'divider-glass-white',
            props.glass && props.color === 'black' && 'divider-glass-black',
            props.glass && props.color === 'primary' && 'divider-glass-primary',
            props.glass && props.color === 'accent' && 'divider-glass-accent',
            props.glass && props.color === 'info' && 'divider-glass-info',
            props.glass && props.color === 'success' && 'divider-glass-success',
            props.glass && props.color === 'warning' && 'divider-glass-warning',
            props.glass && props.color === 'error' && 'divider-glass-error',
            !props.glass && props.color === 'neutral' && 'divider-neutral',
            !props.glass && props.color === 'primary' && 'divider-primary',
            !props.glass && props.color === 'secondary' && 'divider-secondary',
            !props.glass && props.color === 'accent' && 'divider-accent',
            !props.glass && props.color === 'success' && 'divider-success',
            !props.glass && props.color === 'warning' && 'divider-warning',
            !props.glass && props.color === 'info' && 'divider-info',
            !props.glass && props.color === 'error' && 'divider-error',
            props.direction === 'horizontal' && 'divider-horizontal',
            props.direction === 'vertical' && 'divider-vertical',
            props.placement === 'start' && 'divider-start',
            props.placement === 'end' && 'divider-end',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <slot />
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style lang="scss" scoped>
.divider-glass {

    &::before,
    &::after {
        background: none;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    &.divider-horizontal {
        &::before {
            background: linear-gradient(to right, transparent 0%, var(--divider-color, #fff) 15%, var(--divider-color, #fff) 85%, transparent 100%);
        }

        &::after {
            background: linear-gradient(to left, transparent 0%, var(--divider-color, #fff) 15%, var(--divider-color, #fff) 85%, transparent 100%);
        }
    }

    &.divider-vertical {
        &::before {
            background: linear-gradient(to bottom, transparent 0%, var(--divider-color, #fff) 15%, var(--divider-color, #fff) 85%, transparent 100%);
        }

        &::after {
            background: linear-gradient(to top, transparent 0%, var(--divider-color, #fff) 15%, var(--divider-color, #fff) 85%, transparent 100%);
        }
    }
}

// Couleurs glass explicites
.divider-glass-white {
    --divider-color: #fff;
}

.divider-glass-black {
    --divider-color: #000;
}

.divider-glass-primary {
    --divider-color: theme('colors.primary.DEFAULT', #60a5fa);
}

.divider-glass-accent {
    --divider-color: theme('colors.accent.DEFAULT', #d8b4fe);
}

.divider-glass-info {
    --divider-color: theme('colors.info.DEFAULT', #2dd4bf);
}

.divider-glass-success {
    --divider-color: theme('colors.success.DEFAULT', #bef264);
}

.divider-glass-warning {
    --divider-color: theme('colors.warning.DEFAULT', #fde047);
}

.divider-glass-error {
    --divider-color: theme('colors.error.DEFAULT', #f87171);
}
</style>
