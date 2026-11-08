<script setup>
/**
 * Éditeur Inertia des totaux / contextes composables d’une créature liée (monstre ou PNJ).
 */
import { computed, ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import CharacteristicFormulaField from "@/Pages/Molecules/data-input/CharacteristicFormulaField.vue";
import CreatureComposableCharacteristicRow from "@/Pages/Molecules/entity/CreatureComposableCharacteristicRow.vue";
import LifeDiceCalculatorModal from "@/Pages/Organismes/entity/LifeDiceCalculatorModal.vue";
import { buildCharacteristicKeySuggestionsFromStore } from "@/Composables/characteristic/useCharacteristicKeySuggestions";
import {
    CREATURE_COMPOSABLE_COLUMNS,
    contextColumnForComposable,
} from "@/Support/Creature/creatureComposableColumns";
import { buildCreatureComposableEditSections } from "@/Utils/Entity/buildCreatureComposableEditSections";

const props = defineProps({
    creature: { type: Object, required: true },
    entityId: { type: [Number, String], required: true },
    updateRouteName: { type: String, required: true },
    updateRouteParamName: { type: String, required: true },
    /** 'class' (PNJ) ou 'formula' (monstre) */
    lifeDiceSource: { type: String, default: "class" },
    classLifeDice: { type: String, default: "1d8" },
    lifeFormula: { type: String, default: "" },
    vitalityModifier: { type: [Number, String], default: 0 },
});

const sections = computed(() => buildCreatureComposableEditSections());

const suggestions = computed(() => buildCharacteristicKeySuggestionsFromStore());

const calculatorOpen = ref(false);

function buildInitialData(creature) {
    /** @type {Record<string, string|number|null>} */
    const data = {
        level: creature?.level ?? "",
        life_dice_total: creature?.life_dice_total ?? "",
    };
    for (const col of CREATURE_COMPOSABLE_COLUMNS) {
        const ctx = contextColumnForComposable(col);
        data[col] = creature?.[col] ?? "";
        data[ctx] = creature?.[ctx] ?? "";
    }
    return data;
}

const form = useForm(buildInitialData(props.creature));

watch(
    () => props.creature,
    (creature) => {
        if (!creature) return;
        const next = buildInitialData(creature);
        Object.keys(next).forEach((key) => {
            form[key] = next[key];
        });
    },
    { deep: true },
);

const previewVariables = computed(() => {
    const lvl = parseInt(String(form.level || props.creature?.level || "1"), 10);
    return { level: Number.isFinite(lvl) ? lvl : 1, niveau: Number.isFinite(lvl) ? lvl : 1 };
});

const explicitLifeFilled = computed(() => {
    const raw = form.life;
    return raw !== null && raw !== undefined && String(raw).trim() !== "";
});

function submit() {
    form.patch(
        route(props.updateRouteName, { [props.updateRouteParamName]: props.entityId }),
        { preserveScroll: true },
    );
}

/**
 * @param {{ lifeDiceTotal: number, clearExplicitLife: boolean, lifeFormula: string|null }} payload
 */
function onCalculatorApply(payload) {
    form.life_dice_total = payload.lifeDiceTotal;
    if (payload.clearExplicitLife) {
        form.life = "";
    }
    /** @type {Record<string, number|string|null>} */
    const data = {
        life_dice_total: payload.lifeDiceTotal,
    };
    if (payload.clearExplicitLife) {
        data.life = "";
    }
    if (props.lifeDiceSource === "formula" && payload.lifeFormula) {
        data.life_formula = payload.lifeFormula;
    }
    router.patch(
        route(props.updateRouteName, { [props.updateRouteParamName]: props.entityId }),
        data,
        { preserveScroll: true },
    );
}
</script>

<template>
    <div class="creature-composable-editor space-y-4">
        <p class="text-xs text-base-content/70">
            Un <strong>total explicite</strong> non vide est affiché tel quel. Sinon le moteur compose
            base système + bonus d’objets + bonus contextuel.
        </p>

        <div class="flex flex-wrap items-end gap-3">
            <CharacteristicFormulaField
                v-model="form.level"
                label="Niveau"
                hint="Fourchettes et dés autorisés ici uniquement (ex. {[5-8]})."
                :allow-domains="true"
                :preview-variables="previewVariables"
                :suggestions="suggestions"
                class="flex-1 min-w-[12rem]"
            />
            <div class="form-control">
                <label class="label py-0">
                    <span class="label-text text-xs">Part des dés</span>
                </label>
                <input
                    v-model="form.life_dice_total"
                    type="number"
                    min="0"
                    class="input input-bordered input-sm w-28"
                    placeholder="—"
                />
            </div>
            <Btn
                color="secondary"
                size="sm"
                type="button"
                @click="calculatorOpen = true"
            >
                Calculateur de PV
            </Btn>
        </div>
        <p v-if="form.errors.level" class="text-xs text-error">{{ form.errors.level }}</p>
        <p v-if="form.errors.life_dice_total" class="text-xs text-error">
            {{ form.errors.life_dice_total }}
        </p>

        <div v-for="section in sections" :key="section.id" class="space-y-3">
            <h3 class="text-sm font-bold border-b border-base-300 pb-1">{{ section.title }}</h3>
            <CreatureComposableCharacteristicRow
                v-for="col in section.dbColumns"
                :key="col"
                :db-column="col"
                :form="form"
                :preview-variables="previewVariables"
                :suggestions="suggestions"
                :errors="form.errors"
            />
        </div>

        <div class="flex justify-end pt-2">
            <Btn
                color="primary"
                size="sm"
                type="button"
                :disabled="form.processing"
                @click="submit"
            >
                Enregistrer les caractéristiques
            </Btn>
        </div>

        <LifeDiceCalculatorModal
            :open="calculatorOpen"
            :source-type="lifeDiceSource"
            :class-life-dice="classLifeDice"
            :life-formula="lifeFormula"
            :level="form.level || creature?.level || 1"
            :vitality-modifier="vitalityModifier"
            :current-life-dice-total="form.life_dice_total"
            :explicit-life-filled="explicitLifeFilled"
            @close="calculatorOpen = false"
            @apply="onCalculatorApply"
        />
    </div>
</template>
