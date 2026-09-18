<script setup>
/**
 * DiceFormulaStrip Molecule
 *
 * @description
 * Fine bande sous un champ formule : min / moy / max, équivalent en dés des
 * tranches, bouton icône pour simuler un lancer. Le résultat s’affiche à droite
 * du bouton. À n’afficher que si la saisie est une formule reconnue.
 *
 * @props {Number|null} min - Borne minimale
 * @props {Number|null} max - Borne maximale
 * @props {Number|null} average - Espérance
 * @props {Array} rangeEquivalents - Libellés « [2-6] = 1d5+1 »
 * @props {Number|String|null} rollResult - Dernier lancer, ou null
 * @props {String} density - compact (recherche) ou comfortable (outil)
 * @emits roll - Demande une simulation
 *
 * @example
 * <DiceFormulaStrip :min="2" :max="6" :average="4" :range-equivalents="['[2-6] = 1d5+1']" @roll="simulate" />
 */
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
    min: { type: Number, default: null },
    max: { type: Number, default: null },
    average: { type: Number, default: null },
    rangeEquivalents: { type: Array, default: () => [] },
    rollResult: { type: [Number, String], default: null },
    density: {
        type: String,
        default: 'compact',
        validator: (value) => ['compact', 'comfortable'].includes(value),
    },
});

defineEmits(['roll']);

function formatStat(value) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '—';
    }
    const numeric = Number(value);
    if (Number.isInteger(numeric)) {
        return String(numeric);
    }
    const rounded = Math.round(numeric * 1000) / 1000;
    return String(rounded);
}
</script>

<template>
    <div
        class="dice-formula-strip flex min-w-0 items-center gap-2 overflow-x-auto rounded-box border border-base-300/70 bg-base-100/80 text-base-content/80"
        :class="density === 'compact' ? 'h-7 px-2 text-[11px]' : 'min-h-9 px-3 py-1.5 text-sm'"
        role="status"
        aria-live="polite"
    >
        <span class="flex shrink-0 items-center gap-1.5 tabular-nums">
            <span>
                <span class="opacity-60">Min</span>
                {{ formatStat(min) }}
            </span>
            <span class="opacity-30" aria-hidden="true">·</span>
            <span>
                <span class="opacity-60">Moy</span>
                {{ formatStat(average) }}
            </span>
            <span class="opacity-30" aria-hidden="true">·</span>
            <span>
                <span class="opacity-60">Max</span>
                {{ formatStat(max) }}
            </span>
        </span>

        <span
            v-if="rangeEquivalents.length"
            class="min-w-0 truncate opacity-80"
            :title="rangeEquivalents.join(' · ')"
        >
            <span class="opacity-60">{{ density === 'compact' ? 'Dés' : 'Équivalent' }}</span>
            {{ rangeEquivalents.join(' · ') }}
        </span>

        <span class="ml-auto flex shrink-0 items-center gap-1.5">
            <Btn
                type="button"
                circle
                variant="ghost"
                color="primary"
                :size="density === 'compact' ? 'xs' : 'sm'"
                aria-label="Simuler un lancer"
                title="Simuler un lancer"
                @mousedown.prevent
                @click="$emit('roll')"
            >
                <Icon source="fa-dice" pack="solid" alt="" :size="density === 'compact' ? 'xs' : 'sm'" />
            </Btn>
            <span
                v-if="rollResult !== null && rollResult !== undefined && rollResult !== ''"
                class="font-semibold tabular-nums text-primary"
            >
                {{ formatStat(rollResult) }}
            </span>
        </span>
    </div>
</template>
