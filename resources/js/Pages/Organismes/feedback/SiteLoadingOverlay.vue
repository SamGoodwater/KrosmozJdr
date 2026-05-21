<script setup>
/**
 * SiteLoadingOverlay — Écran de chargement plein page (effet tunnel / zoom).
 *
 * @description
 * Pendant le chargement : zoom lent 5 s puis dézoom (boucle). À la fin du chargement :
 * zoom infini linéaire avant fondu de sortie. Titre Krosmoz / JDR, « Chargement » + dots DaisyUI.
 *
 * @see useSiteLoadingOverlay
 * @example
 * <!-- Monté une fois dans app.js, au-dessus de l’app Inertia -->
 */
import { computed, onMounted, onUnmounted } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import {
    siteLoadingActive,
    siteLoadingControlsVisible,
    siteLoadingExiting,
    siteLoadingImageSources,
    siteLoadingPhase,
    removeBootSplash,
    useSiteLoadingOverlay,
} from "@/Composables/layout/useSiteLoadingOverlay";

const { dismissManual, initSiteLoadingReadyWatcher, markControlsVisible, markSiteLoadingReady } =
    useSiteLoadingOverlay();

const stageZoomClass = computed(() => {
    if (siteLoadingExiting.value) {
        return "";
    }
    if (siteLoadingPhase.value === "ready") {
        return "site-loading-overlay__stage--infinite";
    }
    return "site-loading-overlay__stage--pulse";
});

let stopReadyWatcher = null;

onMounted(() => {
    removeBootSplash();
    markControlsVisible();
    if (document.readyState === "complete") {
        markSiteLoadingReady();
    }
    stopReadyWatcher = initSiteLoadingReadyWatcher();
});

onUnmounted(() => {
    stopReadyWatcher?.();
});
</script>

<template>
    <Teleport to="body">
        <Transition name="site-loading-fade">
            <div
                v-if="siteLoadingActive"
                class="site-loading-overlay fixed inset-0 flex items-center justify-center overflow-hidden bg-black"
                :class="siteLoadingExiting ? 'site-loading-overlay--exiting' : ''"
                role="dialog"
                aria-modal="true"
                aria-label="Chargement du site"
                aria-live="polite"
            >
                <div
                    class="site-loading-overlay__stage absolute inset-0"
                    :class="stageZoomClass"
                    aria-hidden="true"
                >
                    <picture>
                        <source type="image/webp" :srcset="siteLoadingImageSources.webp" />
                        <img
                            class="site-loading-overlay__image h-full w-full object-cover"
                            :src="siteLoadingImageSources.png"
                            alt=""
                            decoding="async"
                            fetchpriority="high"
                        />
                    </picture>
                </div>

                <div
                    class="site-loading-overlay__content pointer-events-none absolute inset-0 z-1 flex flex-col items-center justify-center text-center"
                    :class="siteLoadingControlsVisible ? 'site-loading-overlay__content--visible' : ''"
                >
                    <h1 class="site-loading-overlay__brand font-heading font-bold leading-none tracking-wide text-white drop-shadow-[0_2px_28px_rgba(0,0,0,0.9)]">
                        <span class="site-loading-overlay__brand-krosmoz block text-8xl sm:text-9xl">Krosmoz</span>
                        <span class="site-loading-overlay__brand-jdr mt-3 block text-5xl font-semibold tracking-[0.35em] text-white/95 drop-shadow-[0_2px_22px_rgba(0,0,0,0.85)] sm:text-6xl">
                            JDR
                        </span>
                    </h1>

                    <p
                        class="site-loading-overlay__status mt-10 flex items-center justify-center gap-3 text-2xl font-medium text-white/90 sm:text-3xl"
                        aria-hidden="true"
                    >
                        <span>Chargement</span>
                        <Loading type="dots" size="lg" class="text-white" />
                    </p>
                </div>

                <button
                    v-show="siteLoadingControlsVisible"
                    type="button"
                    class="site-loading-overlay__close btn btn-ghost btn-sm btn-circle absolute top-4 right-4 z-10 border border-white/10 bg-black/25 text-white/55 hover:border-white/25 hover:bg-black/45 hover:text-white/90"
                    aria-label="Fermer l’écran de chargement"
                    @click="dismissManual"
                >
                    <Icon source="fa-xmark" pack="solid" size="sm" alt="" />
                </button>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped lang="scss">
.site-loading-overlay {
    z-index: 10050;
}

.site-loading-overlay__stage {
    transform-origin: center center;
    will-change: transform;
}

.site-loading-overlay__stage--pulse {
    animation: site-loading-pulse 10s ease-in-out infinite;
}

.site-loading-overlay__stage--infinite {
    animation: site-loading-infinite 5s linear infinite;
}

.site-loading-overlay--exiting .site-loading-overlay__stage--pulse,
.site-loading-overlay--exiting .site-loading-overlay__stage--infinite {
    animation-play-state: paused;
}

/** 5 s zoom in, 5 s zoom out — boucle tant que le document charge. */
@keyframes site-loading-pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.55);
    }
}

/** Zoom continu une fois le site prêt (avant le fondu de sortie). */
@keyframes site-loading-infinite {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.55);
    }
}

.site-loading-overlay__content {
    opacity: 0;
    transform: translateY(1.25rem) scale(0.88);
}

.site-loading-overlay__content--visible {
    animation: site-loading-content-enter 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes site-loading-content-enter {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.site-loading-fade-enter-active,
.site-loading-fade-leave-active {
    transition: opacity 0.52s ease;
}

.site-loading-fade-enter-from,
.site-loading-fade-leave-to {
    opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
    .site-loading-overlay__stage--pulse,
    .site-loading-overlay__stage--infinite {
        animation: none;
        transform: scale(1.06);
    }

    .site-loading-overlay__content--visible {
        animation: none;
        opacity: 1;
        transform: none;
    }
}
</style>
