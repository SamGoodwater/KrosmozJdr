/**
 * Pile de z-index partagée pour tooltips / popovers téléportés vers `body`.
 * Chaque ouverture reçoit une couche plus haute : le dernier survolé reste au-dessus.
 *
 * @returns {number} z-index > 1100, croissant jusqu’à un plafond puis réinitialisé.
 *
 * @example
 * watch(() => isOpen.value, (open, wasOpen) => {
 *   if (open && wasOpen !== true) {
 *     z.value = allocateTooltipZIndex();
 *   }
 * }, { immediate: true });
 */
const TOOLTIP_Z_BASE = 1100;
const TOOLTIP_Z_CEILING = 2_147_400_000;

let highWater = TOOLTIP_Z_BASE;

export function allocateTooltipZIndex() {
    highWater += 1;
    if (highWater > TOOLTIP_Z_CEILING) {
        highWater = TOOLTIP_Z_BASE + 1;
    }
    return highWater;
}
