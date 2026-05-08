<script setup>
/**
 * CreatureTraitViewMinimal — Vue minimal (CreatureTrait)
 *
 * @description
 * Carte compacte : pastille d’état, image si présente, nom, description.
 * Pas de métadonnées (niveaux, dates, auteur).
 *
 * @props {Object} creatureTrait - Instance CreatureTrait (facade toCell)
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";

const props = defineProps({
    creatureTrait: { type: Object, required: true },
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

const stateValue = computed(
    () => props.creatureTrait?.state ?? props.creatureTrait?._data?.state ?? null
);

const imageUrl = computed(() => {
    const u = props.creatureTrait?.image ?? props.creatureTrait?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const getCell = (fieldKey) =>
    props.creatureTrait.toCell(fieldKey, {
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
    const creatureTraitId = props.creatureTrait.id;
    if (!creatureTraitId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.creature-traits.show", { creatureTrait: creatureTraitId }));
            emit("view", props.creatureTrait);
            break;
        case "edit":
            router.visit(route("entities.creature-traits.edit", { creatureTrait: creatureTraitId }));
            emit("edit", props.creatureTrait);
            break;
        case "delete":
            emit("delete", props.creatureTrait);
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
        <div class="absolute top-1 left-1 z-20">
            <EntityUsableDot :state="stateValue" />
        </div>

        <div class="flex flex-1 flex-col gap-2 p-3 pt-6">
            <div class="flex items-start justify-between gap-2">
                <div class="flex min-w-0 flex-1 items-start gap-2">
                    <div
                        v-if="imageUrl"
                        class="w-10 h-10 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            :src="imageUrl"
                            :alt="creatureTrait.name || 'Trait'"
                            fit="contain"
                            class="h-full w-full object-contain"
                        />
                    </div>
                    <div
                        v-else
                        class="w-10 h-10 shrink-0 rounded bg-base-200 flex items-center justify-center"
                    >
                        <Icon source="fa-solid fa-star" alt="" size="sm" class="text-base-content/40" />
                    </div>
                    <Tooltip :content="creatureTrait.name || 'Trait'" placement="top">
                        <span class="block min-w-0 wrap-break-word text-sm font-semibold text-primary-100">
                            <CellRenderer :cell="getCell('name')" ui-color="primary" />
                        </span>
                    </Tooltip>
                </div>

                <div v-if="showActions && isHovered" class="shrink-0">
                    <EntityActions
                        entity-type="creature-traits"
                        :entity="creatureTrait"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false, inMinimal: true }"
                        @action="handleAction"
                    />
                </div>
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
