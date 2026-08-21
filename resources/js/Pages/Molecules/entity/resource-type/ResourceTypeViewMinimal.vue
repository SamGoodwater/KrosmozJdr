<script setup>
/**
 * ResourceTypeViewMinimal — Vue Minimal pour ResourceType
 *
 * @description
 * EntityMinimalCard : identité + méta compacte ; champs étendus en déployé.
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";

const props = defineProps({
    resourceType: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits([
    "edit", "copy-link", "refresh", "view", "quick-view", "quick-edit", "delete", "action",
]);

const entity = computed(() => props.resourceType);
const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("resourceType", "viewAny"),
        createAny: permissions.can("resourceType", "createAny"),
        updateAny: permissions.can("resourceType", "updateAny"),
        deleteAny: permissions.can("resourceType", "deleteAny"),
        manageAny: permissions.can("resourceType", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getResourceTypeFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const metaFields = computed(() =>
    ["decision", "read_level", "resources_count"].filter(canShowField),
);

const technicalFieldsOrder = [
    "id", "slug", "state", "is_public", "read_level", "write_level", "created_at", "updated_at", "deleted_at",
];
const technicalFieldRank = new Map(technicalFieldsOrder.map((key, index) => [key, index]));

const expandedFields = computed(() => {
    const excluded = new Set(["name", "image", "state", "decision", "read_level", "resources_count"]);
    const fields = Object.keys(descriptors.value || {}).filter(
        (key) => canShowField(key) && !excluded.has(key),
    );
    return [...fields].sort((a, b) => {
        const rankA = technicalFieldRank.has(a) ? technicalFieldRank.get(a) : -1;
        const rankB = technicalFieldRank.has(b) ? technicalFieldRank.get(b) : -1;
        if (rankA === -1 && rankB === -1) return 0;
        if (rankA === -1) return -1;
        if (rankB === -1) return 1;
        return rankA - rankB;
    });
});

const getFieldIcon = (fieldKey) =>
    descriptors.value?.[fieldKey]?.general?.icon || "fa-solid fa-info-circle";

const getCell = (fieldKey) =>
    props.resourceType.toCell(fieldKey, { size: "sm", context: "minimal" });

const tooltipForField = (fieldKey, cell) => {
    const label = descriptors.value?.[fieldKey]?.general?.label || fieldKey;
    const value =
        cell?.value === null || typeof cell?.value === "undefined" || String(cell?.value) === ""
            ? "-"
            : cell.value;
    return `${label} : ${value}`;
};

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "resource-types",
    showRoute: "entities.resource-types.show",
    editRoute: "entities.resource-types.edit",
    routeParam: "resourceType",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const displayName = computed(() => props.resourceType?.name || "Type de ressource");
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="resource-types"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="flex flex-col gap-1.5 p-2">
                <div class="flex gap-2">
                    <Icon source="fa-solid fa-tag" :alt="displayName" size="sm" class="mt-0.5 shrink-0 text-primary-400" />
                    <div class="min-w-0 flex-1">
                        <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                        <div class="mt-1 flex flex-wrap gap-1">
                            <template v-for="field in metaFields" :key="field">
                                <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                    <div class="inline-flex items-center gap-1 rounded bg-base-200 px-1.5 py-0.5">
                                        <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
                                        <span class="text-[11px] text-primary-300">
                                            <CellRenderer :cell="getCell(field)" ui-color="primary" />
                                        </span>
                                    </div>
                                </Tooltip>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #expanded>
            <div data-cy="entity-minimal-card-expanded" class="flex flex-col gap-1.5 p-2">
                <div class="flex gap-2">
                    <Icon source="fa-solid fa-tag" :alt="displayName" size="sm" class="mt-0.5 shrink-0 text-primary-400" />
                    <div class="min-w-0 flex-1">
                        <div class="flex w-full min-w-0 items-start gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="resource-types"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="mt-1 flex flex-wrap gap-1">
                            <template v-for="field in metaFields" :key="field">
                                <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                    <div class="inline-flex items-center gap-1 rounded bg-base-200 px-1.5 py-0.5">
                                        <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
                                        <span class="text-[11px] text-primary-300">
                                            <CellRenderer :cell="getCell(field)" ui-color="primary" />
                                        </span>
                                    </div>
                                </Tooltip>
                            </template>
                        </div>
                    </div>
                </div>
                <div v-if="expandedFields.length" class="space-y-1 border-t border-base-300/80 pt-1.5 text-xs">
                    <div v-for="key in expandedFields" :key="key" class="flex items-start gap-2">
                        <Icon :source="getFieldIcon(key)" size="xs" class="mt-0.5 shrink-0 text-primary-400" />
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-primary-400">
                                {{ descriptors?.[key]?.general?.label || key }}
                            </div>
                            <div class="truncate text-primary-200">
                                <CellRenderer :cell="getCell(key)" ui-color="primary" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
