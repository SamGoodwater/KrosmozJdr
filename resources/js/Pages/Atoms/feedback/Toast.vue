<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Toast Atom (Custom - basé sur DaisyUI)
 *
 * @description
 * Composant atomique Toast personnalisé basé sur DaisyUI (v5.x) et Atomic Design.
 * - Rend un <div class="toast-custom"> avec styles personnalisés
 * - Props DaisyUI : horizontal (start/center/end), vertical (top/middle/bottom)
 * - Gestion des couleurs : type (success, error, info, warning, primary, secondary)
 * - Utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Accessibilité : ariaLabel, role, tabindex, id
 * - Slot par défaut : contenu du toast (souvent des <Alert>)
 * - Utilise une classe personnalisée 'toast-custom' au lieu de 'toast' DaisyUI
 *
 * @see https://daisyui.com/components/toast/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Toast horizontal="end" vertical="top" type="success" shadow="md">
 *   <Alert color="info">Nouveau message !</Alert>
 * </Toast>
 *
 * @props {String} horizontal - Placement horizontal ('', 'start', 'center', 'end')
 * @props {String} vertical - Placement vertical ('', 'top', 'middle', 'bottom')
 * @props {String} type - Type/couleur du toast ('', 'success', 'error', 'info', 'warning', 'primary', 'secondary')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du toast (souvent des <Alert>)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    horizontal: {
        type: String,
        default: '',
        validator: v => ['', 'start', 'center', 'end'].includes(v),
    },
    vertical: {
        type: String,
        default: '',
        validator: v => ['', 'top', 'middle', 'bottom'].includes(v),
    },
    type: {
        type: String,
        default: '',
        validator: v => ['', 'success', 'error', 'info', 'warning', 'primary', 'secondary'].includes(v),
    },
});

// Mapping des types vers les couleurs DaisyUI
const colorMap = {
    success: 'success',
    error: 'error',
    info: 'info',
    warning: 'warning',
    primary: 'primary',
    secondary: 'secondary',
    '': '',
};

const color = computed(() => colorMap[props.type] || '');

const atomClasses = computed(() =>
    mergeClasses(
        [
            'toast-custom',
            'box-glass-sm',
            // Classes de placement
            props.horizontal === 'start' && 'toast-start',
            props.horizontal === 'center' && 'toast-center',
            props.horizontal === 'end' && 'toast-end',
            props.vertical === 'top' && 'toast-top',
            props.vertical === 'middle' && 'toast-middle',
            props.vertical === 'bottom' && 'toast-bottom',
            // Classes de couleur
            color.value && `color-${color.value}`,
            color.value === 'success' && 'bg-success-800/10 text-success-100',
            color.value === 'error' && 'bg-error-800/10 text-error-100',
            color.value === 'info' && 'bg-info-800/10 text-info-100',
            color.value === 'warning' && 'bg-warning-800/10 text-warning-100',
            color.value === 'primary' && 'bg-primary-800/10 text-primary-100',
            color.value === 'secondary' && 'bg-secondary-800/10 text-secondary-100',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped>
/* Styles personnalisés pour le toast (basés sur DaisyUI mais sans positionnement absolu) */
.toast-custom {
    /* Styles de base du toast DaisyUI sans positionnement */
    position: relative; /* Nécessaire pour positionner la barre de progression */
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 3rem;
    padding: 0.5rem;
    border-radius: 0.125rem;
    overflow: hidden;
    /* Animation d'entrée */
    animation: toast-enter 0.3s ease-out;
}

/* Animation d'entrée */
@keyframes toast-enter {
    from {
        opacity: 0;
        transform: translateX(100%) scale(0.8);
        filter: blur(4px);
    }
    to {
        opacity: 1;
        transform: translateX(0) scale(1);
        filter: blur(0);
    }
}

/* Animation de sortie */
.toast-custom.leaving {
    animation: toast-leave 0.3s ease-in forwards;
}

@keyframes toast-leave {
    from {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateX(100%) scale(0.8);
    }
}
</style>
