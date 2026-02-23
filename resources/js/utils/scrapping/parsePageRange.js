/**
 * Parse la plage de pages saisie (ex: "1-6", "4,5", "1-3,5,7").
 * @param {string} text - Texte saisi
 * @returns {number[]} Liste ordonnée de numéros de page (1-based), ou [] si vide/invalide
 */
export function parsePageRange(text) {
    const raw = String(text ?? "").trim();
    if (!raw) return [];
    const parts = raw.split(",").map((p) => p.trim()).filter(Boolean);
    const numbers = new Set();
    for (const part of parts) {
        const dash = part.indexOf("-");
        if (dash >= 0) {
            const a = Math.max(1, Math.floor(Number(part.slice(0, dash)) || 1));
            const b = Math.max(1, Math.floor(Number(part.slice(dash + 1)) || 1));
            const lo = Math.min(a, b);
            const hi = Math.max(a, b);
            for (let p = lo; p <= hi; p++) numbers.add(p);
        } else {
            const n = Math.max(1, Math.floor(Number(part) || 0));
            if (n > 0) numbers.add(n);
        }
    }
    return Array.from(numbers).sort((a, b) => a - b);
}
