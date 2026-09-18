<script setup>
/**
 * DiceRollerModal Organism
 *
 * @description
 * Modal lanceur de dés : formule (dés, tranches, opérateurs), raccourcis ndX,
 * bande unique min / moy / max / équivalent / valeur / lancer + historique.
 *
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @emits close - Fermeture du modal
 */
import { ref, computed, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import DiceFormulaStrip from '@/Pages/Molecules/data-display/DiceFormulaStrip.vue';
import { parseDiceFormula, rollDiceFormula } from '@/Utils/dice/diceParser.js';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const formula = ref('');
const lastResult = ref(null);
const lastError = ref(null);
/** Historique de session (vidé à la fermeture, pas de localStorage / cookie). */
const history = ref([]);
const HISTORY_MAX = 30;

const stats = computed(() => parseDiceFormula(formula.value));

const DICE_SHORTCUTS = [
    { label: 'd4', value: 4 },
    { label: 'd6', value: 6 },
    { label: 'd8', value: 8 },
    { label: 'd10', value: 10 },
    { label: 'd12', value: 12 },
    { label: 'd20', value: 20 },
    { label: 'd100', value: 100 },
];

function setShortcut(faces) {
    formula.value = `1d${faces}`;
}

function roll() {
    const result = rollDiceFormula(formula.value);
    if (result.isValid) {
        lastResult.value = result.result;
        lastError.value = null;
        history.value = [
            ...history.value,
            { formula: formula.value.trim(), value: result.result },
        ].slice(-HISTORY_MAX);
    } else {
        lastError.value = result.error;
        lastResult.value = null;
    }
}

function closeModal() {
    emit('close');
}

watch(() => props.open, (isOpen) => {
    if (!isOpen) {
        formula.value = '';
        lastResult.value = null;
        lastError.value = null;
        history.value = [];
    }
});

watch(formula, () => {
    lastResult.value = null;
    lastError.value = null;
});
</script>

<template>
    <Modal
        :open="open"
        size="md"
        variant="glass"
        placement="middle-center"
        close-on-esc
        @close="closeModal"
    >
        <template #header>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <Icon source="fa-dice-d20" pack="solid" alt="" size="md" />
                Lanceur de dés
            </h3>
        </template>

        <form class="space-y-3" @submit.prevent="roll">
            <p class="text-sm text-base-content/70 leading-snug">
                Écris une formule avec des <strong>dés</strong> (<code class="text-xs">d12</code>,
                <code class="text-xs">3d8</code>), des <strong>tranches</strong>
                (<code class="text-xs">[2-6]</code> → <code class="text-xs">1d5+1</code>)
                et des <strong>opérateurs</strong> <code class="text-xs">+</code>
                <code class="text-xs">-</code> <code class="text-xs">×</code>
                <code class="text-xs">/</code> (aussi <code class="text-xs">x</code> et
                <code class="text-xs">÷</code>). Les nombres à virgule sont acceptés.
            </p>

            <div class="flex flex-wrap gap-1">
                <button
                    v-for="d in DICE_SHORTCUTS"
                    :key="d.value"
                    type="button"
                    class="badge badge-sm badge-ghost cursor-pointer hover:bg-base-content/10 transition-colors"
                    :aria-label="`Remplir avec dé ${d.value} faces`"
                    @click="setShortcut(d.value)"
                >
                    {{ d.label }}
                </button>
            </div>

            <InputField
                v-model="formula"
                label="Formule"
                default-label-position="top"
                placeholder="2d6+3, [2-6], 3d8×2…"
                class="w-full"
                :validation="formula.trim() && stats.error ? { state: 'error', message: stats.error } : undefined"
                aria-label="Formule de dés (ex: 2d6+3 ou [2-6])"
            />

            <DiceFormulaStrip
                v-if="stats.isValid"
                density="comfortable"
                :min="stats.min"
                :max="stats.max"
                :average="stats.average"
                :range-equivalents="stats.rangeEquivalents"
                :roll-result="lastResult"
                :history="history"
                @roll="roll"
            />

            <p
                v-else-if="lastError"
                class="text-sm text-error"
            >
                {{ lastError }}
            </p>
        </form>

        <template #actions>
            <div class="flex gap-2 justify-end w-full">
                <Btn
                    color="neutral"
                    variant="ghost"
                    @click="closeModal"
                >
                    Fermer
                </Btn>
            </div>
        </template>
    </Modal>
</template>
