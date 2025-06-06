<script setup>
/**
 * Avatar Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Composant atomique Avatar conforme DaisyUI et Atomic Design.
 * - Props DaisyUI : size, rounded, ring, ringColor, ringOffset, ringOffsetColor
 * - Props : src, alt (obligatoire), loader (slot), tooltip, tooltip_placement, id, ariaLabel, role, tabindex, disabled
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Toutes les classes DaisyUI et utilitaires custom sont écrites explicitement dans le code
 * - Slot par défaut : fallback (initiales, icône, etc.)
 * - Responsive : width/height explicites selon size
 * - Accessibilité renforcée
 *
 * @example
 * <Avatar src="/img/avatar.jpg" alt="Avatar" size="lg" rounded="full" ring="md" ringColor="primary" />
 * <Avatar alt="A" size="sm" ringOffset="sm" ringOffsetColor="base-200">A</Avatar>
 *
 * @props {String} src - URL de l'image (optionnel)
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} size - Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
 * @props {String} rounded - Arrondi (sm, md, lg, xl, full)
 * @props {String} ring - Epaisseur de l'anneau (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
 * @props {String} ringColor - Couleur de l'anneau (primary, secondary, accent, info, success, warning, error, base-100, base-200, base-300, neutral)
 * @props {String} ringOffset - Epaisseur du ring-offset (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
 * @props {String} ringOffsetColor - Couleur du ring-offset (idem ringColor)
 * @props {String} shadow, backdrop, opacity - utilitaires custom ('' | 'xs' | ...)
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex, disabled - hérités de commonProps
 * @slot default - Fallback (initiales, icône, etc.)
 * @slot loader - Loader personnalisé (optionnel)
 */
import { computed, ref } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const sizeMap = {
    xs: 'w-8 h-8',
    sm: 'w-12 h-12',
    md: 'w-16 h-16',
    lg: 'w-24 h-24',
    xl: 'w-32 h-32',
    '2xl': 'w-40 h-40',
    '3xl': 'w-48 h-48',
    '4xl': 'w-56 h-56',
};
const roundedMap = {
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    full: 'rounded-full',
};
const ringMap = {
    xs: 'ring-1',
    sm: 'ring-2',
    md: 'ring-4',
    lg: 'ring-8',
    xl: 'ring-12',
    '2xl': 'ring-[16px]',
    '3xl': 'ring-[24px]',
    '4xl': 'ring-[32px]',
};
const ringColorMap = {
    primary: 'ring-primary',
    secondary: 'ring-secondary',
    accent: 'ring-accent',
    info: 'ring-info',
    success: 'ring-success',
    warning: 'ring-warning',
    error: 'ring-error',
    'base-100': 'ring-base-100',
    'base-200': 'ring-base-200',
    'base-300': 'ring-base-300',
    neutral: 'ring-neutral',
};
const ringOffsetMap = {
    xs: 'ring-offset-1',
    sm: 'ring-offset-2',
    md: 'ring-offset-4',
    lg: 'ring-offset-8',
    xl: 'ring-offset-12',
    '2xl': 'ring-offset-[16px]',
    '3xl': 'ring-offset-[24px]',
    '4xl': 'ring-offset-[32px]',
};
const ringOffsetColorMap = {
    primary: 'ring-offset-primary',
    secondary: 'ring-offset-secondary',
    accent: 'ring-offset-accent',
    info: 'ring-offset-info',
    success: 'ring-offset-success',
    warning: 'ring-offset-warning',
    error: 'ring-offset-error',
    'base-100': 'ring-offset-base-100',
    'base-200': 'ring-offset-base-200',
    'base-300': 'ring-offset-base-300',
    neutral: 'ring-offset-neutral',
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    src: { type: String, default: '' },
    alt: { type: String, required: true },
    size: {
        type: String,
        default: 'md',
        validator: v => Object.keys(sizeMap).includes(v),
    },
    rounded: {
        type: String,
        default: 'full',
        validator: v => Object.keys(roundedMap).includes(v),
    },
    ring: {
        type: String,
        default: '',
        validator: v => v === '' || Object.keys(ringMap).includes(v),
    },
    ringColor: {
        type: String,
        default: '',
        validator: v => v === '' || Object.keys(ringColorMap).includes(v),
    },
    ringOffset: {
        type: String,
        default: '',
        validator: v => v === '' || Object.keys(ringOffsetMap).includes(v),
    },
    ringOffsetColor: {
        type: String,
        default: '',
        validator: v => v === '' || Object.keys(ringOffsetColorMap).includes(v),
    },
});

const isLoading = ref(false);
const imageError = ref(false);

function getAvatarClasses(props) {
    const classes = ['avatar'];
    // Utilitaires custom (box-shadow, backdrop, opacity)
    classes.push(...getCustomUtilityClasses(props));
    return classes.join(' ');
}
function getInnerClasses(props) {
    const classes = [];
    // Taille
    if (props.size && sizeMap[props.size]) classes.push(sizeMap[props.size]);
    // Arrondi
    if (props.rounded && roundedMap[props.rounded]) classes.push(roundedMap[props.rounded]);
    // Ring
    if (props.ring && ringMap[props.ring]) classes.push('ring', ringMap[props.ring]);
    // Ring color
    if (props.ringColor && ringColorMap[props.ringColor]) classes.push(ringColorMap[props.ringColor]);
    // Ring offset
    if (props.ringOffset && ringOffsetMap[props.ringOffset]) classes.push(ringOffsetMap[props.ringOffset]);
    // Ring offset color
    if (props.ringOffsetColor && ringOffsetColorMap[props.ringOffsetColor]) classes.push(ringOffsetColorMap[props.ringOffsetColor]);
    // Overflow
    classes.push('overflow-hidden');
    // Position
    classes.push('flex', 'items-center', 'justify-center');
    return classes.join(' ');
}

const atomClasses = computed(() => getAvatarClasses(props));
const innerClasses = computed(() => getInnerClasses(props));
const attrs = computed(() => getCommonAttrs(props));

function onLoad() {
    isLoading.value = false;
    imageError.value = false;
}
function onError() {
    isLoading.value = false;
    imageError.value = true;
}
function onStart() {
    isLoading.value = true;
    imageError.value = false;
}
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs">
            <div :class="innerClasses">
                <template v-if="props.src && !imageError">
                    <img :src="props.src" :alt="props.alt" @load="onLoad" @error="onError" @loadstart="onStart"
                        class="w-full h-full object-cover" />
                    <template v-if="isLoading">
                        <slot name="loader">
                            <Loading type="spinner" size="sm" color="primary" />
                        </slot>
                    </template>
                </template>
                <template v-else>
                    <slot>
                        <span class="text-xl font-bold text-base-content select-none">{{ props.alt?.charAt(0) }}</span>
                    </slot>
                </template>
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
