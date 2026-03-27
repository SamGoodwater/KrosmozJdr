<script setup>
/**
 * CapabilityViewLarge — Vue Large pour Capability
 *
 * @description
 * Alignée sur SpellViewLarge / PanoplyViewLarge : EntityViewHeader, métas, grille détail, paramètres niveaux.
 *
 * @props {Capability} capability - Instance du modèle Capability
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
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";

const props = defineProps({
    capability: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
});

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
const { downloadPdf } = useDownloadPdf("capability");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("capabilities", "viewAny"),
        createAny: permissions.can("capabilities", "createAny"),
        updateAny: permissions.can("capabilities", "updateAny"),
        deleteAny: permissions.can("capabilities", "deleteAny"),
        manageAny: permissions.can("capabilities", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCapabilityFieldDescriptors(ctx.value));

const stateValue = computed(() => props.capability?.state ?? props.capability?._data?.state ?? null);

const mediaSrc = computed(() => {
    const c = props.capability;
    const u = c?.image ?? c?._data?.image;
    return u && String(u).trim() ? String(u) : "";
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[CapabilityViewLarge] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ["level"].filter(canShowField));

const metaFields = computed(() =>
    [
        "pa",
        "po",
        "element",
        "capability_summary_cast",
        "capability_summary_metier",
        "capability_summary_relations",
        "state",
    ]
        .filter(canShowField)
        .filter((k) => !headlineFields.value.includes(k))
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() => ["created_by", "created_at", "updated_at"].filter(canShowField));

const bodyFields = computed(() =>
    [
        "effect",
        "time_before_use_again",
        "casting_time",
        "duration",
        "is_magic",
        "ritual_available",
        "powerful",
    ].filter(canShowField)
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "capability",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.capability.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        level: "warning",
        read_level: "primary",
        write_level: "secondary",
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
    const capabilityId = props.capability.id;
    if (!capabilityId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.capabilities.show", { capability: capabilityId }));
            emit("view", props.capability);
            break;
        case "edit":
            router.visit(route("entities.capabilities.edit", { capability: capabilityId }));
            emit("edit", props.capability);
            break;
        case "quick-edit":
            emit("quick-edit", props.capability);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("capability");
            const url = resolveEntityRouteUrl("capability", "show", capabilityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la capacité copié !");
            }
            emit("copy-link", props.capability);
            break;
        }
        case "download-pdf":
            await downloadPdf(capabilityId);
            emit("download-pdf", props.capability);
            break;
        case "refresh":
            router.reload({ only: ["capabilities"] });
            emit("refresh", props.capability);
            break;
        case "delete":
            emit("delete", props.capability);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <EntityViewHeader mode="large">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div class="absolute top-2 left-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <EntityUsableDot :state="stateValue" />
                    </div>

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

                    <ImageViewer
                        v-if="mediaSrc"
                        :source="mediaSrc"
                        :alt="capability.name || 'Capacité'"
                        :caption="capability.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-bolt" :alt="capability.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 wrap-break-word">{{ capability.name }}</h2>
            </template>

            <template #subtitle>
                <p v-if="capability.description" class="text-primary-300 mt-2 wrap-break-word">
                    {{ capability.description }}
                </p>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="capability"
                        entity-type="capability"
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
                        entity-type="capabilities"
                        :entity="capability"
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

        <div v-if="bodyFields.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="fieldKey in bodyFields" :key="fieldKey" class="p-3 bg-base-200 entity-radius-box">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    :alt="getFieldLabel(fieldKey)"
                                    size="xs"
                                    class="text-primary-400"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="text-xs text-primary-400 uppercase font-semibold">{{ getFieldLabel(fieldKey) }}</span>
                            </div>
                        </Tooltip>
                    </div>
                    <div class="text-primary-100 wrap-break-word">
                        <CellRenderer :cell="getCell(fieldKey)" ui-color="primary" />
                    </div>
                </div>
            </div>
        </div>

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
