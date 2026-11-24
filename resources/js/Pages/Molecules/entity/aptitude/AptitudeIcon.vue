<script setup>
/**
 * AptitudeIcon — Pictogramme d’aptitude sur fond en forme de bouclier.
 *
 * @description
 * Distingue les aptitudes des sorts (étoile), traits (losange) et états (hexagone) :
 * cadre en écu fuchsia. Si la fiche a une `image`, elle est affichée dans le cadre ;
 * sinon un glyphe FA unique par nom (voir aptitudeGlyphs.js).
 *
 * @example
 * <AptitudeIcon label="Politicien" size="xs" />
 * <AptitudeIcon :src="imageUrl" label="Rituel" size="md" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    aptitudeGlyphFill,
    resolveAptitudeGlyph,
} from "@/Utils/entity/aptitudeGlyphs";
import { resolveEntityImageUrl } from "@/Utils/entity/entityThumb";

const props = defineProps({
    src: { type: String, default: "" },
    /** Nom de l’aptitude (résolution du glyphe). */
    label: { type: String, default: "Aptitude" },
    /** Id optionnel pour le hash de repli. */
    aptitudeId: { type: [Number, String], default: null },
    size: {
        type: String,
        default: "xs",
        validator: (v) => ["xs", "sm", "md", "lg"].includes(v),
    },
});

const customSrc = computed(() => {
    const direct = String(props.src || "").trim();
    return direct ? resolveEntityImageUrl(direct) : "";
});

const glyph = computed(() =>
    resolveAptitudeGlyph({
        name: props.label,
        id: props.aptitudeId,
    }),
);

const fill = computed(() => aptitudeGlyphFill(glyph.value));

const sizeClass = computed(() => {
    switch (props.size) {
        case "sm":
            return "aptitude-icon--sm";
        case "md":
            return "aptitude-icon--md";
        case "lg":
            return "aptitude-icon--lg";
        default:
            return "aptitude-icon--xs";
    }
});

const iconSize = computed(() => {
    switch (props.size) {
        case "sm":
            return "sm";
        case "md":
            return "md";
        case "lg":
            return "xl";
        default:
            return "xs";
    }
});
</script>

<template>
    <div
        class="aptitude-icon shrink-0"
        :class="sizeClass"
        :title="label"
        :style="{ '--aptitude-icon-fill': fill }"
        role="img"
        :aria-label="label"
    >
        <span class="aptitude-icon__shield" aria-hidden="true" />
        <img
            v-if="customSrc"
            :src="customSrc"
            :alt="label"
            class="aptitude-icon__img"
            loading="lazy"
            decoding="async"
        />
        <Icon
            v-else
            :source="glyph.icon"
            :alt="label"
            :size="iconSize"
            class="aptitude-icon__glyph"
        />
    </div>
</template>

<style scoped>
/* Écu / bouclier — silhouette distincte du losange (trait), hexagone (état), étoile (sort). */
.aptitude-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.aptitude-icon__shield {
    position: absolute;
    inset: 0;
    z-index: 0;
    background: var(--aptitude-icon-fill, var(--color-aptitude-700, #a21caf));
    clip-path: polygon(50% 0%, 92% 12%, 96% 55%, 50% 100%, 4% 55%, 8% 12%);
    box-shadow: inset 0 0 0 1.5px color-mix(in oklch, #1c1917 55%, transparent);
}

.aptitude-icon__img,
.aptitude-icon__glyph {
    position: relative;
    z-index: 1;
}

.aptitude-icon__img {
    max-width: 62%;
    max-height: 55%;
    object-fit: contain;
}

.aptitude-icon__glyph {
    color: color-mix(in oklch, white 92%, var(--aptitude-icon-fill));
    filter: drop-shadow(0 0 1px #1c1917);
}

.aptitude-icon--xs {
    width: 2.5rem;
    height: 2.75rem;
}

.aptitude-icon--sm {
    width: 3rem;
    height: 3.35rem;
}

.aptitude-icon--md {
    width: 4rem;
    height: 4.5rem;
}

.aptitude-icon--lg {
    width: 6rem;
    height: 6.75rem;
}
</style>
