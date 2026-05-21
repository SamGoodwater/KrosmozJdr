<script setup>
/**
 * BreedViewFull — Vue Full pour Breed
 *
 * En-tête + blocs gameplay structuraux (orientations, traits, langues, variantes sorts).
 * Le contenu narratif (spécificité, dé de vie, évolution, capacités en prose) est dans les sections liées.
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import { resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";

const props = defineProps({
    breed: {
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

const headerMode = computed(() => (props.inModal ? "compact" : "full"));

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

const mediaSrc = computed(() => {
    const b = props.breed;
    const u = b?.image ?? b?.icon ?? b?._data?.image ?? b?._data?.icon;
    return u && String(u).trim() ? String(u) : "";
});

const spellSlotGroups = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    return buildSpellSlotGroups(raw);
});

const hasSpellSlots = computed(() => spellSlotGroups.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw = props.breed?._data?.creatureTraits ?? props.breed?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const linkedLanguages = computed(() => {
    const raw = props.breed?._data?.languages ?? props.breed?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const descriptionFast = computed(() => {
    const b = props.breed?._data ?? props.breed;
    const t = b?.description_fast;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const descriptionFull = computed(() => {
    const b = props.breed?._data ?? props.breed;
    const t = b?.description ?? props.breed?.description;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const orientationMap = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    return normalizeElementOrientationMap(raw?.element_orientations);
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[BreedViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

/** Résumé sorts uniquement si pas encore de variantes structurées. */
const summaryMetaFields = computed(() =>
    hasSpellSlots.value ? [] : ["breed_summary_relations"].filter(canShowField),
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "breed",
    });

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
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
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
                <div
                    v-if="descriptionFast || descriptionFull"
                    class="mt-3 max-w-3xl space-y-2 border-t border-base-300/60 pt-3"
                >
                    <p
                        v-if="descriptionFast"
                        class="text-sm leading-relaxed text-primary-300 break-words"
                    >
                        {{ descriptionFast }}
                    </p>
                    <p
                        v-if="descriptionFull && descriptionFull !== descriptionFast"
                        class="text-sm leading-relaxed whitespace-pre-wrap text-primary-200/90 break-words"
                    >
                        {{ descriptionFull }}
                    </p>
                </div>
            </template>

            <template #mainInfos>
                <div
                    v-if="summaryMetaFields.length > 0"
                    class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2"
                >
                    <EntityPropertyDisplay
                        v-for="fieldKey in summaryMetaFields"
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

        <div class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Voix élémentaires</h3>
            <p class="text-xs text-primary-400/90">
                Chaque voix (air, terre, feu, eau) peut être associée à une orientation de jeu (icônes configurables).
            </p>
            <BreedElementOrientationsDisplay :orientation-map="orientationMap" size="md" />
        </div>

        <div
            v-if="hasLinkedCreatureTraits"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Traits de classe"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" show-level size="sm" />
        </div>

        <div
            v-if="hasLinkedLanguages"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Langues"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Langues</h3>
            <EntityLanguagesInline :languages="linkedLanguages" :show-label="false" />
        </div>

        <BreedVariantsDisplay
            v-if="hasSpellSlots"
            :breed="breed?._data ?? breed"
            density="full"
            :characteristic-runtime="characteristicRuntime"
        />
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
