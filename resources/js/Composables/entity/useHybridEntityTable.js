/**
 * useHybridEntityTable
 *
 * @description
 * Composable générique pour activer un mode "client" sur un index d'entités :
 * - snapshot de la requête serveur (search/filters/sort/order)
 * - chargement d'un dataset via `/api/entity-table/{entityKey}`
 * - rechargement du dataset en conservant les filtres client
 * - retour au mode serveur
 *
 * Le composable ne gère pas les navigations Inertia (tri/filtre/pagination serveur) : la page conserve ça.
 *
 * @example
 * const { tableMode, allRows, loadingAll, baseServerQuery, loadClientMode, reloadClientDataset, switchToServerMode } =
 *   useHybridEntityTable({ entityKey: 'resources', search, filters, serverSort, serverOrder, notifySuccess, notifyError });
 */

import { ref } from "vue";
import axios from "axios";

/**
 * @param {object} opts
 * @param {string} opts.entityKey - ex: 'resources', 'resource-types' (sert à construire l'URL API)
 * @param {import('vue').Ref<string>} opts.search
 * @param {import('vue').Ref<object>} opts.filters
 * @param {import('vue').Ref<string>} opts.serverSort
 * @param {import('vue').Ref<string>} opts.serverOrder
 * @param {(msg:string)=>void} [opts.notifySuccess]
 * @param {(msg:string)=>void} [opts.notifyError]
 * @param {number} [opts.limit=5000]
 */
export function useHybridEntityTable(opts) {
  const {
    entityKey,
    search,
    filters,
    serverSort,
    serverOrder,
    notifySuccess = null,
    notifyError = null,
    limit = 5000,
  } = opts || {};

  const tableMode = ref("server"); // server | client
  const allRows = ref([]);
  const loadingAll = ref(false);
  const baseServerQuery = ref(null); // snapshot { search, filters, sort, order }

  const apiUrl = `/api/entity-table/${String(entityKey)}`;

  const loadClientMode = async () => {
    if (loadingAll.value) return;
    loadingAll.value = true;
    try {
      baseServerQuery.value = {
        search: search.value,
        filters: { ...(filters.value || {}) },
        sort: serverSort?.value || null,
        order: serverOrder?.value || null,
      };

      const params = {
        limit,
        search: baseServerQuery.value.search || "",
        ...(baseServerQuery.value.filters || {}),
      };
      if (baseServerQuery.value.sort) params.sort = baseServerQuery.value.sort;
      if (baseServerQuery.value.order) params.order = baseServerQuery.value.order;

      const response = await axios.get(apiUrl, { params });
      allRows.value = response.data?.data?.data ?? [];
      tableMode.value = "client";

      // Les filtres UI deviennent une couche additionnelle côté client
      search.value = "";
      filters.value = {};

      if (typeof notifySuccess === "function") {
        notifySuccess(`Mode client activé (${allRows.value.length} lignes chargées).`);
      }
    } catch (e) {
      console.error(e);
      if (typeof notifyError === "function") {
        notifyError("Impossible de charger le dataset pour le mode client (API).");
      } else {
        // fallback sans store (évite de casser)
        alert("Impossible de charger le dataset pour le mode client (API).");
      }
    } finally {
      loadingAll.value = false;
    }
  };

  const reloadClientDataset = async () => {
    if (loadingAll.value) return;
    if (tableMode.value !== "client" || !baseServerQuery.value) return;

    const clientSearch = search.value;
    const clientFilters = { ...(filters.value || {}) };

    loadingAll.value = true;
    try {
      const params = {
        limit,
        search: baseServerQuery.value.search || "",
        ...(baseServerQuery.value.filters || {}),
      };
      if (baseServerQuery.value.sort) params.sort = baseServerQuery.value.sort;
      if (baseServerQuery.value.order) params.order = baseServerQuery.value.order;

      const response = await axios.get(apiUrl, { params });
      allRows.value = response.data?.data?.data ?? [];

      // Conserver les filtres client
      search.value = clientSearch;
      filters.value = clientFilters;

      if (typeof notifySuccess === "function") {
        notifySuccess(`Dataset rechargé (${allRows.value.length} lignes).`);
      }
    } catch (e) {
      console.error(e);
      if (typeof notifyError === "function") {
        notifyError("Impossible de recharger le dataset client (API).");
      } else {
        alert("Impossible de recharger le dataset client (API).");
      }
    } finally {
      loadingAll.value = false;
    }
  };

  const switchToServerMode = () => {
    tableMode.value = "server";
    baseServerQuery.value = null;
  };

  return {
    tableMode,
    allRows,
    loadingAll,
    baseServerQuery,
    loadClientMode,
    reloadClientDataset,
    switchToServerMode,
  };
}


