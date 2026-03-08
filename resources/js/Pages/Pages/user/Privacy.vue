<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

const page = usePage();

const requests = computed(() => page.props.requests || []);
const exportsList = computed(() => page.props.exports || []);

const exportForm = useForm({});
const deletionForm = useForm({
    current_password: '',
});

const showExportModal = ref(false);
const showDeleteModal = ref(false);
const showDownloadModal = ref(false);
const pendingDownloadUrl = ref(null);

function openExportModal() {
    showExportModal.value = true;
}

function openDeleteModal() {
    showDeleteModal.value = true;
}

function openDownloadModal(url) {
    pendingDownloadUrl.value = url;
    showDownloadModal.value = true;
}

function onExportConfirmed() {
    exportForm.post(route('user.privacy.export'), {
        preserveScroll: true,
    });
}

function onDeleteConfirmed(password) {
    deletionForm.current_password = password;
    deletionForm.post(route('user.privacy.delete.request'), {
        preserveScroll: true,
        onSuccess: () => {
            deletionForm.reset('current_password');
        },
    });
}

function onDownloadConfirmed() {
    if (pendingDownloadUrl.value) {
        window.location.href = pendingDownloadUrl.value;
        pendingDownloadUrl.value = null;
    }
}
</script>

<template>
    <div class="container mx-auto px-4 py-6 max-w-5xl space-y-6">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="text-2xl font-bold">Mes données personnelles</h1>
            <Route :href="route('user.settings')" class="btn btn-ghost btn-sm">Retour aux paramètres</Route>
        </div>

        <div class="alert alert-warning">
            <span>Ces actions sont sensibles et protégées. Une confirmation de mot de passe récente est requise.</span>
        </div>

        <section class="rounded-lg border border-base-300 bg-base-200/30 p-4 space-y-3">
            <h2 class="text-lg font-semibold">Exporter mes données (RGPD)</h2>
            <p class="text-sm text-content-500">
                Tu peux demander une archive contenant les données liées à ton compte.
            </p>
            <Btn color="primary" :disabled="exportForm.processing" @click="openExportModal">
                Demander un export
            </Btn>
        </section>

        <section class="rounded-lg border border-error/40 bg-error/10 p-4 space-y-3">
            <h2 class="text-lg font-semibold text-error">Supprimer mon compte</h2>
            <p class="text-sm text-content-500">
                Cette action supprime ton compte et anonymise tes données. Confirme avec ton mot de passe.
            </p>

            <Btn color="error" :disabled="deletionForm.processing" @click="openDeleteModal">
                Supprimer mon compte
            </Btn>
        </section>

        <section class="rounded-lg border border-base-300 bg-base-200/30 p-4 space-y-3">
            <h2 class="text-lg font-semibold">Exports disponibles</h2>
            <div v-if="exportsList.length === 0" class="text-sm text-content-500">
                Aucun export disponible.
            </div>
            <div v-else class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th>Expire le</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in exportsList" :key="item.id">
                            <td>#{{ item.id }}</td>
                            <td>{{ item.status }}</td>
                            <td>{{ item.created_at || '-' }}</td>
                            <td>{{ item.expires_at || '-' }}</td>
                            <td>
                                <Btn
                                    v-if="item.download_url"
                                    color="primary"
                                    size="xs"
                                    @click="openDownloadModal(item.download_url)"
                                >
                                    Télécharger
                                </Btn>
                                <span v-else class="text-xs text-content-500">Indisponible</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-base-300 bg-base-200/30 p-4 space-y-3">
            <h2 class="text-lg font-semibold">Historique des exports</h2>
            <div v-if="requests.length === 0" class="text-sm text-content-500">
                Aucune demande.
            </div>
            <div v-else class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Demandé le</th>
                            <th>Traité le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in requests" :key="item.id">
                            <td>#{{ item.id }}</td>
                            <td>{{ item.type }}</td>
                            <td>{{ item.status }}</td>
                            <td>{{ item.requested_at || '-' }}</td>
                            <td>{{ item.processed_at || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <ConfirmPasswordModal
            v-model:open="showExportModal"
            title="Confirmer l'export"
            message="Entre ton mot de passe pour demander une archive de tes données."
            confirm-label="Demander l'export"
            @confirmed="onExportConfirmed"
        />
        <ConfirmPasswordModal
            v-model:open="showDeleteModal"
            title="Supprimer mon compte"
            message="Cette action est irréversible. Entre ton mot de passe pour confirmer la suppression de ton compte."
            confirm-label="Supprimer mon compte"
            @confirmed="onDeleteConfirmed"
        />
        <ConfirmPasswordModal
            v-model:open="showDownloadModal"
            title="Télécharger l'export"
            message="Entre ton mot de passe pour télécharger l'archive de tes données."
            confirm-label="Télécharger"
            @confirmed="onDownloadConfirmed"
        />
    </div>
</template>

