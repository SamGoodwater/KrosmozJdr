<script setup>
/**
 * AptitudeViewFull — Vue Full pour Aptitude
 *
 * @props {Aptitude} aptitude
 * @props {Boolean} showActions
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
import { getAptitudeFieldDescriptors } from "@/Entities/aptitude/aptitude-descriptors";

const props = defineProps({
    aptitude: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    inModal: { type: Boolean, default: false },
    titleTag: {
        type: String,
        default: "h2",
        validator: (v) => ["h1", "h2", "h3"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
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
const { downloadPdf } = useDownloadPdf("aptitudes");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("aptitudes", "viewAny"),
        createAny: permissions.can("aptitudes", "createAny"),
        updateAny: permissions.can("aptitudes", "updateAny"),
        deleteAny: permissions.can("aptitudes", "deleteAny"),
        manageAny: permissions.can("aptitudes", "manageAny"),
    };
    return { capabilities, meta: { capabilities }, entity: props.aptitude };
});

const descriptors = computed(() => getAptitudeFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.visibleIf || desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[AptitudeViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const mediaSrc = computed(() => {
    const u = props.aptitude?.image ?? props.aptitude?._data?.image;
    return u && String(u).trim() ? String(u) : "";
});

const usageFields = computed(() =>
    ["reuse_type", "duration_type", "range_type"].filter(canShowField),
);

const metaFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["created_by", "created_at", "updated_at"].filter(canShowField),
);

const getCell = (fieldKey) =>
    props.aptitude.toCell(fieldKey, { size: "lg", context: "extended" });

const descriptionCell = computed(() => getCell("description"));
const hasDescription = computed(() => {
    const v = descriptionCell.value?.value;
    if (v == null) return false;
    return String(v).replace(/<[^>]*>/g, "").trim().length > 0;
});

const effectCell = computed(() => getCell("effect"));
const hasEffect = computed(() => {
    const v = effectCell.value?.value;
    if (v == null || v === "-") return false;
    return String(v).replace(/<[^>]*>/g, "").trim().length > 0;
});

const handleAction = async (actionKey) => {
    const aptitudeId = props.aptitude.id;
    if (!aptitudeId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.aptitudes.show", { aptitude: aptitudeId }));
            emit("view", props.aptitude);
            break;
        case "edit":
            router.visit(route("entities.aptitudes.edit", { aptitude: aptitudeId }));
            emit("edit", props.aptitude);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("aptitudes");
            const url = resolveEntityRouteUrl("aptitudes", "show", aptitudeId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'aptitude copié !");
            }
            emit("copy-link", props.aptitude);
            break;
        }
        case "download-pdf":
            await downloadPdf(aptitudeId);
            emit("download-pdf", props.aptitude);
            break;
        case "refresh":
            router.reload({ only: ["aptitude"] });
            emit("refresh", props.aptitude);
            break;
        case "delete":
            emit("delete", props.aptitude);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative size-24 shrink-0 sm:size-28 md:size-32">
                    <ImageViewer
                        v-if="mediaSrc"
                        :source="mediaSrc"
                        :alt="aptitude.name || 'Aptitude'"
                        :caption="aptitude.name || ''"
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
                        <Icon source="fa-solid fa-sparkles" :alt="aptitude.name" size="lg" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-xl font-bold break-words text-primary-100 md:text-2xl">
                    <CellRenderer :cell="getCell('name')" ui-color="primary" />
                </component>
            </template>

            <template v-if="hasDescription" #subtitle>
                <div class="prose prose-sm prose-invert max-w-none text-sm leading-relaxed break-words text-primary-200 md:text-base">
                    <CellRenderer :cell="descriptionCell" ui-color="primary" />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="aptitudes"
                        :entity="aptitude"
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
            v-if="usageFields.length > 0"
            class="grid grid-cols-1 gap-3 md:grid-cols-3"
        >
            <div
                v-for="fieldKey in usageFields"
                :key="fieldKey"
                class="entity-radius-box bg-base-200 p-3"
            >
                <EntityPropertyDisplay
                    :field-key="fieldKey"
                    :entity="aptitude"
                    entity-type="aptitudes"
                    display-mode="extended"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    size="sm"
                />
            </div>
        </div>

        <div
            v-if="hasEffect"
            class="entity-radius-box bg-base-200 p-3"
        >
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-primary-300/80">Effet</p>
            <div class="prose prose-sm prose-invert max-w-none text-sm text-primary-100">
                <CellRenderer :cell="effectCell" ui-color="primary" />
            </div>
        </div>

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
                    :entity="aptitude"
                    entity-type="aptitudes"
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
                :entity="aptitude"
                entity-type="aptitudes"
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
