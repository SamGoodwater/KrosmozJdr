<script setup>
/**
 * Rendu récursif d’un arbre HTML (voir parseRichTextHtml) avec puces kref Vue.
 */
import KrefInlineChip from "@/Pages/Molecules/data-display/KrefInlineChip.vue";
import RichTextHtmlTree from "@/Pages/Molecules/data-display/RichTextHtmlTree.vue";

defineProps({
    node: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <KrefInlineChip
        v-if="node.kind === 'kref'"
        :kref-type="node.krefType"
        :kref-payload="node.krefPayload"
        :label="node.label"
    />
    <component :is="node.tag" v-else-if="node.kind === 'el'" v-bind="node.attrs">
        <RichTextHtmlTree v-for="(child, index) in node.children" :key="index" :node="child" />
    </component>
    <template v-else>{{ node.text }}</template>
</template>
