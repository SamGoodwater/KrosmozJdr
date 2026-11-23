<script setup>
/**
 * Progression classe : une section CMS par niveau (header partage / édition / repli),
 * corps Vue densifié ({@link BreedLevelBody}) en lecture.
 *
 * @props {Object} breed
 * @props {Object|null} characteristicRuntime
 */
import { computed } from "vue";
import BreedLevelBody from "@/Pages/Molecules/entity/breed/BreedLevelBody.vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { Capability } from "@/Models/Entity/Capability";

const CARAC_OR_TRAIT_LEVELS = new Set([4, 10, 16]);

const props = defineProps({
    breed: { type: Object, required: true },
    characteristicRuntime: { type: Object, default: null },
});

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const hitDieLabel = computed(() => {
    const raw = rawBreed.value?.life_dice ?? props.breed?.lifeDice ?? "d8";
    const s = String(raw).trim();
    if (!s) return "d8";
    return s.startsWith("d") || s.startsWith("D") ? s.toLowerCase() : `d${s}`;
});

const spellLevels = computed(() => {
    const set = new Set();
    for (const g of splitBreedSpellSlotGroups(rawBreed.value).variantGroups) {
        if ((g.spells || []).length > 0) set.add(Number(g.character_level));
    }
    return set;
});

const capabilitiesByLevel = computed(() => {
    const map = new Map();
    const { otherCapabilities } = partitionBreedCapabilities(rawBreed.value?.capabilities);
    for (const raw of otherCapabilities) {
        const cap = raw instanceof Capability ? raw : new Capability(raw);
        const level = Number(cap.level) || 0;
        if (level < 1) continue;
        if (!map.has(level)) map.set(level, []);
        map.get(level).push(cap);
    }
    return map;
});

/** Sections CMS « Niveau N » indexées par niveau. */
const cmsSectionByLevel = computed(() => {
    const sections = props.breed?.sections ?? rawBreed.value?.sections;
    const list = Array.isArray(sections) ? sections : [];
    /** @type {Map<number, object>} */
    const map = new Map();
    for (const section of list) {
        const title = String(section?.title || "").trim();
        const match = /^Niveau\s+(\d+)\s*$/i.exec(title);
        if (!match) continue;
        const level = Number(match[1]);
        if (!Number.isFinite(level) || level < 1) continue;
        map.set(level, section);
    }
    return map;
});

const levels = computed(() => {
    const out = [];
    for (let level = 1; level <= 20; level += 1) {
        const cmsSection = cmsSectionByLevel.value.get(level) || null;
        out.push({
            level,
            spells: spellLevels.value.has(level),
            caps: capabilitiesByLevel.value.get(level) || [],
            caracOrTrait: CARAC_OR_TRAIT_LEVELS.has(level),
            creationCarac: level === 1,
            cmsSection,
        });
    }
    return out;
});
</script>

<template>
    <div class="breed-progression-read space-y-4" aria-label="Progression de classe">
        <header class="space-y-1">
            <h3 class="text-sm font-semibold tracking-wide text-primary-100 m-0">
                Progression par niveau
            </h3>
            <p class="text-xs text-base-content/70 m-0">
                Niveau après niveau : points de vie, sorts (vue minimale), caractéristiques et capacités.
                Les catalogues ci-dessus rappellent tous les choix ; ici le détail principal.
                Les gains de spécialisation sont sur sa fiche (paliers 1, 3, 6, 9, 12, 15, 20).
            </p>
        </header>

        <div class="space-y-3">
            <template v-for="row in levels" :key="row.level">
                <SectionRenderer
                    v-if="row.cmsSection"
                    :section="row.cmsSection"
                    :force-full-width="true"
                    header-title-size="compact"
                >
                    <template #read>
                        <BreedLevelBody
                            :breed="breed"
                            :level="row.level"
                            :hit-die-label="hitDieLabel"
                            :spells="row.spells"
                            :caps="row.caps"
                            :carac-or-trait="row.caracOrTrait"
                            :creation-carac="row.creationCarac"
                            :characteristic-runtime="characteristicRuntime"
                        />
                    </template>
                </SectionRenderer>

                <article
                    v-else
                    :id="`breed-level-${row.level}`"
                    class="rounded-box border border-base-300 bg-base-100/40 px-3 py-2.5 space-y-2"
                >
                    <h4 class="text-sm font-semibold tracking-wide text-primary-100 m-0">
                        Niveau {{ row.level }}
                    </h4>
                    <BreedLevelBody
                        :breed="breed"
                        :level="row.level"
                        :hit-die-label="hitDieLabel"
                        :spells="row.spells"
                        :caps="row.caps"
                        :carac-or-trait="row.caracOrTrait"
                        :creation-carac="row.creationCarac"
                        :characteristic-runtime="characteristicRuntime"
                    />
                </article>
            </template>
        </div>
    </div>
</template>
