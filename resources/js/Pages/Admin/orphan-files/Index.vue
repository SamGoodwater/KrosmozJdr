<script setup>
/**
 * Lance / suit / annule un nettoyage des fichiers MediaLibrary orphelins (super admin).
 */
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const props = defineProps({
    scannedRoots: { type: Array, default: () => [] },
    recentJobs: { type: Array, default: () => [] },
    activeJob: { type: Object, default: null },
});

const { setPageTitle } = usePageTitle();
setPageTitle('Fichiers orphelins');

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const liveJob = ref(props.activeJob);
const recent = ref([...props.recentJobs]);
const pollError = ref('');
const cancelling = ref(false);
let pollTimer = null;

const form = useForm({
    delete: false,
    skip_notify: false,
});

const canSubmit = computed(() => unlocked.value && !form.processing && !isActiveStatus(liveJob.value?.status));

const progressPercent = computed(() => {
    const total = Number(liveJob.value?.progress_total || 0);
    const done = Number(liveJob.value?.progress_done || 0);
    if (total <= 0) {
        return 0;
    }
    return Math.min(100, Math.round((done / total) * 100));
});

/**
 * @param {string|undefined|null} status
 */
function isActiveStatus(status) {
    return status === 'queued' || status === 'running';
}

/**
 * @param {string} status
 */
function statusLabel(status) {
    return (
        {
            queued: 'En file',
            running: 'En cours',
            succeeded: 'Terminé',
            failed: 'Échec',
            cancelled: 'Annulé',
        }[status] || status
    );
}

/**
 * @param {Record<string, any>|null|undefined} job
 */
function summaryText(job) {
    const s = job?.summary;
    if (!s || typeof s !== 'object') {
        return '—';
    }
    const candidates = s.candidateCount ?? 0;
    const deleted = s.deletedCount ?? 0;
    if (job.mode === 'delete') {
        return `${deleted} supprimé(s) / ${candidates} candidat(s)`;
    }
    return `${candidates} candidat(s) (dry-run)`;
}

function onPasswordConfirmed() {
    unlocked.value = true;
}

function submit() {
    form.post(route('admin.orphan-files.run'), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['recentJobs', 'activeJob'] });
        },
    });
}

async function fetchStatus(jobId) {
    pollError.value = '';
    try {
        const res = await fetch(route('admin.orphan-files.status', jobId), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.message || 'Statut indisponible');
        }
        liveJob.value = json.data;
        upsertRecent(json.data);
        if (!isActiveStatus(json.data.status)) {
            stopPolling();
        }
    } catch (e) {
        pollError.value = e?.message || 'Erreur de suivi';
    }
}

/**
 * @param {Record<string, any>} job
 */
function upsertRecent(job) {
    const list = recent.value.filter((j) => j.id !== job.id);
    list.unshift(job);
    recent.value = list.slice(0, 15);
}

function startPolling(jobId) {
    stopPolling();
    fetchStatus(jobId);
    pollTimer = window.setInterval(() => fetchStatus(jobId), 2000);
}

function stopPolling() {
    if (pollTimer) {
        window.clearInterval(pollTimer);
        pollTimer = null;
    }
}

async function cancelJob() {
    if (!liveJob.value?.id || !isActiveStatus(liveJob.value.status)) {
        return;
    }
    cancelling.value = true;
    pollError.value = '';
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(route('admin.orphan-files.cancel', liveJob.value.id), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({}),
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.message || 'Annulation impossible');
        }
        liveJob.value = json.data;
        upsertRecent(json.data);
    } catch (e) {
        pollError.value = e?.message || 'Erreur d’annulation';
    } finally {
        cancelling.value = false;
    }
}

watch(
    () => props.activeJob,
    (job) => {
        liveJob.value = job;
        if (job?.id && isActiveStatus(job.status)) {
            startPolling(job.id);
        }
    },
);

watch(
    () => props.recentJobs,
    (jobs) => {
        recent.value = [...jobs];
    },
);

onMounted(() => {
    if (liveJob.value?.id && isActiveStatus(liveJob.value.status)) {
        startPolling(liveJob.value.id);
    }
});

onBeforeUnmount(() => {
    stopPolling();
});
</script>

<template>
    <Head title="Fichiers orphelins" />

    <div class="space-y-6 pb-8 max-w-3xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Fichiers orphelins</h1>
            <p class="mt-2 text-sm text-base-content/70">
                Scanne les racines MediaLibrary publiques et repère les fichiers sans ligne
                <code class="rounded bg-base-300 px-1">media</code>
                en base. Dry-run par défaut ; la suppression réelle est irréversible. Un worker
                (<code class="rounded bg-base-300 px-1">queue:work</code>) doit traiter le job.
            </p>
            <p class="mt-2 text-xs text-base-content/60">
                Racines : {{ scannedRoots.join(', ') }}
            </p>
        </div>

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
        >
            <p class="text-warning-content text-sm">Confirmez votre mot de passe pour lancer un nettoyage.</p>
            <Btn color="primary" @click="showConfirmModal = true">Confirmer</Btn>
        </div>

        <form
            v-else
            class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4"
            @submit.prevent="submit"
        >
            <label class="flex items-start gap-2 cursor-pointer text-sm">
                <input v-model="form.delete" type="checkbox" class="checkbox checkbox-sm mt-0.5 checkbox-error" />
                <span>
                    <span class="font-medium text-error">Supprimer réellement</span>
                    les fichiers orphelins (sinon dry-run uniquement).
                </span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input v-model="form.skip_notify" type="checkbox" class="checkbox checkbox-sm" />
                Ne pas notifier les admins à la fin
            </label>

            <div class="flex flex-wrap gap-2">
                <Btn type="submit" color="primary" :disabled="!canSubmit">
                    {{ form.processing ? 'Envoi…' : form.delete ? 'Lancer la suppression' : 'Lancer le dry-run' }}
                </Btn>
            </div>
        </form>

        <div
            v-if="liveJob"
            class="rounded-box border border-base-content/10 bg-base-100/50 p-4 space-y-3"
        >
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-medium">Job en cours / récent</h2>
                <span class="badge badge-outline">{{ statusLabel(liveJob.status) }}</span>
            </div>
            <p class="text-xs text-base-content/60 font-mono break-all">{{ liveJob.id }}</p>
            <p class="text-sm">
                Mode : <strong>{{ liveJob.mode === 'delete' ? 'suppression' : 'dry-run' }}</strong>
                — {{ liveJob.progress_done }} / {{ liveJob.progress_total }} fichier(s)
            </p>
            <progress
                class="progress progress-primary w-full"
                :value="progressPercent"
                max="100"
            />
            <p class="text-sm text-base-content/80">{{ summaryText(liveJob) }}</p>
            <p v-if="liveJob.error" class="text-sm text-error">{{ liveJob.error }}</p>
            <p v-if="pollError" class="text-sm text-warning">{{ pollError }}</p>
            <Btn
                v-if="isActiveStatus(liveJob.status)"
                color="error"
                variant="outline"
                :disabled="cancelling"
                @click="cancelJob"
            >
                {{ cancelling ? 'Annulation…' : 'Annuler' }}
            </Btn>
        </div>

        <div class="rounded-box border border-base-content/10 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Mode</th>
                        <th>Résumé</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="recent.length === 0">
                        <td colspan="4" class="text-base-content/60">Aucun job pour l’instant.</td>
                    </tr>
                    <tr v-for="job in recent" :key="job.id">
                        <td class="whitespace-nowrap text-xs">
                            {{ job.created_at ? new Date(job.created_at).toLocaleString('fr-FR') : '—' }}
                        </td>
                        <td>{{ statusLabel(job.status) }}</td>
                        <td>{{ job.mode === 'delete' ? 'delete' : 'dry-run' }}</td>
                        <td class="text-xs">{{ summaryText(job) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="Le nettoyage des fichiers orphelins peut supprimer des données disque. Entrez votre mot de passe."
            confirm-label="Continuer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
