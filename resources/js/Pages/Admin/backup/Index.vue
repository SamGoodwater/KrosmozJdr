<script setup>
/**
 * Gestion des sauvegardes projet : lancement, inventaire, suppression, restauration.
 */
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useProjectConsoleJob } from '@/Composables/admin/useProjectConsoleJob';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import AdminCommandMeta from '@/Pages/Admin/_components/AdminCommandMeta.vue';
import AdminConsoleJobPanel from '@/Pages/Admin/_components/AdminConsoleJobPanel.vue';
import AdminRunAction from '@/Pages/Admin/_components/AdminRunAction.vue';
import BackupArchiveList from '@/Pages/Admin/_components/BackupArchiveList.vue';
import BackupRestoreStatusPanel from '@/Pages/Admin/_components/BackupRestoreStatusPanel.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const props = defineProps({
    consoleJob: { type: Object, default: null },
    seederExportAvailable: { type: Boolean, default: false },
    backupDirectory: { type: String, default: '' },
    retentionDays: { type: Number, default: 30 },
    backups: { type: Array, default: () => [] },
    operationLocked: { type: Boolean, default: false },
    restoreStatus: { type: Object, default: null },
    schedule: { type: Object, default: null },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const liveRestoreStatus = ref(props.restoreStatus);
const liveLocked = ref(props.operationLocked);
let pollTimer = null;

const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, {
    title: 'Sauvegarde',
});

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    no_database: false,
    no_storage: false,
    no_game: false,
    no_seeder_data: false,
    no_prune: false,
    prune_only: false,
    dry_run: false,
    retention_days: '',
});

const actionsBlocked = computed(
    () => busy.value || liveLocked.value || form.processing,
);

const subtitle = computed(() => {
    const base =
        'Archive ZIP horodatée (BDD + storage/app + private/game), inventaire, restauration sécurisée et rotation.';
    if (props.seederExportAvailable) {
        return (
            base +
            ' Hors production, les fichiers de seed versionnés peuvent aussi être réécrits depuis la base.'
        );
    }

    return base + ' L’export des fichiers de seed est désactivé en production.';
});

const scheduleLabel = computed(() => {
    if (!props.schedule) return null;
    if (!props.schedule.enabled) {
        return 'Planification cron : désactivée (activer dans Planning cron).';
    }
    return `Planification cron : active (${props.schedule.cron_expression || '—'}). Prérequis serveur : schedule:run chaque minute.`;
});

const restoreBusy = computed(() => {
    const state = liveRestoreStatus.value?.state;
    return state === 'queued' || state === 'running';
});

watch(
    () => props.restoreStatus,
    (value) => {
        liveRestoreStatus.value = value;
    },
);

watch(
    () => props.operationLocked,
    (value) => {
        liveLocked.value = value;
    },
);

async function pollRestoreStatus() {
    try {
        const response = await fetch(route('admin.backup.restore-status'), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!response.ok) return;
        const data = await response.json();
        const previousBusy = restoreBusy.value;
        liveRestoreStatus.value = data.restoreStatus;
        liveLocked.value = Boolean(data.operationLocked);
        if (previousBusy && !restoreBusy.value) {
            router.reload({ only: ['backups', 'operationLocked', 'restoreStatus'], preserveScroll: true });
        }
    } catch {
        /* ignore transient poll errors (ex. redémarrage pendant maintenance) */
    }
}

function startPoll() {
    stopPoll();
    pollTimer = window.setInterval(() => {
        if (restoreBusy.value) {
            pollRestoreStatus();
        }
    }, 1500);
}

function stopPoll() {
    if (pollTimer) {
        window.clearInterval(pollTimer);
        pollTimer = null;
    }
}

watch(restoreBusy, (isBusy, wasBusy) => {
    if (isBusy && !wasBusy) {
        startPoll();
        pollRestoreStatus();
    }
});

onMounted(() => {
    if (restoreBusy.value) {
        startPoll();
        pollRestoreStatus();
    }
});

onBeforeUnmount(() => {
    stopPoll();
});

function submit() {
    if (!unlocked.value || actionsBlocked.value) return;
    form.post(route('admin.backup.run'), { preserveScroll: true });
}

function onArchivesChanged() {
    router.reload({ only: ['backups', 'operationLocked', 'restoreStatus'], preserveScroll: true });
}
</script>

<template>
    <Head title="Sauvegarde" />

    <PageHeader title="Sauvegarde">
        <template #subtitle>
            {{ subtitle }}
            Un worker doit traiter la file pour le lancement asynchrone.
        </template>
        <template #meta>
            <AdminCommandMeta
                signature="project:backup"
                cron-key="project_backup"
                cron-command="project:backup"
            />
        </template>
        <template #primary>
            <AdminRunAction
                :unlocked="unlocked"
                :busy="actionsBlocked"
                :processing="form.processing"
                @confirm="showConfirmModal = true"
                @run="submit"
            />
        </template>
    </PageHeader>

    <div class="space-y-6 pb-8 max-w-5xl">
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

        <p v-if="scheduleLabel" class="text-sm text-base-content/70">
            {{ scheduleLabel }}
            <a :href="route('admin.project-schedule.index')" class="link link-hover ml-1">Ouvrir le planning</a>
        </p>

        <p class="text-xs text-base-content/60">
            Répertoire :
            <code>{{ backupDirectory }}</code>
            · Rétention : {{ retentionDays }} j
            · Format : <code>project-backup_YYYY-MM-DD_HH-mm-ss_xxxx.zip</code>
            · Jamais inclus : <code>.env</code>
        </p>

        <BackupRestoreStatusPanel :status="liveRestoreStatus" />

        <AdminConsoleJobPanel :job="liveJob" :poll-error="pollError" :cancelling="cancelling" @cancel="cancelJob" />

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm"
        >
            Confirmez votre mot de passe (bouton « Confirmer » en haut) pour lancer, supprimer ou restaurer.
        </div>

        <template v-else>
            <form
                class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4 max-w-2xl"
                @submit.prevent="submit"
            >
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input v-model="form.no_database" type="checkbox" class="checkbox checkbox-sm" />
                    Exclure la base de données
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input v-model="form.no_storage" type="checkbox" class="checkbox checkbox-sm" />
                    Exclure le dossier storage/app
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input v-model="form.no_game" type="checkbox" class="checkbox checkbox-sm" />
                    Exclure private/game
                </label>
                <label
                    v-if="seederExportAvailable"
                    class="flex items-start gap-2 cursor-pointer text-sm"
                >
                    <input v-model="form.no_seeder_data" type="checkbox" class="checkbox checkbox-sm mt-0.5" />
                    <span>
                        Ne pas réécrire les fichiers de seed du dépôt
                        <span class="block text-xs text-base-content/60">
                            Par défaut, la sauvegarde exporte caractéristiques, types item, mappings scrapping et
                            équipements versionnés (base → fichiers).
                        </span>
                    </span>
                </label>
                <p
                    v-else
                    class="text-xs text-base-content/60 rounded-box border border-base-content/10 px-3 py-2"
                >
                    Environnement production : seuls le dump SQL, storage/app et private/game sont concernés.
                </p>
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

            <BackupArchiveList
                :backups="backups"
                :disabled="actionsBlocked"
                @deleted="onArchivesChanged"
                @restored="onArchivesChanged"
            />
        </template>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="La sauvegarde accède aux données du serveur. Entrez votre mot de passe."
            confirm-label="Confirmer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
