<script setup>
/**
 * ConditionMechanicalFlags — chips des effets mécaniques actifs d’un état.
 *
 * @description
 * Affiche uniquement les flags à true (immobilisation, invulnérabilité, etc.).
 *
 * @example
 * <ConditionMechanicalFlags :condition="condition" />
 */
import { computed } from "vue";
import { listActiveMechanicalFlags } from "@/Composables/condition/conditionDisplay";

const props = defineProps({
    condition: { type: Object, default: null },
    /** compact = badges xs, default = badges sm */
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm"].includes(v),
    },
    showEmpty: { type: Boolean, default: false },
});

const flags = computed(() => listActiveMechanicalFlags(props.condition));
</script>

<template>
    <div v-if="flags.length" class="flex flex-wrap gap-1">
        <span
            v-for="flag in flags"
            :key="flag.key"
            class="badge badge-outline border-warning/50 bg-warning/10 text-warning-content"
            :class="size === 'xs' ? 'badge-xs' : 'badge-sm'"
        >
            {{ flag.label }}
        </span>
    </div>
    <p v-else-if="showEmpty" class="text-xs italic text-base-content/50">
        Aucun effet mécanique
    </p>
</template>
