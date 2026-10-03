<script setup>
/**
 * Alert Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Alert conforme DaisyUI (v5.x) et Atomic Design.
 * - Slots : #icon (icône SVG ou composant), #content (contenu HTML), #action (boutons)
 * - Prop content : texte simple (prioritaire si pas de slot #content)
 * - Props DaisyUI : color (info, success, warning, error), variant (outline, dash, soft), direction (vertical/horizontal)
 * - Mode glass (défaut) : voile semi-opaque teinté (`.bg-color-*-400` + `.color-*`),
 *   flou `.bd-glass-lg`, texte `base-content` (lisible sur le fond du thème).
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Responsive : vertical sur mobile, horizontal sur desktop
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 *
 * @see https://daisyui.com/components/alert/
 * @version DaisyUI v5.x
 *
 * @example
 * <Alert color="info" content="Nouvelle mise à jour disponible !" />
 * <Alert color="success" variant="outline">
 *   <template #icon><svg ... /></template>
 *   <template #content><b>Succès !</b> Opération réussie.</template>
 *   <template #action><Btn color="success">OK</Btn></template>
 * </Alert>
 *
 * @props {String} color - Couleur DaisyUI ('', 'info', 'success', 'warning', 'error')
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft'). outline/dash retirent le voile.
 * @props {String} direction - Direction ('', 'vertical', 'horizontal'), défaut responsive
 * @props {String} content - Texte simple à afficher (optionnel, prioritaire sur slot #content)
 * @props {Boolean} glass - Voile teinté + flou (`.bd-glass-lg`, `.color-*`, `.bg-color-*`), défaut true
 * @props {String} shadow, backdrop, opacity - utilitaires custom ('' | 'xs' | ...)
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot icon - Icône SVG ou composant
 * @slot content - Contenu HTML complexe
 * @slot action - Boutons ou actions
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'outline', 'dash', 'soft'].includes(v),
    },
    direction: {
        type: String,
        default: '',
        validator: v => ['', 'vertical', 'horizontal'].includes(v),
    },
    content: {
        type: String,
        default: '',
    },
    showIcon: {
        type: Boolean,
        default: true,
    },
    /** Voile teinté semi-opaque + flou (recommandé). */
    glass: {
        type: Boolean,
        default: true,
    },
});

/**
 * Teinte du verre : `--color` (filet) et `--bg-color` (voile).
 * Classes écrites en toutes lettres (pas de concaténation).
 *
 * @param {string} color
 * @returns {string[]}
 */
function glassToneClasses(color) {
    switch (color) {
        case 'info':
            return ['color-info', 'bg-color-info-400'];
        case 'success':
            return ['color-success', 'bg-color-success-400'];
        case 'warning':
            return ['color-warning', 'bg-color-warning-400'];
        case 'error':
            return ['color-error', 'bg-color-error-400'];
        case 'primary':
            return ['color-primary', 'bg-color-primary-400'];
        case 'secondary':
            return ['color-secondary', 'bg-color-secondary-400'];
        case 'accent':
            return ['color-accent', 'bg-color-accent-400'];
        case 'neutral':
            return ['color-neutral', 'bg-color-neutral-400'];
        default:
            return ['color-neutral', 'bg-color-neutral-400'];
    }
}

/** Teinte d’icône (contraste sur fond glass). */
const iconToneClass = computed(() => {
    switch (props.color) {
        case 'info':
            return 'text-info';
        case 'success':
            return 'text-success';
        case 'warning':
            return 'text-warning';
        case 'error':
            return 'text-error';
        case 'primary':
            return 'text-primary';
        case 'secondary':
            return 'text-secondary';
        case 'accent':
            return 'text-accent';
        default:
            return 'text-base-content/80';
    }
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'alert',
            props.glass && 'relative',
            props.glass && 'isolate',
            props.glass && 'bd-glass-lg',
            props.glass && 'text-base-content',
            ...(props.glass ? glassToneClasses(props.color) : []),
            !props.glass && 'alert-solid',
            props.color === 'info' && 'alert-info',
            props.color === 'success' && 'alert-success',
            props.color === 'warning' && 'alert-warning',
            props.color === 'error' && 'alert-error',
            props.variant === 'outline' && 'alert-outline',
            props.variant === 'dash' && 'alert-dash',
            props.variant === 'soft' && 'alert-soft',
            props.direction === 'vertical' && 'alert-vertical',
            props.direction === 'horizontal' && 'alert-horizontal',
            !props.direction && 'alert-vertical',
            !props.direction && 'sm:alert-horizontal',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" role="alert" v-on="$attrs">
        <!-- Icone + contenu côte à côte -->
        <div class="flex items-start gap-3 flex-1">
            <span v-if="$slots.icon && showIcon" class="shrink-0" :class="glass ? iconToneClass : undefined">
                <slot name="icon" />
            </span>
            <span v-else-if="color === 'info' && showIcon" class="shrink-0" :class="glass ? iconToneClass : undefined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'success' && showIcon" class="shrink-0" :class="glass ? iconToneClass : undefined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'warning' && showIcon" class="shrink-0" :class="glass ? iconToneClass : undefined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'error' && showIcon" class="shrink-0" :class="glass ? iconToneClass : undefined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>
            <!-- Contenu -->
            <span class="flex-1">
                <slot name="content">
                    <span v-if="content && !$slots.default">{{ content }}</span>
                    <slot v-else />
                </slot>
            </span>
        </div>
        <!-- Actions -->
        <div v-if="$slots.action" class="flex items-center gap-2 ml-4">
            <slot name="action" />
        </div>
    </div>
</template>
