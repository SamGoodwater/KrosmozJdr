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

const COUNT_CHIP_KEYS = ["capabilities_count", "spells_count"];

const cellHasContent = (cell) => {
    const v = cell?.value;
    if (v === null || typeof v === "undefined") return false;
    const s = String(v).trim();
    return s !== "" && s !== "-";
};

const getFieldIcon = (fieldKey) => descriptors.value?.[fieldKey]?.icon || "fa-solid fa-info-circle";

const getCell = (fieldKey) =>
    props.specialization.toCell(fieldKey, { size: "sm", context: "minimal" });

const tooltipForField = (fieldKey, cell) => {
    const label = descriptors.value?.[fieldKey]?.general?.label || fieldKey;
    const value = cellHasContent(cell) ? cell.value : "—";
    return `${label} : ${value}`;
};

const chipFields = computed(() =>
    COUNT_CHIP_KEYS.filter((key) => canShowField(key) && cellHasContent(getCell(key))),
);

const shortPitch = computed(() => {
    const raw =
        props.specialization?.shortDescription ??
        props.specialization?._data?.short_description ??
        props.specialization?.short_description;
    const s = String(raw ?? "").trim();
    return s.length ? s : "";
});

const linkedCreatureTraits = computed(() => {
    const raw = props.specialization?._data?.creatureTraits ?? props.specialization?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

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
                        <div v-if="chipFields.length" class="mt-1 flex flex-wrap gap-1.5">
                            <template v-for="field in chipFields" :key="field">
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
                        <div v-if="chipFields.length" class="mt-1 flex flex-wrap gap-1.5">
                            <template v-for="field in chipFields" :key="field">
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
                        <p
                            v-if="shortPitch"
                            class="mt-1 text-[11px] leading-snug italic text-base-content/55"
                            :title="shortPitch"
                        >
                            {{ shortPitch }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="hasLinkedCreatureTraits"
                    class="space-y-1.5 border-t border-base-300/80 pt-1.5 text-xs"
                >
                    <CreatureTraitBadges
                        :traits="linkedCreatureTraits"
                        show-level
                        size="xs"
                    />
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
