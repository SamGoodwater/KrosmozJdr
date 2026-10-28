<script setup>
/**
 * Onglets d’édition des degrés : pastilles niveau + bouton « + Degré » + source des propriétés.
 *
 * @description
 * API dédiée à {@link SpellDegreesEditor} (distincte de {@link SpellDegreeTabs} lecture).
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import {
    spellDegreeLevelNumber,
    spellDegreeTabLabel,
} from "@/Composables/entity/useSpellDisplayDegrees";

const props = defineProps({
    degrees: { type: Array, default: () => [] },
    activeIndex: { type: Number, default: 0 },
    /** ids des degrés dirty (`Set` ou objet Set-like). */
    dirtyIds: { type: Object, default: () => new Set() },
    propertiesSource: { type: String, default: "own" },
    saving: { type: Boolean, default: false },
    showPreviousSource: { type: Boolean, default: true },
});

const emit = defineEmits(["select", "add", "update:propertiesSource"]);

const list = computed(() => (Array.isArray(props.degrees) ? props.degrees : []));

const sources = [
    { value: "own", label: "Propres" },
    { value: "previous", label: "Degré précédent" },
    { value: "spell", label: "Sort de base" },
];

/**
 * @param {object} deg
 * @returns {boolean}
 */
function isDirty(deg) {
    const ids = props.dirtyIds;
    if (!ids || typeof ids.has !== "function") return false;
    return ids.has(Number(deg?.id)) || ids.has(deg?.id);
}

/**
 * @param {object} deg
 * @param {number} idx
 * @returns {string}
 */
function levelAutoLabel(deg, idx) {
    return String(spellDegreeLevelNumber(deg, idx));
}
</script>

<template>
    <div class="space-y-2" data-cy="spell-degree-tabs">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div role="tablist" class="flex flex-wrap items-center gap-1">
                <button
                    v-for="(deg, idx) in list"
                    :key="deg.id ?? idx"
                    type="button"
                    role="tab"
                    class="inline-flex items-center gap-1"
                    :aria-selected="activeIndex === idx"
                    @click="emit('select', idx)"
                >
                    <Badge
                        color="auto"
                        :auto-label="levelAutoLabel(deg, idx)"
                        auto-scheme="level"
                        :variant="activeIndex === idx ? '' : 'outline'"
                        size="sm"
                        strong
                        class="pointer-events-none"
                    >
                        {{ spellDegreeTabLabel(deg, idx) }}
                    </Badge>
                    <span
                        v-if="isDirty(deg)"
                        class="inline-block h-1.5 w-1.5 rounded-full bg-warning"
                        title="Modifié"
                    />
                </button>
            </div>
            <button
                type="button"
                class="btn btn-sm btn-primary"
                :disabled="saving"
                data-cy="spell-degree-add"
                @click="emit('add')"
            >
                + Degré
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="shrink-0 text-xs text-base-content/70">Propriétés :</span>
            <div class="join">
                <button
                    v-for="src in sources"
                    :key="src.value"
                    type="button"
                    class="btn btn-xs join-item"
                    :class="
                        propertiesSource === src.value
                            ? 'btn-neutral'
                            : 'btn-ghost border border-base-300'
                    "
                    :disabled="saving || (src.value === 'previous' && !showPreviousSource)"
                    @click="emit('update:propertiesSource', src.value)"
                >
                    {{ src.label }}
                </button>
            </div>
        </div>
    </div>
</template>
