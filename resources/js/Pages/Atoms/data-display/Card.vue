<script setup>
/**
 * Card Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Card conforme DaisyUI et Atomic Design, avec support des utilitaires custom box-shadow, backdrop-blur et opacity (xs, sm, md, ...).
 * - Slots : figure (image), title, subtitle, default (contenu), actions, footer
 * - Props DaisyUI : color (bg-*), size (xs, sm, md, lg, xl), bordered, dash, side, imageFull
 * - Props custom : shadow (box-shadow-*), backdrop (bd-blur-*), opacity (bd-opacity-*)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Les classes utilitaires custom sont ajoutées dynamiquement
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Card color="bg-base-100" size="md" bordered shadow="lg" backdrop="md" opacity="sm">
 *   <template #title>Carte personnalisée</template>
 *   <p>Contenu…</p>
 * </Card>
 *
 * @props {String} color - Couleur de fond DaisyUI (bg-base-100, bg-primary, etc.)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {Boolean} bordered - Ajoute une bordure (card-bordered)
 * @props {Boolean} dash - Bordure dash (card-dash)
 * @props {Boolean} side - Image sur le côté (card-side)
 * @props {Boolean} imageFull - Image en fond (image-full)
 * @props {String} shadow - Ombre custom ('' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl') → .box-shadow-*
 * @props {String} backdrop - Flou de fond custom ('' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl') → .bd-blur-*
 * @props {String} opacity - Opacité custom ('' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl') → .bd-opacity-*
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot figure - Image ou illustration
 * @slot title - Titre de la carte
 * @slot subtitle - Sous-titre
 * @slot default - Contenu principal
 * @slot actions - Actions (boutons, etc.)
 * @slot footer - Footer optionnel
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: { type: String, default: 'bg-base-100' },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    bordered: { type: Boolean, default: false },
    dash: { type: Boolean, default: false },
    side: { type: Boolean, default: false },
    imageFull: { type: Boolean, default: false },
});

function getAtomClasses(props) {
    const classes = ['card'];
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('card-xs');
    if (props.size === 'sm') classes.push('card-sm');
    if (props.size === 'md') classes.push('card-md');
    if (props.size === 'lg') classes.push('card-lg');
    if (props.size === 'xl') classes.push('card-xl');
    // Couleur DaisyUI
    if (props.color) classes.push(props.color);
    // Bordure DaisyUI
    if (props.bordered) classes.push('card-bordered');
    if (props.dash) classes.push('card-dash');
    // Side DaisyUI
    if (props.side) classes.push('card-side');
    // Utilitaires custom (box-shadow, backdrop, opacity)
    classes.push(...getCustomUtilityClasses(props));
    // Image full (sur figure)
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs">
            <figure v-if="$slots.figure" :class="{ 'image-full': imageFull }">
                <slot name="figure" />
            </figure>
            <div class="card-body">
                <h2 v-if="$slots.title" class="card-title">
                    <slot name="title" />
                </h2>
                <p v-if="$slots.subtitle" class="card-subtitle">
                    <slot name="subtitle" />
                </p>
                <slot />
                <div v-if="$slots.actions" class="card-actions justify-end">
                    <slot name="actions" />
                </div>
            </div>
            <div v-if="$slots.footer" class="card-footer">
                <slot name="footer" />
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
