<script setup>
/**
 * Alert Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Alert conforme DaisyUI et Atomic Design.
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
 * @props {String} shadow, backdrop, opacity - utilitaires custom ('' | 'xs' | ...)
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot icon - Icône SVG ou composant
 * @slot content - Contenu HTML complexe
 * @slot action - Boutons ou actions
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => ['', 'info', 'success', 'warning', 'error'].includes(v),
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
    show_icon: {
        type: Boolean,
        default: true,
    },
});

function getAtomClasses(props) {
    const classes = ['alert'];
    // Couleur DaisyUI
    if (props.color === 'info') classes.push('alert-info');
    if (props.color === 'success') classes.push('alert-success');
    if (props.color === 'warning') classes.push('alert-warning');
    if (props.color === 'error') classes.push('alert-error');
    // Variant DaisyUI
    if (props.variant === 'outline') classes.push('alert-outline');
    if (props.variant === 'dash') classes.push('alert-dash');
    if (props.variant === 'soft') classes.push('alert-soft');
    // Direction DaisyUI
    if (props.direction === 'vertical') classes.push('alert-vertical');
    if (props.direction === 'horizontal') classes.push('alert-horizontal');
    // Responsive : vertical sur mobile, horizontal sur desktop
    if (!props.direction) classes.push('alert-vertical', 'sm:alert-horizontal');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs" role="alert">
            <!-- Icone + contenu côte à côte -->
            <div class="flex items-center gap-3 flex-1">
                <span v-if="$slots.icon && show_icon" class="shrink-0">
                    <slot name="icon" />
                </span>
                <span v-else-if="color === 'info' && show_icon" class="shrink-0">
                    <!-- Icône info par défaut DaisyUI -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span v-else-if="color === 'success' && show_icon" class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-success h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span v-else-if="color === 'warning' && show_icon" class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-warning h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span v-else-if="color === 'error' && show_icon" class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-error h-6 w-6">
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
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
