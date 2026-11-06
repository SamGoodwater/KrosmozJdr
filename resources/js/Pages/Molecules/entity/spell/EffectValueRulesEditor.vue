<script setup>
/**
 * Règles de valeur conditionnelles : remplacer / ajouter / soustraire une formule.
 * Brouillon local : une règle nouvellement ajoutée reste visible même sans formule encore.
 */
import { ref, watch } from "vue";
import FormulaExpressionInput from "@/Pages/Molecules/data-input/FormulaExpressionInput.vue";
import EffectConditionList from "@/Pages/Molecules/entity/spell/EffectConditionList.vue";
import EffectConditionHelp from "@/Pages/Molecules/entity/spell/EffectConditionHelp.vue";
import {
    VALUE_RULE_ACTIONS,
    draftValueRules,
    emptyValueRule,
} from "@/Utils/Entity/effectConditions.js";

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    conditionSuggestions: { type: Array, default: () => [] },
    valueSuggestions: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue", "dirty"]);

/** @type {import('vue').Ref<ReturnType<typeof draftValueRules>>} */
const draft = ref(draftValueRules(props.modelValue));

let syncingFromParent = false;

watch(
    () => props.modelValue,
    (next) => {
        syncingFromParent = true;
        draft.value = draftValueRules(next);
        syncingFromParent = false;
    },
    { deep: true },
);

function emitDraft() {
    if (syncingFromParent) return;
    emit(
        "update:modelValue",
        draft.value.map((r) => ({
            action: r.action,
            formula: r.formula,
            conditions: (r.conditions ?? []).map((c) => ({ ...c })),
        })),
    );
    emit("dirty");
}

function updateRule(index, patch) {
    const next = draft.value.map((r) => ({
        ...r,
        conditions: (r.conditions ?? []).map((c) => ({ ...c })),
    }));
    if (!next[index]) return;
    next[index] = { ...next[index], ...patch };
    draft.value = next;
    emitDraft();
}

function addRule() {
    const next = draft.value.map((r) => ({
        ...r,
        conditions: (r.conditions ?? []).map((c) => ({ ...c })),
    }));
    next.push(emptyValueRule());
    draft.value = next;
    emitDraft();
}

function removeRule(index) {
    draft.value = draft.value.filter((_, i) => i !== index);
    emitDraft();
}
</script>

<template>
    <div class="space-y-2" data-cy="effect-value-rules-editor">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label class="text-xs font-medium text-base-content/80">
                Règles de valeur (selon caractéristiques)
            </label>
            <button
                type="button"
                class="btn btn-ghost btn-xs"
                data-cy="effect-value-rule-add"
                @click="addRule"
            >
                + Règle
            </button>
        </div>

        <p v-if="draft.length === 0" class="text-xs text-base-content/55">
            Aucune règle. Ajoute-en une pour modifier la valeur selon des conditions
            (ex. +1 si Agilité ≥ 3).
        </p>

        <div
            v-for="(rule, index) in draft"
            :key="'rule-' + index"
            class="space-y-2 rounded-box border border-base-300 bg-base-200/20 p-2"
            data-cy="effect-value-rule-row"
        >
            <div class="flex flex-wrap items-end gap-2">
                <div class="min-w-48 flex-1">
                    <label class="label text-xs py-0">Action</label>
                    <select
                        class="select select-bordered select-sm w-full"
                        :value="rule.action"
                        @change="updateRule(index, { action: $event.target.value })"
                    >
                        <option
                            v-for="a in VALUE_RULE_ACTIONS"
                            :key="a.value"
                            :value="a.value"
                        >
                            {{ a.label }}
                        </option>
                    </select>
                </div>
                <div class="min-w-[10rem] flex-[2]">
                    <label class="label text-xs py-0">Nouvelle valeur (formule)</label>
                    <FormulaExpressionInput
                        :model-value="rule.formula"
                        :suggestions="valueSuggestions"
                        :min-query-length="0"
                        :open-on-focus="true"
                        placeholder="ex: 1 ou [strong]"
                        @update:model-value="updateRule(index, { formula: $event })"
                    />
                </div>
                <button
                    type="button"
                    class="btn btn-ghost btn-sm btn-square text-error"
                    title="Supprimer cette règle"
                    @click="removeRule(index)"
                >
                    ×
                </button>
            </div>

            <EffectConditionList
                :model-value="rule.conditions"
                :suggestions="conditionSuggestions"
                :show-help="false"
                compact
                @update:model-value="updateRule(index, { conditions: $event })"
                @dirty="emit('dirty')"
            />
        </div>

        <p class="text-xs text-base-content/60">
            Les règles s’appliquent dans l’ordre : remplacer écrase la valeur courante ;
            ajouter et soustraire la modifient.
        </p>
        <EffectConditionHelp mode="value" />
    </div>
</template>
