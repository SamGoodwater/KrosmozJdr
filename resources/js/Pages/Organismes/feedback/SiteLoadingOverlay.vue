<script setup>
/**
 * SiteLoadingOverlay — Écran de chargement plein page (effet tunnel / zoom).
 *
 * @description
 * Pendant le chargement : zoom lent 20 s puis dézoom 20 s (boucle). Entrée du texte :
 * fondu d’opacité long (police) + léger zoom. Sortie : plongée rapide (~500 ms) avec
 * opacité à 0 avant la fin du zoom pour révéler le site. Titre Krosmoz / JDR + dots.
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
    transform-origin: center center;
    will-change: transform, opacity;
}

.site-loading-overlay__stage {
    transform-origin: center center;
    will-change: transform;
}

.site-loading-overlay__stage--pulse {
    animation: site-loading-pulse 40s ease-in-out infinite;
}

.site-loading-overlay__stage--infinite {
    animation: site-loading-infinite 20s linear infinite;
}

/** Sortie : plongée rapide ; le stage reste figé (échelle courante) pendant que l’overlay scale. */
.site-loading-overlay--exiting {
    pointer-events: none;
    animation: site-loading-exit 500ms cubic-bezier(0.4, 0, 1, 1) forwards;
}

.site-loading-overlay--exiting .site-loading-overlay__stage--pulse,
.site-loading-overlay--exiting .site-loading-overlay__stage--infinite {
    animation-play-state: paused;
}

.site-loading-overlay--exiting .site-loading-overlay__content {
    animation: site-loading-content-exit 280ms ease-in forwards;
}

.site-loading-overlay--exiting .site-loading-overlay__close {
    animation: site-loading-content-exit 220ms ease-in forwards;
}

/** 20 s zoom in, 20 s zoom out — boucle tant que le document charge. */
@keyframes site-loading-pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.55);
    }
}

/** Zoom continu 20 s une fois le site prêt (avant le fondu de sortie). */
@keyframes site-loading-infinite {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.55);
    }
}

/**
 * Plongée : opacité à 0 vers ~65 % pour révéler le site avant la fin du zoom.
 */
@keyframes site-loading-exit {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    65% {
        opacity: 0;
    }
    100% {
        opacity: 0;
        transform: scale(2.45);
    }
}

.site-loading-overlay__content {
    opacity: 0;
    transform: scale(0.9);
    transform-origin: center center;
}

/**
 * Entrée texte : fondu long (masque le swap de police) + léger zoom / dézoom.
 */
.site-loading-overlay__content--visible {
    animation: site-loading-content-enter 2.8s ease-out forwards;
}

@keyframes site-loading-content-enter {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    40% {
        opacity: 0.45;
        transform: scale(1.04);
    }
    62% {
        transform: scale(1);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes site-loading-content-exit {
    to {
        opacity: 0;
        transform: scale(1.28);
    }
}

/** Retrait DOM après sortie CSS : pas de second fondu. */
.site-loading-fade-enter-active,
.site-loading-fade-leave-active {
    transition: none;
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

    .site-loading-overlay--exiting {
        animation: site-loading-exit-reduced 320ms ease forwards;
    }

    .site-loading-overlay--exiting .site-loading-overlay__content,
    .site-loading-overlay--exiting .site-loading-overlay__close {
        animation: none;
        opacity: 0;
    }

    .site-loading-overlay__content--visible {
        animation: site-loading-content-enter-reduced 1.6s ease-out forwards;
    }
}

@keyframes site-loading-exit-reduced {
    to {
        opacity: 0;
    }
}

@keyframes site-loading-content-enter-reduced {
    to {
        opacity: 1;
        transform: none;
    }
}
</style>
