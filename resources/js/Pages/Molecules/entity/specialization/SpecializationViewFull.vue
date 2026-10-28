<script setup>
/**
 * SpecializationViewFull — Vue Full pour Specialization
 *
 * @description
 * Alignée Breed : EntityViewHeader (image | nom / accroche / corps), traits et
 * relations en sections `v-if` (SpecializationRelationsByLevel).
 *
 * @props {Specialization} specialization - Instance du modèle Specialization
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import SpecializationRelationsByLevel from "@/Pages/Molecules/entity/specialization/SpecializationRelationsByLevel.vue";

const props = defineProps({
    specialization: {
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
const { downloadPdf } = useDownloadPdf("specialization");

const linkedCreatureTraits = computed(() => {
    const raw = props.specialization?._data?.creatureTraits ?? props.specialization?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const shortPitch = computed(() => {
    const raw =
        props.specialization?.shortDescription ??
        props.specialization?._data?.short_description ??
        props.specialization?.short_description;
    const s = String(raw ?? "").trim();
    return s.length ? s : "";
});

const longDescription = computed(() => {
    const raw = props.specialization?.description ?? props.specialization?._data?.description;
    const s = String(raw ?? "").trim();
    return s.length ? s : "";
});

const mediaSrc = computed(() => {
    const raw = props.specialization?.image ?? props.specialization?._data?.image;
    return String(raw ?? "").trim();
});

const getCell = (fieldKey) =>
    props.specialization.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });

const handleAction = async (actionKey) => {
    const specializationId = props.specialization.id;
    if (!specializationId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.specializations.show", { specialization: specializationId }));
            emit("view", props.specialization);
            break;
        case "edit":
            router.visit(route("entities.specializations.edit", { specialization: specializationId }));
            emit("edit", props.specialization);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("specialization");
            const url = resolveEntityRouteUrl("specialization", "show", specializationId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de la spécialisation copié !");
            }
            emit("copy-link", props.specialization);
            break;
        }
        case "download-pdf":
            await downloadPdf(specializationId);
            emit("download-pdf", props.specialization);
            break;
        case "refresh":
            router.reload({ only: ["specializations"] });
            emit("refresh", props.specialization);
            break;
        case "delete":
            emit("delete", props.specialization);
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
                        :alt="specialization.name || 'Spécialisation'"
                        :caption="specialization.name || ''"
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
                        <Icon source="fa-solid fa-compass" :alt="specialization.name" size="lg" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-xl font-bold break-words text-primary-100 md:text-2xl">
                    <CellRenderer :cell="getCell('name')" ui-color="primary" />
                </component>
            </template>

            <template v-if="shortPitch" #mainInfos>
                <p class="specialization-pitch text-base font-semibold leading-snug text-primary-50 break-words md:text-lg">
                    {{ shortPitch }}
                </p>
            </template>

            <template v-if="longDescription" #subtitle>
                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words text-primary-200 md:text-base">
                    {{ longDescription }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="specializations"
                        :entity="specialization"
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
            v-if="hasLinkedCreatureTraits"
            class="space-y-2 rounded-box border border-base-300 bg-base-100/40 p-4"
            role="region"
            aria-label="Traits de spécialisation"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" show-level size="sm" />
        </div>

        <SpecializationRelationsByLevel :specialization="specialization" />
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}

.specialization-pitch {
    border-left: 3px solid color-mix(in oklch, hsl(var(--p)) 65%, transparent);
    padding-left: 0.85rem;
    margin-left: 0.1rem;
}
</style>
