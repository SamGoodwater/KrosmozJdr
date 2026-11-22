<script setup>
/**
 * Fiche lecture d’une classe (Breed).
 *
 * Ordre : en-tête → présentation → voies → passif → mécanique → progression
 * (une carte par niveau avec sorts en vue minimale) → rappel icônes.
 *
 * @props {Object} breed - Payload BreedResource
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Breed } from "@/Models/Entity/Breed";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import BreedViewFull from "@/Pages/Molecules/entity/breed/BreedViewFull.vue";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedCapabilitiesDisplay from "@/Pages/Molecules/entity/breed/BreedCapabilitiesDisplay.vue";
import BreedMechanicsDisplay from "@/Pages/Molecules/entity/breed/BreedMechanicsDisplay.vue";
import BreedProgressionRead from "@/Pages/Molecules/entity/breed/BreedProgressionRead.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import BreedWriteMetaPanel from "@/Pages/Molecules/entity/breed/BreedWriteMetaPanel.vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { isPlaceholderSectionTitle } from "@/Utils/section/sectionTitle";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
});

const breed = computed(() => {
    const raw = props.breed || page.props.breed || {};
    return raw instanceof Breed ? raw : new Breed(raw);
});

const rawBreed = computed(() => breed.value?._data ?? breed.value);

const breedSections = computed(() => {
    const sections = breed.value?.sections;
    return Array.isArray(sections) ? sections : [];
});

/** Section CMS « Texte » (présentation seule — le récap est en Vue). */
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
    // Retire l’ancien tableau HTML de progression (remplacé par BreedProgressionRecap).
    const cleaned = html
        .replace(/<h2[^>]*>\s*Progression\s*<\/h2>/gi, "")
        .replace(/<table[\s\S]*?<\/table>/gi, "");
    if (cleaned === html) return base;
    return {
        ...base,
        data: { ...(base.data || {}), content: cleaned },
    };
});

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

const orientationMap = computed(() =>
    normalizeElementOrientationMap(rawBreed.value?.element_orientations),
);

const linkedCapabilities = computed(() => {
    const list = rawBreed.value?.capabilities;
    return Array.isArray(list) ? list : [];
});

const mechanicsHtml = computed(() => {
    const t = rawBreed.value?.mechanics ?? breed.value?.mechanics;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const hasSpellSlots = computed(() => buildSpellSlotGroups(rawBreed.value).length > 0);

setPageTitle(`Classe : ${breed.value.name || "-"}`);

const goEdit = () => {
    if (!breed.value.id) return;
    router.visit(route("entities.breeds.edit", { breed: breed.value.id }));
};
</script>

<template>
    <Head :title="`Classe : ${breed?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.breeds.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="breed?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <!-- 1. En-tête (identité) -->
                <BreedViewFull
                    :breed="breed"
                    :show-actions="true"
                    :show-orientations="false"
                    :show-capabilities="false"
                    :show-traits="false"
                    :show-languages="false"
                    :show-mechanics="false"
                    :show-spell-variants="false"
                    :characteristic-runtime="characteristicRuntime"
                />

                <!-- 2. Présentation CMS -->
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

                <!-- 3. Voies -->
                <section
                    class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3"
                    aria-label="Voies élémentaires"
                >
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                            Voies
                        </h2>
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

                <!-- 4. Passif uniquement -->
                <BreedCapabilitiesDisplay
                    v-if="linkedCapabilities.length"
                    :capabilities="linkedCapabilities"
                    density="full"
                    :show-other-section="false"
                    :characteristic-runtime="characteristicRuntime"
                />

                <!-- 5. Mécanique de classe (si présente) -->
                <BreedMechanicsDisplay :html="mechanicsHtml" />

                <!-- 6. Une section par niveau (PV + caracs + sorts minimal + capacité) -->
                <BreedProgressionRead
                    :breed="breed"
                    :characteristic-runtime="characteristicRuntime"
                />

                <!-- 7. Rappel compact de tous les sorts -->
                <BreedVariantsDisplay
                    v-if="hasSpellSlots"
                    :breed="rawBreed"
                    density="full"
                    :characteristic-runtime="characteristicRuntime"
                />

                <BreedWriteMetaPanel :breed="breed" />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
