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
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import EntityPanel from "@/Pages/Admin/Content/IaGeneration/EntityPanel.vue";

defineOptions({ layout: AdminArea });

const props = defineProps({
    config: { type: Object, required: true },
    is_stored: { type: Boolean, default: false },
    updated_at: { type: String, default: null },
    entity_labels: { type: Object, required: true },
    characteristic_options: { type: Object, default: () => ({}) },
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
    generation: {
        max_retries: props.config.generation?.max_retries ?? 2,
        few_shot_count: props.config.generation?.few_shot_count ?? 8,
        max_effects_per_spell: props.config.generation?.max_effects_per_spell ?? 3,
    },
    entities: cloneEntities(props.config.entities),
});

const entityKeys = computed(() => Object.keys(props.entity_labels || {}));

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
    for (const key of ["item", "spell", "monster", "npc"]) {
        const row = source[key] && typeof source[key] === "object" ? source[key] : {};
        out[key] = {
            has_dofus_source: Boolean(row.has_dofus_source),
            frozen_fields: row.frozen_fields === "*" ? "*" : [...(row.frozen_fields || [])],
            writable_fields: [...(row.writable_fields || [])],
            frozen_characteristics:
                row.frozen_characteristics === "*" ? "*" : [...(row.frozen_characteristics || [])],
            writable_characteristics: [...(row.writable_characteristics || [])],
            example_ids: [...(row.example_ids || [])],
        };
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
