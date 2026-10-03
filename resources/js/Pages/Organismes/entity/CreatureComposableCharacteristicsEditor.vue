<script setup>
/**
 * Éditeur Inertia des totaux / contextes composables d’une créature liée (monstre ou PNJ).
 */
import { computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import CharacteristicFormulaField from "@/Pages/Molecules/data-input/CharacteristicFormulaField.vue";
import CreatureComposableCharacteristicRow from "@/Pages/Molecules/entity/CreatureComposableCharacteristicRow.vue";
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
});

const sections = computed(() => buildCreatureComposableEditSections());

const suggestions = computed(() => buildCharacteristicKeySuggestionsFromStore());

function buildInitialData(creature) {
    /** @type {Record<string, string>} */
    const data = { level: creature?.level ?? "" };
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

function submit() {
    form.patch(
        route(props.updateRouteName, { [props.updateRouteParamName]: props.entityId }),
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

        <CharacteristicFormulaField
            v-model="form.level"
            label="Niveau"
            hint="Fourchettes et dés autorisés ici uniquement (ex. {[5-8]})."
            :allow-domains="true"
            :preview-variables="previewVariables"
            :suggestions="suggestions"
        />
        <p v-if="form.errors.level" class="text-xs text-error">{{ form.errors.level }}</p>

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
    </div>
</template>
