<script setup>
/**
 * Affichage HTML TipTap en lecture seule (mêmes extensions que l’éditeur, dont références kref).
 */
import { onBeforeUnmount, ref, watch } from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import { createRichTextExtensions } from "@/Composables/richText/richTextExtensions";
import RichTextKrefInteractions from "@/Pages/Molecules/data-display/RichTextKrefInteractions.vue";

const props = defineProps({
    html: {
        type: String,
        default: "",
    },
    /** Active le nœud referenceInline (doit matcher l’édition / le contenu). */
    enableRichReferences: {
        type: Boolean,
        default: false,
    },
});

const rootRef = ref(null);

const editor = useEditor({
    content: props.html || "",
    editable: false,
    extensions: createRichTextExtensions({
        placeholder: " ",
        maxCharacters: null,
        enableReferenceInline: props.enableRichReferences,
    }),
});

watch(
    () => props.html,
    (h) => {
        if (!editor.value) return;
        const next = String(h || "");
        const cur = editor.value.getHTML();
        if (next !== cur) {
            editor.value.commands.setContent(next, false);
        }
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div ref="rootRef" class="rich-text-readonly prose prose-sm max-w-none">
        <EditorContent v-if="editor" :editor="editor" />
        <RichTextKrefInteractions
            v-if="enableRichReferences"
            :root-element="rootRef"
            :enabled="enableRichReferences"
        />
    </div>
</template>

<style scoped lang="scss">
.rich-text-readonly :deep(.ProseMirror) {
    outline: none;
    min-height: 0;

    .kref {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        padding: 0.05em 0.45em;
        margin: 0 0.05em;
        border-radius: 0.35rem;
        font-size: 0.85em;
        font-weight: 600;
        vertical-align: baseline;
        background: hsl(var(--p) / 0.18);
        color: hsl(var(--p));
        border: 1px solid hsl(var(--p) / 0.35);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}
</style>
