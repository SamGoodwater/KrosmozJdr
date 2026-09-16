<script setup>
/**
 * Panneau de réglages IA pour un type d’entité (gel des champs / caracs, étalons).
 */
import { computed, ref } from "vue";
import CheckboxField from "@/Pages/Molecules/data-input/CheckboxField.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import ExamplePicker from "@/Pages/Admin/Content/IaGeneration/ExamplePicker.vue";

const props = defineProps({
    entity: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: { type: Object, required: true },
    characteristicOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue"]);

const filter = ref("");

const hasDofusSource = computed({
    get: () => Boolean(props.modelValue.has_dofus_source),
    set: (checked) => patch({ has_dofus_source: Boolean(checked) }),
});

const freezeAllFields = computed({
    get: () => props.modelValue.frozen_fields === "*",
    set: (checked) =>
        patch({
            frozen_fields: checked ? "*" : [],
        }),
});

const freezeAllCharacteristics = computed({
    get: () => props.modelValue.frozen_characteristics === "*",
    set: (checked) =>
        patch({
            frozen_characteristics: checked ? "*" : [],
        }),
});

const writableFieldsText = computed({
    get: () => listToText(props.modelValue.writable_fields),
    set: (text) => patch({ writable_fields: textToList(text) }),
});

const frozenFieldsText = computed({
    get: () => (props.modelValue.frozen_fields === "*" ? "" : listToText(props.modelValue.frozen_fields)),
    set: (text) => patch({ frozen_fields: textToList(text) }),
});

const writableCharacteristicsText = computed({
    get: () => listToText(props.modelValue.writable_characteristics),
    set: (text) => patch({ writable_characteristics: textToList(text) }),
});

const frozenCharacteristicsText = computed({
    get: () =>
        props.modelValue.frozen_characteristics === "*"
            ? ""
            : listToText(props.modelValue.frozen_characteristics),
    set: (text) => patch({ frozen_characteristics: textToList(text) }),
});

const exampleIds = computed({
    get: () => (Array.isArray(props.modelValue.example_ids) ? props.modelValue.example_ids : []),
    set: (ids) => patch({ example_ids: Array.isArray(ids) ? ids : [] }),
});

const taskPromptText = computed({
    get: () => props.modelValue.task_prompt || "",
    set: (text) => patch({ task_prompt: text }),
});

const filteredCharacteristics = computed(() => {
    const q = filter.value.trim().toLowerCase();
    const options = props.characteristicOptions || [];
    if (!q) {
        return options;
    }
    return options.filter(
        (row) =>
            String(row.key).toLowerCase().includes(q) || String(row.name).toLowerCase().includes(q)
    );
});

function patch(partial) {
    emit("update:modelValue", { ...props.modelValue, ...partial });
}

function listToText(value) {
    return Array.isArray(value) ? value.join("\n") : "";
}

function textToList(text) {
    return String(text || "")
        .split(/[\n,]+/)
        .map((item) => item.trim())
        .filter(Boolean);
}

function isWritableCharacteristic(key) {
    return (props.modelValue.writable_characteristics || []).includes(key);
}

function toggleWritableCharacteristic(key, checked) {
    const current = Array.isArray(props.modelValue.writable_characteristics)
        ? [...props.modelValue.writable_characteristics]
        : [];
    const next = checked ? [...new Set([...current, key])] : current.filter((item) => item !== key);
    patch({ writable_characteristics: next });
}
</script>

<template>
    <section class="rounded-box border border-base-300 bg-base-100/50 p-4 space-y-4" :id="'ia-entity-' + entity">
        <h2 class="text-lg font-semibold text-base-content">{{ label }}</h2>

        <CheckboxField v-model="hasDofusSource" label="Fiche sourcée Dofus (recopier l’identité figée)" />

        <CheckboxField v-model="freezeAllFields" label="Figer tous les champs" />
        <TextareaField
            v-if="freezeAllFields"
            v-model="writableFieldsText"
            label="Exceptions (champs que l’IA peut écrire)"
            helper="Une clé par ligne, ex. effect"
            rows="3"
            default-label-position="top"
        />
        <TextareaField
            v-else
            v-model="frozenFieldsText"
            label="Champs figés"
            helper="Une clé par ligne. Vide = rien n’est figé."
            rows="3"
            default-label-position="top"
        />

        <CheckboxField v-model="freezeAllCharacteristics" label="Figer toutes les caractéristiques" />
        <template v-if="freezeAllCharacteristics">
            <p class="text-sm text-base-content/70">
                Exceptions : l’IA pourra modifier ces caracs malgré le gel.
            </p>
            <InputField
                v-if="characteristicOptions.length"
                v-model="filter"
                label="Filtrer les caracs"
                default-label-position="top"
            />
            <div
                v-if="characteristicOptions.length"
                class="max-h-48 overflow-y-auto rounded-box border border-base-300 p-2 space-y-1"
            >
                <label
                    v-for="row in filteredCharacteristics"
                    :key="row.key"
                    class="flex items-center gap-2 text-sm"
                >
                    <input
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        :checked="isWritableCharacteristic(row.key)"
                        @change="toggleWritableCharacteristic(row.key, $event.target.checked)"
                    />
                    <span>{{ row.name }}</span>
                    <code class="text-xs text-base-content/60">{{ row.key }}</code>
                </label>
            </div>
            <TextareaField
                v-model="writableCharacteristicsText"
                label="Caracs que l’IA peut modifier"
                helper="Complète les cases : une clé characteristics.key par ligne."
                rows="3"
                default-label-position="top"
            />
        </template>
        <TextareaField
            v-else
            v-model="frozenCharacteristicsText"
            label="Caractéristiques figées"
            helper="Clés characteristics.key, une par ligne. Vide = aucune figée."
            rows="3"
            default-label-position="top"
        />

        <ExamplePicker v-model="exampleIds" :entity="entity" />

        <TextareaField
            v-model="taskPromptText"
            label="Prompt de tâche"
            helper="Vide = fiche Création du type (ou texte par défaut). Injecté dans l’assembleur."
            rows="6"
            default-label-position="top"
        />
    </section>
</template>
