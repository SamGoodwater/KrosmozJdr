/**
 * Retourne une couleur interpolée pour un level (1-20) sur un gradient froid → chaud.
 * Vert (#22c55e) → Jaune (#eab308) → Rouge (#ef4444).
 *
 * @param {number} level - Level 1..20
 * @param {number} [maxLevel=20]
 * @returns {string} Couleur hex
 */
export function levelColor(level, maxLevel = 20) {
    const t = Math.max(0, Math.min(1, (level - 1) / (maxLevel - 1)));
    const r1 = 34, g1 = 197, b1 = 94;
    const r2 = 234, g2 = 179, b2 = 8;
    const r3 = 239, g3 = 68, b3 = 68;

    let r, g, b;
    if (t <= 0.5) {
        const s = t * 2;
        r = Math.round(r1 + (r2 - r1) * s);
        g = Math.round(g1 + (g2 - g1) * s);
        b = Math.round(b1 + (b2 - b1) * s);
    } else {
        const s = (t - 0.5) * 2;
        r = Math.round(r2 + (r3 - r2) * s);
        g = Math.round(g2 + (g3 - g2) * s);
        b = Math.round(b2 + (b3 - b2) * s);
    }
    return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
}

/**
 * Retourne une couleur semi-transparente pour un level.
 *
 * @param {number} level
 * @param {number} [opacity=0.2]
 * @param {number} [maxLevel=20]
 * @returns {string} Couleur rgba
 */
export function levelBgColor(level, opacity = 0.2, maxLevel = 20) {
    const hex = levelColor(level, maxLevel);
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}
