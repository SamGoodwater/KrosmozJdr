<script setup>
/**
 * EntityThumb — Vignette entité (image ou initiales, tokens site).
 *
 * @description
 * Wrapper autour de {@link Avatar} pour listes, lignes de tableau, recherche et pickers.
 * Arrondi par défaut : `rounded-box` (`--radius-box`).
 *
 * @example
 * <EntityThumb size="line" :src="imageUrl" :label="entity.name" />
 * <EntityThumb size="compact" :label="spell.name" />
 */
import { computed } from "vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";
import { mergeClasses } from "@/Utils/atomic-design/uiHelper";
import {
    normalizeEntityThumbLabel,
    resolveEntityImageUrl,
} from "@/Utils/entity/entityThumb";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    src: { type: String, default: "" },
    source: { type: String, default: "" },
    label: { type: String, default: "" },
    alt: { type: String, default: "" },
    size: {
        type: String,
        default: "line",
        validator: (v) => ["search", "table", "xs", "compact", "line"].includes(v),
    },
    rounded: {
        type: String,
        default: "box",
    },
    fit: {
        type: String,
        default: "contain",
        validator: (v) => ["cover", "contain"].includes(v),
    },
    class: { type: String, default: "" },
});

const imageSrc = computed(() => {
    const direct = String(props.src || props.source || "").trim();

    return direct ? resolveEntityImageUrl(direct) : "";
});

const displayLabel = computed(() =>
    normalizeEntityThumbLabel(props.label || props.alt),
);

const displayAlt = computed(() =>
    String(props.alt || displayLabel.value || "Entité").trim(),
);

const wrapperClass = computed(() =>
    mergeClasses([
        "entity-thumb",
        "shrink-0",
        "overflow-hidden",
        "rounded-box",
        "bg-base-200",
        "flex",
        "items-center",
        "justify-center",
        props.size === "search" && "entity-thumb--search",
        props.size === "table" && "entity-thumb--table",
        props.size === "xs" && "entity-thumb--xs",
        props.size === "compact" && "entity-thumb--compact",
        props.size === "line" && "entity-thumb--line",
        props.class,
    ]),
);
</script>

<template>
    <div :class="wrapperClass">
        <Avatar
            size="fill"
            :src="imageSrc"
            :label="displayLabel"
            :alt="displayAlt"
            :rounded="rounded"
            :fit="fit"
            class="entity-thumb__avatar"
        />
    </div>
</template>

<style scoped>
.entity-thumb--search {
    width: 2.25rem;
    height: 2.25rem;
    font-size: 0.7rem;
}

.entity-thumb--table {
    width: 2rem;
    height: 2rem;
    font-size: 0.65rem;
}

.entity-thumb--xs {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 0.7rem;
}

.entity-thumb--compact {
    width: 3.5rem;
    height: 3.5rem;
    font-size: 0.75rem;
}

.entity-thumb--line {
    width: 5rem;
    height: 100%;
    min-height: 5rem;
    align-self: stretch;
    font-size: 0.85rem;
}

.entity-thumb {
    position: relative;
}

.entity-thumb__avatar {
    display: block;
    width: 100%;
    height: 100%;
    min-height: 100%;
}

.entity-thumb__avatar :deep(.avatar-initials) {
    font-size: inherit;
}
</style>
