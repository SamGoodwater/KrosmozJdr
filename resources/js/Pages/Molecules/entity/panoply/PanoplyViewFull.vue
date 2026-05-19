<script setup>
/**
 * PanoplyViewFull — Vue Full pour Panoply
 *
 * @description
 * Alignée sur BreedViewFull : EntityViewHeader, métas via EntityPropertyDisplay, bandeaux techniques / niveaux.
 *
 * @props {Panoply} panoply - Instance du modèle Panoply
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getPanoplyFieldDescriptors } from "@/Entities/panoply/panoply-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";

const props = defineProps({
    panoply: {
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
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "quick-edit",
    "delete",
    "action",
]);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("panoply");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("panoplies", "viewAny"),
        createAny: permissions.can("panoplies", "createAny"),
        updateAny: permissions.can("panoplies", "updateAny"),
        deleteAny: permissions.can("panoplies", "deleteAny"),
        manageAny: permissions.can("panoplies", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getPanoplyFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[PanoplyViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ["items_count"].filter(canShowField));

const metaFields = computed(() =>
    ["bonus", "panoply_summary_relations", "state"].filter(canShowField).filter((k) => !headlineFields.value.includes(k))
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["dofusdb_id", "created_by", "created_at", "updated_at"].filter(canShowField)
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "panoply",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.panoply.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        items_count: "warning",
        read_level: "primary",
        write_level: "secondary",
        dofusdb_id: "neutral",
        created_by: "neutral",
        created_at: "neutral",
        updated_at: "neutral",
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
    });
    return { autoLabel, autoScheme, autoTone };
};

const asTextCell = (cell) => {
    if (!cell) return { type: "text", value: "-", params: {} };
    const v = cell?.value;
    return {
        type: "text",
        value: v === null || typeof v === "undefined" || String(v) === "" ? "-" : String(v),
        params: cell?.params || {},
    };
};

const handleAction = async (actionKey) => {
    const panoplyId = props.panoply.id;
    if (!panoplyId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.panoplies.show", { panoply: panoplyId }));
            emit("view", props.panoply);
            break;
        case "edit":
            router.visit(route("entities.panoplies.edit", { panoply: panoplyId }));
            emit("edit", props.panoply);
            break;
        case "quick-edit":
            emit("quick-edit", props.panoply);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("panoply");
            const url = resolveEntityRouteUrl("panoply", "show", panoplyId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la panoplie copié !");
            }
            emit("copy-link", props.panoply);
            break;
        }
        case "download-pdf":
            await downloadPdf(panoplyId);
            emit("download-pdf", props.panoply);
            break;
        case "refresh":
            router.reload({ only: ["panoplies"] });
            emit("refresh", props.panoply);
            break;
        case "delete":
            emit("delete", props.panoply);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div
                        v-if="headlineFields.length > 0"
                        class="absolute top-2 right-2 z-20 flex flex-wrap gap-1 justify-end max-w-[75%] transition-opacity duration-150 group-hover:opacity-0"
                    >
                        <template v-for="fieldKey in headlineFields" :key="fieldKey">
                            <Badge
                                :color="getBadgeColor(fieldKey)"
                                :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                size="sm"
                            >
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </Badge>
                        </template>
                    </div>

                    <div
                        class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box border border-base-300/60"
                    >
                        <Icon source="fa-solid fa-layer-group" :alt="panoply.name || 'Panoplie'" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 wrap-break-word">{{ panoply.name }}</h2>
            </template>

            <template #subtitle>
                <p v-if="panoply.description" class="text-primary-300 mt-2 wrap-break-word">{{ panoply.description }}</p>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="panoply"
                        entity-type="panoply"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal wrap-break-word"
                    />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="panoplies"
                        :entity="panoply"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false, inPage: true }"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                size="xs"
                                class="text-primary-300 shrink-0"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 wrap-break-word">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    size="xs"
                                    class="text-primary-300 shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 wrap-break-word">
                                    <Badge
                                        :color="getBadgeColor(fieldKey)"
                                        :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                        :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                        :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                        size="sm"
                                    >
                                        <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                    </Badge>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
