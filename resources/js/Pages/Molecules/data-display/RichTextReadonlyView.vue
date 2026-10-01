<script setup>
/**
 * Affichage HTML TipTap en lecture seule (sans instancier l’éditeur TipTap).
 * Les spans `.kref` sont hydratés en puces (résumé, vue minimale, extrait de page).
 */
import { computed, ref } from "vue";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import { htmlContainsKrefMarkers } from "@/Utils/richText/htmlContainsKrefMarkers";
import { parseRichTextHtml } from "@/Composables/richText/parseRichTextHtml";
import RichTextKrefInteractions from "@/Pages/Molecules/data-display/RichTextKrefInteractions.vue";
import RichTextHtmlTree from "@/Pages/Molecules/data-display/RichTextHtmlTree.vue";

const props = defineProps({
    html: {
        type: String,
        default: "",
    },
    /** Active les interactions kref sur les spans `.kref`. */
    enableRichReferences: {
        type: Boolean,
        default: false,
    },
});

const rootRef = ref(null);

const sanitizedHtml = computed(() => sanitizeHtml(props.html || ""));
const useKrefTree = computed(
    () => props.enableRichReferences && htmlContainsKrefMarkers(sanitizedHtml.value),
);
const tree = computed(() => (useKrefTree.value ? parseRichTextHtml(sanitizedHtml.value) : []));
</script>

<template>
    <div class="rich-text-readonly-root">
        <div
            ref="rootRef"
            class="rich-text-readonly prose prose-sm max-w-none"
        >
            <template v-if="useKrefTree">
                <RichTextHtmlTree v-for="(node, index) in tree" :key="index" :node="node" />
            </template>
            <!-- eslint-disable-next-line vue/no-v-html -- contenu sanitizé, sans puce kref -->
            <div v-else v-html="sanitizedHtml" />
        </div>
        <RichTextKrefInteractions
            v-if="enableRichReferences"
            :root-element="rootRef"
            :enabled="enableRichReferences"
        />
    </div>
</template>
