import { ref } from "vue";

const SESSION_DISMISS_KEY = "krosmoz-site-loading-dismissed";
const MIN_VISIBLE_MS = 1400;
const READY_HOLD_MS = 900;
const MAX_VISIBLE_MS = 22000;
/** Durée de la sortie zoom-through (doit matcher l’anim CSS `--exiting`). */
const EXIT_MS = 500;

/** @type {import('vue').Ref<'loading'|'ready'>} */
export const siteLoadingPhase = ref("loading");

/** @type {number|null} Horodatage passage en phase `ready`. */
let siteLoadingReadyAt = null;

const LOADING_WEBP = "/storage/images/backgrounds/loading.webp";
const LOADING_PNG = "/storage/images/backgrounds/loading.png";

/** @type {import('vue').Ref<boolean>} */
export const siteLoadingActive = ref(readInitialActive());

/** @type {import('vue').Ref<boolean>} */
export const siteLoadingExiting = ref(false);

/** @type {import('vue').Ref<boolean>} */
export const siteLoadingControlsVisible = ref(false);

export const siteLoadingImageSources = Object.freeze({
    webp: LOADING_WEBP,
    png: LOADING_PNG,
});

/**
 * État de l’écran de chargement plein page (zoom tunnel).
 *
 * @returns {{ dismissManual: () => void, startExit: () => void, initSiteLoadingReadyWatcher: () => void, markControlsVisible: () => void }}
 */
export function useSiteLoadingOverlay() {
    function startExit() {
        if (!siteLoadingActive.value || siteLoadingExiting.value) {
            return;
        }
        siteLoadingExiting.value = true;
        window.setTimeout(() => {
            siteLoadingActive.value = false;
            siteLoadingExiting.value = false;
            siteLoadingControlsVisible.value = false;
            siteLoadingPhase.value = "loading";
            siteLoadingReadyAt = null;
            removeBootSplash();
        }, EXIT_MS);
    }

    function dismissManual() {
        try {
            sessionStorage.setItem(SESSION_DISMISS_KEY, "1");
        } catch {
            /* sessionStorage indisponible */
        }
        startExit();
    }

    function markControlsVisible() {
        siteLoadingControlsVisible.value = true;
    }

    function markSiteLoadingReady() {
        if (siteLoadingPhase.value !== "loading") {
            return;
        }
        siteLoadingPhase.value = "ready";
        siteLoadingReadyAt = performance.now();
    }

    function initSiteLoadingReadyWatcher() {
        if (!siteLoadingActive.value) {
            removeBootSplash();
            return;
        }

        const startedAt = performance.now();
        let disposed = false;

        if (isDocumentReady()) {
            markSiteLoadingReady();
        }

        const poll = () => {
            if (disposed || !siteLoadingActive.value) {
                return;
            }

            const elapsed = performance.now() - startedAt;

            if (elapsed >= MAX_VISIBLE_MS) {
                startExit();
                return;
            }

            if (isDocumentReady() && siteLoadingPhase.value === "loading") {
                markSiteLoadingReady();
            }

            if (
                siteLoadingReadyAt !== null
                && elapsed >= MIN_VISIBLE_MS
                && performance.now() - siteLoadingReadyAt >= READY_HOLD_MS
            ) {
                startExit();
                return;
            }

            window.setTimeout(poll, 120);
        };

        void waitForPaint().then(() => {
            if (!disposed) {
                poll();
            }
        });

        return () => {
            disposed = true;
        };
    }

    return {
        dismissManual,
        startExit,
        initSiteLoadingReadyWatcher,
        markControlsVisible,
        markSiteLoadingReady,
    };
}

function readInitialActive() {
    if (typeof window === "undefined") {
        return true;
    }
    try {
        return sessionStorage.getItem(SESSION_DISMISS_KEY) !== "1";
    } catch {
        return true;
    }
}

function isDocumentReady() {
    return document.readyState === "complete";
}

function waitForPaint() {
    return new Promise((resolve) => {
        if (document.readyState === "complete") {
            requestAnimationFrame(() => requestAnimationFrame(resolve));
            return;
        }

        const onLoad = () => {
            requestAnimationFrame(() => requestAnimationFrame(resolve));
        };

        window.addEventListener("load", onLoad, { once: true });
    });
}

/** Retire le splash HTML injecté dans app.blade.php avant l’hydratation Vue. */
export function removeBootSplash() {
    document.getElementById("site-loading-boot")?.remove();
}
