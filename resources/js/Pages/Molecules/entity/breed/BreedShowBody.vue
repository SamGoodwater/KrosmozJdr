<script setup>
/**
 * Corps de fiche classe (bibliothèque CMS + page Show) :
 * présentation → voies → passif+mécanique → catalogues sorts/capacités → progression.
 *
 * @props {Object} breed - Breed / payload
 */
import { computed } from "vue";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedCapabilitiesDisplay from "@/Pages/Molecules/entity/breed/BreedCapabilitiesDisplay.vue";
import BreedCapabilitiesCatalog from "@/Pages/Molecules/entity/breed/BreedCapabilitiesCatalog.vue";
import BreedMechanicsDisplay from "@/Pages/Molecules/entity/breed/BreedMechanicsDisplay.vue";
import BreedProgressionRead from "@/Pages/Molecules/entity/breed/BreedProgressionRead.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import AdjacentSectionsRow from "@/Pages/Molecules/section/AdjacentSectionsRow.vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import { adjacentCellClass } from "@/Utils/section/sectionLayout";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";
import { isPlaceholderSectionTitle } from "@/Utils/section/sectionTitle";

const props = defineProps({
    breed: { type: Object, required: true },
    characteristicRuntime: { type: Object, default: null },
});

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const breedSections = computed(() => {
    const sections = props.breed?.sections ?? rawBreed.value?.sections;
    return Array.isArray(sections) ? sections : [];
});

/** Présentation CMS seule (hors sections « Niveau N »). */
const presentationSection = computed(() => {
    const sections = breedSections.value;
    const named = sections.find((s) => {
        const title = String(s?.title || "").trim();
        return title === "Texte" || isPlaceholderSectionTitle(title);
    });
    const base =
        named ||
        sections.find((s) => !/^Niveau\s+\d+/i.test(String(s?.title || "").trim())) ||
        null;
    if (!base) return null;
    const html = String(base?.data?.content || "");
    const cleaned = html
        .replace(/<h2[^>]*>\s*Progression\s*<\/h2>/gi, "")
        .replace(/<table[\s\S]*?<\/table>/gi, "");
    if (cleaned === html) return base;
    return {
        ...base,
        data: { ...(base.data || {}), content: cleaned },
    };
});

const orientationMap = computed(() =>
    normalizeElementOrientationMap(rawBreed.value?.element_orientations),
);

const linkedCapabilities = computed(() => {
    const list = rawBreed.value?.capabilities;
    return Array.isArray(list) ? list : [];
});

const partitionedCapabilities = computed(() =>
    partitionBreedCapabilities(linkedCapabilities.value),
);

const hasPassiveCapabilities = computed(
    () => partitionedCapabilities.value.passiveCapabilities.length > 0,
);

const hasClassCapabilities = computed(
    () => partitionedCapabilities.value.otherCapabilities.length > 0,
);

const mechanicsHtml = computed(() => {
    const t = rawBreed.value?.mechanics ?? props.breed?.mechanics;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const hasSpellSlots = computed(() => buildSpellSlotGroups(rawBreed.value).length > 0);
</script>

<template>
    <div class="breed-show-body space-y-6 w-full min-w-0">
        <section
            v-if="presentationSection"
            class="space-y-2"
            aria-label="Présentation"
        >
            <SectionRenderer
                :section="{
                    ...presentationSection,
                    can: { ...(presentationSection.can || {}), update: false, delete: false },
                }"
                :immersive="true"
                :force-full-width="true"
            />
        </section>

        <section
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3"
            aria-label="Voies élémentaires"
        >
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Voies
                </h3>
                <p class="text-xs text-primary-400/90 mt-1">
                    Chaque voie (Air, Terre, Feu, Eau) porte un rôle à la création : dégâts,
                    soin, placement…
                </p>
            </div>
            <BreedElementOrientationsDisplay
                :orientation-map="orientationMap"
                variant="cards"
            />
        </section>

        <AdjacentSectionsRow
            v-if="hasPassiveCapabilities || mechanicsHtml"
            class="w-full min-w-0"
        >
            <div
                v-if="hasPassiveCapabilities"
                :class="adjacentCellClass(mechanicsHtml ? 'half' : 'fill')"
            >
                <BreedCapabilitiesDisplay
                    :capabilities="linkedCapabilities"
                    density="full"
                    :show-other-section="false"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
            <div
                v-if="mechanicsHtml"
                :class="adjacentCellClass(hasPassiveCapabilities ? 'half' : 'fill')"
            >
                <BreedMechanicsDisplay :html="mechanicsHtml" />
            </div>
        </AdjacentSectionsRow>

        <BreedVariantsDisplay
            v-if="hasSpellSlots"
            :breed="rawBreed"
            density="full"
            :characteristic-runtime="characteristicRuntime"
        />

        <BreedCapabilitiesCatalog
            v-if="hasClassCapabilities"
            :capabilities="linkedCapabilities"
            density="full"
            :characteristic-runtime="characteristicRuntime"
        />

        <BreedProgressionRead
            :breed="breed"
            :characteristic-runtime="characteristicRuntime"
        />
    </div>
</template>
