<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Card Atom (DaisyUI)
 *
 * @description
 * Composant atomique Card conforme DaisyUI (v5.x) et Atomic Design, avec support des utilitaires custom box-shadow, backdrop-blur et opacity (xs, sm, md, ...).
 * - Slots : figure (image), title, subtitle, default (contenu), actions, footer
 * - Props DaisyUI : color (bg-*), size (xs, sm, md, lg, xl), bordered, dash, side, imageFull
 * - Props custom : shadow (box-shadow-*), backdrop (bd-blur-*), opacity (bd-opacity-*)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Les classes utilitaires custom sont ajoutées dynamiquement
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @see https://daisyui.com/components/card/
 * @version DaisyUI v5.x
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
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    color: { type: String, default: 'bg-base-100' },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    bordered: { type: Boolean, default: false },
    dash: { type: Boolean, default: false },
    side: { type: Boolean, default: false },
    imageFull: { type: Boolean, default: false },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'card',
            props.size === 'xs' && 'card-xs',
            props.size === 'sm' && 'card-sm',
            props.size === 'md' && 'card-md',
            props.size === 'lg' && 'card-lg',
            props.size === 'xl' && 'card-xl',
            props.color,
            props.bordered && 'card-bordered',
            props.dash && 'card-dash',
            props.side && 'card-side',
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
