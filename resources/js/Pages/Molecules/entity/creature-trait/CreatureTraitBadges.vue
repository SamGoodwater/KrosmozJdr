<script setup>
const props = defineProps({
    traits: { type: Array, default: () => [] },
    showLevel: { type: Boolean, default: false },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const traitName = (trait) => trait?.name || trait?._data?.name || `#${trait?.id ?? "?"}`;
const traitLevel = (trait) => trait?.pivot?.level ?? trait?.level ?? null;
</script>

<template>
    <div v-if="traits.length" class="flex flex-wrap gap-1.5">
        <span
            v-for="trait in traits"
            :key="trait.id"
            class="badge badge-outline border-secondary/50 bg-secondary/10 text-secondary-content"
            :class="{
                'badge-xs': size === 'xs',
                'badge-sm': size === 'sm',
                'badge-md': size === 'md',
            }"
            :title="trait.description || traitName(trait)"
        >
            {{ traitName(trait) }}
            <span v-if="showLevel && traitLevel(trait)" class="ml-1 opacity-75">nv. {{ traitLevel(trait) }}</span>
        </span>
    </div>
</template>
