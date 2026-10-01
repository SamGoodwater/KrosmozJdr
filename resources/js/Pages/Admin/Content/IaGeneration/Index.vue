<script setup>
/**
 * Admin — réglages IA métier (champs figés, caracs, étalons).
 * Types d’entité en onglets colonne (`SidebarNav`, même pattern que caractéristiques).
 */
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePageForms } from "@/Composables/form/usePageForms";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { useProtectedAdminAction } from "@/Composables/auth/useProtectedAdminAction";
import { getEntityIconUrl } from "@/config/entities";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import { ACTION } from "@/Utils/atomic-design/actionLabels";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import SidebarNav from "@/Pages/Organismes/layout/SidebarNav.vue";
import EntityPanel from "@/Pages/Admin/Content/IaGeneration/EntityPanel.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";

defineOptions({ layout: AdminArea });

const props = defineProps({
    config: { type: Object, required: true },
    is_stored: { type: Boolean, default: false },
    updated_at: { type: String, default: null },
    entity_labels: { type: Object, required: true },
    characteristic_options: { type: Object, default: () => ({}) },
    items_seeder: {
        type: Object,
        default: () => ({
            relative_root: "",
            file_count: 0,
            auto_count: 0,
            can_export: false,
            can_import: false,
            allowed: false,
        }),
    },
    usage: { type: Object, default: () => ({ available: false, message: "" }) },
    estimates: { type: Array, default: () => [] },
    has_api_key: { type: Boolean, default: false },
    available_models: { type: Array, default: () => [] },
    invalid_example_refs: { type: Object, default: () => ({}) },
});

const page = usePage();
const notificationStore = useNotificationStore();
const {
    showPasswordModal,
    passwordModalTitle,
    passwordModalMessage,
    passwordModalConfirmLabel,
    requirePassword,
    onPasswordConfirmed,
    onPasswordModalCancel,
} = useProtectedAdminAction();

const form = useForm({
    supervisor_prompt: props.config.supervisor_prompt ?? "",
    generation: {
        max_retries: props.config.generation?.max_retries ?? 2,
        few_shot_count: props.config.generation?.few_shot_count ?? 8,
        max_effects_per_spell: props.config.generation?.max_effects_per_spell ?? 3,
        model: props.config.generation?.model ?? "claude-haiku-4-5",
        prompt_cache: props.config.generation?.prompt_cache !== false,
    },
    entities: cloneEntities(props.config.entities),
});

const entityKeys = computed(() => Object.keys(props.entity_labels || {}));

const modelOptions = computed(() =>
    (props.available_models || []).map((row) => ({
        value: row.id,
        label: row.label,
    }))
);

const selectedModelHint = computed(() => {
    const id = form.generation.model;
    const row = (props.available_models || []).find((item) => item.id === id);
    return row?.hint || "Modèle envoyé à Anthropic pour chaque conversion.";
});

/** Classes DaisyUI figées (interdit de interpoler des tokens Tailwind). */
const ENTITY_NAV_CLASSES = {
    item: "color-indigo-500 box-shadow-glass-xs",
    spell: "color-violet-500 box-shadow-glass-xs",
    monster: "color-pink-500 box-shadow-glass-xs",
    npc: "color-green-500 box-shadow-glass-xs",
    consumable: "color-orange-500 box-shadow-glass-xs",
};

const entityItems = computed(() =>
    entityKeys.value.map((key) => ({
        key,
        label: props.entity_labels[key],
    }))
);

const activeEntityKey = ref(entityKeys.value[0] || "item");

const activeEntityLabel = computed(() => props.entity_labels[activeEntityKey.value] || "");

/**
 * Active l’onglet d’un type d’entité (colonne gauche, comme caractéristiques).
 *
 * @param {{ key?: string }} item
 * @returns {void}
 */
function selectEntity(item) {
    if (item?.key) {
        activeEntityKey.value = item.key;
    }
}

const localTokensLabel = computed(() => {
    const usage = props.usage || {};
    const input = Number(usage.local_input_tokens || 0).toLocaleString("fr-FR");
    const output = Number(usage.local_output_tokens || 0).toLocaleString("fr-FR");
    const runs = Number(usage.local_runs || 0).toLocaleString("fr-FR");
    return `${input} entrée · ${output} sortie · ${runs} conversion(s)`;
});

const remainingCreditLabel = computed(() => {
    const remaining = props.usage?.remaining_credits_usd;
    if (typeof remaining !== "number") {
        return "Inconnu (clé org Anthropic ou usage fournisseur indisponible)";
    }
    return `${Number(remaining).toLocaleString("fr-FR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} $`;
});

const lingeringInvalid = computed(() => {
    const invalid = props.invalid_example_refs || {};
    const rows = [];
    for (const key of entityKeys.value) {
        const bad = new Set((invalid[key] || []).map((ref) => String(ref)));
        const current = form.entities[key]?.example_ids || [];
        const refs = current.map((id) => String(id)).filter((id) => bad.has(id));
        if (refs.length > 0) {
            rows.push({
                key,
                label: props.entity_labels[key] || key,
                refs,
            });
        }
    }
    return rows;
});

const invalidExampleCount = computed(() =>
    lingeringInvalid.value.reduce((sum, row) => sum + row.refs.length, 0),
);

const formErrorMessages = computed(() => {
    const errors = form.errors || {};
    return Object.values(errors)
        .flatMap((value) => (Array.isArray(value) ? value : [value]))
        .filter((value) => typeof value === "string" && value !== "");
});

const updatedLabel = computed(() => {
    if (!props.updated_at) {
        return null;
    }
    const d = new Date(props.updated_at);
    if (Number.isNaN(d.getTime())) {
        return null;
    }
    return d.toLocaleString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

function cloneEntities(entities) {
    const source = entities && typeof entities === "object" ? entities : {};
    const out = {};
    for (const key of ["item", "spell", "monster", "npc", "consumable"]) {
        const row = source[key] && typeof source[key] === "object" ? source[key] : {};
        out[key] = {
            has_dofus_source: Boolean(row.has_dofus_source),
            frozen_fields: row.frozen_fields === "*" ? "*" : [...(row.frozen_fields || [])],
            writable_fields: [...(row.writable_fields || [])],
            frozen_characteristics:
                row.frozen_characteristics === "*" ? "*" : [...(row.frozen_characteristics || [])],
            writable_characteristics: [...(row.writable_characteristics || [])],
            example_ids: [...(row.example_ids || [])],
            task_prompt: row.task_prompt || "",
        };
        if (key === "item") {
            out[key].few_shot_panoplies = [...(row.few_shot_panoplies || [])];
        }
    }
    return out;
}

/**
 * Retire du formulaire les étalons que le serveur a marqués comme non jouables.
 *
 * @returns {void}
 */
function dropInvalidExamples() {
    for (const row of lingeringInvalid.value) {
        const bad = new Set(row.refs);
        const current = form.entities[row.key];
        if (!current) {
            continue;
        }
        form.entities[row.key] = {
            ...current,
            example_ids: (current.example_ids || []).filter((id) => !bad.has(String(id))),
        };
    }
}

/**
 * @param {Record<string, string|string[]>} errors
 * @returns {void}
 */
function onSaveError(errors) {
    const first = Object.values(errors || {})[0];
    const message = Array.isArray(first) ? first[0] : first;
    notificationStore.error(
        typeof message === "string" && message !== ""
            ? message
            : "Enregistrement refusé. Vérifie les étalons jouables.",
    );
    const entityKey = Object.keys(errors || {})
        .map((key) => key.match(/^entities\.([^.]+)\./)?.[1])
        .find((key) => key && form.entities[key]);
    if (entityKey) {
        activeEntityKey.value = entityKey;
    }
}

/** Callbacks de `usePageForms` en attente de la confirmation du mot de passe. */
let pendingSaveCallbacks = null;

/**
 * @param {{ onSuccess: Function, onError: Function, onCancel: Function }} callbacks
 * @returns {void}
 */
function submitConfig(callbacks) {
    pendingSaveCallbacks = callbacks;
    requirePassword(
        "Enregistrer les réglages IA",
        "Ces réglages contrôlent ce que l’IA a le droit de modifier. Confirme ton mot de passe.",
        ACTION.save.label,
        () => {
            pendingSaveCallbacks = null;
            form.put(route("admin.content.ia-generation.update"), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    notificationStore.success("Réglages IA enregistrés.");
                    callbacks.onSuccess();
                },
                onError: (errors) => {
                    onSaveError(errors);
                    callbacks.onError(errors);
                },
                onCancel: callbacks.onCancel,
            });
        }
    );
}

const pageForms = usePageForms();
pageForms.register("config", form, submitConfig);

function onPasswordCancel() {
    onPasswordModalCancel();
    pendingSaveCallbacks?.onCancel();
    pendingSaveCallbacks = null;
}

function resetToFile() {
    requirePassword(
        "Reprendre le fichier du dépôt",
        "La version en base sera effacée. On reprend le fichier du dépôt.",
        "Reprendre le fichier",
        () => {
            form.delete(route("admin.content.ia-generation.destroy"), {
                preserveScroll: true,
                onSuccess: () => notificationStore.success("Fichier du dépôt rétabli."),
            });
        }
    );
}

const seederForm = useForm({});

function exportItemsToFiles() {
    requirePassword(
        "Écrire les objets auto dans le dépôt",
        `Les objets à l’état auto remplaceront le contenu de ${props.items_seeder.relative_root}. Les fichiers qui ne correspondent plus seront supprimés.`,
        "Écrire les fichiers",
        () => {
            seederForm.post(route("admin.content.ia-generation.items-seeder.export"), {
                preserveScroll: true,
            });
        }
    );
}

function importItemsFromFiles() {
    requirePassword(
        "Rejouer les fichiers d’objets en base",
        "Les objets décrits par les fichiers du dépôt seront créés ou mis à jour en base.",
        "Rejouer en base",
        () => {
            seederForm.post(route("admin.content.ia-generation.items-seeder.import"), {
                preserveScroll: true,
            });
        }
    );
}
</script>

<template>
    <Head title="IA métier" />

    <div class="space-y-6 pb-8">
        <PageHeader
            title="IA métier"
            subtitle="Ce que le modèle a le droit de modifier, par type d’entité. Tant que rien n’est enregistré ici, c’est le JSON du dépôt qui s’applique."
            :forms="pageForms"
        >
            <template #meta>
                <span v-if="is_stored" class="badge badge-ghost badge-sm" data-testid="ia-stored-badge">
                    Version en base<template v-if="updatedLabel"> · {{ updatedLabel }}</template>
                </span>
                <span v-else class="badge badge-ghost badge-sm">Fichier du dépôt</span>
            </template>
            <template #actions>
                <Btn
                    type="button"
                    variant="ghost"
                    size="sm"
                    :disabled="form.processing || !is_stored"
                    data-testid="ia-reset-to-file"
                    @click="resetToFile"
                >
                    <i :class="ACTION.discard.icon" class="mr-1.5" aria-hidden="true"></i>
                    Reprendre le fichier du dépôt
                </Btn>
            </template>
        </PageHeader>

        <section
            v-if="lingeringInvalid.length"
            class="rounded-box border border-warning/40 bg-warning/10 px-4 py-3 space-y-3"
            data-testid="ia-invalid-examples"
        >
            <p class="text-sm text-base-content">
                <span class="font-medium">{{ invalidExampleCount }} étalon(s)</span>
                de ce réglage ne correspondent pas à une fiche jouable.
                Enregistrer exige que chaque étalon le soit : retire-les, ou passe les fiches en Jouable
                puis sélectionne-les dans l’onglet du type.
            </p>
            <ul class="text-sm space-y-1">
                <li v-for="row in lingeringInvalid" :key="row.key">
                    <button type="button" class="link link-hover font-medium" @click="selectEntity(row)">
                        {{ row.label }}
                    </button>
                    — {{ row.refs.join(", ") }}
                </li>
            </ul>
            <Btn type="button" variant="outline" data-testid="ia-drop-invalid-examples" @click="dropInvalidExamples">
                Retirer les étalons introuvables
            </Btn>
        </section>

        <p
            v-if="page.props.flash?.success"
            class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2"
        >
            {{ page.props.flash.success }}
        </p>
        <p
            v-if="page.props.flash?.error"
            class="text-error text-sm rounded-box border border-error/30 bg-error/10 px-3 py-2"
        >
            {{ page.props.flash.error }}
        </p>

        <form class="space-y-6" @submit.prevent="pageForms.saveAll()">
            <section
                class="rounded-box border border-base-300 bg-base-100/50 p-4 space-y-3"
                data-testid="ia-usage"
            >
                <h2 class="text-lg font-semibold text-base-content">Solde et coûts</h2>
                <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-base-content/60">Tokens ce mois (cette app)</dt>
                        <dd class="font-medium">{{ localTokensLabel }}</dd>
                    </div>
                    <div>
                        <dt class="text-base-content/60">Crédit Anthropic restant</dt>
                        <dd class="font-medium">{{ remainingCreditLabel }}</dd>
                    </div>
                    <div v-if="usage?.remaining_hint" class="sm:col-span-2">
                        <dt class="text-base-content/60">Capacité restante</dt>
                        <dd class="font-medium">{{ usage.remaining_hint }}</dd>
                    </div>
                </dl>
                <p class="text-sm text-base-content/70">{{ usage.message || "Usage indisponible." }}</p>
                <p v-if="!has_api_key" class="text-sm text-warning">
                    Aucune clé <code>ANTHROPIC_API_KEY</code> : la génération est bloquée, les estimés restent affichés.
                </p>
                <ul class="text-sm space-y-1" data-testid="ia-estimates">
                    <li v-for="row in estimates" :key="row.action">
                        <span class="font-medium">{{ row.label }}</span>
                        — {{ row.formatted }}
                        <span class="text-base-content/60"> ({{ row.hint }})</span>
                    </li>
                </ul>
            </section>

            <TextareaField
                v-model="form.supervisor_prompt"
                label="Prompt superviseur"
                helper="Court, stable, mis en cache. Interdits + sortie JSON uniquement."
                rows="6"
                default-label-position="top"
                :error="form.errors.supervisor_prompt"
            />

            <section class="rounded-box border border-base-300 bg-base-100/50 p-4 space-y-4">
                <div class="grid gap-4 md:grid-cols-2" data-testid="ia-model-cache">
                    <SelectField
                        v-model="form.generation.model"
                        label="Modèle Anthropic"
                        :helper="selectedModelHint"
                        :options="modelOptions"
                        :searchable="false"
                        required
                        default-label-position="top"
                        :error="form.errors['generation.model']"
                    />
                    <label class="label cursor-pointer justify-start gap-3 items-start py-0">
                        <input
                            type="checkbox"
                            class="checkbox checkbox-primary mt-1"
                            data-testid="ia-prompt-cache"
                            :checked="form.generation.prompt_cache"
                            @change="form.generation.prompt_cache = $event.target.checked"
                        />
                        <span>
                            <span class="label-text font-medium text-base-content">Cache prompt Anthropic</span>
                            <span class="block text-sm text-base-content/60 font-normal">
                                Préfixe (règles, schéma, exemples) réutilisé 5 min : un hit coûte ~10 % du
                                prix d’entrée. Laisser coché, sauf pour déboguer.
                            </span>
                        </span>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <InputField
                        v-model="form.generation.max_retries"
                        type="number"
                        label="Tentatives de correction"
                        helper="0 à 5 retries si le validateur refuse le JSON."
                        default-label-position="top"
                        :error="form.errors['generation.max_retries']"
                    />
                    <InputField
                        v-model="form.generation.few_shot_count"
                        type="number"
                        label="Nombre d’exemples"
                        helper="Fiches playable envoyées au modèle (plafond)."
                        default-label-position="top"
                        :error="form.errors['generation.few_shot_count']"
                    />
                    <InputField
                        v-model="form.generation.max_effects_per_spell"
                        type="number"
                        label="Effets max par sort"
                        helper="1 effet principal + secondaires, plafond JDR."
                        default-label-position="top"
                        :error="form.errors['generation.max_effects_per_spell']"
                    />
                </div>
            </section>

            <div
                class="flex min-h-0 w-full flex-col lg:flex-row"
                data-testid="ia-entity-tabs"
            >
                <SidebarNav
                    title="Types d’entité"
                    description="Gel, étalons et prompt de tâche. Un type à la fois."
                    :items="entityItems"
                    :get-item-href="() => null"
                    :get-item-click="selectEntity"
                    :get-item-label="(item) => item.label"
                    :get-item-key="(item) => item.key"
                    :is-item-active="(item) => item.key === activeEntityKey"
                    :get-item-icon-url="(item) => getEntityIconUrl(item.key)"
                    :get-item-css-classes="(item) => ENTITY_NAV_CLASSES[item.key] || ''"
                />
                <div class="min-w-0 flex-1 overflow-y-auto p-4 sm:p-6">
                    <EntityPanel
                        v-if="form.entities[activeEntityKey]"
                        :key="activeEntityKey"
                        :entity="activeEntityKey"
                        :label="activeEntityLabel"
                        :model-value="form.entities[activeEntityKey]"
                        :characteristic-options="characteristic_options[activeEntityKey] || []"
                        :invalid-example-refs="invalid_example_refs[activeEntityKey] || []"
                        @update:model-value="(row) => (form.entities[activeEntityKey] = row)"
                    />
                </div>
            </div>

            <ul
                v-if="formErrorMessages.length"
                class="text-sm text-error space-y-1 rounded-box border border-error/30 bg-error/10 px-3 py-2"
                data-testid="ia-form-errors"
            >
                <li v-for="(message, index) in formErrorMessages" :key="index">{{ message }}</li>
            </ul>
        </form>

        <section class="rounded-box border border-base-300 bg-base-100/50 p-4 space-y-3">
            <div>
                <h2 class="text-lg font-semibold text-base-content">Étalons d’équipement</h2>
                <p class="mt-1 text-sm text-base-content/70 max-w-3xl">
                    Les objets relus à la main sont versionnés en JSON dans le dépôt, un fichier par
                    objet. On peut donc recréer ce socle sur n’importe quelle base pour faire tourner
                    l’IA, et repartir de la base après une session de relecture.
                </p>
                <p class="mt-2 text-sm text-base-content/70">
                    <span class="font-medium">{{ items_seeder.file_count }}</span> fichier(s) dans
                    <code class="text-xs">{{ items_seeder.relative_root }}</code> ·
                    <span class="font-medium">{{ items_seeder.auto_count }}</span> objet(s)
                    auto en base.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <Btn
                    type="button"
                    variant="outline"
                    :disabled="seederForm.processing || !items_seeder.allowed || !items_seeder.can_export"
                    @click="exportItemsToFiles"
                >
                    Base → fichiers
                </Btn>
                <Btn
                    type="button"
                    variant="outline"
                    :disabled="seederForm.processing || !items_seeder.allowed || !items_seeder.can_import"
                    @click="importItemsFromFiles"
                >
                    Fichiers → base
                </Btn>
            </div>

            <p v-if="!items_seeder.allowed" class="text-sm text-base-content/60">
                Réservé au super administrateur.
            </p>
            <p v-else-if="!items_seeder.can_export" class="text-sm text-base-content/60">
                L’écriture dans le dépôt n’est possible qu’en développement. Le rejeu en base reste
                disponible.
            </p>
        </section>
    </div>

    <ConfirmPasswordModal
        v-model:open="showPasswordModal"
        :title="passwordModalTitle"
        :message="passwordModalMessage"
        :confirm-label="passwordModalConfirmLabel"
        @confirmed="onPasswordConfirmed"
        @cancel="onPasswordCancel"
    />
</template>
