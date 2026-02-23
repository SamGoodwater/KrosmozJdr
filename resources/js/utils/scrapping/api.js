/**
 * Helpers API sécurisés : fetch + parsing JSON sans throw.
 * En cas d’erreur HTTP ou JSON invalide, retourne { ok: false, error }.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

/**
 * Récupère le token CSRF depuis la meta du document (Laravel).
 * @returns {string|null}
 */
export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ?? null;
}

/**
 * GET JSON. Ne lance jamais.
 * @param {string} url - URL
 * @param {{ headers?: Record<string, string> }} [options]
 * @returns {Promise<{ ok: boolean, data?: any, error?: string, status?: number }>}
 */
export async function getJson(url, options = {}) {
    try {
        const res = await fetch(url, {
            method: "GET",
            headers: { Accept: "application/json", ...options.headers },
        });
        let data;
        try {
            const text = await res.text();
            data = text ? JSON.parse(text) : {};
        } catch {
            return { ok: false, error: "Réponse invalide (non JSON)", status: res.status };
        }
        if (!res.ok) {
            return { ok: false, data, error: data?.message ?? data?.error ?? `Erreur ${res.status}`, status: res.status };
        }
        return { ok: true, data, status: res.status };
    } catch (e) {
        return { ok: false, error: e?.message ?? "Erreur réseau" };
    }
}

/**
 * POST JSON. Ne lance jamais.
 * @param {string} url - URL
 * @param {Object} body - Corps (sera JSON.stringify)
 * @param {{ headers?: Record<string, string> }} [options]
 * @returns {Promise<{ ok: boolean, data?: any, error?: string, status?: number }>}
 */
export async function postJson(url, body, options = {}) {
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...options.headers,
            },
            body: JSON.stringify(body),
        });
        let data;
        try {
            const text = await res.text();
            data = text ? JSON.parse(text) : {};
        } catch {
            return { ok: false, error: "Réponse invalide (non JSON)", status: res.status };
        }
        if (!res.ok) {
            return { ok: false, data, error: data?.message ?? data?.error ?? `Erreur ${res.status}`, status: res.status };
        }
        return { ok: true, data, status: res.status };
    } catch (e) {
        return { ok: false, error: e?.message ?? "Erreur réseau" };
    }
}
