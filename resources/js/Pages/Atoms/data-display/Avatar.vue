<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Avatar Atom (DaisyUI)
 *
 * @description
 * Composant atomique Avatar conforme DaisyUI (v5.x) et Atomic Design.
 * - Props DaisyUI : size, rounded, ring, ringColor, ringOffset, ringOffsetColor
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props : src, alt (obligatoire), loader (slot), id, ariaLabel, role, tabindex, disabled
 * - Toutes les classes DaisyUI et utilitaires custom sont écrites explicitement dans le code
 * - Slot par défaut : fallback (initiales, icône, etc.)
 * - Slot loader : loader personnalisé (optionnel)
 * - Responsive : width/height explicites selon size
 * - Accessibilité renforcée (alt obligatoire, aria, etc.)
 *
 * @see https://daisyui.com/components/avatar/
 * @version DaisyUI v5.x
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
 * @props {String|Object} id, ariaLabel, role, tabindex, disabled - hérités de commonProps
 * @slot default - Fallback (initiales, icône, etc.)
 * @slot loader - Loader personnalisé (optionnel)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité renforcée : alt obligatoire, aria, etc.
 */
import { computed, ref } from 'vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';
import { sizeMap, roundedMap, ringMap, ringColorMap, ringOffsetMap, ringOffsetColorMap } from './data-displayMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    src: { type: String, default: '' },
    alt: { type: String, required: true },
    size: {
        type: String,
        default: 'md',
        validator: v => sizeXlList.includes(v),
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

const atomClasses = computed(() =>
    mergeClasses(
        [
            'avatar',
            // Classes de taille DaisyUI pour l'avatar
            props.size === 'xs' && 'avatar-xs',
            props.size === 'sm' && 'avatar-sm',
            props.size === 'md' && 'avatar-md',
            props.size === 'lg' && 'avatar-lg',
            props.size === 'xl' && 'avatar-xl',
            props.size === '2xl' && 'avatar-2xl',
            props.size === '3xl' && 'avatar-3xl',
            props.size === '4xl' && 'avatar-4xl',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const innerClasses = computed(() =>
    mergeClasses(
        [
            props.rounded && roundedMap[props.rounded],
            props.ring && ringMap[props.ring] && 'ring',
            props.ring && ringMap[props.ring],
            props.ringColor && ringColorMap[props.ringColor],
            props.ringOffset && ringOffsetMap[props.ringOffset],
            props.ringOffsetColor && ringOffsetColorMap[props.ringOffsetColor],
            'overflow-hidden',
            'flex',
            'items-center',
            'justify-center',
        ].filter(Boolean)
    )
);

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
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <div :class="innerClasses">
            <template v-if="props.src && !imageError">
                <img 
                    :src="props.src" 
                    :alt="props.alt" 
                    @load="onLoad" 
                    @error="onError" 
                    @loadstart="onStart"
                    class="w-full h-full object-cover rounded-full" 
                />
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
</template>

<style scoped>
/* Classes de taille pour l'avatar si DaisyUI ne les fournit pas */
.avatar-xs {
    width: 0.875rem;
    height: 0.875rem;
    font-size: 0.875rem;
}

.avatar-sm {
    width: 1rem;
    height: 1rem;
    font-size: 1rem;
}

.avatar-md {
    width: 1.5rem;
    height: 1.5rem;
    font-size: 1.5rem;
}

.avatar-lg {
    width: 2rem;
    height: 2rem;
    font-size: 2rem;
}

.avatar-xl {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 2.5rem;
}

.avatar-2xl {
    width: 4rem;
    height: 4rem;
    font-size: 4rem;
}

.avatar-3xl {
    width: 6rem;
    height: 6rem;
    font-size: 6rem;
}

.avatar-4xl {
    width: 8rem;
    height: 8rem;
    font-size: 8rem;
}

/* Style de base pour l'avatar */
.avatar {
    position: relative;
    display: inline-block;
    border-radius: 50%;
}
</style>
