<script setup>
/**
 * Champ formule ou table par caractéristique.
 *
 * - Un champ simple (formule ou valeur fixe) avec un bouton [+] à droite.
 * - Clic sur [+] : affiche la table (caractéristique de référence + lignes "à partir de" / "valeur").
 * - Chaque ligne a un bouton [-] ; minimum 1 ligne. Si une seule ligne reste, on repasse en vue simple
 *   (champ unique + [+]), sans afficher la référence ni la colonne "à partir de".
 *
 * @see Utils/characteristic/formulaConfig.js (decodeFormulaConfig, encodeFormulaConfig)
 */
import { computed } from 'vue';
import { decodeFormulaConfig, encodeFormulaConfig } from '@/Utils/characteristic/formulaConfig';

const props = defineProps({
    /** Valeur : formule simple (string) ou JSON table {"characteristic":"...", "1": ..., "7": ...} */
    modelValue: { type: String, default: '' },
    /** Options pour le select "Caractéristique de référence" : [{ id, name }] */
    characteristicOptions: { type: Array, default: () => [] },
    label: { type: String, default: '' },
    placeholder: { type: String, default: 'ex: [level]*2 ou 42' },
});

const emit = defineEmits(['update:modelValue']);

const decoded = computed(() => decodeFormulaConfig(props.modelValue ?? ''));

/** True si on affiche la table (2+ lignes). Si 1 seule ligne en table, on affiche comme formule simple. */
const isTableExpanded = computed(() => {
    const d = decoded.value;
    return d.type === 'table' && d.entries && d.entries.length >= 2;
});

/** En mode "simple" : soit formule, soit table à 1 ligne (on n’affiche que la valeur). */
const simpleInputValue = computed({
    get() {
        const d = decoded.value;
        if (d.type === 'formula') return d.expression ?? '';
        if (d.type === 'table' && d.entries?.length === 1) return String(d.entries[0].value ?? '');
        return '';
    },
    set(v) {
        const d = decoded.value;
        if (d.type === 'table' && d.entries?.length === 1) {
            const entries = [{ ...d.entries[0], value: v }];
            emit('update:modelValue', encodeFormulaConfig({ type: 'table', characteristic: d.characteristic || 'level', entries }));
        } else {
            emit('update:modelValue', encodeFormulaConfig({ type: 'formula', expression: v }));
        }
    },
});

const refCharacteristic = computed({
    get() {
        return decoded.value.type === 'table' ? (decoded.value.characteristic || '') : '';
    },
    set(v) {
        if (decoded.value.type !== 'table') return;
        emit('update:modelValue', encodeFormulaConfig({
            type: 'table',
            characteristic: v || 'level',
            entries: decoded.value.entries,
        }));
    },
});

function expandToTable() {
    const d = decoded.value;
    if (d.type === 'formula') {
        const firstId = (props.characteristicOptions[0]?.id) || 'level';
        emit('update:modelValue', encodeFormulaConfig({
            type: 'table',
            characteristic: firstId,
            entries: [{ from: 1, value: (d.expression ?? '').trim() || '0' }],
        }));
        return;
    }
    if (d.type === 'table' && d.entries?.length === 1) {
        const e = d.entries[0];
        const newEntries = [
            { from: e.from, value: e.value },
            { from: (Number(e.from) || 0) + 1, value: '' },
        ];
        emit('update:modelValue', encodeFormulaConfig({
            type: 'table',
            characteristic: d.characteristic || 'level',
            entries: newEntries,
        }));
    }
}

function addRow() {
    const d = decoded.value;
    if (d.type !== 'table' || !d.entries?.length) return;
    const lastFrom = d.entries[d.entries.length - 1].from;
    const newEntries = [...d.entries, { from: (Number(lastFrom) || 0) + 1, value: '' }];
    emit('update:modelValue', encodeFormulaConfig({
        type: 'table',
        characteristic: d.characteristic || 'level',
        entries: newEntries,
    }));
}

function removeRow(index) {
    const d = decoded.value;
    if (d.type !== 'table' || index < 0 || index >= d.entries.length) return;
    const newEntries = d.entries.filter((_, i) => i !== index);
    if (newEntries.length === 0) return;
    if (newEntries.length === 1) {
        emit('update:modelValue', encodeFormulaConfig({
            type: 'table',
            characteristic: d.characteristic || 'level',
            entries: newEntries,
        }));
        return;
    }
    emit('update:modelValue', encodeFormulaConfig({
        type: 'table',
        characteristic: d.characteristic || 'level',
        entries: newEntries,
    }));
}

function updateEntryFrom(index, raw) {
    const d = decoded.value;
    if (d.type !== 'table' || index < 0 || index >= d.entries.length) return;
    const from = Number(raw);
    if (Number.isNaN(from)) return;
    const newEntries = d.entries.map((e, i) => (i === index ? { ...e, from } : e));
    emit('update:modelValue', encodeFormulaConfig({
        type: 'table',
        characteristic: d.characteristic || 'level',
        entries: newEntries,
    }));
}

function updateEntryValue(index, value) {
    const d = decoded.value;
    if (d.type !== 'table' || index < 0 || index >= d.entries.length) return;
    const newEntries = d.entries.map((e, i) => (i === index ? { ...e, value: value ?? '' } : e));
    emit('update:modelValue', encodeFormulaConfig({
        type: 'table',
        characteristic: d.characteristic || 'level',
        entries: newEntries,
    }));
}

const tableEntries = computed(() => (decoded.value.type === 'table' ? decoded.value.entries : []));
const canRemoveRow = computed(() => tableEntries.value.length > 1);
</script>

<template>
    <div class="form-control w-full">
        <label v-if="label" class="label py-1">
            <span class="label-text">{{ label }}</span>
        </label>
        <!-- Mode simple : un input + bouton [+] -->
        <div v-if="!isTableExpanded" class="flex gap-2 items-center">
            <input
                type="text"
                :value="simpleInputValue"
                class="input input-bordered input-sm w-full min-w-0 font-mono text-sm"
                :placeholder="placeholder"
                @input="simpleInputValue = $event.target.value"
            />
            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm shrink-0"
                aria-label="Passer en table par caractéristique"
                title="Table par caractéristique"
                @click="expandToTable"
            >
                <span class="text-lg leading-none">+</span>
            </button>
        </div>
        <!-- Mode table (2+ lignes) : référence + lignes (à partir de / valeur) + [-] et [+] -->
        <div v-else class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
            <div>
                <label class="label label-text text-xs">Caractéristique de référence</label>
                <select
                    :value="refCharacteristic"
                    class="select select-bordered select-sm w-full max-w-xs"
                    @change="refCharacteristic = $event.target.value"
                >
                    <option
                        v-for="c in characteristicOptions"
                        :key="c.id"
                        :value="c.id"
                    >
                        {{ c.name ?? c.id }}
                    </option>
                </select>
                <p class="mt-1 text-xs text-base-content/70">
                    À partir de chaque valeur ci‑dessous, ce résultat s’applique jusqu’à la valeur suivante (non comprise). La dernière ligne s’applique à toutes les valeurs supérieures.
                </p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="label-text text-xs">À partir de (valeur) → valeur fixe ou formule</span>
                    <button type="button" class="btn btn-ghost btn-xs" @click="addRow">+ Ajouter</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-xs">
                        <thead>
                            <tr>
                                <th>À partir de</th>
                                <th>Valeur (fixe ou formule)</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, idx) in tableEntries" :key="idx">
                                <td>
                                    <input
                                        type="number"
                                        class="input input-bordered input-xs w-20"
                                        :value="row.from"
                                        @input="updateEntryFrom(idx, $event.target.value)"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        class="input input-bordered input-xs w-full font-mono"
                                        :value="row.value"
                                        placeholder="0 ou [level]*2"
                                        @input="updateEntryValue(idx, $event.target.value)"
                                    />
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-ghost btn-xs btn-square"
                                        aria-label="Supprimer la ligne"
                                        :disabled="!canRemoveRow"
                                        @click="removeRow(idx)"
                                    >
                                        −
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
