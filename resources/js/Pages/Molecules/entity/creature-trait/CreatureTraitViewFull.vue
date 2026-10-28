<script setup>
/**
 * CreatureTraitViewFull — Vue Full pour CreatureTrait
 *
 * @description
 * Alignement léger sur Condition : EntityViewHeader, description en subtitle,
 * grille sans doublon name/description.
 *
 * @props {CreatureTrait} creatureTrait - Instance du modèle CreatureTrait
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
import { getCreatureTraitFieldDescriptors } from "@/Entities/creature-trait/creature-trait-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";

const props = defineProps({
    creatureTrait: {
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

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

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
const { downloadPdf } = useDownloadPdf("creature-traits");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("creature-traits", "viewAny"),
        createAny: permissions.can("creature-traits", "createAny"),
        updateAny: permissions.can("creature-traits", "updateAny"),
        deleteAny: permissions.can("creature-traits", "deleteAny"),
        manageAny: permissions.can("creature-traits", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCreatureTraitFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[CreatureTraitViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const mediaSrc = computed(() => {
    const u = props.creatureTrait?.image ?? props.creatureTrait?._data?.image;
    return u && String(u).trim() ? String(u) : "";
});

const metaFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["created_by", "created_at", "updated_at"].filter(canShowField),
);

const getCell = (fieldKey) =>
    props.creatureTrait.toCell(fieldKey, {
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
        case "copy-link": {
            const cfg = getEntityRouteConfig("creature-traits");
            const url = resolveEntityRouteUrl("creature-traits", "show", creatureTraitId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'attribut copié !");
            }
            emit("copy-link", props.creatureTrait);
            break;
        }
        case "download-pdf":
            await downloadPdf(creatureTraitId);
            emit("download-pdf", props.creatureTrait);
            break;
        case "refresh":
            router.reload({ only: ["creatureTraits"] });
            emit("refresh", props.creatureTrait);
            break;
        case "delete":
            emit("delete", props.creatureTrait);
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
                        :alt="creatureTrait.name || 'Trait'"
                        :caption="creatureTrait.name || ''"
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
                        <Icon source="fa-solid fa-fingerprint" :alt="creatureTrait.name" size="lg" />
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
                        entity-type="creature-traits"
                        :entity="creatureTrait"
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
                    :entity="creatureTrait"
                    entity-type="creature-traits"
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
                :entity="creatureTrait"
                entity-type="creature-traits"
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
