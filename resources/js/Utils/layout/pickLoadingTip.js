/**
 * Tirage aléatoire pondéré des astuces d’écran de chargement.
 *
 * Une astuce `featured` pèse 3, une normale 1. On évite de répéter
 * immédiatement la même astuce s’il en existe une autre.
 *
 * @param {Array<{ body: string, url?: string|null, featured?: boolean }>} tips
 * @param {{ body: string, url?: string|null, featured?: boolean }|null} [previous]
 * @param {() => number} [random] générateur ∈ [0, 1) — injectable pour les tests
 * @returns {{ body: string, url?: string|null, featured?: boolean }|null}
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
