<script setup>
/**
 * Liste de conditions (formule gauche / opérateur / formule droite) liées par ET ou OU.
 *
 * @example
 * <EffectConditionList v-model="row.params.conditions" :suggestions="actorSuggestions" @dirty="markDirty" />
 */
import { computed } from "vue";
import FormulaExpressionInput from "@/Pages/Molecules/data-input/FormulaExpressionInput.vue";
import EffectConditionHelp from "@/Pages/Molecules/entity/spell/EffectConditionHelp.vue";
import {
    CONDITION_JOINS,
    CONDITION_OPERATORS,
    emptyCondition,
    normalizeConditions,
} from "@/Utils/Entity/effectConditions.js";

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    /** Suggestions avec ids éventuellement préfixés caster:/target: */
    suggestions: { type: Array, default: () => [] },
    showHelp: { type: Boolean, default: true },
    compact: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "dirty"]);

const rows = computed({
    get() {
        const list = normalizeConditions(props.modelValue);
        return list.length > 0 ? list : [];
    },
    set(next) {
        emit("update:modelValue", next);
        emit("dirty");
    },
});

function ensureMutable() {
    return rows.value.map((r) => ({ ...r }));
}

function updateRow(index, patch) {
    const next = ensureMutable();
    if (!next[index]) return;
    next[index] = { ...next[index], ...patch };
    rows.value = next;
}

function addCondition() {
    const next = ensureMutable();
    next.push(emptyCondition(next.length));
    rows.value = next;
}

function removeCondition(index) {
    const next = ensureMutable().filter((_, i) => i !== index);
    rows.value = next;
}

function setJoin(index, join) {
    updateRow(index, { join });
}
</script>

<template>
    <div class="space-y-2" data-cy="effect-condition-list">
        <div
            v-for="(row, index) in rows"
            :key="index"
            class="space-y-1.5 rounded-box border border-dashed border-base-300 p-2"
        >
            <div v-if="index > 0" class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">Lien</span>
                <select
                    class="select select-bordered select-xs w-24"
                    :value="row.join || 'AND'"
                    @change="setJoin(index, $event.target.value)"
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
                        :min-query-length="2"
                        placeholder="ex: [caster:modifier_agility_creature]"
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
                        :min-query-length="2"
                        placeholder="ex: 3 ou [target:modifier_chance_creature]"
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
            <button type="button" class="btn btn-ghost btn-xs" @click="addCondition">
                + Condition
            </button>
            <p v-if="!compact" class="text-xs text-base-content/60">
                L’effet (ou la règle) ne s’applique que si ces conditions sont vraies.
            </p>
        </div>

        <EffectConditionHelp v-if="showHelp" mode="condition" />
    </div>
</template>
