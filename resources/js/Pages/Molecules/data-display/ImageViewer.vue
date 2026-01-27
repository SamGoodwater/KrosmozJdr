<script setup>
/**
 * ImageViewer Molecule (PhotoSwipe)
 *
 * @description
 * Molecule d’affichage d’image avec interactions “viewer” via PhotoSwipe :
 * - ouverture plein écran
 * - zoom / pan (natif PhotoSwipe)
 * - caption (optionnelle)
 * - préchargement optionnel (hover/open) pour récupérer les dimensions et accélérer l’ouverture
 *
 * Version v1 : image seule.
 * L’API prévoit déjà la possibilité d’évoluer vers une galerie (items/startIndex).
 *
 * @example
 * <ImageViewer
 *   source="items/some-image.png"
 *   alt="Illustration"
 *   caption="Aperçu"
 *   preload="hover"
 * />
 */

import { computed, onBeforeUnmount, ref, watch } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { ImageService } from "@/Utils/file/ImageService";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    /**
     * Source d’image (mêmes conventions que l’Atom `Image`)
     */
    src: { type: String, default: "" },
    source: { type: String, default: "" },
    transform: { type: Object, default: () => ({}) },
    alt: { type: String, required: true },

    /**
     * Caption affichée dans le viewer (fallback: alt)
     */
    caption: { type: String, default: "" },

    /**
     * Ouvrir au clic sur l’image
     */
    openOnClick: { type: Boolean, default: true },

    /**
     * Préchargement :
     * - "off" : aucun
     * - "hover" : précharge au survol (ou focus)
     * - "open" : précharge lors de l’ouverture
     */
    preload: {
        type: String,
        default: "off",
        validator: (v) => ["off", "hover", "open"].includes(v),
    },

    /**
     * Afficher une icône “loupe” au hover
     */
    showHoverIcon: { type: Boolean, default: true },

    /**
     * Télécharger (bouton custom dans la toolbar PhotoSwipe)
     */
    downloadEnabled: { type: Boolean, default: true },

    /**
     * Prop passthrough : options visuelles pour l’Atom `Image`
     * (on reste volontairement permissif pour éviter de dupliquer toute l’API)
     */
    imageProps: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["open", "close"]);

const resolvedFullUrl = ref("");
const resolvedSize = ref({ w: 0, h: 0 });
const isResolving = ref(false);

let destroyed = false;

async function resolveUrls() {
    isResolving.value = true;
    try {
        // Full
        if (props.src) {
            resolvedFullUrl.value = props.src.startsWith("http://") || props.src.startsWith("https://")
                ? props.src
                : props.src.startsWith("/")
                  ? props.src
                  : `/${props.src}`;
        } else if (props.source) {
            resolvedFullUrl.value = await ImageService.getImageUrl(props.source);
        } else {
            resolvedFullUrl.value = "";
        }
    } finally {
        isResolving.value = false;
    }
}

async function preloadImage(url) {
    if (!url) return { w: 0, h: 0 };
    return await new Promise((resolve) => {
        const img = new window.Image();
        img.decoding = "async";
        img.loading = "eager";
        img.onload = () => resolve({ w: img.naturalWidth || 0, h: img.naturalHeight || 0 });
        img.onerror = () => resolve({ w: 0, h: 0 });
        img.src = url;
    });
}

async function ensureSize(preloadMode) {
    // preloadMode: "hover" | "open"
    if (!resolvedFullUrl.value) return;
    if (resolvedSize.value.w && resolvedSize.value.h) return;
    if (destroyed) return;

    const size = await preloadImage(resolvedFullUrl.value);
    if (destroyed) return;
    if (size.w && size.h) resolvedSize.value = size;
}

const effectiveCaption = computed(() => props.caption || props.alt);

// Base: toujours garder les urls à jour
watch(
    () => [props.src, props.source, props.transform],
    () => resolveUrls(),
    { deep: true, immediate: true },
);

async function openViewer() {
    if (!resolvedFullUrl.value) return;

    if (props.preload === "open") {
        await ensureSize("open");
    }

    // Dynamic import (bundle perf)
    const [{ default: PhotoSwipe }, { default: PhotoSwipeLightbox }] = await Promise.all([
        import("photoswipe"),
        import("photoswipe/lightbox"),
    ]);

    // IMPORTANT: vos modals utilisent <dialog> (top layer).
    // Si PhotoSwipe est append sur <body>, il peut passer "derrière" une modal ouverte.
    // On append donc le viewer au dialog ouvert le plus haut quand il existe.
    const openDialogs = Array.from(document.querySelectorAll("dialog[open]"));
    const topDialog = openDialogs.length ? openDialogs[openDialogs.length - 1] : null;
    const appendToEl = topDialog || document.body;

    // Créer une instance lightbox “dataSource” (image seule pour l’instant)
    const lightbox = new PhotoSwipeLightbox({
        dataSource: [
            {
                src: resolvedFullUrl.value,
                width: resolvedSize.value.w || 0,
                height: resolvedSize.value.h || 0,
                alt: props.alt,
                caption: effectiveCaption.value,
            },
        ],
        appendToEl,
        pswpModule: PhotoSwipe,
    });

    // UI: caption + download
    lightbox.on("uiRegister", () => {
        // Caption (bottom)
        lightbox.pswp?.ui?.registerElement({
            name: "krosmoz-caption",
            order: 9,
            isButton: false,
            appendTo: "root",
            html: "",
            onInit: (el, pswp) => {
                el.className = "pswp__custom-caption";
                pswp.on("change", () => {
                    const slide = pswp.currSlide;
                    const cap = slide?.data?.caption || slide?.data?.alt || "";
                    el.textContent = cap;
                });
            },
        });

        if (props.downloadEnabled) {
            lightbox.pswp?.ui?.registerElement({
                name: "download",
                order: 8,
                isButton: true,
                tagName: "button",
                html: "⭳",
                onClick: (e, el, pswp) => {
                    const url = pswp?.currSlide?.data?.src;
                    if (!url) return;
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "";
                    a.rel = "noopener";
                    a.target = "_blank";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                },
            });
        }
    });

    lightbox.init();
    emit("open");
    lightbox.loadAndOpen(0);

    // cleanup
    const teardown = () => {
        try {
            lightbox.destroy();
        } catch {
            // noop
        }
        emit("close");
    };

    // PhotoSwipe v5 : close event sur pswp instance
    lightbox.on("close", teardown);
}

function onTriggerClick() {
    if (!props.openOnClick) return;
    openViewer();
}

async function onPreloadIntent() {
    if (props.preload !== "hover") return;
    if (!resolvedFullUrl.value) return;
    await ensureSize("hover");
}

onBeforeUnmount(() => {
    destroyed = true;
});
</script>

<template>
    <span
        class="group relative inline-flex"
        @mouseenter="onPreloadIntent"
        @focusin="onPreloadIntent"
    >
        <!-- Trigger -->
        <button
            type="button"
            class="relative inline-flex"
            :class="{ 'cursor-zoom-in': openOnClick }"
            :disabled="!openOnClick || isResolving || !resolvedFullUrl"
            @click="onTriggerClick"
            @keydown.enter.prevent="onTriggerClick"
            @keydown.space.prevent="onTriggerClick"
        >
            <Image
                :src="src"
                :source="source"
                :transform="transform"
                :alt="alt"
                v-bind="imageProps"
            />

            <!-- Hover icon -->
            <span
                v-if="showHoverIcon && openOnClick"
                class="pointer-events-none absolute right-2 top-2 opacity-0 scale-90 transition-[opacity,transform] duration-150 ease-out group-hover:opacity-100 group-hover:scale-100"
            >
                <span
                    class="inline-flex h-8 w-8 items-center justify-center box-glass-sm rounded-full"
                    aria-hidden="true"
                >
                    <Icon source="fa-solid fa-magnifying-glass-plus" alt="Agrandir" size="sm" />
                </span>
            </span>
        </button>
    </span>
</template>

<style>
/* Toujours au-dessus (notamment dans un <dialog> top-layer) */
.pswp {
    z-index: 2147483647;
}

/* Caption simple (global, PhotoSwipe est global) */
.pswp__custom-caption {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0.75rem;
    padding: 0.5rem 0.75rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
    pointer-events: none;
}
</style>

