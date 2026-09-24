<script setup>
/**
 * DiceFormulaStrip Molecule
 *
 * @description
 * Bande sous un champ formule : min / moy / max, équivalent en dés des
 * tranches, « Valeur : … » et bouton icône (tooltip « Lancer ») pour simuler
 * un jet. L’historique session s’affiche en dessous, une opération après
 * l’autre (`formule = valeur`). À n’afficher que si la saisie est reconnue.
 *
 * @props {Number|null} min - Borne minimale
 * @props {Number|null} max - Borne maximale
 * @props {Number|null} average - Espérance
 * @props {Array} rangeEquivalents - Libellés « [2-6] = 1d5+1 »
 * @props {Number|String|null} rollResult - Dernier lancer, ou null
 * @props {Array} history - Jets de la session `{ formula, value }` (mémoire seule)
 * @props {String} density - compact (recherche) ou comfortable (outil)
 * @emits roll - Demande une simulation
 *
 * @example
 * <DiceFormulaStrip
 *   :min="2" :max="6" :average="4"
 *   :range-equivalents="['[2-6] = 1d5+1']"
 *   :history="[{ formula: '[2-6]', value: 4 }]"
 *   @roll="simulate"
 * />
 */
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

defineProps({
    min: { type: Number, default: null },
    max: { type: Number, default: null },
    average: { type: Number, default: null },
    rangeEquivalents: { type: Array, default: () => [] },
    rollResult: { type: [Number, String], default: null },
    history: { type: Array, default: () => [] },
    density: {
        type: String,
        default: 'compact',
        validator: (value) => ['compact', 'comfortable'].includes(value),
    },
});

defineEmits(['roll']);

/**
 * @param {unknown} value
 * @returns {string}
 */
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

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function hasRollResult(value) {
    return value !== null && value !== undefined && value !== '';
}
</script>

<template>
    <div
        class="dice-formula-strip flex min-w-0 flex-col rounded-box border border-base-300/70 bg-base-100/80 text-base-content"
        :class="density === 'compact' ? 'gap-1 px-2.5 py-1.5 text-sm' : 'gap-1.5 px-3 py-2.5 text-base'"
        role="status"
        aria-live="polite"
    >
        <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1">
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

            <span class="ml-auto flex shrink-0 items-center gap-2 bg-transparent">
                <span class="flex items-baseline gap-1.5 tabular-nums leading-none">
                    <span
                        class="font-medium text-base-content/80"
                        :class="density === 'compact' ? 'text-sm' : 'text-base'"
                    >
                        Valeur :
                    </span>
                    <span
                        class="font-bold text-primary"
                        :class="density === 'compact' ? 'text-2xl' : 'text-3xl'"
                    >
                        {{ hasRollResult(rollResult) ? formatStat(rollResult) : '—' }}
                    </span>
                </span>
                <Tooltip content="Lancer" placement="top" opaque>
                    <Btn
                        type="button"
                        variant="ghost"
                        color="primary"
                        animation="none"
                        aria-label="Lancer"
                        @mousedown.prevent
                        @click="$emit('roll')"
                    >
                        <Icon
                            source="fa-dice"
                            pack="solid"
                            alt=""
                            :size="density === 'compact' ? 'md' : 'lg'"
                        />
                    </Btn>
                </Tooltip>
            </span>
        </div>

        <ol
            v-if="history.length"
            class="flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-0.5 border-t border-base-300/50 pt-1 text-base-content/70"
            :class="density === 'compact' ? 'text-xs' : 'text-sm'"
            aria-label="Historique des lancers"
        >
            <li
                v-for="(entry, index) in history"
                :key="`${index}-${entry.formula}`"
                class="inline-flex min-w-0 max-w-full items-baseline"
            >
                <span v-if="index > 0" class="mr-2 opacity-30" aria-hidden="true">·</span>
                <span class="truncate">{{ entry.formula }}</span>
                <span class="opacity-50">&nbsp;=&nbsp;</span>
                <span class="font-semibold tabular-nums text-base-content">{{ formatStat(entry.value) }}</span>
            </li>
        </ol>
    </div>
</template>
