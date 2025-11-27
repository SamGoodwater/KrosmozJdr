<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Avatar Atom (DaisyUI)
 *
 * @description
 * Composant atomique Avatar conforme DaisyUI (v5.x) et Atomic Design.
 * - Props DaisyUI : size, rounded, ring, ringColor, ringOffset, ringOffsetColor
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props : src, alt (obligatoire), label (pour initiales), defaultAvatar
 * - Système d'avatar placeholder avec initiales et couleurs pastels
 * - Fallback intelligent : image → initiales → image par défaut → no_found.svg
 * - Utilise l'utilitaire Color pour générer les couleurs pastels
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
 * <Avatar label="John Doe" alt="John Doe" size="sm" ringOffset="sm" ringOffsetColor="base-200" />
 * <Avatar alt="A" size="sm" ringOffset="sm" ringOffsetColor="base-200">A</Avatar>
 *
 * @props {String} src - URL de l'image (optionnel)
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} label - Texte pour générer les initiales (optionnel, utilise alt si non fourni)
 * @props {String} defaultAvatar - URL de l'image par défaut (optionnel)
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
import { generateColorFromString } from '@/Utils/color/Color';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    src: { type: String, default: '' },
    alt: { type: String, required: true },
    label: { type: String, default: '' },
    defaultAvatar: { type: String, default: '' },
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
const defaultAvatarError = ref(false);

// Constante pour l'image de fallback
const FALLBACK_AVATAR = "/storage/images/avatar/default_avatar_head.webp";
const FALLBACK_IMAGE = "/storage/images/no_found.svg";

/**
 * Génère les initiales à partir d'un label
 * @param {string} label - Le label à traiter
 * @returns {string} Les initiales (1 ou 2 caractères)
 */
function generateInitials(label) {
    if (!label || typeof label !== 'string') return '';
    
    const words = label.trim().split(/\s+/).filter(word => word.length > 0);
    
    if (words.length === 0) return '';
    
    if (words.length === 1) {
        // Un seul mot : première lettre
        return words[0].charAt(0).toUpperCase();
    } else {
        // Plusieurs mots : première lettre des deux premiers mots
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    }
}

// Computed properties
const displayLabel = computed(() => props.label || '');
const initials = computed(() => generateInitials(displayLabel.value));
const avatarColorHex = computed(() => generateColorFromString(displayLabel.value, {
  normalize: {
    minLightness: 0.3,
    maxLightness: 0.7,
    minSaturation: 0.4,
    maxSaturation: 0.8
  },
  format: 'hex',
  fallback: '#3b82f6'
}));

const avatarColorRgb = computed(() => generateColorFromString(displayLabel.value, {
  normalize: {
    minLightness: 0.3,
    maxLightness: 0.7,
    minSaturation: 0.4,
    maxSaturation: 0.8
  },
  format: 'rgb',
  fallback: 'rgb(59, 130, 246)'
}));
const displayAlt = computed(() => props.alt || displayLabel.value || '');

// État de l'avatar
const showImage = computed(() => props.src && !imageError.value);
const showDefaultAvatar = computed(() => !showImage.value && props.defaultAvatar && !defaultAvatarError.value);
const showPlaceholder = computed(() => !showImage.value && !showDefaultAvatar.value && displayLabel.value);
const showFallbackAvatar = computed(() => !showImage.value && !showDefaultAvatar.value && !displayLabel.value);

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
            // Classe placeholder pour DaisyUI
            showPlaceholder.value && 'avatar-placeholder',
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

// Event handlers
function onLoad() {
    isLoading.value = false;
    imageError.value = false;
    defaultAvatarError.value = false;
}

function onError() {
    isLoading.value = false;
    imageError.value = true;
}

function onDefaultAvatarError() {
    defaultAvatarError.value = true;
}

function onStart() {
    isLoading.value = true;
    imageError.value = false;
    defaultAvatarError.value = false;
}
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <div :class="innerClasses">
            <!-- Image principale -->
            <template v-if="showImage">
                <img 
                    :src="props.src" 
                    :alt="displayAlt" 
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
            
            <!-- Image par défaut -->
            <template v-else-if="showDefaultAvatar">
                <img 
                    :src="props.defaultAvatar" 
                    :alt="displayAlt" 
                    @load="onLoad" 
                    @error="onDefaultAvatarError" 
                    @loadstart="onStart"
                    class="w-full h-full object-cover rounded-full" 
                />
                <template v-if="isLoading">
                    <slot name="loader">
                        <Loading type="spinner" size="sm" color="primary" />
                    </slot>
                </template>
            </template>
            
            <!-- Placeholder avec initiales -->
            <template v-else-if="showPlaceholder">
                <slot>
                    <span 
                        class="text-white font-bold rounded-full select-none flex items-center justify-center w-full h-full avatar-initials"
                        :style="{ '--color': avatarColorRgb }"
                    >
                        {{ initials }}
                    </span>
                </slot>
            </template>
            
            <!-- Avatar par défaut -->
            <template v-else-if="showFallbackAvatar">
                <img 
                    :src="FALLBACK_AVATAR" 
                    :alt="displayAlt" 
                    class="w-full h-full object-cover rounded-full" 
                />
            </template>
            
            <!-- Fallback final (ne devrait jamais arriver) -->
            <template v-else>
                <img 
                    :src="FALLBACK_IMAGE" 
                    :alt="displayAlt" 
                    class="w-full h-full object-cover rounded-full" 
                />
            </template>
        </div>
    </div>
</template>

<style scoped>
<<<<<<< HEAD
/* Style pour les initiales avec variable CSS --color */
.avatar-initials {
    background: linear-gradient(
        45deg,
        color-mix(in srgb, var(--color) 75%, transparent) 20%,
        color-mix(in srgb, var(--color) 85%, transparent) 30%,
        color-mix(in srgb, var(--color) 95%, transparent) 40%,
        color-mix(in srgb, var(--color) 80%, transparent) 50%
    );
    backdrop-filter: blur(10px);
}

/* Classes de taille pour l'avatar si DaisyUI ne les fournit pas */
.avatar-xs {
    width: 0.9rem;
    height: 0.9rem;
    font-size: 0.6rem;
}

.avatar-sm {
    width: 1.1rem;
    height: 1.1rem;
    font-size: 0.7rem;
}

.avatar-md {
    width: 1.6rem;
    height: 1.6rem;
    font-size: 0.8rem;
}

.avatar-lg {
    width: 2.2rem;
    height: 2.2rem;
    font-size: 1rem;
}

.avatar-xl {
    width: 2.8rem;
    height: 2.8rem;
    font-size: 1.2rem;
}

.avatar-2xl {
    width: 4.2rem;
    height: 4.2rem;
    font-size: 1.5rem;
}

.avatar-3xl {
    width: 6rem;
    height: 6rem;
    font-size: 2rem;
}

.avatar-4xl {
    width: 8rem;
    height: 8rem;
    font-size: 2.5rem;
}

/* Style de base pour l'avatar */
.avatar {
    position: relative;
    display: inline-block;
    border-radius: 50%;
}

/* Style pour le placeholder avec initiales */
.avatar-placeholder span {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Tailles de police adaptées pour les initiales */
.avatar-xs span {
    font-size: 0.6rem;
}

.avatar-sm span {
    font-size: 0.7rem;
}

.avatar-md span {
    font-size: 0.8rem;
}

.avatar-lg span {
    font-size: 1rem;
}

.avatar-xl span {
    font-size: 1.25rem;
}

.avatar-2xl span {
    font-size: 1.5rem;
}

.avatar-3xl span {
    font-size: 2rem;
}

.avatar-4xl span {
    font-size: 2.5rem;
}
</style>
