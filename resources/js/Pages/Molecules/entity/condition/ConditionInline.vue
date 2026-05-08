<script setup>
/**
 * Condition — libellé + icônes (définitions d’effets / états).
 *
 * @props {{ id: number, name: string, icon?: string|null, dissipable?: boolean|null }} condition
 * @props {string} [nameFallback] - Si pas d’objet condition complète (ex. chip API)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    formatConditionDispellable,
    getConditionDispellableIcon,
    resolveEntityDissipable,
} from "@/Composables/condition/conditionDisplay";

const props = defineProps({
    condition: {
        type: Object,
        default: null,
    },
    nameFallback: {
        type: String,
        default: "",
    },
});

const displayName = computed(() => {
    const st = props.condition;
    if (st && typeof st === "object" && st.name != null && String(st.name).trim() !== "") {
        return String(st.name).trim();
    }
    const f = props.nameFallback != null ? String(props.nameFallback).trim() : "";
    return f !== "" ? f : "État";
});

const entityIcon = computed(() => {
    const st = props.condition;
    if (st && typeof st === "object" && st.icon != null && String(st.icon).trim() !== "") {
        return String(st.icon).trim();
    }
    return null;
});

const dissipableIcon = computed(() => {
    const st = props.condition;
    if (!st || typeof st !== "object") return null;
    const d = resolveEntityDissipable(st.dissipable ?? st._data?.dissipable);
    return getConditionDispellableIcon(d);
});

const dissipableAlt = computed(() => {
    const st = props.condition;
    if (!st || typeof st !== "object") return "";
    const d = resolveEntityDissipable(st.dissipable ?? st._data?.dissipable);
    return formatConditionDispellable(d) || "";
});

const tooltip = computed(() => {
    const st = props.condition;
    const parts = [dissipableAlt.value, st?.id != null ? `${displayName.value} (#${st.id})` : displayName.value].filter(Boolean);
    return parts.join(" · ");
});
</script>

<template>
    <Tooltip :content="tooltip" placement="top" class="inline-flex max-w-full min-w-0">
        <span class="inline-flex min-w-0 max-w-full items-center gap-1 font-medium text-base-content">
            <Image
                v-if="dissipableIcon"
                :source="dissipableIcon"
                :alt="dissipableAlt"
                fit="contain"
                width="1rem"
                height="1rem"
                class="inline-flex shrink-0 max-h-4 max-w-4 opacity-95"
            />
            <Icon v-if="entityIcon" :source="entityIcon" :alt="displayName" size="xs" class="shrink-0 opacity-90" />
            <span class="min-w-0 truncate">{{ displayName }}</span>
        </span>
    </Tooltip>
</template>
