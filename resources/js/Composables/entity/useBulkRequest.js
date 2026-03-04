/**
 * useBulkRequest
 *
 * @description
 * Helper unique pour les appels bulk (PATCH JSON) :
 * - récupère le token CSRF
 * - exécute un fetch PATCH JSON
 * - gère les notifications de succès/erreur de façon cohérente
 *
 * @example
 * const { bulkPatchJson } = useBulkRequest()
 * const ok = await bulkPatchJson({
 *   url: "/api/entities/resources/bulk",
 *   payload,
 * })
 */

import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

/**
 * @typedef {Object} BulkPatchJsonOptions
 * @property {string} url
 * @property {any} payload
 * @property {string} [successMessage]
 * @property {string} [errorMessage]
 */

export function useBulkRequest() {
  const { notifySuccess, notifyError } = useUxFeedback();

  /**
   * @returns {string|null}
   */
  const getCsrfToken = () => {
    return document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || null;
  };

  /**
   * @param {BulkPatchJsonOptions} opts
   * @returns {Promise<boolean>}
   */
  const bulkPatchJson = async (opts) => {
    const url = String(opts?.url || "").trim();
    if (!url) return false;

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
      notifyError("Token CSRF introuvable. Recharge la page.");
      return false;
    }

    try {
      const response = await fetch(url, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
          Accept: "application/json",
        },
        body: JSON.stringify(opts?.payload ?? {}),
      });

      let data = null;
      try {
        data = await response.json();
      } catch {
        data = null;
      }

      if (!response.ok || !data?.success) {
        notifyError(data?.message || opts?.errorMessage || "Erreur lors de la mise à jour en masse.");
        return false;
      }

      const updated = data?.summary?.updated;
      const requested = data?.summary?.requested;
      const defaultSuccess =
        typeof updated !== "undefined" && typeof requested !== "undefined"
          ? `Mis à jour: ${updated}/${requested}`
          : "Mis à jour.";

      notifySuccess(opts?.successMessage || defaultSuccess);

      return true;
    } catch (e) {
      notifyError("Erreur lors de la mise à jour en masse: " + (e?.message || "unknown"));
      return false;
    }
  };

  return { getCsrfToken, bulkPatchJson };
}

export default useBulkRequest;


