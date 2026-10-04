<script setup>
/**
 * SiteLoadingOverlay — Écran de chargement plein page (effet tunnel / zoom).
 *
 * @description
 * Pendant le chargement : zoom lent ~8,5 s puis dézoom ~8,5 s (cycle 17 s). Entrée du texte :
 * fondu d’opacité long (police) + léger zoom. Sortie : plongée rapide (~500 ms) avec
 * opacité à 0 avant la fin du zoom pour révéler le site. Titre Krosmoz / JDR + dots.
 * Astuces bas centrées : fondu une par une jusqu’à la sortie de l’écran.
 *
 * @see useSiteLoadingOverlay
 * @see pickLoadingTip
 * @example
 * <!-- Monté une fois dans app.js, au-dessus de l’app Inertia -->
 * <SiteLoadingOverlay :tips="loadingTips" />
 */
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
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
import { pickLoadingTip, resolveLoadingTipHoldMs } from "@/Utils/layout/pickLoadingTip";

const props = defineProps({
    /** @type {import('vue').PropType<Array<{ body: string, url?: string|null, featured?: boolean, duration_seconds?: number }>>} */
    tips: { type: Array, default: () => [] },
});

const FADE_MS = 600;

const { dismissManual, initSiteLoadingReadyWatcher, markControlsVisible, markSiteLoadingReady } =
    useSiteLoadingOverlay();

const stageZoomClass = computed(() => {
    if (siteLoadingPhase.value === "ready") {
        return "site-loading-overlay__stage--infinite";
    }
    return "site-loading-overlay__stage--pulse";
});

const currentTip = ref(null);
const tipVisible = ref(false);
const tipKey = ref(0);

let stopReadyWatcher = null;
/** @type {ReturnType<typeof setTimeout>|null} */
let tipTimer = null;
let tipCycleStopped = false;

function clearTipTimer() {
    if (tipTimer !== null) {
        clearTimeout(tipTimer);
        tipTimer = null;
    }
}

function prefersReducedMotion() {
    return typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

function scheduleTipCycle() {
    clearTipTimer();
    if (tipCycleStopped || !siteLoadingActive.value || siteLoadingExiting.value) {
        return;
    }
    if (!Array.isArray(props.tips) || props.tips.length === 0) {
        currentTip.value = null;
        tipVisible.value = false;
        return;
    }

    const next = pickLoadingTip(props.tips, currentTip.value);
    if (!next) {
        currentTip.value = null;
        tipVisible.value = false;
        return;
    }

    currentTip.value = next;
    tipKey.value += 1;
    tipVisible.value = false;

    const fadeMs = prefersReducedMotion() ? 0 : FADE_MS;
    const holdMs = resolveLoadingTipHoldMs(next);

    nextTick(() => {
        if (tipCycleStopped || !siteLoadingActive.value || siteLoadingExiting.value) {
            return;
        }
        tipVisible.value = true;
        tipTimer = window.setTimeout(() => {
            tipVisible.value = false;
            tipTimer = window.setTimeout(() => {
                scheduleTipCycle();
            }, fadeMs);
        }, fadeMs + holdMs);
    });
}

function stopTipCycle() {
    tipCycleStopped = true;
    clearTipTimer();
    tipVisible.value = false;
}

watch(siteLoadingExiting, (exiting) => {
    if (exiting) {
        stopTipCycle();
    }
});

watch(siteLoadingActive, (active) => {
    if (!active) {
        stopTipCycle();
        currentTip.value = null;
    }
});

onMounted(() => {
    removeBootSplash();
    markControlsVisible();
    if (document.readyState === "complete") {
        markSiteLoadingReady();
    }
    stopReadyWatcher = initSiteLoadingReadyWatcher();
    if (siteLoadingActive.value) {
        tipCycleStopped = false;
        scheduleTipCycle();
    }
});

onUnmounted(() => {
    stopReadyWatcher?.();
    stopTipCycle();
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

                <div
                    v-if="currentTip"
                    class="site-loading-overlay__tips pointer-events-auto absolute inset-x-0 bottom-6 z-10 px-6 text-center sm:bottom-8"
                >
                    <a
                        v-if="currentTip.url"
                        :key="`tip-link-${tipKey}`"
                        :href="currentTip.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="site-loading-overlay__tip site-loading-overlay__tip--link inline-block max-w-3xl text-white/90 underline-offset-4 hover:underline"
                        :class="tipVisible ? 'site-loading-overlay__tip--visible' : ''"
                    >
                        {{ currentTip.body }}
                    </a>
                    <p
                        v-else
                        :key="`tip-text-${tipKey}`"
                        class="site-loading-overlay__tip mx-auto max-w-3xl text-white/90"
                        :class="tipVisible ? 'site-loading-overlay__tip--visible' : ''"
                    >
                        {{ currentTip.body }}
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
    animation: site-loading-pulse 17s ease-in-out infinite;
}

.site-loading-overlay__stage--infinite {
    animation: site-loading-infinite 8.5s linear infinite;
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

.site-loading-overlay--exiting .site-loading-overlay__close,
.site-loading-overlay--exiting .site-loading-overlay__tips {
    animation: site-loading-content-exit 220ms ease-in forwards;
}

/** ~8,5 s zoom in, ~8,5 s zoom out — boucle tant que le document charge. */
@keyframes site-loading-pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.55);
    }
}

/** Zoom continu 8,5 s une fois le site prêt (avant le fondu de sortie). */
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

.site-loading-overlay__tip {
    font-size: clamp(0.875rem, 2.2vw, 1.125rem);
    line-height: 1.45;
    text-shadow: 0 2px 16px rgba(0, 0, 0, 0.85);
    opacity: 0;
    transition: opacity 600ms ease;
}

.site-loading-overlay__tip--visible {
    opacity: 1;
}

.site-loading-overlay__tip--link {
    cursor: pointer;
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
    .site-loading-overlay--exiting .site-loading-overlay__close,
    .site-loading-overlay--exiting .site-loading-overlay__tips {
        animation: none;
        opacity: 0;
    }

    .site-loading-overlay__content--visible {
        animation: site-loading-content-enter-reduced 1.6s ease-out forwards;
    }

    .site-loading-overlay__tip {
        transition: none;
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
