<script setup>
const props = defineProps({
    conditions: { type: Array, default: () => [] },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const conditionName = (condition) => condition?.name || condition?._data?.name || `#${condition?.id ?? "?"}`;
</script>

<template>
    <div v-if="conditions.length" class="flex flex-wrap gap-1.5">
        <span
            v-for="condition in conditions"
            :key="condition.id"
            class="badge badge-outline border-info/50 bg-info/10 text-info-content"
            :class="{
                'badge-xs': size === 'xs',
                'badge-sm': size === 'sm',
                'badge-md': size === 'md',
            }"
            :title="condition.description || conditionName(condition)"
        >
            {{ conditionName(condition) }}
        </span>
    </div>
</template>
