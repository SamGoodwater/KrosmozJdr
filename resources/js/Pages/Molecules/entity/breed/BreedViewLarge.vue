<script setup>
/**
 * BreedViewLarge — Vue Large pour Breed
 *
 * @description
 * Vue complète d'une classe, alignée sur SpellViewLarge (EntityViewHeader, métas, paramètres).
 *
 * @props {Breed} breed - Instance du modèle Breed
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
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";

const props = defineProps({
    breed: {
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
    characteristicRuntime: { type: Object, default: null },
});

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
const { downloadPdf } = useDownloadPdf("breed");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("breeds", "viewAny"),
        createAny: permissions.can("breeds", "createAny"),
        updateAny: permissions.can("breeds", "updateAny"),
        deleteAny: permissions.can("breeds", "deleteAny"),
        manageAny: permissions.can("breeds", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getBreedFieldDescriptors(ctx.value));

const stateValue = computed(() => props.breed?.state ?? props.breed?._data?.state ?? null);

const autoUpdateValue = computed(() => {
    const v = props.breed?.auto_update ?? props.breed?._data?.auto_update;
    return typeof v === "boolean" ? v : null;
});

const mediaSrc = computed(() => {
    const b = props.breed;
    const u = b?.image ?? b?.icon ?? b?._data?.image ?? b?._data?.icon;
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
            console.warn("[BreedViewLarge] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ["life", "life_dice"].filter(canShowField));

const metaFields = computed(() =>
    ["specificity", "state", "breed_summary_relations"].filter(canShowField).filter((k) => !headlineFields.value.includes(k))
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ["auto_update", "read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["dofus_version", "dofusdb_id", "official_id", "created_by", "created_at", "updated_at"].filter(canShowField)
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "breed",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.breed.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        life: "warning",
        life_dice: "warning",
        auto_update: "warning",
        read_level: "primary",
        write_level: "secondary",
        dofusdb_id: "neutral",
        official_id: "neutral",
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
    const breedId = props.breed.id;
    if (!breedId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.breeds.show", { breed: breedId }));
            emit("view", props.breed);
            break;
        case "edit":
            router.visit(route("entities.breeds.edit", { breed: breedId }));
            emit("edit", props.breed);
            break;
        case "quick-edit":
            emit("quick-edit", props.breed);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("breed");
            const url = resolveEntityRouteUrl("breed", "show", breedId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la classe copié !");
            }
            emit("copy-link", props.breed);
            break;
        }
        case "download-pdf":
            await downloadPdf(breedId);
            emit("download-pdf", props.breed);
            break;
        case "refresh":
            router.reload({ only: ["breeds"] });
            emit("refresh", props.breed);
            break;
        case "delete":
            emit("delete", props.breed);
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
                        :alt="breed.name || 'Classe'"
                        :caption="breed.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-graduation-cap" :alt="breed.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">{{ breed.name }}</h2>
            </template>

            <template #subtitle>
                <p v-if="breed.description" class="text-primary-300 mt-2 break-words">{{ breed.description }}</p>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="breed"
                        entity-type="breed"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal break-words"
                    />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="breeds"
                        :entity="breed"
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
                                class="text-primary-300 flex-shrink-0"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 break-words">
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
                                    class="text-primary-300 flex-shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
                                    <template v-if="fieldKey === 'auto_update'">
                                        <Icon
                                            v-if="autoUpdateValue !== null"
                                            :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                                            :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                            size="sm"
                                            :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                                        />
                                        <span v-else>—</span>
                                    </template>
                                    <template v-else>
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
