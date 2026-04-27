<script setup>
import { computed } from "vue";

const props = defineProps({
    l1Title: {
        type: String,
        default: "",
    },
    pageTitle: {
        type: String,
        default: "",
    },
    sections: {
        type: Array,
        default: () => [],
    },
    showHeading: {
        type: Boolean,
        default: true,
    },
});

const normalizedSections = computed(() =>
    (Array.isArray(props.sections) ? props.sections : []).map((section) => {
        const title = String(section?.title || "").trim() || "Section";
        const hash = String(section?.hash || "").trim();
        const l4Headings = Array.isArray(section?.l4Headings) ? section.l4Headings : [];
        return { title, hash, l4Headings };
    }),
);
</script>

<template>
    <section class="rules-page-plan rounded-box border border-base-300/40 bg-base-100/40 p-4 md:p-5">
        <p v-if="showHeading" class="mb-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-base-content/60">
            Plan de la page
        </p>
        <p class="mb-3 text-sm text-base-content/70 truncate" :class="{ 'mt-0': !showHeading }">
            <span v-if="l1Title">{{ l1Title }} / </span>{{ pageTitle || "Page" }}
        </p>

        <ol class="space-y-2">
            <li v-for="(section, index) in normalizedSections" :key="`${section.hash}-${index}`" class="text-sm">
                <a
                    :href="section.hash || undefined"
                    class="inline-flex max-w-full items-center gap-2 text-base-content/90 hover:text-primary hover:underline"
                >
                    <span class="text-base-content/45">{{ index + 1 }}.</span>
                    <span class="truncate">{{ section.title }}</span>
                </a>
                <ul v-if="section.l4Headings.length" class="ml-6 mt-1.5 space-y-1">
                    <li
                        v-for="heading in section.l4Headings"
                        :key="heading.id"
                        class="text-xs text-base-content/65"
                    >
                        <a
                            :href="heading.hash || undefined"
                            class="inline-flex max-w-full items-center gap-2 hover:text-primary hover:underline"
                        >
                            <span class="opacity-60">-</span>
                            <span class="truncate">{{ heading.text }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ol>
    </section>
</template>
