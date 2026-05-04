<script setup>
/**
 * Ligne « Langues : » + chips, ou variante compacte (chips seuls).
 */
import { computed } from "vue";
import LanguageChip from "@/Pages/Molecules/entity/language/LanguageChip.vue";

const props = defineProps({
    languages: {
        type: Array,
        default: () => [],
    },
    /** text : préfixe « Langues : » ; minimal : pastilles seules */
    variant: {
        type: String,
        default: "text",
        validator: (v) => ["text", "minimal"].includes(v),
    },
    /** Si false, n’affiche pas le préfixe (utile quand un titre de section existe déjà). */
    showLabel: {
        type: Boolean,
        default: true,
    },
});

const sorted = computed(() => {
    const list = [...(props.languages || [])];
    list.sort((a, b) => {
        const sa = Number(a.sort_order ?? a.sortOrder ?? 0);
        const sb = Number(b.sort_order ?? b.sortOrder ?? 0);
        if (sa !== sb) return sa - sb;
        return String(a.name || "").localeCompare(String(b.name || ""));
    });
    return list;
});
</script>

<template>
    <div v-if="sorted.length" class="flex flex-wrap items-center gap-x-1.5 gap-y-1">
        <template v-if="variant === 'text' && showLabel">
            <span class="text-xs font-medium text-base-content/75 shrink-0">Langues :</span>
        </template>
        <LanguageChip v-for="lang in sorted" :key="lang.id" :language="lang" />
    </div>
</template>
