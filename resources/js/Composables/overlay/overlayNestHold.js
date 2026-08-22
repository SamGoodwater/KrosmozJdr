/**
 * Maintient un overlay hover ouvert tant qu’un overlay enfant (popover vue texte) est ouvert.
 *
 * @example
 * const nestCount = provideOverlayNestHold();
 * const parentHold = useOverlayNestHold();
 */
import { inject, provide, ref } from "vue";

export const OVERLAY_NEST_HOLD_KEY = Symbol("overlayNestHold");

/**
 * @returns {import('vue').Ref<number>}
 */
export function provideOverlayNestHold() {
    const holdCount = ref(0);

    provide(OVERLAY_NEST_HOLD_KEY, {
        acquire() {
            holdCount.value += 1;
        },
        release() {
            holdCount.value = Math.max(0, holdCount.value - 1);
        },
    });

    return holdCount;
}

/**
 * @returns {{ acquire: () => void, release: () => void } | null}
 */
export function useOverlayNestHold() {
    return inject(OVERLAY_NEST_HOLD_KEY, null);
}
