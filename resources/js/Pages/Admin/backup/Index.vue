<script setup>
/**
 * Lance `project:backup` via file d’attente (super admin).
 */
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useProjectConsoleJob } from '@/Composables/admin/useProjectConsoleJob';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import AdminCommandMeta from '@/Pages/Admin/_components/AdminCommandMeta.vue';
import AdminConsoleJobPanel from '@/Pages/Admin/_components/AdminConsoleJobPanel.vue';
import AdminRunAction from '@/Pages/Admin/_components/AdminRunAction.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const props = defineProps({
    consoleJob: { type: Object, default: null },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, { title: 'Sauvegarde' });

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    no_database: false,
    no_storage: false,
    no_prune: false,
    prune_only: false,
    dry_run: false,
    retention_days: '',
});

function submit() {
    if (!unlocked.value || form.processing || busy.value) return;
    form.post(route('admin.backup.run'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Sauvegarde" />

    <PageHeader title="Sauvegarde">
        <template #subtitle>
            Enfile un job qui exécute <code class="rounded bg-base-300 px-1">project:backup</code> (dump BDD + archive
            storage, rotation). Un worker doit traiter la file.
        </template>
        <template #meta>
            <AdminCommandMeta signature="project:backup" cron-key="project_backup" cron-command="project:backup" />
        </template>
        <template #primary>
            <AdminRunAction
                :unlocked="unlocked"
                :busy="busy"
                :processing="form.processing"
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

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm"
        >
            Confirmez votre mot de passe (bouton « Confirmer » en haut) pour lancer une sauvegarde.
        </div>

        <form v-else class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4" @submit.prevent="submit">
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.no_database" type="checkbox" class="checkbox checkbox-sm" />
                Exclure la base de données
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.no_storage" type="checkbox" class="checkbox checkbox-sm" />
                Exclure le dossier storage/app
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.no_prune" type="checkbox" class="checkbox checkbox-sm" />
                Ne pas purger les anciennes sauvegardes
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.prune_only" type="checkbox" class="checkbox checkbox-sm" />
                Purge uniquement (sans nouvelle sauvegarde)
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.dry_run" type="checkbox" class="checkbox checkbox-sm" />
                Simulation (ex. purge à blanc)
            </label>
            <div>
                <label class="text-sm text-base-content/80">Rétention (jours, optionnel)</label>
                <input
                    v-model="form.retention_days"
                    type="number"
                    min="1"
                    max="3650"
                    class="input input-bordered input-sm w-full max-w-xs mt-1"
                    placeholder="Défaut : config / .env"
                />
            </div>
        </form>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="La sauvegarde accède aux données du serveur. Entrez votre mot de passe."
            confirm-label="Confirmer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
