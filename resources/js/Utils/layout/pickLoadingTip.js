/** Durée d’affichage par défaut (secondes), hors fondu. */
export const DEFAULT_LOADING_TIP_DURATION_SECONDS = 8;
export const MIN_LOADING_TIP_DURATION_SECONDS = 2;
export const MAX_LOADING_TIP_DURATION_SECONDS = 30;

/**
 * Convertit la durée d’une astuce en millisecondes de maintien (hors fondu).
 *
 * @param {{ duration_seconds?: number|null }|null|undefined} tip
 * @param {number} [fallbackSeconds]
 * @returns {number}
 *
 * @example
 * resolveLoadingTipHoldMs({ duration_seconds: 10 }) // 10000
 */
export function resolveLoadingTipHoldMs(
    tip,
    fallbackSeconds = DEFAULT_LOADING_TIP_DURATION_SECONDS,
) {
    const raw = Number(tip?.duration_seconds);
    const seconds = Number.isFinite(raw)
        ? Math.min(
              MAX_LOADING_TIP_DURATION_SECONDS,
              Math.max(MIN_LOADING_TIP_DURATION_SECONDS, Math.round(raw)),
          )
        : fallbackSeconds;

    return seconds * 1000;
}

/**
 * Tirage aléatoire pondéré des astuces d’écran de chargement.
 *
 * Une astuce `featured` pèse 3, une normale 1. On évite de répéter
 * immédiatement la même astuce s’il en existe une autre.
 *
 * @param {Array<{ body: string, url?: string|null, featured?: boolean, duration_seconds?: number }>} tips
 * @param {{ body: string, url?: string|null, featured?: boolean, duration_seconds?: number }|null} [previous]
 * @param {() => number} [random] générateur ∈ [0, 1) — injectable pour les tests
 * @returns {{ body: string, url?: string|null, featured?: boolean, duration_seconds?: number }|null}
 *
 * @example
 * pickLoadingTip([{ body: 'A', featured: true }, { body: 'B' }], null)
 */
export function pickLoadingTip(tips, previous = null, random = Math.random) {
    if (!Array.isArray(tips) || tips.length === 0) {
        return null;
    }

    let pool = tips;
    if (previous && tips.length > 1) {
        const withoutPrevious = tips.filter((tip) => tip !== previous && tip.body !== previous.body);
        if (withoutPrevious.length > 0) {
            pool = withoutPrevious;
        }
    }

    let totalWeight = 0;
    const weighted = pool.map((tip) => {
        const weight = tip.featured ? 3 : 1;
        totalWeight += weight;
        return { tip, weight };
    });

    if (totalWeight <= 0) {
        return pool[0] ?? null;
    }

    let ticket = random() * totalWeight;
    for (const entry of weighted) {
        ticket -= entry.weight;
        if (ticket < 0) {
            return entry.tip;
        }
    }

    return weighted[weighted.length - 1]?.tip ?? null;
}
