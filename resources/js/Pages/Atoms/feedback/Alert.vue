<script setup>
/**
 * Alert Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Alert conforme DaisyUI (v5.x) et Atomic Design.
 * - Slots : #icon (icône SVG ou composant), #content (contenu HTML), #action (boutons)
 * - Prop content : texte simple (prioritaire si pas de slot #content)
 * - Props DaisyUI : color (info, success, warning, error), variant (outline, dash, soft), direction (vertical/horizontal)
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Responsive : vertical sur mobile, horizontal sur desktop
 * - Icone et contenu côte à côte dans une div
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Les classes utilitaires custom sont ajoutées dynamiquement
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/alert/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
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
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft')
 * @props {String} direction - Direction ('', 'vertical', 'horizontal'), défaut responsive
 * @props {String} content - Texte simple à afficher (optionnel, prioritaire sur slot #content)
 * @props {Boolean} glass - Style glassmorphism (box-glass-sm, paires color-X et bg-color-X, texte blanc, défaut true)
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
    /** Style glass sombre à fort contraste (recommandé pour les bandeaux informatifs). */
    glass: {
        type: Boolean,
        default: true,
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'alert',
            !props.glass && props.color === 'info' && 'alert-info',
            !props.glass && props.color === 'success' && 'alert-success',
            !props.glass && props.color === 'warning' && 'alert-warning',
            !props.glass && props.color === 'error' && 'alert-error',
            props.variant === 'outline' && 'alert-outline',
            props.variant === 'dash' && 'alert-dash',
            props.variant === 'soft' && !props.glass && 'alert-soft',
            props.glass && 'relative',
            props.glass && 'box-glass-sm',
            props.glass && 'box-soft-sm',
            props.glass && 'rounded-sm',
            props.glass && 'text-white',
            props.glass && props.color === 'info' && 'color-info',
            props.glass && props.color === 'info' && 'bg-color-info',
            props.glass && props.color === 'success' && 'color-success',
            props.glass && props.color === 'success' && 'bg-color-success',
            props.glass && props.color === 'warning' && 'color-warning',
            props.glass && props.color === 'warning' && 'bg-color-warning',
            props.glass && props.color === 'error' && 'color-error',
            props.glass && props.color === 'error' && 'bg-color-error',
            props.glass && !props.color && 'color-base',
            props.glass && !props.color && 'bg-color-base',
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
            <span v-if="$slots.icon && showIcon" class="shrink-0">
                <slot name="icon" />
            </span>
            <span v-else-if="color === 'info' && showIcon" class="shrink-0">
                <!-- Icône info par défaut DaisyUI -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'success' && showIcon" class="shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'warning' && showIcon" class="shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 stroke-current opacity-90">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span v-else-if="color === 'error' && showIcon" class="shrink-0">
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

<style scoped></style>
