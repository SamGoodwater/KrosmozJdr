<script setup>
/**
 * Liste de conditions (formule gauche / opérateur / formule droite) liées par ET ou OU.
 *
 * Le brouillon local conserve les lignes vides pendant la saisie ; la normalisation
 * (lignes incomplètes écartées) n’intervient qu’à l’enregistrement côté parent/API.
 */
import { ref, watch } from "vue";
import FormulaExpressionInput from "@/Pages/Molecules/data-input/FormulaExpressionInput.vue";
import EffectConditionHelp from "@/Pages/Molecules/entity/spell/EffectConditionHelp.vue";
import {
    CONDITION_JOINS,
    CONDITION_OPERATORS,
    draftConditions,
    emptyCondition,
} from "@/Utils/Entity/effectConditions.js";

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    suggestions: { type: Array, default: () => [] },
    showHelp: { type: Boolean, default: true },
    compact: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "dirty"]);

/** @type {import('vue').Ref<ReturnType<typeof draftConditions>>} */
const draft = ref(draftConditions(props.modelValue));

let syncingFromParent = false;

watch(
    () => props.modelValue,
    (next) => {
        syncingFromParent = true;
        draft.value = draftConditions(next);
        syncingFromParent = false;
    },
    { deep: true },
);

function emitDraft() {
    if (syncingFromParent) return;
    emit("update:modelValue", draft.value.map((r) => ({ ...r })));
    emit("dirty");
}

function updateRow(index, patch) {
    const next = draft.value.map((r) => ({ ...r }));
    if (!next[index]) return;
    next[index] = { ...next[index], ...patch };
    draft.value = next;
    emitDraft();
}

function addCondition() {
    const next = draft.value.map((r) => ({ ...r }));
    next.push(emptyCondition(next.length));
    draft.value = next;
    emitDraft();
}

function removeCondition(index) {
    draft.value = draft.value.filter((_, i) => i !== index).map((r, i) => {
        const copy = { ...r };
        if (i === 0) {
            delete copy.join;
        } else if (!copy.join) {
            copy.join = "AND";
        }
        return copy;
    });
    emitDraft();
}
</script>

<template>
    <div class="space-y-2" data-cy="effect-condition-list">
        <div
            v-for="(row, index) in draft"
            :key="'cond-' + index"
            class="space-y-1.5 rounded-box border border-dashed border-base-300 p-2"
            data-cy="effect-condition-row"
        >
            <div v-if="index > 0" class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">Lien</span>
                <select
                    class="select select-bordered select-xs w-24"
                    :value="row.join || 'AND'"
                    @change="updateRow(index, { join: $event.target.value })"
                >
                    <option v-for="j in CONDITION_JOINS" :key="j.value" :value="j.value">
                        {{ j.label }}
                    </option>
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-1.5">
                <div class="min-w-[8rem] flex-1">
                    <label class="label text-xs py-0">Formule gauche</label>
                    <FormulaExpressionInput
                        :model-value="row.left"
                        :suggestions="suggestions"
                        :min-query-length="0"
                        :open-on-focus="true"
                        placeholder="ex: [modifier_agility_creature] ou […:target]"
                        @update:model-value="updateRow(index, { left: $event })"
                    />
                </div>
                <div class="w-28 shrink-0">
                    <label class="label text-xs py-0">Comparateur</label>
                    <select
                        class="select select-bordered select-sm w-full"
                        :value="row.op"
                        :title="CONDITION_OPERATORS.find((o) => o.value === row.op)?.label"
                        @change="updateRow(index, { op: $event.target.value })"
                    >
                        <option
                            v-for="op in CONDITION_OPERATORS"
                            :key="op.value"
                            :value="op.value"
                            :title="op.label"
                        >
                            {{ op.symbol }}
                        </option>
                    </select>
                </div>
                <div class="min-w-[8rem] flex-1">
                    <label class="label text-xs py-0">Formule droite</label>
                    <FormulaExpressionInput
                        :model-value="row.right"
                        :suggestions="suggestions"
                        :min-query-length="0"
                        :open-on-focus="true"
                        placeholder="ex: 3 ou [modifier_chance_creature:target]"
                        @update:model-value="updateRow(index, { right: $event })"
                    />
                </div>
                <button
                    type="button"
                    class="btn btn-ghost btn-sm btn-square text-error"
                    title="Supprimer cette condition"
                    @click="removeCondition(index)"
                >
                    ×
                </button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                class="btn btn-ghost btn-xs"
                data-cy="effect-condition-add"
                @click="addCondition"
            >
                + Condition
            </button>
            <p v-if="!compact" class="text-xs text-base-content/60">
                L’effet (ou la règle) ne s’applique que si ces conditions sont vraies.
            </p>
        </div>

        <EffectConditionHelp v-if="showHelp" mode="condition" />
    </div>
</template>
