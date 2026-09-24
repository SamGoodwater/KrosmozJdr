<script setup>
/**
 * Modal unique DofusDB + conversion IA + injection JSON (une icône menu).
 *
 * @example
 * <EntitySourceModal :open="source.open" :show-dofusdb="true" :show-ai="isAdmin" />
 */
import { computed, ref, watch } from "vue";
import axios from "axios";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import EntityUpdateDiffView from "@/Pages/Molecules/entity/EntityUpdateDiffView.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useProtectedAdminAction } from "@/Composables/auth/useProtectedAdminAction";

const props = defineProps({
    open: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    applying: { type: Boolean, default: false },
    preview: { type: Object, default: null },
    error: { type: String, default: "" },
    playable: { type: Boolean, default: false },
    entityLabel: { type: String, default: "cette fiche" },
    entityType: { type: String, default: "" },
    showDofusdb: { type: Boolean, default: true },
    showAi: { type: Boolean, default: false },
    /** Injection JSON (admin) — indépendant de la conversion LLM. */
    showJson: { type: Boolean, default: false },
    aiBrief: { type: String, default: "" },
    aiSubmitting: { type: Boolean, default: false },
    aiError: { type: String, default: "" },
    aiSuccess: { type: String, default: "" },
    aiEstimate: { type: Object, default: null },
    aiUsage: { type: Object, default: null },
    aiActionLabel: { type: String, default: "Conversion IA" },
    diff: { type: Object, default: null },
    diffBusy: { type: Boolean, default: false },
});

const emit = defineEmits([
    "close",
    "confirm",
    "update:aiBrief",
    "convert",
    "inject",
    "save-diff",
    "restore-diff",
]);

const { isAdmin } = usePermissions();
const {
    isAdminUnlocked,
    showPasswordModal,
    passwordModalTitle,
    passwordModalMessage,
    passwordModalConfirmLabel,
    requirePassword,
    onPasswordConfirmed,
    onPasswordModalCancel,
} = useProtectedAdminAction();
const iaUnlocked = computed(() => Boolean(isAdminUnlocked?.value));
const updateContent = ref(true);
const includeImage = ref(true);
const force = ref(false);
const pane = ref(resolveDefaultPane(props));
const localUsage = ref(null);
const localEstimate = ref(null);
const jsonText = ref("");
const jsonLocalError = ref("");
const jsonExampleLoading = ref(false);
const jsonDropActive = ref(false);
const jsonFileInput = ref(null);

function resolveDefaultPane(p) {
    if (p.showDofusdb) return "dofusdb";
    if (p.showAi) return "ia";
    if (p.showJson) return "json";
    return "dofusdb";
}

watch(
    () => props.open,
    (open) => {
        if (open) {
            updateContent.value = true;
            includeImage.value = true;
            force.value = Boolean(props.playable);
            pane.value = resolveDefaultPane(props);
            localUsage.value = null;
            localEstimate.value = null;
            jsonText.value = "";
            jsonLocalError.value = "";
            jsonDropActive.value = false;
            if (!props.showDofusdb && (props.showAi || props.showJson)) {
                promptIaUnlock();
            }
        }
    },
    { immediate: true }
);

watch(
    () => [props.showDofusdb, props.showAi, props.showJson],
    () => {
        if (pane.value === "dofusdb" && !props.showDofusdb) {
            pane.value = resolveDefaultPane(props);
        }
        if (pane.value === "ia" && !props.showAi) {
            pane.value = resolveDefaultPane(props);
        }
        if (pane.value === "json" && !props.showJson) {
            pane.value = resolveDefaultPane(props);
        }
    }
);

const tabCount = computed(
    () => [props.showDofusdb, props.showAi, props.showJson].filter(Boolean).length
);
const showSourceTabs = computed(() => tabCount.value > 1);

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
    const row = localEstimate.value ?? props.aiEstimate;
    if (!row) return "";
    return row.formatted || row.label || "";
});

const hasDiff = computed(() => Boolean(props.diff && typeof props.diff === "object"));

const activeUsage = computed(() => localUsage.value ?? props.aiUsage);

const missingApiKey = computed(() => activeUsage.value && activeUsage.value.has_api_key === false);

const remainingUsageLabel = computed(() => {
    if (!iaUnlocked.value) {
        return "Confirme ton mot de passe pour voir le solde et lancer une conversion.";
    }
    const usage = activeUsage.value;
    if (!usage || typeof usage !== "object") return "Solde : chargement…";
    if (usage.has_api_key === false) {
        return "Clé Anthropic absente : aucun appel ne sera lancé.";
    }
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
    if (!updateContent.value && !includeImage.value) {
        return;
    }
    const mode = updateContent.value ? "full" : "images_only";
    emit("confirm", { mode, includeImage: includeImage.value, force: force.value });
}

const canSubmitDofusdb = computed(() => updateContent.value || includeImage.value);

const iaUnlockTitle = "Déverrouiller l’IA";
const iaUnlockMessage =
    "Confirme ton mot de passe pour utiliser la conversion IA. Une fois déverrouillé, l’accès reste ouvert pendant un moment d’inactivité, comme la gestion admin.";

async function loadAiStatusAfterUnlock() {
    try {
        const { data } = await axios.get("/api/ia/status", { headers: { Accept: "application/json" } });
        localUsage.value = data?.usage && typeof data.usage === "object" ? data.usage : null;
        const estimates = Array.isArray(data?.estimates) ? data.estimates : [];
        const currentAction = props.aiEstimate?.action;
        localEstimate.value = currentAction
            ? estimates.find((row) => row.action === currentAction) || props.aiEstimate
            : props.aiEstimate;
    } catch {
        localUsage.value = props.aiUsage;
    }
}

function promptIaUnlock() {
    if (iaUnlocked.value) {
        void loadAiStatusAfterUnlock();
        return;
    }
    requirePassword(iaUnlockTitle, iaUnlockMessage, "Déverrouiller", () => {
        void loadAiStatusAfterUnlock();
    });
}

function selectIaPane() {
    pane.value = "ia";
    promptIaUnlock();
}

function selectJsonPane() {
    pane.value = "json";
    promptIaUnlock();
}

function submitAi() {
    requirePassword(iaUnlockTitle, iaUnlockMessage, "Déverrouiller", () => {
        emit("convert");
    });
}

function parseJsonText() {
    const raw = String(jsonText.value || "").trim();
    if (raw === "") {
        jsonLocalError.value = "Colle ou dépose un JSON non vide.";
        return null;
    }
    try {
        const decoded = JSON.parse(raw);
        if (!decoded || typeof decoded !== "object" || Array.isArray(decoded)) {
            jsonLocalError.value = "Le JSON racine doit être un objet.";
            return null;
        }
        jsonLocalError.value = "";
        return decoded;
    } catch (error) {
        jsonLocalError.value = error?.message ? `JSON invalide : ${error.message}` : "JSON invalide.";
        return null;
    }
}

function submitJson() {
    const payload = parseJsonText();
    if (!payload) return;
    requirePassword(iaUnlockTitle, iaUnlockMessage, "Déverrouiller", () => {
        emit("inject", { payload });
    });
}

async function loadJsonExample() {
    const entityType = String(props.entityType || "").trim();
    if (!entityType || jsonExampleLoading.value) return;
    jsonExampleLoading.value = true;
    jsonLocalError.value = "";
    try {
        const { data } = await axios.get(`/api/ia/schema/${encodeURIComponent(entityType)}`, {
            headers: { Accept: "application/json" },
        });
        if (data?.example && typeof data.example === "object") {
            jsonText.value = JSON.stringify(data.example, null, 2);
        } else {
            jsonLocalError.value = "Aucun exemple disponible pour ce type.";
        }
    } catch (error) {
        if (error?.response?.status === 423) {
            promptIaUnlock();
            jsonLocalError.value = "Confirme ton mot de passe pour charger l’exemple.";
        } else {
            jsonLocalError.value = String(
                error?.response?.data?.message || "Impossible de charger l’exemple de schéma."
            );
        }
    } finally {
        jsonExampleLoading.value = false;
    }
}

function onJsonFileSelected(event) {
    const file = event?.target?.files?.[0];
    if (file) {
        void readJsonFile(file);
    }
    if (event?.target) {
        event.target.value = "";
    }
}

function onJsonDrop(event) {
    jsonDropActive.value = false;
    const file = event?.dataTransfer?.files?.[0];
    if (file) {
        void readJsonFile(file);
    }
}

async function readJsonFile(file) {
    if (!file || typeof file.text !== "function") {
        jsonLocalError.value = "Fichier illisible.";
        return;
    }
    const name = String(file.name || "").toLowerCase();
    if (name && !name.endsWith(".json") && file.type && !file.type.includes("json") && !file.type.includes("text")) {
        jsonLocalError.value = "Utilise un fichier .json.";
        return;
    }
    try {
        const text = await file.text();
        jsonText.value = text;
        parseJsonText();
    } catch {
        jsonLocalError.value = "Impossible de lire le fichier.";
    }
}
</script>

<template>
    <Modal
        :open="open"
        :size="hasDiff ? 'xl' : 'lg'"
        placement="middle-center"
        close-on-esc
        @close="hasDiff ? emit('restore-diff') : emit('close')"
    >
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">
                    {{ hasDiff ? "Avant / après" : "Sources de la fiche" }}
                </div>
                <Btn v-if="!hasDiff" size="sm" variant="ghost" @click="emit('close')">Fermer</Btn>
            </div>
        </template>

        <div class="space-y-4">
            <EntityUpdateDiffView
                v-if="hasDiff"
                :diff="diff"
                :busy="diffBusy"
                @save="(payload) => emit('save-diff', payload)"
                @restore="emit('restore-diff')"
            />

            <div
                v-else-if="showSourceTabs"
                role="tablist"
                class="tabs tabs-box tabs-sm bg-base-200/60 p-1 w-fit"
                data-testid="entity-source-tabs"
            >
                <button
                    v-if="showDofusdb"
                    type="button"
                    role="tab"
                    class="tab"
                    :class="{ 'tab-active': pane === 'dofusdb' }"
                    :aria-selected="pane === 'dofusdb' ? 'true' : 'false'"
                    data-testid="entity-source-tab-dofusdb"
                    @click="pane = 'dofusdb'"
                >
                    Conversion DofusDB
                </button>
                <button
                    v-if="showAi"
                    type="button"
                    role="tab"
                    class="tab"
                    :class="{ 'tab-active': pane === 'ia' }"
                    :aria-selected="pane === 'ia' ? 'true' : 'false'"
                    data-testid="entity-source-tab-ia"
                    @click="selectIaPane"
                >
                    Conversion IA
                </button>
                <button
                    v-if="showJson"
                    type="button"
                    role="tab"
                    class="tab"
                    :class="{ 'tab-active': pane === 'json' }"
                    :aria-selected="pane === 'json' ? 'true' : 'false'"
                    data-testid="entity-source-tab-json"
                    @click="selectJsonPane"
                >
                    JSON
                </button>
            </div>

            <div v-if="!hasDiff && pane === 'dofusdb' && showDofusdb" class="space-y-4">
                <p class="text-sm text-base-content/80">
                    Conversion algorithmique de « {{ entityLabel }} » depuis DofusDB (aperçu puis
                    écriture). Tu choisis le contenu, l’image, ou les deux.
                </p>

                <div v-if="loading" class="text-sm text-base-content/60">Chargement de l’aperçu…</div>
                <p
                    v-else-if="error"
                    class="rounded-box border border-error/40 bg-error/10 px-3 py-2 text-sm text-error"
                >
                    {{ error }}
                </p>
                <div v-if="!loading" class="space-y-3">
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
                        <legend class="text-sm font-medium">Quoi récupérer</legend>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="updateContent" type="checkbox" class="checkbox checkbox-sm" />
                            Contenu (conversion algorithmique)
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                v-model="includeImage"
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                data-testid="entity-source-include-image"
                            />
                            Récupérer l’image
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
                        :disabled="loading || applying || (playable && !isAdmin) || !canSubmitDofusdb"
                        @click="submitDofusdb"
                    >
                        <Icon source="fa-arrow-rotate-right" pack="solid" alt="" class="mr-2" />
                        {{ applying ? "Mise à jour…" : "Confirmer DofusDB" }}
                    </Btn>
                </div>
            </div>

            <div v-else-if="!hasDiff && pane === 'ia' && showAi" class="space-y-4">
                <p class="text-sm text-base-content/80">
                    Conversion IA de « {{ entityLabel }} » — {{ aiActionLabel }}. Proposition en état
                    <span class="font-medium">auto</span>, jamais jouable.
                </p>
                <template v-if="!iaUnlocked">
                    <p class="text-sm text-base-content/70" data-testid="ia-source-remaining">
                        {{ remainingUsageLabel }}
                    </p>
                    <p class="text-sm text-base-content/70">
                        Comme la gestion admin, l’IA n’est disponible qu’après confirmation du mot de passe.
                    </p>
                    <div class="flex justify-end gap-2">
                        <Btn variant="ghost" @click="emit('close')">Annuler</Btn>
                        <Btn color="primary" data-testid="ia-unlock" @click="promptIaUnlock">
                            <Icon source="fa-lock" pack="solid" alt="" class="mr-2" />
                            Déverrouiller l’IA
                        </Btn>
                    </div>
                </template>
                <template v-else>
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
                    <p v-if="missingApiKey" class="text-sm text-warning">
                        Aucune clé Anthropic configurée : la conversion est bloquée. Ce n’est pas un succès.
                    </p>
                    <p v-if="aiError" class="text-sm text-error">{{ aiError }}</p>
                    <p v-if="aiSuccess" class="text-sm text-success">{{ aiSuccess }}</p>
                    <div class="flex justify-end gap-2">
                        <Btn variant="ghost" :disabled="aiSubmitting" @click="emit('close')">Annuler</Btn>
                        <Btn color="primary" :disabled="aiSubmitting || missingApiKey" @click="submitAi">
                            <Icon source="fa-wand-magic-sparkles" pack="solid" alt="" class="mr-2" />
                            {{ aiSubmitting ? "Conversion…" : "Lancer la conversion" }}
                        </Btn>
                    </div>
                </template>
            </div>

            <div v-else-if="!hasDiff && pane === 'json' && showJson" class="space-y-4" data-testid="entity-source-json-pane">
                <p class="text-sm text-base-content/80">
                    Colle ou dépose un paquet JSON au format IA. Même validation et écriture
                    <span class="font-medium">auto</span> que la conversion, sans appel LLM.
                </p>
                <template v-if="!iaUnlocked">
                    <p class="text-sm text-base-content/70">
                        Déverrouille avec ton mot de passe admin pour injecter un JSON.
                    </p>
                    <div class="flex justify-end gap-2">
                        <Btn variant="ghost" @click="emit('close')">Annuler</Btn>
                        <Btn color="primary" data-testid="json-unlock" @click="promptIaUnlock">
                            <Icon source="fa-lock" pack="solid" alt="" class="mr-2" />
                            Déverrouiller
                        </Btn>
                    </div>
                </template>
                <template v-else>
                    <div
                        class="rounded-box border border-dashed border-base-300 bg-base-200/40 px-3 py-4 text-center text-sm transition-colors"
                        :class="{ 'border-primary bg-primary/10': jsonDropActive }"
                        data-testid="entity-source-json-drop"
                        @dragenter.prevent="jsonDropActive = true"
                        @dragover.prevent="jsonDropActive = true"
                        @dragleave.prevent="jsonDropActive = false"
                        @drop.prevent="onJsonDrop"
                    >
                        <p class="text-base-content/80">Glisse un fichier .json ici</p>
                        <Btn
                            size="sm"
                            variant="ghost"
                            class="mt-2"
                            data-testid="entity-source-json-file-btn"
                            @click="jsonFileInput?.click()"
                        >
                            Choisir un fichier
                        </Btn>
                        <input
                            ref="jsonFileInput"
                            type="file"
                            accept=".json,application/json,text/json,text/plain"
                            class="hidden"
                            data-testid="entity-source-json-file"
                            @change="onJsonFileSelected"
                        />
                    </div>
                    <TextareaField
                        v-model="jsonText"
                        label="Paquet JSON"
                        helper="Même structure que la réponse submit_json de l’IA."
                        rows="10"
                        default-label-position="top"
                        data-testid="entity-source-json-text"
                    />
                    <div class="flex flex-wrap justify-between gap-2">
                        <Btn
                            size="sm"
                            variant="ghost"
                            :disabled="jsonExampleLoading || !entityType"
                            data-testid="entity-source-json-example"
                            @click="loadJsonExample"
                        >
                            {{ jsonExampleLoading ? "Exemple…" : "Charger un exemple" }}
                        </Btn>
                        <div class="flex gap-2">
                            <Btn variant="ghost" :disabled="aiSubmitting" @click="emit('close')">Annuler</Btn>
                            <Btn
                                color="primary"
                                :disabled="aiSubmitting"
                                data-testid="entity-source-json-submit"
                                @click="submitJson"
                            >
                                <Icon source="fa-file-code" pack="solid" alt="" class="mr-2" />
                                {{ aiSubmitting ? "Injection…" : "Injecter le JSON" }}
                            </Btn>
                        </div>
                    </div>
                    <p v-if="jsonLocalError" class="text-sm text-error" data-testid="entity-source-json-local-error">
                        {{ jsonLocalError }}
                    </p>
                    <p v-if="aiError" class="text-sm text-error">{{ aiError }}</p>
                    <p v-if="aiSuccess" class="text-sm text-success">{{ aiSuccess }}</p>
                </template>
            </div>
        </div>
    </Modal>
    <ConfirmPasswordModal
        v-model:open="showPasswordModal"
        :title="passwordModalTitle"
        :message="passwordModalMessage"
        :confirm-label="passwordModalConfirmLabel"
        @confirmed="onPasswordConfirmed"
        @cancel="onPasswordModalCancel"
    />
</template>
