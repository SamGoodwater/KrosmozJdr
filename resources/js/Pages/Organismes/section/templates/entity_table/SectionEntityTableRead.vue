<script setup>
/**
 * SectionEntityTableRead Template
 *
 * Rend un vrai TanStack Table d'entités via EntityTanStackTable.
 * Source: API Table v2 (`api.tables.{entity}`) au format `entities`.
 */
import { computed } from "vue";
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityConfig, getEntityResponseAdapter } from "@/Entities/entity-registry";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const entityType = computed(() => String(props.settings?.entity || props.data?.entity || "spells"));

const filters = computed(() => {
    const raw = props.settings?.filters ?? props.data?.filters;
    if (typeof raw === "string") {
        try {
            return raw.trim() ? JSON.parse(raw) : {};
        } catch {
            return {};
        }
    }
    return typeof raw === "object" && raw !== null ? raw : {};
});

const limit = computed(() => {
    const n = props.settings?.limit ?? props.data?.limit ?? 50;
    const num = Number(n);
    return Number.isFinite(num) ? Math.min(500, Math.max(1, num)) : 50;
});

const entityConfig = computed(() => getEntityConfig(entityType.value));

const tableConfig = computed(() => {
    if (!entityConfig.value) return null;

    const ctx = {
        capabilities: {
            viewAny: true,
            createAny: false,
            updateAny: false,
            deleteAny: false,
            manageAny: false,
        },
    };

    const descriptors = entityConfig.value.getDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

const responseAdapter = computed(() => getEntityResponseAdapter(entityType.value));

const serverUrl = computed(() => {
    if (!entityConfig.value) return "";

    let baseUrl = "";
    try {
        baseUrl = route(`api.tables.${entityType.value}`);
    } catch {
        return "";
    }

    const params = new URLSearchParams();
    params.set("format", "entities");
    params.set("limit", String(limit.value));

    for (const [key, value] of Object.entries(filters.value || {})) {
        if (value === null || typeof value === "undefined" || value === "") continue;
        const normalized = typeof value === "boolean" ? (value ? "1" : "0") : String(value);
        params.append(`filters[${key}]`, normalized);
        params.append(key, normalized);
    }

    return `${baseUrl}?${params.toString()}`;
});
</script>

<template>
    <div class="section-entity-table-content">
        <div v-if="!entityConfig || !tableConfig || !responseAdapter" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Type d'entité non supporté pour ce tableau.</span>
        </div>

        <EntityTanStackTable
            v-else
            :entity-type="entityType"
            :config="tableConfig"
            :server-url="serverUrl"
            :response-adapter="responseAdapter"
        />
    </div>
</template>
