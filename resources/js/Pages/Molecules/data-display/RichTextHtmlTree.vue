<script setup>
/**
 * Rendu récursif d’un arbre HTML (voir parseRichTextHtml) avec puces kref Vue
 * et pastilles de style de spécialisation (`.spe-style`).
 */
import { computed } from "vue";
import KrefInlineChip from "@/Pages/Molecules/data-display/KrefInlineChip.vue";
import SpecializationStyleChip from "@/Pages/Molecules/entity/specialization/SpecializationStyleChip.vue";
import RichTextHtmlTree from "@/Pages/Molecules/data-display/RichTextHtmlTree.vue";

const props = defineProps({
    node: {
        type: Object,
        required: true,
    },
    specializationSlug: {
        type: String,
        default: "artiste",
    },
});

const speStyleKey = computed(() => {
    if (props.node?.kind !== "el" || props.node?.tag !== "span") return "";
    const attrs = props.node.attrs || {};
    const className = String(attrs.class || "");
    if (!/\bspe-style\b/.test(className)) return "";
    return String(attrs["data-spe-style"] || "").trim();
});

const speStyleLabel = computed(() => {
    if (!speStyleKey.value) return "";
    const children = Array.isArray(props.node.children) ? props.node.children : [];
    return children
        .map((c) => (c?.kind === "text" ? String(c.text || "") : ""))
        .join("")
        .trim();
});
</script>

<template>
    <KrefInlineChip
        v-if="node.kind === 'kref'"
        :kref-type="node.krefType"
        :kref-payload="node.krefPayload"
        :label="node.label"
    />
    <SpecializationStyleChip
        v-else-if="speStyleKey"
        :style-key="speStyleKey"
        :label="speStyleLabel"
        :specialization-slug="specializationSlug"
        appearance="marker"
        size="md"
    />
    <component :is="node.tag" v-else-if="node.kind === 'el'" v-bind="node.attrs">
        <RichTextHtmlTree
            v-for="(child, index) in node.children"
            :key="index"
            :node="child"
            :specialization-slug="specializationSlug"
        />
    </component>
    <template v-else>{{ node.text }}</template>
</template>
