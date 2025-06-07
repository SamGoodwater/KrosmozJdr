<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Stat Atom (DaisyUI)
 *
 * @description
 * Composant atomique Stat conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <div class="stat"> stylé DaisyUI, à utiliser dans un conteneur <div class="stats"> (voir doc DaisyUI)
 * - Slots : title, value, description, action, icon (figure)
 * - Props ou slots pour title, value, description (priorité slot)
 * - Prop icon : source de l'icône (utilise l'atom Icon)
 * - Props DaisyUI : color (appliqué à title et value), colorTitle, colorValue, colorDescription (prioritaires)
 * - Prop size : xs, sm, md, lg, xl (contrôle la taille des textes et de l'icône)
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Tooltip intégré (hors Tooltip lui-même)
 * - Accessibilité renforcée (aria, role, etc.)
 *
 * @see https://daisyui.com/components/stat/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <div class="stats">
 *   <Stat title="Total" value="42" description="Nouveaux inscrits" icon="fa-user" color="primary" size="lg" />
 *   <Stat>
 *     <template #icon><Icon source="fa-star" /></template>
 *     <template #title>Score</template>
 *     <template #value>98%</template>
 *     <template #description>Meilleur score</template>
 *     <template #action><Btn size="sm">Voir</Btn></template>
 *   </Stat>
 * </div>
 *
 * @props {String} title - Titre du stat (optionnel, sinon slot #title)
 * @props {String|Number} value - Valeur principale (optionnel, sinon slot #value)
 * @props {String} description - Description (optionnel, sinon slot #description)
 * @props {String} icon - Source de l'icône (optionnel, sinon slot #icon)
 * @props {String} color - Couleur DaisyUI globale (appliquée à title et value si colorTitle/colorValue non définis)
 * @props {String} colorTitle - Couleur DaisyUI du titre (prioritaire sur color)
 * @props {String} colorValue - Couleur DaisyUI de la value (prioritaire sur color)
 * @props {String} colorDescription - Couleur DaisyUI de la description (prioritaire sur color)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot icon - Icône (figure)
 * @slot title - Titre du stat
 * @slot value - Valeur principale
 * @slot description - Description
 * @slot action - Actions (boutons, etc.)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Utiliser ce composant dans un conteneur <div class="stats"> pour l'affichage en grille.
 * @note Accessibilité renforcée : aria, role, etc.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const colorMap = {
    neutral: 'text-neutral',
    primary: 'text-primary',
    secondary: 'text-secondary',
    accent: 'text-accent',
    info: 'text-info',
    success: 'text-success',
    warning: 'text-warning',
    error: 'text-error',
};
const sizeTitleMap = {
    xs: 'text-base',
    sm: 'text-lg',
    md: 'text-xl',
    lg: 'text-2xl',
    xl: 'text-3xl',
    '': 'text-xl',
};
const sizeValueMap = {
    xs: 'text-lg',
    sm: 'text-2xl',
    md: 'text-3xl',
    lg: 'text-4xl',
    xl: 'text-5xl',
    '': 'text-3xl',
};
const sizeDescMap = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl',
    '': 'text-base',
};
const sizeIconMap = {
    xs: 'sm',
    sm: 'md',
    md: 'lg',
    lg: 'xl',
    xl: '2xl',
    '': 'lg',
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    title: { type: String, default: '' },
    value: { type: [String, Number], default: '' },
    description: { type: String, default: '' },
    icon: { type: String, default: '' },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    colorTitle: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    colorValue: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    colorDescription: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'stat',
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));

const titleClasses = computed(() =>
    mergeClasses(
        [
            'stat-title',
            props.colorTitle ? colorMap[props.colorTitle] : props.color ? colorMap[props.color] : '',
            sizeTitleMap[props.size],
        ].filter(Boolean)
    )
);
const valueClasses = computed(() =>
    mergeClasses(
        [
            'stat-value',
            props.colorValue ? colorMap[props.colorValue] : props.color ? colorMap[props.color] : '',
            sizeValueMap[props.size],
        ].filter(Boolean)
    )
);
const descClasses = computed(() =>
    mergeClasses(
        [
            'stat-desc',
            props.colorDescription ? colorMap[props.colorDescription] : '',
            sizeDescMap[props.size],
        ].filter(Boolean)
    )
);
const iconSize = computed(() => sizeIconMap[props.size]);
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <div v-if="icon || $slots.icon" class="stat-figure">
                <slot name="icon">
                    <Icon v-if="icon" :source="icon" :size="iconSize" />
                </slot>
            </div>
            <div :class="titleClasses">
                <slot name="title">{{ title }}</slot>
            </div>
            <div :class="valueClasses">
                <slot name="value">{{ value }}</slot>
            </div>
            <div v-if="description || $slots.description" :class="descClasses">
                <slot name="description">{{ description }}</slot>
            </div>
            <div v-if="$slots.action" class="stat-actions">
                <slot name="action" />
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
