<script setup>
/**
 * Journal admin + corbeille centralisée des entités JDR.
 */
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmModal from '@/Pages/Molecules/action/ConfirmModal.vue';

defineOptions({ layout: AdminArea });

defineProps({
    logs: { type: Array, default: () => [] },
    trash: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    filterOptions: {
        type: Object,
        default: () => ({ domains: [], actions: [], statuses: [], actors: [] }),
    },
});

const { setPageTitle } = usePageTitle();
setPageTitle('Journal admin');

const confirmState = ref({
    open: false,
    mode: null,
    row: null,
    title: '',
    message: '',
    details: [],
    confirmLabel: 'Confirmer',
    confirmColor: 'primary',
});

function reload() {
    router.reload({ preserveScroll: true });
}

function alertApiError(error, fallback) {
    const message = error?.response?.data?.message
        || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
        || fallback;
    window.alert(message);
}

function closeConfirm() {
    confirmState.value = {
        open: false,
        mode: null,
        row: null,
        title: '',
        message: '',
        details: [],
        confirmLabel: 'Confirmer',
        confirmColor: 'primary',
    };
}

function openRestoreConfirm(row) {
    confirmState.value = {
        open: true,
        mode: 'restore',
        row,
        title: 'Restaurer l’entité',
        message: `Restaurer « ${row.label} » ?`,
        details: ['L’entité redeviendra visible selon ses droits de lecture.'],
        confirmLabel: 'Restaurer',
        confirmColor: 'primary',
    };
}

async function openForceDeleteConfirm(row) {
    let details = [
        'Cette action détache les relations BelongsToMany.',
        'Les médias Spatie liés seront supprimés.',
        'Action irréversible.',
    ];

    try {
        const { data } = await axios.get(
            `/api/entities/${encodeURIComponent(row.entity_type)}/${row.id}/delete-impact`,
            { headers: { Accept: 'application/json' } },
        );
        const relations = Array.isArray(data?.relations) ? data.relations : [];
        const mediaCount = Number(data?.media_count ?? 0);
        details = [
            relations.length > 0
                ? `${relations.length} relation(s) seront détachées : ${relations.join(', ')}.`
                : 'Aucune relation BelongsToMany à détacher.',
            mediaCount > 0
                ? `${mediaCount} média(s) seront supprimés.`
                : 'Aucun média lié.',
            'Action irréversible.',
        ];
    } catch {
        // Garde le récapitulatif générique si l’impact est indisponible.
    }

    confirmState.value = {
        open: true,
        mode: 'force',
        row,
        title: 'Suppression définitive',
        message: `Supprimer définitivement « ${row.label} » ?`,
        details,
        confirmLabel: 'Supprimer définitivement',
        confirmColor: 'error',
    };
}

async function onConfirm() {
    const { mode, row } = confirmState.value;
    if (!row || !mode) return;

    try {
        if (mode === 'restore') {
            await axios.post(`/api/entities/${encodeURIComponent(row.entity_type)}/${row.id}/restore`, {}, {
                headers: { Accept: 'application/json' },
            });
        } else if (mode === 'force') {
            await axios.delete(`/api/entities/${encodeURIComponent(row.entity_type)}/${row.id}/force`, {
                headers: { Accept: 'application/json' },
            });
        }
        closeConfirm();
        reload();
    } catch (error) {
        alertApiError(
            error,
            mode === 'restore'
                ? 'Impossible de restaurer cette entité.'
                : 'Impossible de supprimer définitivement cette entité.',
        );
    }
}
</script>

<template>
    <Head title="Journal admin" />

    <div class="space-y-8 pb-8">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Journal admin</h1>
            <p class="mt-2 max-w-3xl text-sm text-base-content/70">
                Suivi des actions sensibles et corbeille centralisée des entités JDR supprimées logiquement.
            </p>
        </div>

        <form
            method="get"
            action="/admin/activity-log"
            class="grid gap-3 rounded-box border border-base-content/10 bg-base-100/70 p-4 md:grid-cols-5"
        >
            <label class="form-control">
                <span class="label-text">Domaine</span>
                <select name="domain" class="select select-bordered select-sm" :value="filters.domain || ''">
                    <option value="">Tous</option>
                    <option v-for="domain in filterOptions.domains" :key="domain" :value="domain">{{ domain }}</option>
                </select>
            </label>
            <label class="form-control">
                <span class="label-text">Action</span>
                <select name="action" class="select select-bordered select-sm" :value="filters.action || ''">
                    <option value="">Toutes</option>
                    <option v-for="action in filterOptions.actions" :key="action" :value="action">{{ action }}</option>
                </select>
            </label>
            <label class="form-control">
                <span class="label-text">Statut</span>
                <select name="status" class="select select-bordered select-sm" :value="filters.status || ''">
                    <option value="">Tous</option>
                    <option v-for="status in filterOptions.statuses" :key="status" :value="status">{{ status }}</option>
                </select>
            </label>
            <label class="form-control">
                <span class="label-text">Auteur</span>
                <select name="actor_id" class="select select-bordered select-sm" :value="filters.actor_id || ''">
                    <option value="">Tous</option>
                    <option v-for="actor in filterOptions.actors" :key="actor.id" :value="actor.id">{{ actor.name }}</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <Btn size="sm" color="primary" type="submit">Filtrer</Btn>
                <a class="btn btn-sm btn-ghost" href="/admin/activity-log">Réinitialiser</a>
            </div>
        </form>

        <section class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Corbeille entités</h2>
                <span class="badge badge-neutral">{{ trash.length }} élément(s)</span>
            </div>
            <div class="overflow-x-auto rounded-box border border-base-content/10 bg-base-100/70">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Suppression</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in trash" :key="`${row.entity_type}:${row.id}`">
                            <td class="font-mono text-xs">{{ row.entity_type }}</td>
                            <td>{{ row.label }}</td>
                            <td>{{ row.deleted_at }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Btn size="xs" color="primary" type="button" @click="openRestoreConfirm(row)">Restaurer</Btn>
                                    <Btn size="xs" color="error" variant="outline" type="button" @click="openForceDeleteConfirm(row)">
                                        Supprimer
                                    </Btn>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="trash.length === 0">
                            <td colspan="4" class="py-6 text-center text-sm text-base-content/60">Corbeille vide.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Activités récentes</h2>
                <span class="badge badge-neutral">{{ logs.length }} ligne(s)</span>
            </div>
            <div class="overflow-x-auto rounded-box border border-base-content/10 bg-base-100/70">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Domaine</th>
                            <th>Action</th>
                            <th>Sujet</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs" :key="log.id">
                            <td class="whitespace-nowrap">{{ log.created_at }}</td>
                            <td>{{ log.domain }}</td>
                            <td>{{ log.action }}</td>
                            <td>
                                <span class="font-medium">{{ log.subject_label || `#${log.subject_id}` }}</span>
                                <span class="ml-2 text-xs text-base-content/50">{{ log.subject_type }}</span>
                            </td>
                            <td>{{ log.actor_name || 'Système' }}</td>
                            <td>
                                <span class="badge badge-success badge-sm">{{ log.status }}</span>
                            </td>
                        </tr>
                        <tr v-if="logs.length === 0">
                            <td colspan="6" class="py-6 text-center text-sm text-base-content/60">Aucune activité.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <ConfirmModal
        :open="confirmState.open"
        :title="confirmState.title"
        :message="confirmState.message"
        :details="confirmState.details"
        :confirm-label="confirmState.confirmLabel"
        :confirm-color="confirmState.confirmColor"
        confirm-icon="fa-solid fa-trash-can"
        @confirm="onConfirm"
        @cancel="closeConfirm"
        @close="closeConfirm"
    />
</template>
