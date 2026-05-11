<script setup>
import { computed } from "vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";

const props = defineProps({
    sections: {
        type: Array,
        default: () => [],
    },
    emptyMessage: {
        type: String,
        default: "Aucune section disponible.",
    },
    preserveSectionPermissions: {
        type: Boolean,
        default: false,
    },
});

const toNumericOrder = (value, fallback = 0) => {
    const n = Number(value);
    return Number.isFinite(n) ? n : fallback;
};

const normalizedSections = computed(() => {
    const rawSections = Array.isArray(props.sections) ? props.sections : [];

    return rawSections
        .filter((section) => section && typeof section === "object")
        .map((section, index) => {
            const fallbackOrder = index + 1;
            const order =
                section.order ??
                section.pivot_level ??
                section?.pivot?.level ??
                section.level ??
                fallbackOrder;

            const can = props.preserveSectionPermissions
                ? section.can
                : { ...(section.can || {}), update: false, delete: false };

            return {
                ...section,
                can,
                order: toNumericOrder(order, fallbackOrder),
            };
        })
        .sort((a, b) => {
            const orderDiff = toNumericOrder(a.order) - toNumericOrder(b.order);
            if (orderDiff !== 0) return orderDiff;
            return String(a.title || "").localeCompare(String(b.title || ""), "fr");
        });
});
</script>

<template>
    <div class="entity-sections-renderer space-y-4">
        <div v-if="normalizedSections.length > 0" class="sections">
            <SectionRenderer
                v-for="section in normalizedSections"
                :key="section.id"
                :section="section"
            />
        </div>

        <div v-else class="rounded-box border border-base-300/60 bg-base-100/35 p-4 text-sm text-base-content/70">
            {{ emptyMessage }}
        </div>
    </div>
</template>

<style scoped lang="scss">
.sections {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    --section-content-width: clamp(48rem, 84vw, 76rem);

    > * {
        width: min(100%, var(--section-content-width));
        margin-bottom: 1.5rem;

        &:last-child {
            margin-bottom: 0;
        }
    }
}
</style>
