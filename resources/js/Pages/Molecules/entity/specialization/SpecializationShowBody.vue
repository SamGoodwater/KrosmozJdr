<script setup>
/**
 * Corps de fiche spécialisation (bibliothèque CMS + page Show) :
 * présentation CMS → progression par paliers (pas de catalogue capacités).
 *
 * @props {Object} specialization - Specialization / payload
 */
import { computed } from "vue";
import SpecializationProgressionRead from "@/Pages/Molecules/entity/specialization/SpecializationProgressionRead.vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import RichTextReadonlyView from "@/Pages/Molecules/data-display/RichTextReadonlyView.vue";
import { isPlaceholderSectionTitle } from "@/Utils/section/sectionTitle";
import {
    decorateSpecializationStyleHtml,
    sectionContentHtml,
} from "@/Utils/entity/specializationLevelHtml";
import { htmlContainsKrefMarkers } from "@/Utils/richText/htmlContainsKrefMarkers";

const props = defineProps({
    specialization: { type: Object, required: true },
});

const rawSpe = computed(() => props.specialization?._data ?? props.specialization);

/** Slug pour les tokens de style (Artiste → artiste). */
const specializationSlug = computed(() => {
    const name = String(rawSpe.value?.name || props.specialization?.name || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{M}/gu, "");
    return name.replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "") || "artiste";
});

const speSections = computed(() => {
    const sections = props.specialization?.sections ?? rawSpe.value?.sections;
    return Array.isArray(sections) ? sections : [];
});

/** Présentation CMS seule (hors sections « Niveau N »). */
const presentationSection = computed(() => {
    const sections = speSections.value;
    const named = sections.find((s) => {
        const title = String(s?.title || "").trim();
        return title === "Texte" || isPlaceholderSectionTitle(title);
    });
    const base =
        named ||
        sections.find((s) => !/^Niveau\s+\d+/i.test(String(s?.title || "").trim())) ||
        null;
    if (!base) return null;
    const html = String(base?.data?.content || "").trim();
    if (!html) return null;
    return base;
});

/**
 * @param {object|null} section
 * @returns {{ html: string, enableRichReferences: boolean }}
 */
function presentationView(section) {
    const html = decorateSpecializationStyleHtml(
        sectionContentHtml(section),
        specializationSlug.value,
    );
    return {
        html,
        enableRichReferences:
            htmlContainsKrefMarkers(html) ||
            /\bspe-style\b/.test(html) ||
            /\bdata-spe-style=/.test(html),
    };
}
</script>

<template>
    <div class="specialization-show-body space-y-6 w-full min-w-0">
        <section
            v-if="presentationSection"
            class="space-y-2"
            aria-label="Présentation"
        >
            <SectionRenderer
                :section="presentationSection"
                :immersive="true"
                :force-full-width="true"
            >
                <template #read="{ section: hydrated }">
                    <RichTextReadonlyView
                        v-bind="presentationView(hydrated)"
                        :specialization-slug="specializationSlug"
                        class="prose prose-invert max-w-none"
                    />
                </template>
            </SectionRenderer>
        </section>

        <SpecializationProgressionRead
            :specialization="specialization"
            :specialization-slug="specializationSlug"
        />
    </div>
</template>
