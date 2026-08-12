<script setup>
/**
 * Tooltip caractéristique — présentation « fiche de jeu » (peu technique).
 *
 * Affiche : icône, nom, valeur, description, formule lisible, résolution,
 * et la liste des termes (libellés joueur, pas les clés BDD).
 *
 * @props {Object} model — viewModel (useCharacteristicViewModel / formula group)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    humanizeCharacteristicFormulaText,
    mapPlaceholdersForPlayer,
    normalizeCharacteristicIcon,
} from "@/Utils/Entity/characteristicTooltipLabels";

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
});

const iconSource = computed(() =>
    normalizeCharacteristicIcon(props.model?.icon || props.model?._resolvedIcon || ""),
);

const friendlyFormula = computed(() => {
    const raw = props.model?.formulaDisplay || "";
    return humanizeCharacteristicFormulaText(raw);
});

const friendlyResolution = computed(() => {
    const raw = props.model?.substituted || "";
    return humanizeCharacteristicFormulaText(raw);
});

const termRows = computed(() => mapPlaceholdersForPlayer(props.model?.placeholders));

const hasCalculation = computed(
    () => Boolean(friendlyFormula.value || friendlyResolution.value || termRows.value.length),
);

const levelRows = computed(() => {
    const table = props.model?.levelTable;
    if (!Array.isArray(table) || table.length <= 1) return [];
    return table;
});
</script>

<template>
    <div class="characteristic-property-tooltip max-w-xs space-y-2.5 text-left text-sm text-white/95">
        <div class="flex items-start gap-2.5">
            <Icon
                v-if="iconSource"
                :source="iconSource"
                :alt="model.name || ''"
                size="md"
                class="mt-0.5 shrink-0 opacity-95"
            />
            <div class="min-w-0 space-y-0.5">
                <div class="font-semibold leading-snug tracking-wide">{{ model.name }}</div>
                <div
                    v-if="!model.hideDisplayValueInTooltip"
                    class="text-xl font-semibold tabular-nums text-white"
                >
                    {{ model.displayValue ?? "—" }}
                </div>
            </div>
        </div>

        <p v-if="model.helper" class="text-xs leading-relaxed text-white/80">
            {{ model.helper }}
        </p>
        <p
            v-if="model.descriptions && model.descriptions !== model.helper"
            class="text-xs leading-relaxed italic text-white/65"
        >
            {{ model.descriptions }}
        </p>

        <div
            v-if="hasCalculation"
            class="space-y-2 rounded-md border border-white/15 bg-white/5 px-2.5 py-2"
        >
            <p class="text-[10px] font-semibold uppercase tracking-wider text-white/55">
                Calcul
            </p>

            <div v-if="friendlyFormula" class="text-xs leading-snug text-white/85">
                <span class="text-white/55">Formule · </span>
                {{ friendlyFormula }}
            </div>

            <div v-if="friendlyResolution" class="text-xs leading-snug text-white/85">
                <span class="text-white/55">Avec tes valeurs · </span>
                <span class="tabular-nums">{{ friendlyResolution }}</span>
            </div>

            <ul v-if="termRows.length" class="space-y-1 pt-0.5">
                <li
                    v-for="row in termRows"
                    :key="row.id"
                    class="flex items-baseline justify-between gap-3 border-t border-dashed border-white/15 pt-1 text-xs first:border-t-0 first:pt-0"
                >
                    <span class="min-w-0 text-white/75">{{ row.label }}</span>
                    <span class="shrink-0 font-medium tabular-nums text-white">{{ row.value }}</span>
                </li>
            </ul>
        </div>

        <div v-if="levelRows.length" class="space-y-1">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-white/55">
                Selon le niveau
            </p>
            <div class="flex flex-wrap gap-1">
                <span
                    v-for="row in levelRows"
                    :key="row.level"
                    class="inline-flex items-center gap-1 rounded bg-white/10 px-1.5 py-0.5 text-[11px] tabular-nums text-white/85"
                >
                    <span class="text-white/50">Niv.{{ row.level }}</span>
                    <span class="font-medium">{{ row.value ?? row.total }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
