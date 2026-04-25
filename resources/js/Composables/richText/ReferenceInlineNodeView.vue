<script setup>
import { computed } from "vue";
import { NodeViewWrapper, nodeViewProps } from "@tiptap/vue-3";
import { encodeKrefTitle } from "@/Composables/richText/krefCodec";
import { getReferencePresentation } from "@/Composables/richText/referenceRenderService";

const props = defineProps(nodeViewProps);

const presentation = computed(() => getReferencePresentation(props.node.attrs));

const titleAttr = computed(() =>
    encodeKrefTitle({
        krefType: props.node.attrs.krefType,
        krefPayload: props.node.attrs.krefPayload,
        label: props.node.attrs.label,
    }),
);

const wrapperClass = computed(() => {
    const fromEditor = props.HTMLAttributes?.class;
    const base = presentation.value.wrapperClasses;
    if (!fromEditor) return base.join(" ");
    return [...base, fromEditor].filter(Boolean).join(" ");
});
</script>

<template>
    <NodeViewWrapper as="span" :class="wrapperClass" :title="titleAttr">
        <i :class="[presentation.iconClass, 'kref__icon']" aria-hidden="true" />
        <span class="kref__label">{{ presentation.displayLabel }}</span>
    </NodeViewWrapper>
</template>

<style scoped lang="scss">
.kref__icon {
    margin-inline-end: 0.28em;
    font-size: 0.92em;
    opacity: 0.92;
}

.kref__label {
    min-width: 0;
}
</style>
