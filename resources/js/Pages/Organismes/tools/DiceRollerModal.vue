<script setup>
/**
 * DiceRollerModal Organism
 *
 * @description
 * Modal lanceur de dés avec formule ndX (ex: 2d6+3).
 * - Input pour la formule avec affichage min/max/moyenne en temps réel
 * - Raccourcis d4, d6, d8, d10, d12, d20, d100
 * - Lancer via bouton ou touche Entrée
 * - Design glass, conforme à la charte KrosmozJDR
 *
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @emits close - Fermeture du modal
 */
import { ref, computed, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import { parseDiceFormula, rollDiceFormula } from '@/Utils/dice/diceParser.js';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const formula = ref('');
const lastResult = ref(null);
const lastError = ref(null);

const stats = computed(() => {
    const parsed = parseDiceFormula(formula.value);
    return parsed;
});

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
        lastResult.value = result;
        lastError.value = null;
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
    }
});
</script>

<template>
    <Modal
        :open="open"
        size="sm"
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

        <form class="space-y-4" @submit.prevent="roll">
            <!-- Raccourcis (badges discrets, clic = remplir l'input) -->
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

            <!-- Input formule -->
            <InputField
                v-model="formula"
                label="Formule"
                default-label-position="top"
                placeholder="2d6+3, 3d10-1, 4d6/2…"
                class="w-full"
                :validation="stats.error ? { state: 'error', message: stats.error } : undefined"
                aria-label="Formule de dés (ex: 2d6+3)"
            />

            <!-- Stats temps réel : min, max, moyenne -->
            <div
                v-if="formula.trim()"
                class="flex flex-wrap gap-2 items-center"
            >
                <span class="text-sm text-base-content/70">Résultat :</span>
                <span
                    v-if="stats.isValid"
                    class="flex gap-3 text-sm"
                >
                    <span class="badge badge-sm badge-ghost">Min {{ stats.min }}</span>
                    <span class="badge badge-sm badge-ghost">Max {{ stats.max }}</span>
                    <span class="badge badge-sm badge-ghost">Moy. {{ stats.average }}</span>
                </span>
            </div>

            <!-- Résultat du lancer -->
            <div
                v-if="lastResult"
                class="rounded-box box-glass-sm p-3"
            >
                <p class="text-2xl font-bold text-primary">
                    {{ lastResult.result }}
                </p>
                <p
                    v-if="lastResult.breakdown?.length"
                    class="text-xs text-base-content/70 mt-1"
                >
                    {{ lastResult.breakdown.join(' ') }}
                </p>
            </div>
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
                    color="primary"
                    :disabled="!stats.isValid"
                    aria-label="Lancer les dés"
                    @click="roll"
                >
                    <Icon source="fa-dice" pack="solid" alt="" size="sm" class="mr-2" />
                    Lancer
                </Btn>
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
