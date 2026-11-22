<script setup>
/**
 * Marque de trait : logo humain si un PJ ou un PNJ peut le prendre, et le niveau de puissance.
 *
 * @example
 * <CreatureTraitMarks :trait="creatureTrait" mode="minimal" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";

const props = defineProps({
    trait: { type: Object, required: true },
    /**
     * minimal : logo humain seulement s’il est disponible, plus le niveau.
     * full : niveau et phrase, y compris « Créatures seulement ».
     */
    mode: {
        type: String,
        default: "minimal",
        validator: (v) => ["minimal", "full"].includes(v),
    },
});

const source = computed(() => props.trait?._data ?? props.trait ?? {});

const available = computed(() => {
    const v = source.value.available_for_characters
        ?? props.trait?.availableForCharacters
        ?? props.trait?.available_for_characters;
    return v === true || v === 1 || v === "1";
});

const level = computed(() => {
    const v = source.value.level ?? props.trait?.level;
    if (v === null || v === undefined || v === "") return null;
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
});
</script>

<template>
    <span v-if="mode === 'minimal'" class="inline-flex items-center gap-1">
        <LevelBadge v-if="level !== null" :level="level" size="xs" class="shrink-0" hide-tooltip />
        <Icon
            v-if="available"
            source="fa-solid fa-user"
            size="xs"
            class="text-primary-200"
            alt="Utilisable par un PJ ou un PNJ"
        />
    </span>
    <span v-else class="inline-flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-primary-200">
        <span v-if="available" class="inline-flex items-center gap-1.5">
            <Icon source="fa-solid fa-user" size="xs" alt="" />
            PJ et PNJ
        </span>
        <span v-else>Créatures seulement</span>
    </span>
</template>
