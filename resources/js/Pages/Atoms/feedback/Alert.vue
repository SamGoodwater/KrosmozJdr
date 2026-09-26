<script setup>
/**
 * Alert Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Alert conforme DaisyUI (v5.x) et Atomic Design.
 * - Slots : #icon (icône SVG ou composant), #content (contenu HTML), #action (boutons)
 * - Prop content : texte simple (prioritaire si pas de slot #content)
 * - Props DaisyUI : color (info, success, warning, error), variant (outline, dash, soft), direction (vertical/horizontal)
 * - Mode glass (défaut) : fond type carte minimale (`bg-glass-2xl`), texte `base-content`,
 *   contour fin teinté + ombre portée de la couleur d’alerte (lisible, charte projet)
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
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft') — ignoré en mode glass
 * @props {String} direction - Direction ('', 'vertical', 'horizontal'), défaut responsive
 * @props {String} content - Texte simple à afficher (optionnel, prioritaire sur slot #content)
 * @props {Boolean} glass - Surface charte (bg-glass + ombre/bordure teintées), défaut true
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
    /** Surface type carte minimale + accent couleur (recommandé). */
    glass: {
        type: Boolean,
        default: true,
    },
});

/** Accent sémantique pour `--color` (bordure / ombre), voir `.alert-surface`. */
const surfaceColorClass = computed(() => {
    switch (props.color) {
        case 'info':
            return 'color-info';
        case 'success':
            return 'color-success';
        case 'warning':
            return 'color-warning';
        case 'error':
            return 'color-error';
        case 'primary':
            return 'color-primary';
        case 'secondary':
            return 'color-secondary';
        case 'accent':
            return 'color-accent';
        case 'neutral':
            return 'color-neutral';
        default:
            return 'color-neutral';
    }
});

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
            !props.glass && props.color === 'info' && 'alert-info',
            !props.glass && props.color === 'success' && 'alert-success',
            !props.glass && props.color === 'warning' && 'alert-warning',
            !props.glass && props.color === 'error' && 'alert-error',
            !props.glass && props.variant === 'outline' && 'alert-outline',
            !props.glass && props.variant === 'dash' && 'alert-dash',
            !props.glass && props.variant === 'soft' && 'alert-soft',
            props.glass && 'alert-surface',
            props.glass && 'relative',
            props.glass && 'rounded-box',
            props.glass && 'border',
            props.glass && 'bg-glass-2xl',
            props.glass && 'text-base-content',
            props.glass && surfaceColorClass.value,
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
        <div class="flex items-center gap-3 flex-1">
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

<style scoped>
/**
 * Surface charte : fond type carte minimale (`bg-glass-2xl`) ;
 * accent via `--color` (classes `.color-*`).
 * Spécificité > DaisyUI `.alert` pour conserver le glass lisible.
 */
.alert.alert-surface {
    background-color: rgba(255, 255, 255, 0.09);
    background-image: none;
    color: var(--color-base-content);
    border-color: color-mix(in oklch, var(--color, var(--color-base-content)) 32%, transparent);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow:
        0 0 0 1px color-mix(in oklch, var(--color, var(--color-base-content)) 14%, transparent),
        0 12px 28px -12px color-mix(in oklch, var(--color, var(--color-base-content)) 42%, transparent);
}
</style>
