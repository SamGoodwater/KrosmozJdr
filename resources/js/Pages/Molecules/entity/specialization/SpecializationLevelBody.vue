<script setup>
/**
 * Corps d’un palier : narrative CMS aérée (maîtrises, aptitudes, styles) + capacités vue texte
 * + variantes (pastille colorée + capacité).
 */
import { computed } from "vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import SpecializationStyleChip from "@/Pages/Molecules/entity/specialization/SpecializationStyleChip.vue";
import RichTextReadonlyView from "@/Pages/Molecules/data-display/RichTextReadonlyView.vue";
import { Capability } from "@/Models/Entity/Capability";
import {
    prepareSpecializationLevelHtml,
    sectionContentHtml,
} from "@/Utils/entity/specializationLevelHtml";
import {
    resolveSpecializationStyle,
    specializationStyleAccentColor,
} from "@/Utils/entity/specializationStyles";
import { htmlContainsKrefMarkers } from "@/Utils/richText/htmlContainsKrefMarkers";

const props = defineProps({
    cmsSection: { type: Object, default: null },
    rawHtml: { type: String, default: "" },
    capabilities: { type: Array, default: () => [] },
    specializationSlug: { type: String, default: "artiste" },
});

/**
 * @param {object} raw
 * @returns {Capability}
 */
const asCap = (raw) => (raw instanceof Capability ? raw : new Capability(raw));

const prepared = computed(() => {
    const html = sectionContentHtml(props.cmsSection) || String(props.rawHtml || "");
    return prepareSpecializationLevelHtml(html, {
        specializationSlug: props.specializationSlug,
    });
});

const narrativeHtml = computed(() => prepared.value.narrativeHtml);

const hasNarrative = computed(() => {
    const raw = String(narrativeHtml.value || "").trim();
    if (!raw) return false;
    const text = raw
        .replace(/<[^>]+>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .replace(/\s+/g, " ")
        .trim();
    return text.length > 0;
});

/** Krefs ou pastilles de style → arbre Vue (icônes). */
const enableRichReferences = computed(() => {
    const html = narrativeHtml.value;
    return (
        htmlContainsKrefMarkers(html) ||
        /\bspe-style\b/.test(html) ||
        /\bdata-spe-style=/.test(html)
    );
});

const excludeNames = computed(() => new Set(prepared.value.excludeCapabilityNames || []));

const capsByName = computed(() => {
    /** @type {Map<string, Capability>} */
    const map = new Map();
    for (const raw of Array.isArray(props.capabilities) ? props.capabilities : []) {
        const cap = asCap(raw);
        const name = String(cap.name || "").trim().toLowerCase();
        if (name) map.set(name, cap);
    }
    return map;
});

const choiceCapabilities = computed(() => {
    const list = Array.isArray(props.capabilities) ? props.capabilities : [];
    return list
        .map(asCap)
        .filter((c) => !c.isPassive)
        .filter((c) => {
            const name = String(c.name || "").trim().toLowerCase();
            if (!name) return true;
            return !excludeNames.value.has(name);
        });
});

const styleVariantRows = computed(() => {
    const rows = Array.isArray(prepared.value.styleVariants) ? prepared.value.styleVariants : [];
    return rows.map((row) => {
        const key = String(row.capabilityName || "").trim().toLowerCase();
        const token = resolveSpecializationStyle(row.styleKey, props.specializationSlug);
        return {
            ...row,
            capability: key ? capsByName.value.get(key) || null : null,
            columnStyle: {
                "--color": specializationStyleAccentColor(token) || "var(--color-white)",
            },
        };
    });
});

/** Grille colonnes : 2 dès sm, jusqu’à 4 si beaucoup de variantes. */
const styleColumnsClass = computed(() => {
    const n = styleVariantRows.value.length;
    if (n <= 1) return "grid-cols-1";
    if (n === 2) return "grid-cols-1 sm:grid-cols-2";
    if (n === 3) return "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3";
    return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-4";
});

const capacityTitle = computed(() => {
    const pick = Number(prepared.value.capacityPick) || 0;
    if (pick > 0) return `Capacités — choisis-en ${pick}`;
    return "Capacités";
});
</script>

<template>
    <div class="specialization-level-body space-y-7">
        <RichTextReadonlyView
            v-if="hasNarrative"
            :html="narrativeHtml"
            :enable-rich-references="enableRichReferences"
            :specialization-slug="specializationSlug"
            class="spe-level-narrative w-full max-w-none text-sm text-base-content/85 prose prose-invert prose-sm"
        />

        <section
            v-if="styleVariantRows.length"
            class="space-y-3 pt-1"
            aria-label="Selon ton style"
        >
            <h4 class="text-sm font-bold uppercase tracking-wide text-primary-200 m-0">
                Selon ton style
            </h4>
            <ul
                class="grid list-none gap-3 p-0 m-0"
                :class="styleColumnsClass"
            >
                <li
                    v-for="(row, idx) in styleVariantRows"
                    :key="`style-var-${row.styleKey}-${idx}`"
                    class="spe-style-column min-w-0 border-glass-l-xs space-y-2.5 py-1 pl-3 pr-1"
                    :style="row.columnStyle"
                >
                    <SpecializationStyleChip
                        v-if="row.styleKey"
                        :style-key="row.styleKey"
                        :specialization-slug="specializationSlug"
                        appearance="marker"
                        size="md"
                    />
                    <div class="min-w-0">
                        <CapabilityViewText
                            v-if="row.capability"
                            :capability="row.capability"
                            appearance="inline"
                            trigger="hover"
                        />
                        <span
                            v-else-if="row.capabilityName"
                            class="text-sm font-medium text-base-content/85"
                        >
                            {{ row.capabilityName }}
                        </span>
                    </div>
                </li>
            </ul>
        </section>

        <section
            v-if="choiceCapabilities.length"
            class="space-y-2.5 pt-1"
            aria-label="Capacités"
        >
            <h4 class="text-sm font-bold uppercase tracking-wide text-primary-200 m-0">
                {{ capacityTitle }}
            </h4>
            <ul class="flex list-none flex-wrap gap-x-3 gap-y-2 p-0 m-0">
                <li
                    v-for="c in choiceCapabilities"
                    :key="`cap-${c.id}`"
                    class="min-w-0 max-w-full"
                >
                    <CapabilityViewText
                        :capability="c"
                        appearance="inline"
                        trigger="hover"
                    />
                </li>
            </ul>
        </section>
    </div>
</template>

<style scoped>
/*
 * Sur une fiche spé on lit souvent des rangées de puces kref : on élargit la mesure
 * (sinon wrap trop tôt à ~56–76ch) et on laisse de la hauteur de ligne aux chips.
 */
.spe-level-narrative {
    --measure-body: min(100%, 96ch);
    --measure-title: min(100%, 96ch);
    --measure-subtitle: min(100%, 96ch);
}

.spe-level-narrative :deep(p),
.spe-level-narrative :deep(ul),
.spe-level-narrative :deep(ol),
.spe-level-narrative :deep(h2),
.spe-level-narrative :deep(h3),
.spe-level-narrative :deep(h4),
.spe-level-narrative :deep(.spe-level-h2) {
    max-width: none;
    margin-left: 0;
    margin-right: 0;
}

.spe-level-narrative :deep(h2),
.spe-level-narrative :deep(.spe-level-h2) {
    margin: 1.75rem 0 0.7rem;
    padding-bottom: 0.35rem;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: color-mix(in oklch, hsl(var(--p)) 78%, white);
    border-bottom: 1px solid color-mix(in oklch, hsl(var(--p)) 28%, transparent);
}
.spe-level-narrative :deep(h2:first-child),
.spe-level-narrative :deep(.spe-level-h2:first-child) {
    margin-top: 0.25rem;
}
.spe-level-narrative :deep(h3) {
    margin: 1.35rem 0 0.55rem;
    font-size: 1.05rem;
    font-weight: 700;
    line-height: 1.35;
    color: color-mix(in oklch, hsl(var(--bc)) 96%, transparent);
    padding-left: 0;
    border-left: none;
    box-shadow: none;
}
.spe-level-narrative :deep(ul),
.spe-level-narrative :deep(ol) {
    margin: 0.45rem 0 1rem;
    padding-left: 1.15rem;
}
.spe-level-narrative :deep(li) {
    margin: 0.55rem 0;
    /* Hauteur de ligne > hauteur des puces kref pour éviter le chevauchement au wrap. */
    line-height: 2.05;
}
.spe-level-narrative :deep(.spe-masteries-list) {
    list-style: none;
    padding-left: 0;
    margin: 0.55rem 0 1.15rem;
}
.spe-level-narrative :deep(.spe-masteries-list > li) {
    margin: 1.15rem 0;
    padding: 0.15rem 0 0.35rem;
    line-height: 2.1;
}
.spe-level-narrative :deep(.spe-masteries-list > li > strong:first-child) {
    display: block;
    margin: 0 0 0.45rem;
    font-weight: 800;
    font-size: 0.92rem;
    letter-spacing: 0.03em;
    line-height: 1.3;
    color: color-mix(in oklch, hsl(var(--bc)) 98%, transparent);
}
.spe-level-narrative :deep(p) {
    margin: 0.65rem 0;
    line-height: 1.75;
}
/* Puces inline : un peu d’air vertical si elles passent à la ligne. */
.spe-level-narrative :deep(.kref) {
    margin-block: 0.22em;
    margin-inline: 0.18em;
    vertical-align: middle;
}
</style>
