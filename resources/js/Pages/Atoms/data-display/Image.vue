<script setup>
/**
 * Image Atom (Atomic Design, Tailwind)
 *
 * @description
 * Composant atomique pour afficher une image avec gestion explicite de la taille, du ratio, du border-radius, des filtres, du fit, de la position, du mask, du tooltip et de l'accessibilité.
 * - Mapping explicite des classes Tailwind (pas de concaténation dynamique)
 * - Filtres numériques appliqués en style inline, sinon via classes
 * - Ratio géré via un wrapper aspect-*
 * - Tooltip intégré
 * - Accessibilité renforcée (alt obligatoire)
 * - Responsive via la prop 'sizes' (voir exemple)
 *
 * @example
 * <Image src="/img/avatar.jpg" alt="Avatar" size="lg" ratio="1/1" rounded="full" fit="cover" position="top" filter="grayscale" tooltip="Avatar utilisateur" />
 * <Image src="/img/photo.jpg" alt="Photo" :sizes="{ sm: 'xs', md: 'sm', lg: 'md', xl: 'lg' }" ratio="1/1" />
 *
 * @props {String} src - URL de l'image (obligatoire)
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} size - Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl)
 * @props {Object} sizes - Mapping responsive { sm: 'xs', md: 'sm', ... } (prioritaire sur size)
 * @props {String} ratio - Ratio d'aspect (square, video, 16/9, 4/3, 3/2, 2/1, etc.)
 * @props {String} width - Largeur personnalisée (ex: 64, 128) si size/sizes non défini
 * @props {String} height - Hauteur personnalisée (ex: 64, 128) si size/sizes non défini
 * @props {String} rounded - Arrondi (none, sm, md, lg, xl, 2xl, 3xl, full, circle)
 * @props {String} fit - object-fit (cover, contain, fill, none, scale-down)
 * @props {String} position - object-position (center, top, right, bottom, left, top-left, top-right, bottom-left, bottom-right)
 * @props {String|Object} filter - Filtre CSS (grayscale, sepia, blur, brightness, contrast, hue-rotate, invert, saturate) ou objet numérique { type, value }
 * @props {String} mask - Classe Tailwind mask-* (optionnel)
 * @props {String|Object} tooltip - Tooltip (hérité de commonProps)
 * @props {String} tooltip_placement - Position du tooltip (hérité de commonProps)
 * @props {String} id, ariaLabel, role, tabindex, disabled - hérités de commonProps
 *
 * @note Les classes Tailwind sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Les filtres numériques sont appliqués en style inline.
 * @note La prop 'sizes' permet le responsive (voir exemple ci-dessus).
 */
import { computed, ref, watch, onMounted } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atom/atomManager';
import { MediaManager } from '@/Utils/file/MediaManager';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';

const sizeMap = {
    xs: ['w-16', 'h-16'],
    sm: ['w-24', 'h-24'],
    md: ['w-32', 'h-32'],
    lg: ['w-48', 'h-48'],
    xl: ['w-64', 'h-64'],
    '2xl': ['w-96', 'h-96'],
    '3xl': ['w-128', 'h-128'],
    '4xl': ['w-192', 'h-192'],
    '5xl': ['w-256', 'h-256'],
    '6xl': ['w-512', 'h-512'],
};
const ratioMap = {
    '1/1': 'aspect-square',
    'square': 'aspect-square',
    '16/9': 'aspect-video',
    'video': 'aspect-video',
    '4/3': 'aspect-[4/3]',
    '3/2': 'aspect-[3/2]',
    '2/1': 'aspect-[2/1]',
    '3/4': 'aspect-[3/4]',
    '9/16': 'aspect-[9/16]',
};
const roundedMap = {
    none: '',
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    '2xl': 'rounded-2xl',
    '3xl': 'rounded-3xl',
    full: 'rounded-full',
    circle: 'rounded-full',
};
const fitMap = {
    cover: 'object-cover',
    contain: 'object-contain',
    fill: 'object-fill',
    none: 'object-none',
    'scale-down': 'object-scale-down',
};
const positionMap = {
    center: 'object-center',
    top: 'object-top',
    right: 'object-right',
    bottom: 'object-bottom',
    left: 'object-left',
    'top-left': 'object-top-left',
    'top-right': 'object-top-right',
    'bottom-left': 'object-bottom-left',
    'bottom-right': 'object-bottom-right',
};
const filterClassMap = {
    grayscale: 'filter grayscale',
    sepia: 'filter sepia',
    blur: 'filter blur',
    brightness: 'filter brightness-150',
    contrast: 'filter contrast-150',
    'hue-rotate': 'filter hue-rotate-90',
    invert: 'filter invert',
    saturate: 'filter saturate-200',
};

const breakpoints = ['sm', 'md', 'lg', 'xl', '2xl'];

const props = defineProps({
    ...getCommonProps(),
    src: { type: String, default: '' },
    source: { type: String, default: '' },
    alt: { type: String, required: true },
    size: { type: String, default: '' },
    sizes: { type: Object, default: null },
    ratio: { type: String, default: '' },
    width: { type: String, default: '', validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(v) },
    height: { type: String, default: '' },
    rounded: { type: String, default: '' },
    fit: { type: String, default: 'cover', validator: v => Object.keys(fitMap).includes(v) },
    position: { type: String, default: 'center', validator: v => Object.keys(positionMap).includes(v) },
    filter: { type: [String, Object], default: '' },
    mask: { type: String, default: '' },
});

const imageUrl = ref('');
const isLoading = ref(false);

async function resolveImage() {
    if (props.src) {
        imageUrl.value = props.src;
        isLoading.value = false;
    } else if (props.source) {
        isLoading.value = true;
        imageUrl.value = '';
        try {
            imageUrl.value = await MediaManager.get(props.source, 'image');
        } catch (e) {
            imageUrl.value = '';
        } finally {
            isLoading.value = false;
        }
    } else {
        imageUrl.value = '';
        isLoading.value = false;
    }
}

watch(() => [props.src, props.source], resolveImage, { immediate: true });
onMounted(resolveImage);

// Wrapper classes (ratio, size, responsive sizes)
const wrapperClasses = computed(() => {
    const classes = ['relative', 'inline-flex', 'justify-center', 'items-center'];
    if (props.ratio && ratioMap[props.ratio]) {
        classes.push(ratioMap[props.ratio]);
    }
    // Responsive sizes
    if (props.sizes) {
        for (const [bp, sizeKey] of Object.entries(props.sizes)) {
            if (breakpoints.includes(bp) && sizeMap[sizeKey]) {
                classes.push(`${bp}:${sizeMap[sizeKey][0]}`);
                classes.push(`${bp}:${sizeMap[sizeKey][1]}`);
            }
        }
    } else if (props.size && sizeMap[props.size]) {
        classes.push(...sizeMap[props.size]);
    }
    return classes.join(' ');
});

// Image classes (fit, position, rounded, mask, filtre simple)
const atomClasses = computed(() => {
    const classes = [];
    // Fit
    if (fitMap[props.fit]) classes.push(fitMap[props.fit]);
    // Position
    if (positionMap[props.position]) classes.push(positionMap[props.position]);
    // Arrondi
    if (props.rounded && roundedMap[props.rounded]) classes.push(roundedMap[props.rounded]);
    // Mask
    if (props.mask) classes.push(props.mask);
    // Filtre simple (string)
    if (typeof props.filter === 'string' && filterClassMap[props.filter]) {
        classes.push(...filterClassMap[props.filter].split(' '));
    }
    return classes.join(' ');
});

// Filtre numérique (inline style)
const imageStyle = computed(() => {
    if (typeof props.filter === 'object' && props.filter !== null) {
        // { type: 'blur', value: 8 } => filter: blur(8px)
        const { type, value } = props.filter;
        if (type && value !== undefined) {
            if ([
                'grayscale', 'sepia', 'invert', 'saturate', 'contrast', 'brightness'
            ].includes(type)) {
                return { filter: `${type}(${value}%)` };
            }
            if (type === 'blur') {
                return { filter: `blur(${value}px)` };
            }
            if (type === 'hue-rotate') {
                return { filter: `hue-rotate(${value}deg)` };
            }
        }
    }
    return {};
});

const attrs = computed(() => getCommonAttrs(props));

const imgAttrs = computed(() => {
    return {
        ...attrs.value,
        ...(props.width && !props.size ? { width: props.width } : {}),
        ...(props.height && !props.size ? { height: props.height } : {}),
    };
});
</script>

<template>
    <div :class="wrapperClasses">
        <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
            <template v-if="isLoading">
                <slot name="loader">
                    <Loading type="spinner" size="md" color="secondary" />
                </slot>
            </template>
            <img v-else-if="imageUrl" :src="imageUrl" :alt="alt" :class="atomClasses" :style="imageStyle"
                v-bind="imgAttrs" />
            <template v-if="typeof props.tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </Tooltip>
    </div>
</template>

<style scoped>
/* Pour les ratios personnalisés (aspect-[4/3], etc.), Tailwind doit être configuré pour les inclure */
</style>
