<script setup>
/**
 * ConditionViewFull — Vue Full pour Condition
 *
 * @description
 * Header partagé (EntityViewHeader), description en subtitle, grille méta sans
 * doublon name/description, flags mécaniques sans placeholder vide.
 *
 * @props {Condition} condition - Instance du modèle Condition
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getConditionFieldDescriptors } from "@/Entities/condition/condition-descriptors";
import ConditionMechanicalFlags from "@/Pages/Molecules/entity/condition/ConditionMechanicalFlags.vue";
import ConditionDissipableHighlight from "@/Pages/Molecules/entity/condition/ConditionDissipableHighlight.vue";

const props = defineProps({
    condition: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    titleTag: {
        type: String,
        default: "h2",
        validator: (v) => ["h1", "h2", "h3"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});

const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: "modal", viewMode: "full", modalMode: "view" }
        : { inPanel: false, inPage: true, surface: "page", viewMode: "full" },
);

const headerMode = computed(() => (props.inModal ? "compact" : "full"));

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

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("condition");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("condition", "viewAny"),
        createAny: permissions.can("condition", "createAny"),
        updateAny: permissions.can("condition", "updateAny"),
        deleteAny: permissions.can("condition", "deleteAny"),
        manageAny: permissions.can("condition", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getConditionFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[ConditionViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const mediaSrc = computed(() => {
    const u = props.condition?.image ?? props.condition?._data?.image;
    return u && String(u).trim() ? String(u) : "";
});

/** Hors name / description / dissipable (dissipable sous l’image). */
const metaFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["created_by", "created_at", "updated_at"].filter(canShowField),
);

const dissipableValue = computed(
    () => props.condition?.dissipable ?? props.condition?._data?.dissipable,
);

const getCell = (fieldKey) =>
    props.condition.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });

const descriptionCell = computed(() => getCell("description"));
const hasDescription = computed(() => {
    const v = descriptionCell.value?.value;
    if (v == null) return false;
    return String(v).replace(/<[^>]*>/g, "").trim().length > 0;
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
        case "copy-link": {
            const cfg = getEntityRouteConfig("condition");
            const url = resolveEntityRouteUrl("condition", "show", conditionId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'état copié !");
            }
            emit("copy-link", props.condition);
            break;
        }
        case "download-pdf":
            await downloadPdf(conditionId);
            emit("download-pdf", props.condition);
            break;
        case "refresh":
            router.reload({ only: ["conditions"] });
            emit("refresh", props.condition);
            break;
        case "delete":
            emit("delete", props.condition);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="flex flex-col items-center gap-1.5">
                    <div class="relative size-24 shrink-0 sm:size-28 md:size-32">
                        <ImageViewer
                            v-if="mediaSrc"
                            :source="mediaSrc"
                            :alt="condition.name || 'État'"
                            :caption="condition.name || ''"
                            preload="hover"
                            :image-props="{
                                size: 'lg',
                                rounded: 'lg',
                                fit: 'cover',
                                class: 'w-full h-full',
                            }"
                        />
                        <div
                            v-else
                            class="flex size-full items-center justify-center bg-base-200 entity-radius-box"
                        >
                            <Icon source="fa-solid fa-flask" :alt="condition.name" size="lg" />
                        </div>
                    </div>
                    <ConditionDissipableHighlight
                        :dissipable="dissipableValue"
                        variant="caption-hover"
                    />
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-xl font-bold break-words text-primary-100 md:text-2xl">
                    <CellRenderer :cell="getCell('name')" ui-color="primary" />
                </component>
            </template>

            <template #mainInfos>
                <ConditionMechanicalFlags :condition="condition" />
            </template>

            <template v-if="hasDescription" #subtitle>
                <div class="prose prose-sm prose-invert max-w-none text-sm leading-relaxed break-words text-primary-200 md:text-base">
                    <CellRenderer :cell="descriptionCell" ui-color="primary" />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="conditions"
                        :entity="condition"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="actionsContext"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <div
            v-if="metaFields.length > 0"
            class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="fieldKey in metaFields"
                :key="fieldKey"
                class="entity-radius-box bg-base-200 p-3"
            >
                <EntityPropertyDisplay
                    :field-key="fieldKey"
                    :entity="condition"
                    entity-type="conditions"
                    display-mode="extended"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    size="sm"
                />
            </div>
        </div>

        <div
            v-if="technicalFields.length > 0"
            class="flex flex-wrap gap-x-6 gap-y-2 border-t border-base-300 pt-3 text-xs text-primary-200/80"
        >
            <EntityPropertyDisplay
                v-for="fieldKey in technicalFields"
                :key="fieldKey"
                :field-key="fieldKey"
                :entity="condition"
                entity-type="conditions"
                display-mode="extended"
                :descriptors="descriptors"
                :table-meta="tableMeta"
                size="xs"
            />
        </div>
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
