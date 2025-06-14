<script setup>
/**
 * Image Atom (Atomic Design, Tailwind, DaisyUI)
 *
 * @description
 * Composant atomique pour afficher une image avec gestion de la taille, du ratio, des filtres, du fit, de la position, du mask DaisyUI et du tooltip.
 * - Utilise MediaManager pour la résolution des sources d'images
 * - Support des tailles prédéfinies via size ou personnalisées via width/height
 * - Support du ratio d'aspect
 * - Support des filtres (simple ou multiple)
 * - Support des masks DaisyUI
 * - Gestion du chargement et des erreurs
 * - Tooltip intégré
 *
 * @example
 * <Image source="logos/logo" alt="Logo" size="lg" />
 * <Image src="/img/avatar.jpg" alt="Avatar" width="64" height="64" ratio="1/1" />
 * <Image source="photos/landscape" alt="Paysage" size="xl" fit="cover" position="center" />
 * <Image source="avatars/user" alt="Avatar" size="md" rounded="full" filter="grayscale" />
 *
 * @props {String} src - URL directe de l'image
 * @props {String} source - Chemin source pour MediaManager
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} size - Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl)
 * @props {String} width - Largeur personnalisée
 * @props {String} height - Hauteur personnalisée
 * @props {String} ratio - Ratio d'aspect (square, video, 16/9, 4/3, 3/2, 2/1, etc.)
 * @props {String} fit - object-fit (cover, contain, fill, none, scale-down)
 * @props {String} position - object-position (center, top, right, bottom, left, top-left, top-right, bottom-left, bottom-right)
 * @props {String|Array} filter - Filtre(s) CSS (grayscale, sepia, blur, brightness, contrast, hue-rotate, invert, saturate)
 * @props {String} rounded - Arrondi (none, sm, md, lg, xl, 2xl, 3xl, full, circle)
 * @props {String} mask - Classe DaisyUI mask-* (mask, mask-squircle, mask-heart, etc.)
 * @props {String|Object} tooltip - Tooltip
 * @props {String} tooltip_placement - Position du tooltip
 * @props {Object} transform - Options de transformation pour MediaManager (width, height, fit, quality, format)
 *
 * @slot loader - Loader personnalisé pendant le chargement
 * @slot fallback - Contenu alternatif en cas d'erreur
 * @slot tooltip - Contenu personnalisé du tooltip
 */

import { computed, ref, watch, onMounted, useSlots } from "vue";
import { ImageService } from "@/Utils/file/ImageService";
import {
    getCommonProps,
    getCommonAttrs,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Skeleton from "@/Pages/Atoms/feedback/Skeleton.vue";
import {
    sizeMap,
    ratioMap,
    roundedMap,
    fitMap,
    positionMap,
    filterClassMap,
    maskList,
} from "@/Pages/Atoms/data-display/data-displayMap";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    ...getCommonProps(),
    src: { type: String, default: "" },
    source: { type: String, default: "" },
    alt: { type: String, required: true },
    size: { type: String, default: "" },
    width: { type: String, default: "" },
    height: { type: String, default: "" },
    ratio: {
        type: String,
        default: "",
        validator: (v) => v === "" || Object.keys(ratioMap).includes(v),
    },
    fit: {
        type: String,
        default: "cover",
        validator: (v) => Object.keys(fitMap).includes(v),
    },
    position: {
        type: String,
        default: "center",
        validator: (v) => Object.keys(positionMap).includes(v),
    },
    filter: { type: [String, Array], default: "" },
    rounded: {
        type: String,
        default: "",
        validator: (v) => v === "" || Object.keys(roundedMap).includes(v),
    },
    mask: {
        type: String,
        default: "",
        validator: (v) => v === "" || maskList.includes(v),
    },
    transform: {
        type: Object,
        default: () => ({}),
    },
});

// État
const imageUrl = ref("");
const isLoading = ref(false);
const hasError = ref(false);
const FALLBACK_IMAGE = "/storage/images/no_found.svg";
let triedFallback = false;

const slots = useSlots();

// Résolution de l'URL de l'image
async function resolveImage() {
    if (props.src && props.source) {
        console.warn(
            "Image - Les props src et source sont définies, src sera ignoré",
        );
    }

    if (!props.src && !props.source) {
        imageUrl.value = "";
        return;
    }

    isLoading.value = true;
    hasError.value = false;
    triedFallback = false;

    try {
        if (props.src) {
            // URL directe
            imageUrl.value = props.src.startsWith("/")
                ? props.src
                : `/${props.src}`;
        } else {
            // Source via ImageService avec transformations
            if (Object.keys(props.transform).length > 0) {
                imageUrl.value = await ImageService.getThumbnailUrl(
                    props.source,
                    props.transform,
                );
            } else {
                imageUrl.value = await ImageService.getImageUrl(props.source);
            }
        }
    } catch (error) {
        console.error("Image - Erreur de chargement:", error);
        hasError.value = true;
        imageUrl.value = "";
    } finally {
        isLoading.value = false;
    }
}

function onError() {
    if (!triedFallback && imageUrl.value !== FALLBACK_IMAGE) {
        imageUrl.value = FALLBACK_IMAGE;
        triedFallback = true;
        hasError.value = false;
    } else {
        hasError.value = true;
    }
}

// Classes du wrapper (ratio, size)
const wrapperClasses = computed(() =>
    mergeClasses([
        "relative",
        "inline-flex",
        "justify-center",
        "items-center",
        props.ratio && ratioMap[props.ratio],
        props.size && sizeMap[props.size],
    ]),
);

// Classes de l'image (fit, position, rounded, mask, filter)
const imageClasses = computed(() =>
    mergeClasses([
        props.fit && fitMap[props.fit],
        props.position && positionMap[props.position],
        props.rounded && roundedMap[props.rounded],
        props.mask,
        // Gestion des filtres (simple ou multiple)
        ...(Array.isArray(props.filter)
            ? props.filter.map((f) => filterClassMap[f]).filter(Boolean)
            : props.filter && filterClassMap[props.filter]
              ? [filterClassMap[props.filter]]
              : []),
    ]),
);

// Style de l'image (dimensions personnalisées)
const imageStyle = computed(() => ({
    ...(props.width && !props.size ? { width: props.width } : {}),
    ...(props.height && !props.size ? { height: props.height } : {}),
}));

// Attrs communs
const attrs = computed(() => getCommonAttrs(props));

// Attrs spécifiques à l'image
const imgAttrs = computed(() => ({
    ...attrs.value,
    ...(props.width && !props.size ? { width: props.width } : {}),
    ...(props.height && !props.size ? { height: props.height } : {}),
}));

// Watch pour recharger l'image si src, source ou transform change
watch(
    [() => props.src, () => props.source, () => props.transform],
    () => {
        resolveImage();
    },
    { deep: true },
);

onMounted(() => {
    resolveImage();
});
</script>

<template>
    <div :class="wrapperClasses">
        <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
            <template v-if="isLoading">
                <slot name="loader">
                    <Skeleton
                        element="image"
                        :size="props.size"
                        :width="props.width"
                        :height="props.height"
                        :class="imageClasses"
                    />
                </slot>
            </template>

            <img
                v-else-if="imageUrl && !hasError"
                :src="imageUrl"
                :alt="alt"
                :class="imageClasses"
                :style="imageStyle"
                v-bind="imgAttrs"
                v-on="$attrs"
                @error="onError"
            />

            <template v-else-if="slots.fallback">
                <slot name="fallback" />
            </template>

            <img
                v-else
                :src="FALLBACK_IMAGE"
                alt="Image non disponible"
                :class="imageClasses"
                :style="imageStyle"
                v-bind="imgAttrs"
                v-on="$attrs"
            />

            <template v-if="typeof props.tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </Tooltip>
    </div>
</template>

<style scoped>
/* Pour les ratios personnalisés (aspect-[4/3], etc.), Tailwind doit être configuré pour les inclure */
</style>
