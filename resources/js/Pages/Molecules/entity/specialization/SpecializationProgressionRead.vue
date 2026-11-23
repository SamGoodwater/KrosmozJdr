<script setup>
/**
 * Progression spécialisation : sections CMS « Niveau N » (header partage / édition / repli),
 * corps densifié ({@link SpecializationLevelBody}).
 *
 * Le HTML narrative vient de la section **hydratée** (slot `#read`) pour respecter
 * le chargement différé CMS.
 */
import { computed } from "vue";
import SpecializationLevelBody from "@/Pages/Molecules/entity/specialization/SpecializationLevelBody.vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import { Capability } from "@/Models/Entity/Capability";
import { specializationCapabilityPivotLevel } from "@/Utils/entity/specializationLevelHtml";

const props = defineProps({
    specialization: { type: Object, required: true },
    specializationSlug: { type: String, default: "artiste" },
});

const rawSpe = computed(() => props.specialization?._data ?? props.specialization);

const cmsSectionByLevel = computed(() => {
    const sections = props.specialization?.sections ?? rawSpe.value?.sections;
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

const capabilitiesByLevel = computed(() => {
    const list = rawSpe.value?.capabilities;
    const caps = Array.isArray(list) ? list : [];
    /** @type {Map<number, Capability[]>} */
    const map = new Map();
    for (const raw of caps) {
        const cap = raw instanceof Capability ? raw : new Capability(raw);
        const level = specializationCapabilityPivotLevel(cap);
        if (level < 1) continue;
        if (!map.has(level)) map.set(level, []);
        map.get(level).push(cap);
    }
    for (const [, arr] of map) {
        arr.sort((a, b) => {
            const ap = a.isPassive ? 0 : 1;
            const bp = b.isPassive ? 0 : 1;
            if (ap !== bp) return ap - bp;
            return String(a.name || "").localeCompare(String(b.name || ""), "fr");
        });
    }
    return map;
});

const levels = computed(() => {
    const fromCms = Array.from(cmsSectionByLevel.value.keys());
    const fromCaps = Array.from(capabilitiesByLevel.value.keys());
    const set = new Set([...fromCms, ...fromCaps]);
    return Array.from(set)
        .filter((n) => Number.isFinite(n) && n > 0)
        .sort((a, b) => a - b)
        .map((level) => ({
            level,
            cmsSection: cmsSectionByLevel.value.get(level) || null,
            caps: capabilitiesByLevel.value.get(level) || [],
        }));
});
</script>

<template>
    <div class="specialization-progression-read space-y-4" aria-label="Progression de spécialisation">
        <header class="space-y-1">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-100 m-0">
                Progression par palier
            </h3>
            <p class="text-xs text-base-content/70 m-0">
                Maîtrises et aptitudes restent dans le texte du palier. Les capacités à choisir
                sont en vue texte (icône + nom) ; le détail s’ouvre au clic.
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
                    <template #read="{ section: hydratedSection }">
                        <SpecializationLevelBody
                            :cms-section="hydratedSection"
                            :capabilities="row.caps"
                            :specialization-slug="specializationSlug"
                        />
                    </template>
                </SectionRenderer>

                <article
                    v-else
                    class="rounded-box border border-base-300 bg-base-100/40 px-3 py-2.5 space-y-2"
                >
                    <h4 class="text-sm font-semibold tracking-wide text-primary-100 m-0">
                        Niveau {{ row.level }}
                    </h4>
                    <SpecializationLevelBody
                        :capabilities="row.caps"
                        :specialization-slug="specializationSlug"
                    />
                </article>
            </template>
        </div>
    </div>
</template>
