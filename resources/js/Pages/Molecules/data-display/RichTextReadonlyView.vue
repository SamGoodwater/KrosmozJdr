<script setup>
/**
 * Affichage HTML TipTap en lecture seule (sans instancier l’éditeur TipTap).
 * Sanitize + interactions kref ; TipTap reste réservé à l’édition.
 */
import { computed, ref } from "vue";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import RichTextKrefInteractions from "@/Pages/Molecules/data-display/RichTextKrefInteractions.vue";

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
</script>

<template>
    <div class="rich-text-readonly-root">
        <div
            ref="rootRef"
            class="rich-text-readonly prose prose-sm max-w-none"
            v-html="sanitizedHtml"
        />
        <RichTextKrefInteractions
            v-if="enableRichReferences"
            :root-element="rootRef"
            :enabled="enableRichReferences"
        />
    </div>
</template>
