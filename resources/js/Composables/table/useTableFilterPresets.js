import axios from "axios";

/**
 * API presets table filters (phase 2 persisted in DB).
 */
export function useTableFilterPresets() {
    const normalizePreset = (preset) => ({
        id: String(preset?.id || ""),
        entityType: String(preset?.entity_type || ""),
        tableId: preset?.table_id ?? null,
        name: String(preset?.name || ""),
        searchText: String(preset?.search_text || ""),
        filters: typeof preset?.filters === "object" && preset?.filters !== null ? { ...preset.filters } : {},
        limit: Number.isFinite(Number(preset?.limit)) ? Number(preset.limit) : null,
        isDefault: Boolean(preset?.is_default),
        createdAt: preset?.created_at || null,
        updatedAt: preset?.updated_at || null,
    });

    const listPresets = async ({ entityType, tableId = null, includeGlobal = true }) => {
        if (!entityType) return [];

        const response = await axios.get(route("api.table-presets.index"), {
            params: {
                entity_type: entityType,
                table_id: tableId,
                include_global: includeGlobal ? 1 : 0,
            },
        });

        const presets = Array.isArray(response?.data?.presets) ? response.data.presets : [];
        return presets.map(normalizePreset);
    };

    const createPreset = async (payload) => {
        const response = await axios.post(route("api.table-presets.store"), {
            entity_type: payload.entityType,
            table_id: payload.tableId ?? null,
            name: payload.name,
            search_text: payload.searchText ?? "",
            filters: payload.filters ?? {},
            limit: payload.limit ?? null,
            is_default: Boolean(payload.isDefault),
        });

        return normalizePreset(response?.data?.preset || {});
    };

    const updatePreset = async (presetId, payload) => {
        const response = await axios.patch(route("api.table-presets.update", { tablePreset: presetId }), {
            entity_type: payload.entityType,
            table_id: payload.tableId,
            name: payload.name,
            search_text: payload.searchText,
            filters: payload.filters,
            limit: payload.limit,
            is_default: payload.isDefault,
        });

        return normalizePreset(response?.data?.preset || {});
    };

    const deletePreset = async (presetId) => {
        await axios.delete(route("api.table-presets.destroy", { tablePreset: presetId }));
        return true;
    };

    return {
        listPresets,
        createPreset,
        updatePreset,
        deletePreset,
    };
}

