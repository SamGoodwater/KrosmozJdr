<script setup>
/**
 * Modal unique DofusDB + conversion IA (une icône menu).
 *
 * @example
 * <EntitySourceModal :open="source.open" :show-dofusdb="true" :show-ai="isAdmin" />
 */
import { computed, ref, watch } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";

const props = defineProps({
    open: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    applying: { type: Boolean, default: false },
    preview: { type: Object, default: null },
    error: { type: String, default: "" },
    playable: { type: Boolean, default: false },
    entityLabel: { type: String, default: "cette fiche" },
    showDofusdb: { type: Boolean, default: true },
    showAi: { type: Boolean, default: false },
    aiBrief: { type: String, default: "" },
    aiSubmitting: { type: Boolean, default: false },
    aiError: { type: String, default: "" },
    aiSuccess: { type: String, default: "" },
    aiEstimate: { type: Object, default: null },
    aiUsage: { type: Object, default: null },
    aiActionLabel: { type: String, default: "Conversion IA" },
});

const emit = defineEmits(["close", "confirm", "update:aiBrief", "convert"]);

const { isAdmin } = usePermissions();
const mode = ref("full");
const force = ref(false);
const pane = ref(!props.showDofusdb && props.showAi ? "ia" : "dofusdb");

watch(
    () => props.open,
    (open) => {
        if (open) {
            mode.value = "full";
            force.value = false;
            pane.value = props.showDofusdb ? "dofusdb" : "ia";
        }
    },
    { immediate: true }
);

watch(
    () => [props.showDofusdb, props.showAi],
    () => {
        if (!props.showDofusdb && props.showAi) {
            pane.value = "ia";
        }
    }
);

const convertedName = computed(() => {
    const converted = props.preview?.data?.converted;
    if (!converted || typeof converted !== "object") return "";
    const nested = Object.values(converted).find((v) => v && typeof v === "object" && !Array.isArray(v));
    const name = converted.name ?? nested?.name ?? "";
    if (typeof name === "string") return name;
    if (name && typeof name === "object") return name.fr || name.en || "";
    return "";
});

const validationErrors = computed(() => {
    const errors = props.preview?.data?.validation_errors;
    return Array.isArray(errors) ? errors : [];
});

const estimateLabel = computed(() => {
    const row = props.aiEstimate;
    if (!row) return "";
    return row.formatted || row.label || "";
});

const remainingUsageLabel = computed(() => {
    const usage = props.aiUsage;
    if (!usage || typeof usage !== "object") return "Solde : chargement…";
    if (typeof usage.remaining_credits_usd === "number") {
        const credit = Number(usage.remaining_credits_usd).toLocaleString("fr-FR", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        const hint = usage.remaining_hint ? ` ${usage.remaining_hint}` : "";
        return `Crédit restant : ${credit} $${hint}`;
    }
    const input = Number(usage.local_input_tokens || 0).toLocaleString("fr-FR");
    const output = Number(usage.local_output_tokens || 0).toLocaleString("fr-FR");
    const runs = Number(usage.local_runs || 0).toLocaleString("fr-FR");
    return `Tokens ce mois (app) : ${input} entrée · ${output} sortie · ${runs} conversion(s)`;
});

function submitDofusdb() {
    emit("confirm", { mode: mode.value, force: force.value });
}

function submitAi() {
    emit("convert");
}
</script>

<template>
    <Modal :open="open" size="lg" placement="middle-center" close-on-esc @close="emit('close')">
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">Sources de la fiche</div>
                <Btn size="sm" variant="ghost" @click="emit('close')">Fermer</Btn>
            </div>
        </template>

        <div class="space-y-4">
            <div v-if="showDofusdb && showAi" class="tabs tabs-boxed bg-base-200/60 p-1 w-fit">
                <button
                    type="button"
                    class="tab"
                    :class="{ 'tab-active': pane === 'dofusdb' }"
                    @click="pane = 'dofusdb'"
                >
                    DofusDB
                </button>
                <button type="button" class="tab" :class="{ 'tab-active': pane === 'ia' }" @click="pane = 'ia'">
                    Conversion IA
                </button>
            </div>

            <div v-if="pane === 'dofusdb' && showDofusdb" class="space-y-4">
                <p class="text-sm text-base-content/80">
                    Mettre à jour « {{ entityLabel }} » depuis DofusDB (aperçu puis écriture).
                </p>

                <div v-if="loading" class="text-sm text-base-content/60">Chargement de l’aperçu…</div>
                <p v-else-if="error" class="text-sm text-error">{{ error }}</p>
                <div v-else class="space-y-3">
                    <p v-if="convertedName" class="text-sm">
                        Nom converti : <span class="font-medium">{{ convertedName }}</span>
                    </p>
                    <p v-if="preview?.data?.dofusdb_id" class="text-xs text-base-content/60">
                        DofusDB #{{ preview.data.dofusdb_id }}
                    </p>
                    <ul
                        v-if="validationErrors.length"
                        class="list-disc space-y-1 pl-5 text-sm text-warning"
                    >
                        <li v-for="(err, index) in validationErrors" :key="`ve-${index}`">
                            {{ err.message || err.path || JSON.stringify(err) }}
                        </li>
                    </ul>

                    <fieldset class="space-y-2">
                        <legend class="text-sm font-medium">Mode</legend>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="mode" type="radio" class="radio radio-sm" value="full" />
                            Contenu + images
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="mode" type="radio" class="radio radio-sm" value="images_only" />
                            Images seules
                        </label>
                    </fieldset>

                    <label v-if="playable && isAdmin" class="flex items-center gap-2 text-sm">
                        <input v-model="force" type="checkbox" class="checkbox checkbox-sm" />
                        Forcer l’écrasement d’une fiche jouable / archivée
                    </label>
                    <p v-else-if="playable" class="text-sm text-warning">
                        Fiche jouable : un administrateur doit forcer la mise à jour.
                    </p>
                </div>

                <div class="flex justify-end gap-2">
                    <Btn variant="ghost" :disabled="applying" @click="emit('close')">Annuler</Btn>
                    <Btn
                        color="primary"
                        :disabled="loading || applying || Boolean(error) || (playable && !isAdmin)"
                        @click="submitDofusdb"
                    >
                        <Icon source="fa-arrow-rotate-right" pack="solid" alt="" class="mr-2" />
                        {{ applying ? "Mise à jour…" : "Confirmer DofusDB" }}
                    </Btn>
                </div>
            </div>

            <div v-else-if="pane === 'ia' && showAi" class="space-y-4">
                <p class="text-sm text-base-content/80">
                    Conversion IA de « {{ entityLabel }} » — {{ aiActionLabel }}. Proposition en état
                    <span class="font-medium">auto</span>, jamais jouable.
                </p>
                <p v-if="estimateLabel" class="text-sm text-base-content/70">
                    Coût estimé : <span class="font-medium">{{ estimateLabel }}</span>
                </p>
                <p v-if="remainingUsageLabel" class="text-sm text-base-content/70" data-testid="ia-source-remaining">
                    {{ remainingUsageLabel }}
                </p>
                <TextareaField
                    :model-value="aiBrief"
                    label="Brief MJ (optionnel)"
                    helper="Ex. chef Bouftou niveau 10, pas un boss."
                    rows="4"
                    default-label-position="top"
                    @update:model-value="(v) => emit('update:aiBrief', v)"
                />
                <p v-if="aiError" class="text-sm text-error">{{ aiError }}</p>
                <p v-if="aiSuccess" class="text-sm text-success">{{ aiSuccess }}</p>
                <div class="flex justify-end gap-2">
                    <Btn variant="ghost" :disabled="aiSubmitting" @click="emit('close')">Annuler</Btn>
                    <Btn color="primary" :disabled="aiSubmitting" @click="submitAi">
                        <Icon source="fa-wand-magic-sparkles" pack="solid" alt="" class="mr-2" />
                        {{ aiSubmitting ? "Conversion…" : "Lancer la conversion" }}
                    </Btn>
                </div>
            </div>
        </div>
    </Modal>
</template>
