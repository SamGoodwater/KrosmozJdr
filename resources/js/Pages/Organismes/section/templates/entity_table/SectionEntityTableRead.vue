<script setup>
/**
 * SectionEntityTableRead Template
 *
 * Rend un vrai TanStack Table d'entités via EntityTanStackTable.
 * Source: API Table v2 (`api.tables.{entity}`) au format `entities`,
 * en pagination serveur (évite le plafond historique `limit: 50` → 2 pages).
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityConfig, getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getEntitySingularRouteKey } from "@/Composables/entity/entityRouteRegistry";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";

/**
 * Virtualisation client (TanStackTable) — activée uniquement dans les sections CMS.
 * Les pages Index entité gardent le seuil par défaut (500+) sauf config explicite.
 */
const CMS_ENTITY_TABLE_VIRTUALIZATION = {
    enabled: true,
    minRows: 40,
    rowHeight: 64,
};

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

const initialFilterValues = computed(() => normalizeIndexTableFilters(filters.value));

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
    const built = config.build(ctx);

    return {
        ...built,
        features: {
            ...(built.features || {}),
            virtualization: CMS_ENTITY_TABLE_VIRTUALIZATION,
        },
    };
});

const responseAdapter = computed(() => getEntityResponseAdapter(entityType.value));

const serverBaseUrl = computed(() => {
    if (!entityConfig.value) return "";

    try {
        return route(`api.tables.${entityType.value}`);
    } catch {
        return "";
    }
});

/**
 * Résout l’id d’une ligne (instance BaseModel ou objet API).
 *
 * @param {unknown} raw
 * @returns {number|string|null}
 */
const resolveEntityId = (raw) => {
    if (raw == null) return null;
    if (typeof raw.id !== "undefined" && raw.id !== null) return raw.id;
    if (raw._data && typeof raw._data.id !== "undefined") return raw._data.id;
    return null;
};

/**
 * Navigation fiche entité (pages Show / Edit) — requis car ce wrapper n’avait pas d’écouteurs `@action`.
 *
 * @param {string} actionKey
 * @param {unknown} entity
 * @param {unknown} row
 */
const handleTableAction = (actionKey, entity, row) => {
    const raw = entity || row?.rowParams?.entity;
    const entityId = resolveEntityId(raw);
    if (entityId === null || entityId === "") return;

    const plural = entityType.value;
    const paramKey = getEntitySingularRouteKey(plural);

    switch (actionKey) {
        case "view":
        case "quick-view":
        case "expand":
            router.visit(route(`entities.${plural}.show`, { [paramKey]: entityId }));
            break;
        case "edit":
        case "edit-page":
            router.visit(route(`entities.${plural}.edit`, { [paramKey]: entityId }));
            break;
        default:
            break;
    }
};

/**
 * Double-clic : ouvrir la fiche (même comportement que sur les pages Index entité).
 *
 * @param {unknown} row
 */
const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    const entityId = resolveEntityId(raw);
    if (entityId === null || entityId === "") return;

    const plural = entityType.value;
    const paramKey = getEntitySingularRouteKey(plural);
    router.visit(route(`entities.${plural}.show`, { [paramKey]: entityId }));
};
</script>

<template>
    <div class="section-entity-table-content">
        <div v-if="!entityConfig || !tableConfig || !responseAdapter || !serverBaseUrl" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Type d'entité non supporté pour ce tableau.</span>
        </div>

        <EntityTanStackTable
            v-else
            :entity-type="entityType"
            :config="tableConfig"
            server-side
            :server-base-url="serverBaseUrl"
            :initial-filter-values="initialFilterValues"
            :response-adapter="responseAdapter"
            @action="handleTableAction"
            @row-dblclick="handleRowDoubleClick"
        />
    </div>
</template>
