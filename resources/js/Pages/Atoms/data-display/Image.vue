<script setup>
/**
 * Image Atom (Atomic Design, Tailwind, DaisyUI)
 *
 * @description
 * Composant atomique pour afficher une image avec gestion de la taille, du ratio, des filtres, du fit, de la position, du mask DaisyUI.
 * - Indicateur DaisyUI loading-ring pendant le chargement réseau (img en opacity 0 jusqu’au load).
 * - Utilise ImageService pour les chemins relatifs sous storage public.
 *
 * @slot loader - Loader personnalisé (overlay) pendant le chargement réseau
 * @slot fallback - Contenu alternatif en cas d’erreur définitive
 */

import { computed, ref, watch, onMounted, useSlots } from "vue";
import { ImageService, FALLBACK_IMAGE_URL } from "@/Utils/file/ImageService";
import {
    getCommonProps,
    getCommonAttrs,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
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
        default: "box",
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

const imageUrl = ref("");
const imageLoaded = ref(false);
const hasError = ref(false);
let triedFallback = false;
const maxRetries = 3;
let retryCount = 0;

const slots = useSlots();

const showLoadingIndicator = computed(
    () =>
        Boolean(imageUrl.value) &&
        !imageLoaded.value &&
        !hasError.value,
);

/** Taille du loading-ring alignée sur la prop size de l’image. */
const imageLoadingSizeMap = {
    xs: "xs",
    sm: "sm",
    md: "md",
    lg: "lg",
    xl: "xl",
    "2xl": "xl",
    "3xl": "xl",
    "4xl": "xl",
    "5xl": "xl",
    "6xl": "xl",
};

const loadingSize = computed(
    () => imageLoadingSizeMap[props.size] || "sm",
);

const placeholderWrapperClasses = computed(() =>
    mergeClasses([
        "flex",
        "items-center",
        "justify-center",
        "min-w-0",
        ...(!props.width && !props.height && props.size ? sizeMap[props.size] : []),
    ]),
);

async function resolveImage() {
    if (props.src && props.source) {
        console.warn(
            "Image - Les props src et source sont définies, src sera ignoré",
        );
    }

    if (!props.src && !props.source) {
        imageUrl.value = "";
        imageLoaded.value = false;
        return;
    }

    imageLoaded.value = false;
    hasError.value = false;
    triedFallback = false;

    try {
        if (props.src) {
            if (
                props.src.startsWith("http://") ||
                props.src.startsWith("https://") ||
                props.src.startsWith("/")
            ) {
                imageUrl.value = props.src;
            } else {
                // Chemin logique relatif (ex. icons/caracteristics/x.webp) → /storage/images/...
                imageUrl.value = await ImageService.getImageUrl(props.src);
            }
        } else {
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
        if (retryCount < maxRetries) {
            retryCount++;
            await new Promise((resolve) =>
                setTimeout(resolve, 1000 * retryCount),
            );
            return resolveImage();
        }
        hasError.value = true;
        imageUrl.value = "";
        imageLoaded.value = true;
    }
}

function onImgLoad() {
    imageLoaded.value = true;
}

function onError() {
    if (!triedFallback && imageUrl.value !== FALLBACK_IMAGE_URL) {
        imageUrl.value = FALLBACK_IMAGE_URL;
        triedFallback = true;
        hasError.value = false;
        imageLoaded.value = false;
    } else {
        hasError.value = true;
        imageLoaded.value = true;
    }
}

const wrapperClasses = computed(() =>
    mergeClasses([
        "relative",
        "inline-flex",
        "justify-center",
        "items-center",
        "overflow-hidden",
        props.ratio && ratioMap[props.ratio],
        ...(!props.width && !props.height && props.size ? sizeMap[props.size] : []),
        props.rounded && roundedMap[props.rounded],
        props.class,
    ]),
);

const imageClasses = computed(() =>
    mergeClasses([
        !props.width && !props.height ? "h-full w-full" : "",
        "transition-opacity duration-200",
        imageLoaded.value ? "opacity-100" : "opacity-0",
        props.fit && fitMap[props.fit],
        props.position && positionMap[props.position],
        props.rounded && roundedMap[props.rounded],
        props.mask,
        ...(Array.isArray(props.filter)
            ? props.filter.map((f) => filterClassMap[f]).filter(Boolean)
            : props.filter && filterClassMap[props.filter]
              ? [filterClassMap[props.filter]]
              : []),
    ]),
);

const imageStyle = computed(() => {
    if (props.width && props.height) {
        return { width: props.width, height: props.height };
    }
    if (props.width && !props.height) {
        return { width: props.width, height: "100%" };
    }
    if (!props.width && props.height) {
        return { width: "100%", height: props.height };
    }
    return {};
});

const attrs = computed(() => getCommonAttrs(props));

const imgAttrs = computed(() => {
    const attrsObj = { ...attrs.value };
    if (props.width) attrsObj.width = props.width;
    if (props.height) attrsObj.height = props.height;
    return attrsObj;
});

watch(
    [() => props.src, () => props.source, () => props.transform],
    () => {
        retryCount = 0;
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
        <template v-if="!imageUrl && !hasError">
            <div :class="placeholderWrapperClasses" :style="imageStyle">
                <slot name="loader">
                    <Loading
                        type="ring"
                        :size="loadingSize"
                        color="neutral"
                        aria-label="Chargement de l'image"
                    />
                </slot>
            </div>
        </template>

        <template v-else-if="imageUrl && !hasError">
            <div class="relative inline-flex max-h-full max-w-full items-center justify-center">
                <div
                    v-if="showLoadingIndicator"
                    class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center"
                    aria-hidden="true"
                >
                    <slot name="loader">
                        <Loading
                            type="ring"
                            :size="loadingSize"
                            color="neutral"
                            aria-label="Chargement de l'image"
                        />
                    </slot>
                </div>
                <img
                    :src="imageUrl"
                    :alt="alt"
                    :class="imageClasses"
                    :style="imageStyle"
                    v-bind="imgAttrs"
                    v-on="$attrs"
                    loading="lazy"
                    decoding="async"
                    @load="onImgLoad"
                    @error="onError"
                />
            </div>
        </template>

        <template v-else-if="slots.fallback">
            <slot name="fallback" />
        </template>

        <img
            v-else
            :src="FALLBACK_IMAGE_URL"
            alt="Image non disponible"
            :class="mergeClasses([imageClasses, '!opacity-100'])"
            :style="imageStyle"
            v-bind="imgAttrs"
            v-on="$attrs"
            loading="lazy"
            decoding="async"
        />
    </div>
</template>

<style scoped>
/* Pour les ratios personnalisés (aspect-[4/3], etc.), Tailwind doit être configuré pour les inclure */
</style>
