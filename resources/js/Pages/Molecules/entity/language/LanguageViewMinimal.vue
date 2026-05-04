<script setup>
/**
 * Carte compacte pour une langue (nom + description), teinte selon la couleur référentielle.
 */
import { computed } from "vue";

const props = defineProps({
    language: {
        type: Object,
        required: true,
    },
});

const hex = computed(() => {
    const c = props.language?.color;
    if (!c || typeof c !== "string") return "#64748b";
    const s = c.trim();
    return s.startsWith("#") ? s : `#${s}`;
});

const desc = computed(() => {
    const d = props.language?.description;
    return d && String(d).trim() ? String(d).trim() : "";
});
</script>

<template>
    <div
        class="rounded-lg border px-2 py-1.5 text-xs shadow-sm"
        :style="{
            borderColor: hex,
            background: `linear-gradient(135deg, ${hex}28, transparent)`,
        }"
    >
        <div class="font-semibold leading-tight" :style="{ color: hex }">
            {{ language.name }}
        </div>
        <p v-if="desc" class="mt-1 text-[11px] leading-snug text-base-content/80">
            {{ desc }}
        </p>
    </div>
</template>
