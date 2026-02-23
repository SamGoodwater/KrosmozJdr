/**
 * Parse le champ "IDs" de recherche (un ID, liste, ou plage).
 * @param {string} text - Texte saisi (ex: "12", "12,13,14", "12-50")
 * @returns {{ id?: string, ids?: string, idMin?: number, idMax?: number }} Objet pour query API (vide si invalide)
 */
export function parseIdsFilter(text) {
    const txt = String(text ?? "").trim();
    if (!txt) return {};

    // Plage: "a-b"
    const m = txt.match(/^(\d+)\s*-\s*(\d+)$/);
    if (m) {
        const a = Number(m[1]);
        const b = Number(m[2]);
        if (Number.isFinite(a) && Number.isFinite(b)) {
            return { idMin: Math.min(a, b), idMax: Math.max(a, b) };
        }
    }

    // Liste: "1,2,3"
    if (txt.includes(",")) {
        const parts = txt.split(",").map((p) => p.trim()).filter(Boolean);
        return { ids: parts.join(",") };
    }

    // Un seul ID
    if (/^\d+$/.test(txt)) {
        return { id: txt };
    }

    return {};
}
