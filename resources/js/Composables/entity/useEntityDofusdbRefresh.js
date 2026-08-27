/**
 * Rafraîchissement DofusDB unitaire (id local, MJ+).
 *
 * @example
 * const { previewRefresh, applyRefresh } = useEntityDofusdbRefresh();
 * await previewRefresh('spells', 12);
 */
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
}

/**
 * @param {string} entityType
 * @param {number|string} entityId
 * @param {{ mode: 'preview'|'full'|'images_only', force?: boolean }} payload
 * @returns {Promise<{ ok: boolean, status: number, body: Record<string, unknown> }>}
 */
async function postRefresh(entityType, entityId, payload) {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        throw new Error("Token CSRF introuvable. Recharge la page.");
    }
    const plural = normalizeActionEntityType(entityType);
    const url = `/api/entities/${encodeURIComponent(plural)}/${entityId}/dofusdb-refresh`;
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
        body: JSON.stringify(payload),
    });
    let body = {};
    try {
        body = await response.json();
    } catch {
        body = {};
    }
    return { ok: response.ok, status: response.status, body };
}

export function useEntityDofusdbRefresh() {
    const { success, error: showError } = useNotificationStore();

    /**
     * @param {string} entityType
     * @param {number|string} entityId
     */
    const previewRefresh = async (entityType, entityId) => {
        const result = await postRefresh(entityType, entityId, { mode: "preview" });
        if (result.status === 403) {
            showError(result.body.message || "Tu n’as pas le droit de rafraîchir cette fiche.");
            return null;
        }
        if (result.status >= 500) {
            showError(result.body.message || "Erreur lors de l’aperçu DofusDB.");
            return null;
        }
        return result.body;
    };

    /**
     * @param {string} entityType
     * @param {number|string} entityId
     * @param {{ mode?: 'full'|'images_only', force?: boolean }} options
     */
    const applyRefresh = async (entityType, entityId, options = {}) => {
        const mode = options.mode === "images_only" ? "images_only" : "full";
        const result = await postRefresh(entityType, entityId, {
            mode,
            force: Boolean(options.force),
        });
        if (result.ok && result.body.success !== false) {
            success(result.body.message || "Fiche mise à jour depuis DofusDB.");
            return true;
        }
        showError(result.body.message || "Erreur lors de la mise à jour DofusDB.");
        return false;
    };

    return { previewRefresh, applyRefresh };
}
