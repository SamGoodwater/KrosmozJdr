<script setup>
/**
 * Admin — réglages IA métier (champs figés, caracs, étalons).
 */
import { computed } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { useProtectedAdminAction } from "@/Composables/auth/useProtectedAdminAction";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import EntityPanel from "@/Pages/Admin/Content/IaGeneration/EntityPanel.vue";

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
            playable_count: 0,
            can_export: false,
            can_import: false,
            allowed: false,
        }),
    },
    usage: { type: Object, default: () => ({ available: false, message: "" }) },
    estimates: { type: Array, default: () => [] },
    has_api_key: { type: Boolean, default: false },
});

const { setPageTitle } = usePageTitle();
setPageTitle("IA métier");

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
    },
    entities: cloneEntities(props.config.entities),
});

const entityKeys = computed(() => Object.keys(props.entity_labels || {}));

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

function save() {
    requirePassword(
        "Enregistrer les réglages IA",
        "Ces réglages contrôlent ce que l’IA a le droit de modifier. Confirme ton mot de passe.",
        "Enregistrer",
        () => {
            form.put(route("admin.content.ia-generation.update"), {
                preserveScroll: true,
                onSuccess: () => notificationStore.success("Réglages IA enregistrés."),
            });
        }
    );
}

function resetToFile() {
    requirePassword(
        "Réinitialiser les réglages IA",
        "La version en base sera effacée. On reprend le fichier du dépôt.",
        "Réinitialiser",
        () => {
            form.delete(route("admin.content.ia-generation.destroy"), {
                preserveScroll: true,
                onSuccess: () => notificationStore.success("Réglages IA réinitialisés."),
            });
        }
    );
}

const seederForm = useForm({});

function exportItemsToFiles() {
    requirePassword(
        "Écrire les objets jouables dans le dépôt",
        `Les objets à l’état jouable remplaceront le contenu de ${props.items_seeder.relative_root}. Les fichiers qui ne correspondent plus seront supprimés.`,
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

    <div class="space-y-6 pb-8 max-w-4xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">IA métier</h1>
            <p class="mt-2 text-sm text-base-content/70 max-w-3xl">
                Ce que le modèle a le droit de modifier, par type d’entité. Les valeurs sont
                enregistrées en base (pas besoin d’éditer un fichier). Tant que rien n’est sauvé
                ici, c’est le JSON du dépôt qui s’applique.
            </p>
            <p v-if="is_stored" class="mt-2 text-sm text-base-content/70">
                Version en base
                <span v-if="updatedLabel">, enregistrée le {{ updatedLabel }}</span>.
            </p>
            <p v-else class="mt-2 text-sm text-base-content/70">Aucune surcharge en base : fichier du dépôt.</p>
        </div>

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

        <form class="space-y-6" @submit.prevent="save">
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

            <section class="rounded-box border border-base-300 bg-base-100/50 p-4 grid gap-4 md:grid-cols-3">
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
            </section>

            <EntityPanel
                v-for="key in entityKeys"
                :key="key"
                :entity="key"
                :label="entity_labels[key]"
                :model-value="form.entities[key]"
                :characteristic-options="characteristic_options[key] || []"
                @update:model-value="(row) => (form.entities[key] = row)"
            />

            <div class="flex flex-wrap gap-3">
                <Btn type="submit" color="primary" :disabled="form.processing">Enregistrer</Btn>
                <Btn
                    type="button"
                    variant="outline"
                    :disabled="form.processing || !is_stored"
                    @click="resetToFile"
                >
                    Reprendre le fichier du dépôt
                </Btn>
            </div>
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
                    <span class="font-medium">{{ items_seeder.playable_count }}</span> objet(s)
                    jouable(s) en base.
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
        @cancel="onPasswordModalCancel"
    />
</template>
