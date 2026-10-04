<script setup>
/**
 * Liste des archives de sauvegarde avec suppression / restauration sécurisée.
 */
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    backups: { type: Array, default: () => [] },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['deleted', 'restored']);

const restoreTarget = ref(null);
const deleteTarget = ref(null);

const deleteForm = useForm({ name: '' });
const restoreForm = useForm({
    name: '',
    confirm_name: '',
    no_safety_backup: false,
});

const hasRows = computed(() => props.backups.length > 0);

function formatBytes(bytes) {
    const n = Number(bytes) || 0;
    if (n < 1024) return `${n} B`;
    if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)} KiB`;
    return `${(n / (1024 * 1024)).toFixed(1)} MiB`;
}

function formatDate(mtime, createdAt) {
    if (createdAt) {
        try {
            return new Date(createdAt).toLocaleString('fr-FR');
        } catch {
            /* fallthrough */
        }
    }
    if (!mtime) return '—';
    return new Date(mtime * 1000).toLocaleString('fr-FR');
}

function integrityLabel(value) {
    if (value === 'ok') return 'OK';
    if (value === 'legacy') return 'Ancien format';
    if (value === 'invalid') return 'Invalide';
    return 'Inconnu';
}

function openDelete(row) {
    deleteTarget.value = row;
    deleteForm.name = row.name;
}

function openRestore(row) {
    restoreTarget.value = row;
    restoreForm.name = row.name;
    restoreForm.confirm_name = '';
    restoreForm.no_safety_backup = false;
}

function submitDelete() {
    if (props.disabled || deleteForm.processing) return;
    deleteForm.post(route('admin.backup.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
            emit('deleted');
        },
    });
}

function submitRestore() {
    if (props.disabled || restoreForm.processing) return;
    restoreForm.post(route('admin.backup.restore'), {
        preserveScroll: true,
        onSuccess: () => {
            restoreTarget.value = null;
            emit('restored');
        },
    });
}
</script>

<template>
    <div class="space-y-3">
        <h2 class="text-base font-medium">Archives disponibles</h2>

        <p v-if="!hasRows" class="text-sm text-base-content/60">Aucune sauvegarde pour le moment.</p>

        <div v-else class="overflow-x-auto rounded-box border border-base-content/10">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Date</th>
                        <th scope="col">Taille</th>
                        <th scope="col">Contenu</th>
                        <th scope="col">Intégrité</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in backups" :key="row.name">
                        <td class="font-mono text-xs max-w-[14rem] truncate" :title="row.name">
                            {{ row.name }}
                        </td>
                        <td class="text-xs whitespace-nowrap">
                            {{ formatDate(row.mtime, row.created_at) }}
                        </td>
                        <td class="text-xs whitespace-nowrap">{{ formatBytes(row.size) }}</td>
                        <td class="text-xs">{{ (row.components || []).join(', ') || '—' }}</td>
                        <td class="text-xs">
                            <span
                                class="badge badge-sm"
                                :class="{
                                    'badge-success': row.integrity === 'ok',
                                    'badge-warning': row.integrity === 'legacy' || row.integrity === 'unknown',
                                    'badge-error': row.integrity === 'invalid',
                                }"
                            >
                                {{ integrityLabel(row.integrity) }}
                            </span>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs"
                                :disabled="disabled || !row.restorable"
                                :title="row.restorable ? 'Restaurer' : 'Non restaurable automatiquement'"
                                @click="openRestore(row)"
                            >
                                Restaurer
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs text-error"
                                :disabled="disabled"
                                @click="openDelete(row)"
                            >
                                Supprimer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <dialog v-if="deleteTarget" class="modal modal-open" aria-labelledby="backup-delete-title">
            <div class="modal-box space-y-3">
                <h3 id="backup-delete-title" class="font-medium">Supprimer la sauvegarde</h3>
                <p class="text-sm">
                    Supprimer définitivement
                    <code class="text-xs">{{ deleteTarget.name }}</code> ?
                </p>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost btn-sm" @click="deleteTarget = null">
                        Annuler
                    </button>
                    <button
                        type="button"
                        class="btn btn-error btn-sm"
                        :disabled="deleteForm.processing || disabled"
                        @click="submitDelete"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @submit.prevent="deleteTarget = null">
                <button type="submit">close</button>
            </form>
        </dialog>

        <dialog v-if="restoreTarget" class="modal modal-open" aria-labelledby="backup-restore-title">
            <div class="modal-box space-y-3 max-w-lg">
                <h3 id="backup-restore-title" class="font-medium">Restaurer la sauvegarde</h3>
                <p class="text-sm text-warning">
                    Opération destructive : remplace la base, <code class="text-xs">storage/app</code> et
                    <code class="text-xs">private/game</code>. Une sauvegarde de secours est créée avant
                    (sauf option ci-dessous).
                </p>
                <p class="text-sm">
                    Archive :
                    <code class="text-xs break-all">{{ restoreTarget.name }}</code>
                </p>
                <label class="form-control w-full">
                    <span class="label-text text-sm">Retapez le nom exact de l’archive</span>
                    <input
                        v-model="restoreForm.confirm_name"
                        type="text"
                        class="input input-bordered input-sm w-full font-mono"
                        autocomplete="off"
                        :disabled="restoreForm.processing || disabled"
                    />
                    <span v-if="restoreForm.errors.confirm_name" class="text-error text-xs mt-1">
                        {{ restoreForm.errors.confirm_name }}
                    </span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input
                        v-model="restoreForm.no_safety_backup"
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        :disabled="restoreForm.processing || disabled"
                    />
                    Ne pas créer de sauvegarde de secours
                </label>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost btn-sm" @click="restoreTarget = null">
                        Annuler
                    </button>
                    <button
                        type="button"
                        class="btn btn-warning btn-sm"
                        :disabled="
                            restoreForm.processing ||
                            disabled ||
                            restoreForm.confirm_name !== restoreTarget.name
                        "
                        @click="submitRestore"
                    >
                        Restaurer
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @submit.prevent="restoreTarget = null">
                <button type="submit">close</button>
            </form>
        </dialog>
    </div>
</template>
