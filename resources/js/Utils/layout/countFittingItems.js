/**
 * Nombre d’items qui tiennent dans une rangée horizontale avec overflow.
 *
 * @description
 * Calcule le plus grand `k` tel que la somme des `k` premiers items
 * (+ le bouton « more » si besoin) ne dépasse pas `availablePx`.
 *
 * @param {number[]} itemWidthsPx Largeurs des items, dans l’ordre d’affichage.
 * @param {number} moreWidthPx Largeur du bouton overflow (dropdown).
 * @param {number} gapPx Espacement entre items.
 * @param {number} availablePx Largeur utile de la rangée.
 * @param {boolean} [alwaysReserveMore=true] Réserver le bouton more même si tout tient.
 * @returns {number}
 *
 * @example
 * countFittingItems([24, 24, 24], 24, 2, 80, true); // 2
 */
export function countFittingItems(
    itemWidthsPx,
    moreWidthPx,
    gapPx,
    availablePx,
    alwaysReserveMore = true,
) {
    const widths = Array.isArray(itemWidthsPx) ? itemWidthsPx : [];
    const n = widths.length;
    const available = Number(availablePx);
    const gap = Number.isFinite(Number(gapPx)) ? Number(gapPx) : 0;
    const moreW = Number.isFinite(Number(moreWidthPx)) ? Number(moreWidthPx) : 0;

    if (n === 0 || !Number.isFinite(available) || available <= 0) {
        return 0;
    }

    for (let k = n; k >= 0; k -= 1) {
        let sum = 0;
        for (let i = 0; i < k; i += 1) {
            sum += widths[i];
            if (i < k - 1) {
                sum += gap;
            }
        }
        const needMore = alwaysReserveMore || k < n;
        const moreExtra = needMore ? (k > 0 ? gap : 0) + moreW : 0;
        if (sum + moreExtra <= available + 0.5) {
            return k;
        }
    }

    return 0;
}
