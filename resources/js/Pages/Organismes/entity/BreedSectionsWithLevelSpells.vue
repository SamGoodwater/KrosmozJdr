<script setup>
/**
 * Sections CMS d’une classe + cartes SpellViewMinimal sous chaque « Niveau N » à sorts.
 */
import { computed } from "vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import BreedLevelSpellChoices from "@/Pages/Molecules/entity/breed/BreedLevelSpellChoices.vue";
import { pageSectionsUseFullWidth } from "@/Utils/section/sectionLayout";

const props = defineProps({
    breed: { type: Object, required: true },
    sections: { type: Array, default: () => [] },
    characteristicRuntime: { type: Object, default: null },
    immersive: { type: Boolean, default: true },
    alignWithColumn: { type: Boolean, default: true },
});

const toNumericOrder = (value, fallback = 0) => {
    const n = Number(value);
    return Number.isFinite(n) ? n : fallback;
};

/**
 * @param {object} section
 * @returns {number|null}
 */
function levelFromSection(section) {
    const title = String(section?.title || "").trim();
    const titleMatch = title.match(/^Niveau\s+(\d+)\b/i);
    if (titleMatch) return Number(titleMatch[1]);

    const html = String(section?.data?.content || "");
    const anchor = html.match(/data-breed-level-spells=["']?(\d+)/i);
    if (anchor) return Number(anchor[1]);

    return null;
}

const normalizedSections = computed(() => {
    const rawSections = Array.isArray(props.sections) ? props.sections : [];
    return rawSections
        .filter((section) => section && typeof section === "object")
        .map((section, index) => {
            const fallbackOrder = index + 1;
            const order =
                section.order ??
                section.pivot_level ??
                section?.pivot?.level ??
                section.level ??
                fallbackOrder;
            return {
                ...section,
                can: { ...(section.can || {}), update: false, delete: false },
                order: toNumericOrder(order, fallbackOrder),
                _spellLevel: levelFromSection(section),
            };
        })
        .sort((a, b) => {
            const orderDiff = toNumericOrder(a.order) - toNumericOrder(b.order);
            if (orderDiff !== 0) return orderDiff;
            return String(a.title || "").localeCompare(String(b.title || ""), "fr");
        });
});

const sectionsShareFullWidth = computed(
    () => props.alignWithColumn || pageSectionsUseFullWidth(normalizedSections.value),
);
</script>

<template>
    <div class="breed-sections-with-level-spells w-full min-w-0 space-y-4">
        <div v-if="normalizedSections.length > 0" class="sections">
            <div
                v-for="section in normalizedSections"
                :key="section.id || section.slug || section.title"
                class="section-with-spells space-y-3 w-full min-w-0"
            >
                <SectionRenderer
                    :section="section"
                    :immersive="immersive"
                    :force-full-width="sectionsShareFullWidth"
                />
                <BreedLevelSpellChoices
                    v-if="section._spellLevel != null"
                    :breed="breed"
                    :character-level="section._spellLevel"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.sections {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    max-width: 100%;
    min-width: 0;

    > * {
        min-width: 0;
        max-width: 100%;
        margin-bottom: 1.5rem;

        &:last-child {
            margin-bottom: 0;
        }
    }
}
</style>
