<script setup>
/**
 * Calculateur de part des dés de vie (PNJ / monstre).
 *
 * Modes : total (niv. 1 → N) ou montée d’un niveau.
 * Méthodes : aléatoire (relance des 1) ou stable.
 */
import { computed, reactive, ref, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import {
    LIFE_DICE_METHOD_RANDOM,
    LIFE_DICE_METHOD_STABLE,
    defaultFixedBySides,
    gainForClass,
    gainForFormula,
    listDiceGroups,
    parseClassDieSides,
    previewLifePoints,
    totalForClass,
    totalForFormula,
} from '@/Utils/dice/lifeDiceCalculator.js';

const props = defineProps({
    open: { type: Boolean, default: false },
    /** 'class' = dé de classe ; 'formula' = formule monstre */
    sourceType: { type: String, default: 'class' },
    classLifeDice: { type: String, default: '1d8' },
    lifeFormula: { type: String, default: '' },
    level: { type: [Number, String], default: 1 },
    vitalityModifier: { type: [Number, String], default: 0 },
    currentLifeDiceTotal: { type: [Number, String, null], default: null },
    explicitLifeFilled: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'apply']);

const mode = ref('total'); // total | level_up
const method = ref(LIFE_DICE_METHOD_RANDOM);
const levelInput = ref(1);
const dieOrFormula = ref('');
const vitalityMod = ref(0);
const vitalityIncrease = ref(0);
const clearExplicitLife = ref(true);
const preview = ref(null);
const error = ref(null);
const fixedBySides = reactive({});

const isFormulaSource = computed(() => props.sourceType === 'formula');

const diceGroups = computed(() => {
    if (!isFormulaSource.value) return [];
    return listDiceGroups(dieOrFormula.value || '');
});

const uniqueSides = computed(() => {
    const seen = new Set();
    const sides = [];
    for (const group of diceGroups.value) {
        if (seen.has(group.sides)) continue;
        seen.add(group.sides);
        sides.push(group.sides);
    }
    return sides;
});

function syncFixedDefaults() {
    const defaults = defaultFixedBySides(dieOrFormula.value || '');
    Object.keys(fixedBySides).forEach((key) => {
        delete fixedBySides[key];
    });
    Object.entries(defaults).forEach(([sides, value]) => {
        fixedBySides[Number(sides)] = value;
    });
}

function resetFromProps() {
    mode.value = 'total';
    method.value = LIFE_DICE_METHOD_RANDOM;
    levelInput.value = Math.max(1, parseInt(String(props.level || '1'), 10) || 1);
    vitalityMod.value = Number(props.vitalityModifier) || 0;
    vitalityIncrease.value = 0;
    clearExplicitLife.value = true;
    preview.value = null;
    error.value = null;
    dieOrFormula.value = isFormulaSource.value
        ? (props.lifeFormula || '5+2d6/2')
        : (props.classLifeDice || '1d8');
    syncFixedDefaults();
}

watch(
    () => props.open,
    (open) => {
        if (open) resetFromProps();
    },
);

watch(dieOrFormula, () => {
    if (isFormulaSource.value) syncFixedDefaults();
    preview.value = null;
    error.value = null;
});

function buildOptions() {
    /** @type {Record<number, number>} */
    const fixed = {};
    uniqueSides.value.forEach((sides) => {
        const raw = fixedBySides[sides];
        fixed[sides] = Number.isFinite(Number(raw)) ? Math.max(1, Math.trunc(Number(raw))) : Math.floor(sides / 2);
    });
    return { fixedBySides: fixed };
}

function runTotal() {
    error.value = null;
    const level = Math.max(1, Math.min(20, parseInt(String(levelInput.value), 10) || 1));
    let result;
    if (isFormulaSource.value) {
        result = totalForFormula(dieOrFormula.value, level, method.value, buildOptions());
    } else {
        const sides = parseClassDieSides(dieOrFormula.value);
        if (!sides) {
            error.value = 'Dé de classe invalide (ex. 1d8).';
            preview.value = null;
            return;
        }
        result = totalForClass(sides, level, method.value, buildOptions());
    }
    if (!result.ok) {
        error.value = result.error;
        preview.value = null;
        return;
    }
    const mod = Math.max(0, Math.trunc(Number(vitalityMod.value) || 0));
    preview.value = {
        kind: 'total',
        lifeDiceTotal: result.lifeDiceTotal,
        levels: result.levels,
        lifePoints: previewLifePoints(result.lifeDiceTotal, level, mod),
        level,
        mod,
        vitalityCatchUp: null,
    };
}

function runLevelUp() {
    error.value = null;
    const current = props.currentLifeDiceTotal;
    if (current === null || current === undefined || current === '') {
        error.value = 'Aucune part des dés enregistrée. Lance d’abord un calcul total (niv. 1 → N).';
        preview.value = null;
        return;
    }
    const currentTotal = Math.trunc(Number(current));
    if (!Number.isFinite(currentTotal)) {
        error.value = 'Part des dés actuelle invalide.';
        preview.value = null;
        return;
    }

    let gain;
    if (isFormulaSource.value) {
        gain = gainForFormula(dieOrFormula.value, method.value, buildOptions());
    } else {
        const sides = parseClassDieSides(dieOrFormula.value);
        if (!sides) {
            error.value = 'Dé de classe invalide (ex. 1d8).';
            preview.value = null;
            return;
        }
        gain = gainForClass(sides, method.value, buildOptions());
    }
    if (!gain) {
        error.value = 'Impossible de calculer le gain.';
        preview.value = null;
        return;
    }

    const nextTotal = currentTotal + gain.value;
    const level = Math.max(1, Math.min(20, parseInt(String(levelInput.value), 10) || 1));
    const mod = Math.max(0, Math.trunc(Number(vitalityMod.value) || 0));
    const increase = Math.max(0, Math.trunc(Number(vitalityIncrease.value) || 0));
    const catchUp = increase > 0 ? increase * level : 0;

    preview.value = {
        kind: 'level_up',
        lifeDiceTotal: nextTotal,
        levels: [{ level, gain: gain.value, breakdown: gain.breakdown }],
        lifePoints: previewLifePoints(nextTotal, level, mod + increase),
        level,
        mod,
        vitalityCatchUp: increase > 0
            ? `Le mod. de Vitalité augmente de +${increase} au niveau ${level} : +${catchUp} PV (parce que le modificateur compte depuis le niveau 1). Passe le mod. de Vitalité sur la fiche.`
            : null,
    };
}

function calculate() {
    if (mode.value === 'level_up') runLevelUp();
    else runTotal();
}

function apply() {
    if (!preview.value) return;
    emit('apply', {
        lifeDiceTotal: preview.value.lifeDiceTotal,
        clearExplicitLife: Boolean(props.explicitLifeFilled && clearExplicitLife.value),
        lifeFormula: isFormulaSource.value ? String(dieOrFormula.value || '').trim() : null,
    });
    emit('close');
}

function closeModal() {
    emit('close');
}
</script>

<template>
    <Modal :open="open" size="lg" variant="glass" @close="closeModal">
        <template #header>
            <h3 class="text-lg font-bold">Calculateur de dés de vie</h3>
        </template>

        <div class="space-y-4 text-sm">
            <p class="text-xs text-base-content/70">
                On enregistre seulement la <strong>part des dés</strong>. Le mod. de Vitalité et l’équipement
                restent dans la formule de PV. S’il augmente, les PV suivent pour tous les niveaux.
                <span v-if="isFormulaSource">
                    Pas de parenthèses : <code>5 + 2d6 / 2</code> vaut 5 + (2d6 / 2).
                </span>
            </p>

            <div class="flex flex-wrap gap-2">
                <Btn
                    size="sm"
                    type="button"
                    :color="mode === 'total' ? 'primary' : 'ghost'"
                    @click="mode = 'total'"
                >
                    Total (niv. 1 → N)
                </Btn>
                <Btn
                    size="sm"
                    type="button"
                    :color="mode === 'level_up' ? 'primary' : 'ghost'"
                    @click="mode = 'level_up'"
                >
                    Montée d’un niveau
                </Btn>
            </div>

            <div class="flex flex-wrap gap-2">
                <Btn
                    size="sm"
                    type="button"
                    :color="method === LIFE_DICE_METHOD_RANDOM ? 'secondary' : 'ghost'"
                    @click="method = LIFE_DICE_METHOD_RANDOM"
                >
                    Aléatoire (relance les 1)
                </Btn>
                <Btn
                    size="sm"
                    type="button"
                    :color="method === LIFE_DICE_METHOD_STABLE ? 'secondary' : 'ghost'"
                    @click="method = LIFE_DICE_METHOD_STABLE"
                >
                    Stable
                </Btn>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <InputField
                    v-model="levelInput"
                    type="number"
                    label="Niveau"
                    min="1"
                    max="20"
                />
                <InputField
                    v-model="vitalityMod"
                    type="number"
                    label="Mod. de Vitalité (aperçu)"
                    hint="max(0, mod) × niveau s’ajoute aux PV affichés."
                />
                <InputField
                    v-model="dieOrFormula"
                    class="sm:col-span-2"
                    :label="isFormulaSource ? 'Formule de PV (par niveau)' : 'Dé de classe'"
                    :hint="isFormulaSource ? 'Ex. 5 + 2d6 / 2' : 'Ex. 1d8'"
                />
                <InputField
                    v-if="mode === 'level_up'"
                    v-model="vitalityIncrease"
                    type="number"
                    label="Le mod. a augmenté de"
                    hint="0 si inchangé. N’écrit pas le total : rappel seulement."
                    min="0"
                />
            </div>

            <div
                v-if="method === LIFE_DICE_METHOD_STABLE && isFormulaSource && uniqueSides.length"
                class="space-y-2 rounded-lg border border-base-300 p-3"
            >
                <p class="text-xs font-semibold">Valeurs fixes des dés (défaut = moitié des faces)</p>
                <div class="flex flex-wrap gap-3">
                    <label
                        v-for="sides in uniqueSides"
                        :key="sides"
                        class="flex items-center gap-2 text-xs"
                    >
                        <span>d{{ sides }} →</span>
                        <input
                            v-model.number="fixedBySides[sides]"
                            type="number"
                            min="1"
                            :max="sides"
                            class="input input-bordered input-xs w-16"
                        />
                    </label>
                </div>
            </div>

            <div
                v-if="explicitLifeFilled"
                class="rounded-lg border border-warning/40 bg-warning/10 p-3 text-xs"
            >
                Un total explicite est saisi dans la colonne PV (<code>life</code>) : il masque
                l’équipement. Pour laisser la formule s’appliquer,
                <label class="inline-flex items-center gap-2 ml-1">
                    <input v-model="clearExplicitLife" type="checkbox" class="checkbox checkbox-xs" />
                    vider ce total à l’application
                </label>
            </div>

            <div class="flex flex-wrap gap-2">
                <Btn color="accent" size="sm" type="button" @click="calculate">Calculer</Btn>
            </div>

            <p v-if="error" class="text-error text-xs">{{ error }}</p>

            <div v-if="preview" class="space-y-2 rounded-lg border border-base-300 p-3">
                <p>
                    Part des dés :
                    <strong>{{ preview.lifeDiceTotal }}</strong>
                    · PV (dés + mod. × niv.) :
                    <strong>{{ preview.lifePoints }}</strong>
                </p>
                <p v-if="preview.vitalityCatchUp" class="text-xs text-info">
                    {{ preview.vitalityCatchUp }}
                </p>
                <ul class="max-h-40 overflow-auto text-xs space-y-1">
                    <li v-for="row in preview.levels" :key="row.level">
                        Niv. {{ row.level }} : +{{ row.gain }}
                        <span class="opacity-70">({{ row.breakdown.join(' · ') }})</span>
                    </li>
                </ul>
            </div>
        </div>

        <template #actions>
            <Btn type="button" color="ghost" @click="closeModal">Fermer</Btn>
            <Btn
                type="button"
                color="primary"
                :disabled="!preview"
                @click="apply"
            >
                Appliquer
            </Btn>
        </template>
    </Modal>
</template>
