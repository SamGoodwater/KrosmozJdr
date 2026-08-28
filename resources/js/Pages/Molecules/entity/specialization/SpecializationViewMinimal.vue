<script setup>
/**
 * SpecializationViewMinimal — Vue Minimal pour Specialization
 *
 * @description
 * EntityMinimalCard : identité + compteurs ; traits et champs étendus en déployé.
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";

const props = defineProps({
    specialization: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "delete",
    "action",
]);

const entity = computed(() => props.specialization);
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("specializations", "viewAny") || permissions.can("specialization", "viewAny"),
        createAny: permissions.can("specializations", "createAny") || permissions.can("specialization", "createAny"),
        updateAny: permissions.can("specializations", "updateAny") || permissions.can("specialization", "updateAny"),
        deleteAny: permissions.can("specializations", "deleteAny") || permissions.can("specialization", "deleteAny"),
        manageAny: permissions.can("specializations", "manageAny") || permissions.can("specialization", "manageAny"),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getSpecializationFieldDescriptors(ctx.value));

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

const importantFields = computed(() => ["capabilities_count"].filter(canShowField));

const technicalFieldsOrder = [
    "id",
    "slug",
    "state",
    "is_public",
    "read_level",
    "write_level",
    "created_at",
    "updated_at",
    "deleted_at",
];
const technicalFieldRank = new Map(technicalFieldsOrder.map((key, index) => [key, index]));

const expandedFields = computed(() => {
    const excluded = new Set(["name", "image", "state", "capabilities_count"]);
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

const linkedCreatureTraits = computed(() => {
    const raw = props.specialization?._data?.creatureTraits ?? props.specialization?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const getFieldIcon = (fieldKey) => descriptors.value?.[fieldKey]?.icon || "fa-solid fa-info-circle";

const getCell = (fieldKey) =>
    props.specialization.toCell(fieldKey, { size: "sm", context: "minimal" });

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
    entityTypePlural: "specializations",
    showRoute: "entities.specializations.show",
    editRoute: "entities.specializations.edit",
    routeParam: "specialization",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const displayName = computed(() => props.specialization?.name || "Spécialisation");
const imageUrl = computed(() => {
    const u = props.specialization?.image ?? props.specialization?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="specializations"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="flex flex-col gap-1.5 p-2">
                <div class="flex gap-2">
                    <EntityThumb size="compact" :src="imageUrl || ''" :label="displayName" />
                    <div class="min-w-0 flex-1">
                        <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            <template v-for="field in importantFields" :key="field">
                                <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                    <div class="inline-flex items-center gap-1 rounded bg-base-200 px-1.5 py-0.5">
                                        <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
                                        <span class="text-[11px] font-medium text-primary-300">
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
                    <EntityThumb size="compact" :src="imageUrl || ''" :label="displayName" />
                    <div class="min-w-0 flex-1">
                        <div class="flex w-full min-w-0 items-start gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="specializations"
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
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            <template v-for="field in importantFields" :key="field">
                                <Tooltip :content="tooltipForField(field, getCell(field))" placement="top">
                                    <div class="inline-flex items-center gap-1 rounded bg-base-200 px-1.5 py-0.5">
                                        <Icon :source="getFieldIcon(field)" size="xs" class="text-primary-400" />
                                        <span class="text-[11px] font-medium text-primary-300">
                                            <CellRenderer :cell="getCell(field)" ui-color="primary" />
                                        </span>
                                    </div>
                                </Tooltip>
                            </template>
                        </div>
                    </div>
                </div>

                <div
                    v-if="hasLinkedCreatureTraits || expandedFields.length"
                    class="space-y-1.5 border-t border-base-300/80 pt-1.5 text-xs"
                >
                    <CreatureTraitBadges
                        v-if="hasLinkedCreatureTraits"
                        :traits="linkedCreatureTraits"
                        show-level
                        size="xs"
                    />
                    <div
                        v-for="key in expandedFields"
                        :key="key"
                        class="flex items-start gap-2"
                    >
                        <Icon
                            :source="getFieldIcon(key)"
                            size="xs"
                            class="mt-0.5 shrink-0 text-primary-400"
                        />
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-primary-400">
                                {{ descriptors?.[key]?.general?.label || descriptors?.[key]?.label || key }}
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
