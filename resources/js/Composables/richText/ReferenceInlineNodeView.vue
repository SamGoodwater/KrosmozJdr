<script setup>
/**
 * Rendu Vue d’une référence inline TipTap (kref).
 * Le racine doit rester {@link NodeViewWrapper} pour TipTap.
 * L’infobulle (caractéristique, entité, page) est {@link KrefInlineChip}.
 */
import { computed } from "vue";
import { NodeViewWrapper, nodeViewProps } from "@tiptap/vue-3";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";
import KrefInlineChip from "@/Pages/Molecules/data-display/KrefInlineChip.vue";

const props = defineProps(nodeViewProps);

const presentation = computed(() => getReferencePresentation(props.node.attrs));
const wrapperClass = computed(() => {
    const fromEditor = props.HTMLAttributes?.class;
    const base = presentation.value.wrapperClasses;
    if (!fromEditor) return base.join(" ");
    return [...base, fromEditor].filter(Boolean).join(" ");
});
</script>

<template>
    <NodeViewWrapper
        as="span"
        :class="wrapperClass"
        :data-kref-type="props.node.attrs.krefType"
        :data-kref-payload="props.node.attrs.krefPayload"
    >
        <KrefInlineChip
            :as-root="false"
            :kref-type="props.node.attrs.krefType"
            :kref-payload="props.node.attrs.krefPayload"
            :label="props.node.attrs.label"
        />
    </NodeViewWrapper>
</template>
