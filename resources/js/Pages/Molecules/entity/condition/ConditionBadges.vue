<script setup>
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    formatConditionDispellable,
    getConditionDispellableIcon,
    resolveEntityDissipable,
} from "@/Composables/condition/conditionDisplay";

defineProps({
    conditions: { type: Array, default: () => [] },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const conditionName = (condition) => condition?.name || condition?._data?.name || `#${condition?.id ?? "?"}`;

const dissipableIconSrc = (condition) => {
    const d = resolveEntityDissipable(condition?.dissipable ?? condition?._data?.dissipable);
    return getConditionDispellableIcon(d);
};

const dissipableAlt = (condition) => {
    const d = resolveEntityDissipable(condition?.dissipable ?? condition?._data?.dissipable);
    return formatConditionDispellable(d) || "";
};

const badgeTitle = (condition) => {
    const parts = [dissipableAlt(condition), condition?.description || conditionName(condition)].filter(Boolean);
    return parts.join(" · ");
};
</script>

<template>
    <div v-if="conditions.length" class="flex flex-wrap gap-1.5">
        <span
            v-for="condition in conditions"
            :key="condition.id"
            class="badge badge-outline border-info/50 bg-info/10 text-info-content inline-flex items-center gap-1 max-w-full"
            :class="{
                'badge-xs': size === 'xs',
                'badge-sm': size === 'sm',
                'badge-md': size === 'md',
            }"
            :title="badgeTitle(condition)"
        >
            <Image
                v-if="dissipableIconSrc(condition)"
                :source="dissipableIconSrc(condition)"
                :alt="dissipableAlt(condition)"
                fit="contain"
                width="1.1rem"
                height="1.1rem"
                class="inline-flex shrink-0 max-h-4 max-w-4 opacity-95"
            />
            <span class="min-w-0 truncate">{{ conditionName(condition) }}</span>
        </span>
    </div>
</template>
