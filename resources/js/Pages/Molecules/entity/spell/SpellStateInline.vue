<script setup>
/**
 * État de sort — libellé + icône optionnelle (définitions d’effets).
 *
 * @props {{ id: number, name: string, icon?: string|null }} spellState
 * @props {string} [nameFallback] - Si pas d’objet état complet (ex. chip API)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    spellState: {
        type: Object,
        default: null,
    },
    nameFallback: {
        type: String,
        default: "",
    },
});

const displayName = computed(() => {
    const st = props.spellState;
    if (st && typeof st === "object" && st.name != null && String(st.name).trim() !== "") {
        return String(st.name).trim();
    }
    const f = props.nameFallback != null ? String(props.nameFallback).trim() : "";
    return f !== "" ? f : "État";
});

const icon = computed(() => {
    const st = props.spellState;
    if (st && typeof st === "object" && st.icon != null && String(st.icon).trim() !== "") {
        return String(st.icon).trim();
    }
    return null;
});

const tooltip = computed(() => {
    const st = props.spellState;
    if (st?.id != null) {
        return `${displayName.value} (#${st.id})`;
    }
    return displayName.value;
});
</script>

<template>
    <Tooltip :content="tooltip" placement="top" class="inline-flex max-w-full min-w-0">
        <span class="inline-flex min-w-0 max-w-full items-center gap-1 font-medium text-base-content">
            <Icon v-if="icon" :source="icon" :alt="displayName" size="xs" class="shrink-0 opacity-90" />
            <span class="min-w-0 truncate">{{ displayName }}</span>
        </span>
    </Tooltip>
</template>
