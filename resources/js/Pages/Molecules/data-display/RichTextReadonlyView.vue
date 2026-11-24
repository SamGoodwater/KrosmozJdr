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
    /** Slug de la spécialisation, pour l’icône de chaque style. */
    specializationSlug: {
        type: String,
        default: "artiste",
    },
});

const rootRef = ref(null);

const sanitizedHtml = computed(() => sanitizeHtml(props.html || ""));
const useKrefTree = computed(() => {
    if (!props.enableRichReferences) return false;
    const html = sanitizedHtml.value;
    return (
        htmlContainsKrefMarkers(html) ||
        /\bspe-style\b/.test(html) ||
        /\bdata-spe-style=/.test(html)
    );
});
const tree = computed(() => (useKrefTree.value ? parseRichTextHtml(sanitizedHtml.value) : []));
</script>

<template>
    <div class="rich-text-readonly-root">
        <div
            ref="rootRef"
            class="rich-text-readonly prose prose-sm max-w-none"
        >
            <template v-if="useKrefTree">
                <RichTextHtmlTree
                    v-for="(node, index) in tree"
                    :key="index"
                    :node="node"
                    :specialization-slug="specializationSlug"
                />
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
