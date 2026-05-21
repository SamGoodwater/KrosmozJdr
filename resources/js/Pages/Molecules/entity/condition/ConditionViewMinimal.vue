<script setup>
/**
 * ConditionViewMinimal — Vue minimal (Condition)
 *
 * @description
 * Carte compacte : image si présente, nom, description.
 * Pas de métadonnées (niveaux, dates, auteur).
 *
 * @props {Object} condition - Instance Condition (facade toCell)
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import ConditionDissipableHighlight from "@/Pages/Molecules/entity/condition/ConditionDissipableHighlight.vue";

const props = defineProps({
    condition: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["edit", "copy-link", "download-pdf", "refresh", "view", "quick-view", "quick-edit", "delete", "action"]);

const isHovered = ref(props.displayMode === "extended");
const canHoverExpand = computed(() => props.displayMode === "hover");

const dissipableValue = computed(
    () => props.condition?.dissipable ?? props.condition?._data?.dissipable
);

const imageUrl = computed(() => {
    const u = props.condition?.image ?? props.condition?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const getCell = (fieldKey) =>
    props.condition.toCell(fieldKey, {
        size: "sm",
        context: "minimal",
    });

const descriptionCell = computed(() => getCell("description"));
const hasDescription = computed(() => {
    const c = descriptionCell.value;
    const v = c?.value;
    if (v == null) return false;
    const s = String(v).replace(/<[^>]*>/g, "").trim();
    return s.length > 0;
});

const handleAction = async (actionKey) => {
    const conditionId = props.condition.id;
    if (!conditionId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.conditions.show", { condition: conditionId }));
            emit("view", props.condition);
            break;
        case "edit":
            router.visit(route("entities.conditions.edit", { condition: conditionId }));
            emit("edit", props.condition);
            break;
        case "delete":
            emit("delete", props.condition);
            break;
    }
};
</script>

<template>
    <div
        class="relative flex h-full min-h-18 w-full min-w-0 flex-col rounded-box border border-base-300 transition-all duration-300 overflow-hidden"
        :class="{
            'bg-base-200 shadow-lg': isHovered,
            'bg-base-100': !isHovered,
        }"
        @mouseenter="canHoverExpand && (isHovered = true)"
        @mouseleave="canHoverExpand && (isHovered = false)"
    >
        <div class="flex flex-1 flex-col gap-2 p-3">
            <div class="flex items-start justify-between gap-2">
                <div class="flex min-w-0 flex-1 items-start gap-2">
                    <EntityThumb
                        size="xs"
                        :src="imageUrl || ''"
                        :label="condition.name || 'État'"
                    />
                    <Tooltip :content="condition.name || 'État'" placement="top">
                        <span class="block min-w-0 wrap-break-word text-sm font-semibold text-primary-100">
                            <CellRenderer :cell="getCell('name')" ui-color="primary" />
                        </span>
                    </Tooltip>
                </div>

                <div v-if="showActions && isHovered" class="shrink-0">
                    <EntityActions
                        entity-type="conditions"
                        :entity="condition"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false, inMinimal: true }"
                        @action="handleAction"
                    />
                </div>
            </div>

            <div class="flex items-center px-0.5 pt-0.5">
                <ConditionDissipableHighlight :dissipable="dissipableValue" variant="block" />
            </div>

            <div
                v-if="hasDescription"
                class="prose prose-sm prose-invert max-w-none min-w-0 wrap-break-word text-xs leading-snug text-primary-300/90"
            >
                <CellRenderer :cell="descriptionCell" ui-color="primary" />
            </div>
        </div>
    </div>
</template>
