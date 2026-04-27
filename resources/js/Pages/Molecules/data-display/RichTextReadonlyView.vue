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

