<script setup>
/**
 * Lance `project:deps` via file d’attente — réservé aux environnements non production.
 */
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useProjectConsoleJob } from '@/Composables/admin/useProjectConsoleJob';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import AdminCommandMeta from '@/Pages/Admin/_components/AdminCommandMeta.vue';
import AdminConsoleJobPanel from '@/Pages/Admin/_components/AdminConsoleJobPanel.vue';
import AdminRunAction from '@/Pages/Admin/_components/AdminRunAction.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const props = defineProps({
    isProduction: { type: Boolean, default: false },
    consoleJob: { type: Object, default: null },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, { title: 'Mise à jour stack' });

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    all: true,
    with_system: false,
    apt: false,
    composer: false,
    pnpm: false,
});

const canSubmit = computed(
    () => unlocked.value && !form.processing && !props.isProduction && !busy.value
);

function submit() {
    if (!canSubmit.value) return;
    form.post(route('admin.project-update.run'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Mise à jour stack" />

    <PageHeader title="Mise à jour stack">
        <template #subtitle>
            Enfile un job qui exécute <code class="rounded bg-base-300 px-1">project:deps</code> (mise à jour
            Composer + pnpm, puis pipeline IDE / optimize en mode « tout »).
            <strong>Interdit en production</strong> — réservé aux machines de développement.
        </template>
        <template #meta>
            <AdminCommandMeta signature="project:deps" />
        </template>
        <template #primary>
            <AdminRunAction
                v-if="!isProduction"
                :unlocked="unlocked"
                :busy="busy"
                :processing="form.processing"
                label="Lancer la mise à jour"
                @confirm="showConfirmModal = true"
                @run="submit"
            />
        </template>
    </PageHeader>

    <div class="space-y-6 pb-8 max-w-2xl">
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

        <AdminConsoleJobPanel :job="liveJob" :poll-error="pollError" :cancelling="cancelling" @cancel="cancelJob" />

        <div v-if="isProduction" class="alert alert-warning text-sm">
            Cette action n’est pas disponible lorsque <code class="px-1">APP_ENV=production</code>.
        </div>

        <div
            v-else-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm"
        >
            Confirmez votre mot de passe (bouton « Confirmer » en haut) pour lancer une mise à jour.
        </div>

        <form v-else class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4" @submit.prevent="submit">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                <input v-model="form.all" type="checkbox" class="checkbox checkbox-sm" />
                Tout (composer update + pnpm up + optimize)
            </label>
            <label
                v-if="form.all"
                class="flex items-center gap-2 cursor-pointer text-sm text-base-content/80"
            >
                <input v-model="form.with_system" type="checkbox" class="checkbox checkbox-sm" />
                Inclure la mise à jour système (apt / setup --update)
            </label>
            <p class="text-xs text-base-content/60">Décochez « Tout » pour ne sélectionner que des cibles précises :</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.apt" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> apt</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.composer" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" />
                    composer</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.pnpm" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> pnpm</label
                >
            </div>
        </form>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="Cette opération modifie les dépendances du projet. Entrez votre mot de passe."
            confirm-label="Confirmer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
