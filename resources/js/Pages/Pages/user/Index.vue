<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Avatar from '@/Pages/Atoms/data-display/Avatar.vue';
import BadgeRole from '@/Pages/Molecules/user/BadgeRole.vue';
import UserListRowActions from '@/Pages/Molecules/user/UserListRowActions.vue';
import ConfirmModal from '@/Pages/Molecules/action/ConfirmModal.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { useProtectedAdminAction } from '@/Composables/auth/useProtectedAdminAction';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { getRoleTranslation, ROLES } from '@/Utils/user/RoleManager';

defineOptions({ layout: AdminArea });

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    roles: { type: Object, default: () => ({}) },
});

const page = usePage();
const { isSuperAdmin } = usePermissions();
const { success, error: notifyError } = useNotificationStore();
const {
    showPasswordModal,
    passwordModalTitle,
    passwordModalMessage,
    passwordModalConfirmLabel,
    requirePassword,
    onPasswordConfirmed,
    onPasswordModalCancel,
} = useProtectedAdminAction();

const usersData = computed(() => Array.isArray(props.users?.data) ? props.users.data : []);
const links = computed(() => Array.isArray(props.users?.links) ? props.users.links : []);
const meta = computed(() => props.users?.meta || {});
const totalUsersLabel = computed(() => {
    const total = Number(meta.value?.total || usersData.value.length || 0);
    return `${total} utilisateur${total > 1 ? 's' : ''}`;
});
const activeUsersCount = computed(() => usersData.value.filter((u) => !u.deleted_at).length);
const archivedUsersCount = computed(() => usersData.value.filter((u) => Boolean(u.deleted_at)).length);

const roleOptions = computed(() => {
    const base = [{ value: '', label: 'Tous les rôles' }];
    const entries = Object.entries(props.roles || {})
        .map(([value, name]) => ({
            value: Number(value),
            label: getRoleTranslation(name),
        }))
        .filter((opt) => opt.value !== ROLES.SUPER_ADMIN);
    return [...base, ...entries];
});

const statusOptions = [
    { value: 'active', label: 'Actifs' },
    { value: 'trashed', label: 'Supprimés' },
    { value: 'all', label: 'Tous' },
];

const form = useForm({
    search: props.filters?.search ?? '',
    role: props.filters?.role ?? '',
    status: props.filters?.status ?? 'all',
});

const visitOptions = { preserveState: false, preserveScroll: false };

const applyFilters = () => {
    form.get(route('user.index'), {
        preserveState: false,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.role = '';
    form.status = 'all';
    applyFilters();
};

const goToEdit = (userId) => router.visit(route('user.admin.edit', userId), visitOptions);
const goToCreate = () => router.visit(route('user.create'), visitOptions);
const goToResetPassword = (userId) => router.visit(`${route('user.admin.edit', userId)}#password-admin`, visitOptions);
const userAvatarSrc = (u) => (u?.avatar_is_default ? '' : (u?.avatar || ''));
const toLabel = (v) => (v === null || typeof v === 'undefined' || v === '' ? '-' : String(v));
const isCurrentUser = (userId) => Number(page.props?.auth?.user?.id) === Number(userId);
const hasActiveFilters = computed(() => Boolean(form.search || form.role || (form.status && form.status !== 'all')));
const selectedRoleLabel = computed(() => {
    if (!form.role) return null;
    const role = roleOptions.value.find((option) => Number(option.value) === Number(form.role));
    return role?.label || null;
});
const selectedStatusLabel = computed(() => {
    const status = statusOptions.find((option) => option.value === form.status);
    return status?.label || null;
});
const clearSearch = () => {
    form.search = '';
    applyFilters();
};
const clearRole = () => {
    form.role = '';
    applyFilters();
};
const clearStatus = () => {
    form.status = 'all';
    applyFilters();
};
const decodeHtmlEntities = (value) => String(value)
    .replace(/&laquo;/g, '«')
    .replace(/&raquo;/g, '»')
    .replace(/&amp;/g, '&')
    .replace(/&nbsp;/g, ' ')
    .replace(/&#039;/g, "'")
    .replace(/&quot;/g, '"');
const paginationLabel = (label) => decodeHtmlEntities(label).replace(/<[^>]*>/g, '').trim();

const rowActionRefs = ref({});
const showForceDeleteModal = ref(false);
const showSoftDeleteModal = ref(false);
const userToForceDelete = ref(null);
const userToSoftDelete = ref(null);

const setRowActionRef = (userId, el) => {
    if (el) {
        rowActionRefs.value[userId] = el;
    }
};

const openRowContextMenu = (event, user) => {
    event.preventDefault();
    rowActionRefs.value[user.id]?.openContextMenu?.(event);
};

const handleVisitError = (errors) => {
    const message = errors?.user
        || errors?.message
        || 'Action impossible. Vérifie ton mot de passe ou tes droits.';
    notifyError(Array.isArray(message) ? message[0] : message);
};

const openSoftDeleteModal = (u) => {
    userToSoftDelete.value = u;
    showSoftDeleteModal.value = true;
};

const closeSoftDeleteModal = () => {
    showSoftDeleteModal.value = false;
    userToSoftDelete.value = null;
};

const confirmSoftDeletePrompt = () => {
    const target = userToSoftDelete.value;
    showSoftDeleteModal.value = false;
    if (!target?.id) {
        return;
    }
    requirePassword(
        'Confirmer l\'archivage',
        `Entre ton mot de passe pour archiver ${target.name || target.email}.`,
        'Archiver',
        () => {
            router.delete(route('user.admin.delete', target.id), {
                preserveScroll: true,
                onSuccess: () => {
                    success('Utilisateur archivé.');
                    userToSoftDelete.value = null;
                },
                onError: handleVisitError,
            });
        },
    );
};

const restoreUser = (u) => {
    requirePassword(
        'Confirmer la restauration',
        `Entre ton mot de passe pour restaurer ${u.name || u.email}.`,
        'Restaurer',
        () => {
            router.post(route('user.restore', u.id), {}, {
                preserveScroll: true,
                onSuccess: () => success('Utilisateur restauré.'),
                onError: handleVisitError,
            });
        },
    );
};

const openForceDeleteModal = (u) => {
    userToForceDelete.value = u;
    showForceDeleteModal.value = true;
};

const closeForceDeleteModal = () => {
    showForceDeleteModal.value = false;
    userToForceDelete.value = null;
};

const confirmForceDeletePrompt = () => {
    const target = userToForceDelete.value;
    showForceDeleteModal.value = false;
    if (!target?.id) {
        return;
    }
    requirePassword(
        'Confirmer la suppression définitive',
        `Entre ton mot de passe pour supprimer définitivement ${target.name || target.email}.`,
        'Supprimer définitivement',
        () => {
            router.post(route('user.forceDelete', target.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    success('Utilisateur supprimé définitivement.');
                    userToForceDelete.value = null;
                },
                onError: handleVisitError,
            });
        },
    );
};

const handleRowAction = (action, user) => {
    switch (action) {
    case 'open':
        goToEdit(user.id);
        break;
    case 'reset-password':
        goToResetPassword(user.id);
        break;
    case 'archive':
        openSoftDeleteModal(user);
        break;
    case 'restore':
        restoreUser(user);
        break;
    case 'force-delete':
        openForceDeleteModal(user);
        break;
    default:
        break;
    }
};
</script>

<template>
    <section class="space-y-5">
        <header class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold">Utilisateurs</h1>
                <p class="text-sm opacity-70">
                    Gérez les comptes, les accès et les réinitialisations de mot de passe.
                    Clic droit sur une ligne pour le menu d’actions.
                </p>
            </div>
            <Btn color="primary" size="sm" class="gap-2" @click="goToCreate">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Créer un utilisateur
            </Btn>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-3">
                <p class="text-xs uppercase tracking-wide opacity-60">Total</p>
                <p class="text-lg font-semibold">{{ totalUsersLabel }}</p>
            </div>
            <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-3">
                <p class="text-xs uppercase tracking-wide opacity-60">Actifs (page)</p>
                <p class="text-lg font-semibold">{{ activeUsersCount }}</p>
            </div>
            <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-3">
                <p class="text-xs uppercase tracking-wide opacity-60">Supprimés (page)</p>
                <p class="text-lg font-semibold">{{ archivedUsersCount }}</p>
            </div>
        </div>

        <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-4">
            <form class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end" @submit.prevent="applyFilters">
                <InputField
                    v-model="form.search"
                    label="Rechercher"
                    placeholder="Nom ou email"
                />
                <SelectField
                    v-model="form.role"
                    label="Rôle"
                    :options="roleOptions"
                    :searchable="false"
                />
                <SelectField
                    v-model="form.status"
                    label="Statut"
                    :options="statusOptions"
                    :searchable="false"
                />
                <div class="flex items-center gap-2">
                    <Btn color="primary" size="sm" class="gap-2" @click="applyFilters">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i>
                        Filtrer
                    </Btn>
                    <Btn color="neutral" variant="ghost" size="sm" @click="resetFilters">
                        Réinitialiser
                    </Btn>
                </div>
            </form>

            <div v-if="hasActiveFilters" class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs opacity-70">Filtres actifs :</span>
                <button
                    v-if="form.search"
                    type="button"
                    class="badge badge-soft badge-neutral gap-1"
                    @click="clearSearch"
                >
                    Recherche: "{{ form.search }}"
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <button
                    v-if="selectedRoleLabel"
                    type="button"
                    class="badge badge-soft badge-neutral gap-1"
                    @click="clearRole"
                >
                    Rôle: {{ selectedRoleLabel }}
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <button
                    v-if="form.status !== 'all' && selectedStatusLabel"
                    type="button"
                    class="badge badge-soft badge-neutral gap-1"
                    @click="clearStatus"
                >
                    Statut: {{ selectedStatusLabel }}
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="rounded-(--radius-box) border border-base-300 bg-base-100 overflow-x-auto">
            <table class="table table-sm md:table-md">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="u in usersData"
                        :key="u.id"
                        class="cursor-context-menu"
                        @contextmenu="openRowContextMenu($event, u)"
                    >
                        <td>
                            <div class="flex items-center gap-2">
                                <Avatar :src="userAvatarSrc(u)" :label="u.name" :alt="u.name" size="sm" />
                                <div class="min-w-0">
                                    <p class="font-medium truncate">{{ toLabel(u.name) }}</p>
                                    <p class="text-xs opacity-70">#{{ u.id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-72 truncate">{{ toLabel(u.email) }}</td>
                        <td>
                            <BadgeRole :role="u.role_name || 'user'" />
                        </td>
                        <td>
                            <span v-if="u.deleted_at" class="badge badge-warning badge-soft">Supprimé</span>
                            <span v-else class="badge badge-success badge-soft">Actif</span>
                        </td>
                        <td>
                            <UserListRowActions
                                :ref="(el) => setRowActionRef(u.id, el)"
                                :user="u"
                                :is-super-admin="isSuperAdmin"
                                :is-current-user="isCurrentUser(u.id)"
                                @open="handleRowAction('open', u)"
                                @reset-password="handleRowAction('reset-password', u)"
                                @archive="handleRowAction('archive', u)"
                                @restore="handleRowAction('restore', u)"
                                @force-delete="handleRowAction('force-delete', u)"
                            />
                        </td>
                    </tr>
                    <tr v-if="usersData.length === 0">
                        <td colspan="5" class="text-center py-6 opacity-70">
                            <div class="space-y-2">
                                <p>Aucun utilisateur ne correspond aux filtres.</p>
                                <Btn v-if="hasActiveFilters" size="xs" color="neutral" variant="ghost" @click="resetFilters">
                                    Réinitialiser les filtres
                                </Btn>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmModal
            :open="showSoftDeleteModal"
            title="Archiver le compte"
            :message="userToSoftDelete ? `Archiver le compte de ${userToSoftDelete.name || userToSoftDelete.email} ? Il pourra être restauré depuis la liste (filtre « Supprimés » ou « Tous »).` : ''"
            confirm-label="Continuer"
            cancel-label="Annuler"
            confirm-color="warning"
            confirm-icon="fa-solid fa-box-archive"
            @close="closeSoftDeleteModal"
            @confirm="confirmSoftDeletePrompt"
            @cancel="closeSoftDeleteModal"
        />

        <ConfirmModal
            :open="showForceDeleteModal"
            title="Supprimer définitivement"
            :message="userToForceDelete ? `Supprimer définitivement le compte de ${userToForceDelete.name || userToForceDelete.email} ? Cette action est irréversible.` : ''"
            confirm-label="Continuer"
            cancel-label="Annuler"
            confirm-color="error"
            confirm-icon="fa-solid fa-trash"
            @close="closeForceDeleteModal"
            @confirm="confirmForceDeletePrompt"
            @cancel="closeForceDeleteModal"
        />

        <ConfirmPasswordModal
            v-model:open="showPasswordModal"
            :title="passwordModalTitle"
            :message="passwordModalMessage"
            :confirm-label="passwordModalConfirmLabel"
            @confirmed="onPasswordConfirmed"
            @cancel="onPasswordModalCancel"
        />

        <div v-if="links.length > 3" class="flex items-center justify-between gap-3 flex-wrap">
            <p class="text-sm opacity-70">
                {{ totalUsersLabel }}
            </p>
            <div class="join">
                <button
                    v-for="link in links"
                    :key="`${link.url}-${link.label}`"
                    class="join-item btn btn-sm"
                    :class="{ 'btn-active': link.active }"
                    :disabled="!link.url"
                    @click="link.url ? router.visit(link.url, { preserveScroll: true, preserveState: false }) : null"
                >
                    {{ paginationLabel(link.label) }}
                </button>
            </div>
        </div>
    </section>
</template>

<style scoped>
.cursor-context-menu {
    cursor: context-menu;
}
</style>
