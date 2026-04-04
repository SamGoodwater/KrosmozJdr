import { ref, onMounted } from 'vue';
import {
    BREAKPOINT_MD_PX,
    BREAKPOINT_LG_PX,
    MEDIA_QUERY_MOBILE_MAX,
    MEDIA_QUERY_TABLET_ONLY,
    MEDIA_QUERY_DESKTOP_MIN,
} from '@/Composables/layout/viewport-breakpoints';

function readViewportWidth() {
    if (typeof window === 'undefined') {
        return BREAKPOINT_LG_PX;
    }
    return window.innerWidth;
}

function syncFromWidth(width) {
    isMobile.value = width < BREAKPOINT_MD_PX;
    isTablet.value = width >= BREAKPOINT_MD_PX && width < BREAKPOINT_LG_PX;
    isDesktop.value = width >= BREAKPOINT_LG_PX;
}

function syncFromMatchMedia() {
    if (typeof window === 'undefined') {
        return;
    }
    const mMobile = window.matchMedia(MEDIA_QUERY_MOBILE_MAX);
    const mTablet = window.matchMedia(MEDIA_QUERY_TABLET_ONLY);
    const mDesktop = window.matchMedia(MEDIA_QUERY_DESKTOP_MIN);
    isMobile.value = mMobile.matches;
    isTablet.value = mTablet.matches;
    isDesktop.value = mDesktop.matches;
}

const w0 = readViewportWidth();
const isMobile = ref(w0 < BREAKPOINT_MD_PX);
const isTablet = ref(w0 >= BREAKPOINT_MD_PX && w0 < BREAKPOINT_LG_PX);
const isDesktop = ref(w0 >= BREAKPOINT_LG_PX);

let matchMediaBound = false;

/**
 * Viewport : **mobile** (&lt; md), **tablette** (md–lg-1), **desktop** (≥ lg).
 * Aligné sur Tailwind `md` / `lg` via `matchMedia` après montage (recalcul au passage de breakpoint).
 *
 * @returns {{
 *   isMobile: import('vue').Ref<boolean>,
 *   isTablet: import('vue').Ref<boolean>,
 *   isDesktop: import('vue').Ref<boolean>,
 *   updateDeviceFlags: () => void
 * }}
 */
export function useDevice() {
    onMounted(() => {
        if (typeof window === 'undefined') {
            return;
        }
        if (!matchMediaBound) {
            matchMediaBound = true;
            const mMobile = window.matchMedia(MEDIA_QUERY_MOBILE_MAX);
            const mTablet = window.matchMedia(MEDIA_QUERY_TABLET_ONLY);
            const mDesktop = window.matchMedia(MEDIA_QUERY_DESKTOP_MIN);
            const onChange = () => syncFromMatchMedia();
            mMobile.addEventListener('change', onChange);
            mTablet.addEventListener('change', onChange);
            mDesktop.addEventListener('change', onChange);
        }
        syncFromMatchMedia();
    });

    return {
        isMobile,
        isTablet,
        isDesktop,
        /** Force un recalcul depuis `innerWidth` (tests, iframe, cas limites). */
        updateDeviceFlags() {
            syncFromWidth(readViewportWidth());
        },
    };
}
