/**
 * Compte d’items visibles dans une rangée à overflow mesuré.
 *
 * @description
 * Observe `rowRef` (largeur utile) et `measureRef` (rangée fantôme :
 * un enfant par item, plus le bouton « more » en dernier). Recalcule
 * au resize, au montage, et quand `itemCount` change.
 *
 * @param {object} options
 * @param {import('vue').Ref<HTMLElement|null>} options.rowRef
 * @param {import('vue').Ref<HTMLElement|null>} options.measureRef
 * @param {import('vue').Ref<number>|import('vue').ComputedRef<number>} options.itemCount
 * @param {import('vue').Ref<boolean>|boolean} [options.alwaysReserveMore=true]
 * @param {number} [options.gapFallbackPx=2]
 * @returns {{ visibleCount: import('vue').Ref<number> }}
 *
 * @example
 * const { visibleCount } = useHorizontalOverflowCount({ rowRef, measureRef, itemCount });
 */
import {
    computed,
    nextTick,
    onMounted,
    onUnmounted,
    ref,
    unref,
    watch,
} from "vue";
import { countFittingItems } from "@/Utils/layout/countFittingItems";

/**
 * @param {HTMLElement | null} el
 * @param {number} fallback
 * @returns {number}
 */
function readGapPx(el, fallback) {
    if (!el || typeof window === "undefined") {
        return fallback;
    }
    const g = getComputedStyle(el).gap || getComputedStyle(el).columnGap;
    const parsed = parseFloat(g);
    return Number.isFinite(parsed) ? parsed : fallback;
}

export function useHorizontalOverflowCount(options) {
    const rowRef = options.rowRef;
    const measureRef = options.measureRef;
    const itemCount = options.itemCount;
    const gapFallbackPx = options.gapFallbackPx ?? 2;
    const alwaysReserveMore = computed(() =>
        Boolean(unref(options.alwaysReserveMore ?? true)),
    );

    const visibleCount = ref(0);
    const measureWidthPx = ref(0);

    /** @type {ResizeObserver | null} */
    let rowObserver = null;
    /** @type {number} */
    let layoutRetryTimer = 0;

    function updateVisibleCount() {
        const row = rowRef?.value;
        const measure = measureRef?.value;
        const n = Number(unref(itemCount)) || 0;

        if (!row || !measure || n === 0) {
            visibleCount.value = n;
            measureWidthPx.value = 0;
            return;
        }

        const available = row.getBoundingClientRect().width;
        if (available <= 1) {
            return;
        }

        measureWidthPx.value = Math.floor(available);

        const kids = Array.from(measure.children);
        if (kids.length < 2) {
            return;
        }

        const moreEl = kids[kids.length - 1];
        const itemEls = kids.slice(0, -1);
        if (itemEls.length !== n) {
            return;
        }

        const widths = itemEls.map((el) => el.getBoundingClientRect().width);
        const moreW = moreEl.getBoundingClientRect().width;
        const gap = readGapPx(measure, gapFallbackPx);

        visibleCount.value = countFittingItems(
            widths,
            moreW,
            gap,
            available,
            alwaysReserveMore.value,
        );
    }

    function scheduleLayout(retry = 0) {
        if (typeof window !== "undefined" && layoutRetryTimer) {
            window.clearTimeout(layoutRetryTimer);
            layoutRetryTimer = 0;
        }

        nextTick(() => {
            requestAnimationFrame(() => {
                updateVisibleCount();

                const row = rowRef?.value;
                const measure = measureRef?.value;
                const n = Number(unref(itemCount)) || 0;
                const kids = measure ? measure.children.length : 0;
                const measureReady = !measure || kids >= n + 1;
                const rowReady = !row || row.getBoundingClientRect().width > 1;

                if (retry < 4 && n > 0 && (!measureReady || !rowReady)) {
                    layoutRetryTimer = window.setTimeout(
                        () => scheduleLayout(retry + 1),
                        50,
                    );
                }
            });
        });
    }

    function onWindowResize() {
        scheduleLayout();
    }

    onMounted(() => {
        scheduleLayout();
        if (typeof document !== "undefined" && document.fonts?.ready) {
            document.fonts.ready.then(() => scheduleLayout());
        }
        if (typeof window !== "undefined") {
            window.addEventListener("resize", onWindowResize, { passive: true });
        }
    });

    onUnmounted(() => {
        rowObserver?.disconnect();
        rowObserver = null;
        if (layoutRetryTimer) {
            window.clearTimeout(layoutRetryTimer);
            layoutRetryTimer = 0;
        }
        if (typeof window !== "undefined") {
            window.removeEventListener("resize", onWindowResize);
        }
    });

    watch(
        () => Number(unref(itemCount)) || 0,
        () => {
            visibleCount.value = 0;
            scheduleLayout();
        },
    );

    watch(
        rowRef,
        (el) => {
            rowObserver?.disconnect();
            if (el && typeof ResizeObserver !== "undefined") {
                if (!rowObserver) {
                    rowObserver = new ResizeObserver(() => scheduleLayout());
                }
                rowObserver.observe(el);
            }
            scheduleLayout();
        },
        { immediate: true },
    );

    return { visibleCount, measureWidthPx, scheduleLayout };
}
