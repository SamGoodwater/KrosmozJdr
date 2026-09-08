/**
 * Maintient une carte minimale déployée tant qu’un overlay issu d’elle est ouvert.
 *
 * @description
 * Tooltips (`OverlayTrigger`) et menus (`Dropdown`) sont téléportés hors de la carte :
 * le `mouseleave` replierait sinon la vue et démonterait le déclencheur avant
 * d’atteindre le panneau (changement d’état, actions « ⋮ », etc.).
 *
 * @example
 * const holdCount = provideEntityMinimalCardOverlayHold();
 * const hold = useEntityMinimalCardOverlayHold();
 * hold?.acquire();
 */
import { inject, provide, ref } from "vue";

export const ENTITY_MINIMAL_CARD_OVERLAY_HOLD_KEY = Symbol("entityMinimalCardOverlayHold");

/**
 * @returns {import('vue').Ref<number>}
 */
export function provideEntityMinimalCardOverlayHold() {
    const holdCount = ref(0);

    provide(ENTITY_MINIMAL_CARD_OVERLAY_HOLD_KEY, {
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
export function useEntityMinimalCardOverlayHold() {
    return inject(ENTITY_MINIMAL_CARD_OVERLAY_HOLD_KEY, null);
}
