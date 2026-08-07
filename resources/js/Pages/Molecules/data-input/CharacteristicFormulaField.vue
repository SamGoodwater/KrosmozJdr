<script setup>
/**
 * Champ de saisie pour un total explicite ou un bonus contextuel (nombre ou formule `{...}`).
 *
 * @description
 * Validation live via la grammaire JS (miroir de FormulaExpressionParser), aperçu des valeurs
 * par niveau, et aide de syntaxe dans un Popover.
 *
 * @example
 * <CharacteristicFormulaField v-model="form.ca_context" label="Bonus contextuel CA" allow-domains />
 */
import { computed } from "vue";
import Popover from "@/Pages/Atoms/feedback/Popover.vue";
import FormulaExpressionInput from "@/Pages/Molecules/data-input/FormulaExpressionInput.vue";
import {
    enumerateFormulaOutcomes,
    validateFormulaValue,
} from "@/Utils/characteristic/formulaGrammar";

const props = defineProps({
    modelValue: { type: [String, Number], default: "" },
    label: { type: String, default: "" },
    hint: { type: String, default: "" },
    /** Autorise fourchettes / dés (uniquement pour le niveau). */
    allowDomains: { type: Boolean, default: false },
    /** Variables pour l'aperçu (ex. { level: 5 }). */
    previewVariables: { type: Object, default: () => ({}) },
    /** Suggestions d'autocomplétion [{ id, name?, short_name? }]. */
    suggestions: { type: Array, default: () => [] },
    disabled: { type: Boolean, default: false },
    placeholder: { type: String, default: "12 ou {[niveau] / 3}+" },
});

const emit = defineEmits(["update:modelValue"]);

const raw = computed({
    get: () => (props.modelValue == null ? "" : String(props.modelValue)),
    set: (v) => emit("update:modelValue", v),
});

const errors = computed(() => validateFormulaValue(raw.value, { allowDomains: props.allowDomains }));

const preview = computed(() => {
    if (errors.value.length > 0 || String(raw.value).trim() === "") return [];
    return enumerateFormulaOutcomes(raw.value, props.previewVariables, 12);
});
</script>

<template>
    <div class="characteristic-formula-field space-y-1">
        <div class="flex items-center gap-2">
            <label v-if="label" class="text-sm font-medium">{{ label }}</label>
            <Popover placement="bottom-start" max-width="md">
                <button
                    type="button"
                    class="btn btn-ghost btn-xs btn-circle"
                    aria-label="Aide de syntaxe des formules"
                >
                    ?
                </button>
                <template #content>
                    <div class="max-w-xs space-y-2 text-sm">
                        <p class="font-semibold">Syntaxe des formules</p>
                        <ul class="list-disc space-y-1 pl-4 text-xs opacity-90">
                            <li>Nombre simple : <code>12</code>, <code>-3</code></li>
                            <li>Formule : <code>{[niveau] / 3}+</code></li>
                            <li>
                                Arrondi après <code>}</code> :
                                rien = normal, <code>+</code> = supérieur, <code>-</code> = inférieur
                            </li>
                            <li>Référence : <code>[cle]</code> (ex. <code>[vitalite]</code>, <code>[level]</code>)</li>
                            <li v-if="allowDomains">
                                Domaines (niveau seulement) : <code>{[5-8]}</code>, <code>{8 + [1d4]}</code>
                            </li>
                            <li v-else>Fourchettes et dés réservés au champ niveau.</li>
                        </ul>
                        <p class="text-xs opacity-70">
                            Doc : features/characteristics/COMPUTED_VALUES.md
                        </p>
                    </div>
                </template>
            </Popover>
        </div>
        <p v-if="hint" class="text-xs opacity-70">{{ hint }}</p>
        <FormulaExpressionInput
            v-model="raw"
            :suggestions="suggestions"
            :disabled="disabled"
            :placeholder="placeholder"
            :use-brackets="true"
        />
        <p v-for="(err, i) in errors" :key="i" class="text-xs text-error">{{ err }}</p>
        <p v-if="preview.length" class="text-xs opacity-80">
            Aperçu :
            <span class="font-mono">{{ preview.join(", ") }}</span>
        </p>
    </div>
</template>
