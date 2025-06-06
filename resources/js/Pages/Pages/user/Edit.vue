/**
* Edit component for user profile management.
* Provides functionality to update user information, avatar, password, and role.
*
* Features:
* - Profile information editing (name, email)
* - Avatar management (upload, delete)
* - Password update
* - Role management (admin only)
* - Email verification status
*
* Props:
* - mustVerifyEmail: Boolean indicating if email verification is required
* - status: String containing verification status
* - theme: Theme configuration for styling
*
* Events:
* - @profileUpdated: Emitted when profile information is updated
* - @passwordUpdated: Emitted when password is updated
* - @avatarUpdated: Emitted when avatar is updated
* - @roleUpdated: Emitted when role is updated (admin only)
*/
<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { extractTheme, combinePropsWithTheme } from '@/Utils/extractTheme';
import { commonProps } from '@/Utils/commonProps';
import { success, error } from '@/Utils/notification/NotificationManager';
import { verifyRole, ROLES, getRoleTranslation, getRoleColor } from '@/Utils/user/RoleManager';
import useEditableField from '@/Composables/form/useEditableField';

// Composants Atoms
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import FileInput from '@/Pages/Atoms/inputs/FileInput.vue';
import PasswordInput from '@/Pages/Atoms/inputs/PasswordInput.vue';
import Dropdown from '@/Pages/Atoms/actions/Dropdown.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

// Composants Molecules
import Avatar from '@/Pages/Molecules/images/Avatar.vue';
import VerifyMailAlert from '@/Pages/Molecules/auth/VerifyMailAlert.vue';
import BadgeRole from "@/Pages/Molecules/user/badgeRole.vue";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    }
});

const page = usePage();
const user = computed(() => page.props.user.data);

// Gestion des champs éditables
const fields = {
    name: useEditableField(user.value.name, {
        field: 'name',
        route: route('user.update'),
        onSuccess: (response) => {
            updateUserData(response.data);
            success('Le nom a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour du nom')
    }),
    email: useEditableField(user.value.email, {
        field: 'email',
        route: route('user.update'),
        onSuccess: (response) => {
            updateUserData(response.data);
            success('L\'email a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour de l\'email')
    }),
};

// Fonction utilitaire pour mettre à jour les données utilisateur
const updateUserData = (data) => {
    Object.assign(user.value, data);
    if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) ||
        (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
        Object.assign(page.props.auth.user, data);
    }
};

// Gestion du mot de passe
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const formPassword = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    formPassword.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            formPassword.reset();
            success('Le mot de passe a été mis à jour avec succès');
        },
        onError: (errors) => {
            error('Une erreur est survenue lors de la mise à jour du mot de passe');
            if (formPassword.errors.password) {
                formPassword.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (formPassword.errors.current_password) {
                formPassword.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};

// Gestion de l'avatar
const avatarFile = ref(null);
const isHovering = ref(false);
const isPending = ref(false);

const updateAvatar = async () => {
    if (!avatarFile.value || isPending.value) return;

    const formData = new FormData();
    formData.append('file', avatarFile.value);

    if (user.value.avatar) {
        formData.append('deleteOldFile', user.value.avatar);
    }

    const timestamp = Date.now();
    formData.append('timestamp', timestamp);

    isPending.value = true;
    try {
        const response = await axios.post(
            verifyRole(page.props.auth.user.role, ROLES.ADMIN)
                ? route('user.admin.updateAvatar', { user: user.value.id })
                : route('user.updateAvatar'),
            formData,
            {
                headers: { 'Content-Type': 'multipart/form-data' },
                params: { timestamp }
            }
        );

        if (response.data.success) {
            const newAvatarUrl = response.data.data.avatar + `?timestamp=${timestamp}`;
            user.value.avatar = newAvatarUrl;

            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) ||
                (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
                page.props.auth.user.avatar = newAvatarUrl;
            }
            avatarFile.value = null;
            success('L\'avatar a été mis à jour avec succès');
        } else {
            error(response.data.message);
        }
    } catch (err) {
        handleAvatarError(err);
    } finally {
        isPending.value = false;
    }
};

const handleAvatarError = (err) => {
    if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error(validationErrors[0] || 'Erreur de validation');
    } else {
        error(err.response?.data?.message || 'Une erreur est survenue lors de la mise à jour de l\'avatar');
    }
};

const deleteAvatar = async () => {
    if (isPending.value) return;

    isPending.value = true;
    try {
        const response = await axios.delete(
            verifyRole(page.props.auth.user.role, ROLES.ADMIN)
                ? route('user.admin.deleteAvatar', { user: user.value.id })
                : route('user.deleteAvatar'),
            {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            }
        );

        if (response.data.success) {
            user.value.avatar = null;
            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) ||
                (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
                page.props.auth.user.avatar = null;
            }
            success('L\'avatar a été supprimé avec succès');
        } else {
            error(response.data.message);
        }
    } catch (err) {
        console.error('Erreur lors de la suppression de l\'avatar:', err);
        error(err.response?.data?.message || 'Une erreur est survenue lors de la suppression de l\'avatar');
    } finally {
        isPending.value = false;
    }
};

// Watcher pour l'upload automatique de l'avatar
watch(avatarFile, (newFile) => {
    if (newFile) {
        updateAvatar();
    }
});

// Gestion du rôle (admin uniquement)
const formRole = useForm({
    role: user.value.role,
});

const updateRole = () => {
    formRole.patch(route('user.admin.updateRole', { user: user.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            success('Le rôle a été mis à jour avec succès');
        },
        onError: () => {
            error('Une erreur est survenue lors de la mise à jour du rôle');
        },
    });
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-content-300">
                {{ verifyRole(page.props.auth.user.role, ROLES.ADMIN) ? `Modification du profil de ${user.name}` :
                    'Informations du profil' }}
            </h2>

            <p class="mt-1 text-sm text-content-600">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form @submit.prevent class="mt-6 space-y-6" autocomplete="off">
            <div class="flex flex-row gap-4">
                <div class="flex flex-col gap-4 w-1/2">
                    <FileInput ref="avatar" accept="image/*" :maxSize="5242880"
                        tooltip="Déposer ou cliquer pour changer votre avatar"
                        helper="Format accepté : JPG, PNG, GIF, SVG. Taille maximale : 5MB"
                        @error="(message) => error(message)" v-model="avatarFile" :currentFile="user.avatar"
                        @delete="deleteAvatar" class="mt-1" variant="ghost" theme="primary" inputLabel="Avatar">
                        <Avatar :source="user.avatar" :alt-text="user.name" size="3xl" theme="rounded-full" />
                    </FileInput>
                </div>

                <div class="flex flex-col gap-4 w-1/2">
                    <div class="mt-2">
                        <BadgeRole :role="user.role" />
                    </div>
                    <div>
                        <TextInput id="name" class="mt-1 block w-full" :field="fields.name" required autofocus
                            :useFieldComposable="true" inputLabel="Pseudo" tooltip="Votre pseudo d'utilisateur" />
                    </div>
                    <div>
                        <TextInput id="email" class="mt-1 block w-full" :field="fields.email" :useFieldComposable="true"
                            inputLabel="Adresse mail" />
                    </div>
                    <div v-if="!user.is_verified">
                        <VerifyMailAlert />
                    </div>
                </div>
            </div>

            <div v-if="verifyRole(page.props.auth.user.role, ROLES.ADMIN)" class="mt-6">
                <hr class="border-gray-300 dark:border-gray-700 my-4" />

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-content-300">
                        Actions administrateurs
                    </h3>
                </div>

                <div class="mt-6 space-y-4">
                    <PasswordInput ref="currentPasswordInput" v-model="formPassword.current_password" theme="primary"
                        inputLabel="Mot de passe actuel" tooltip="Entrez votre mot de passe actuel"
                        @keyup.enter="updatePassword" />

                    <PasswordInput ref="passwordInput" v-model="formPassword.password" theme="primary"
                        inputLabel="Nouveau mot de passe" tooltip="Entrez votre nouveau mot de passe"
                        @keyup.enter="updatePassword" />

                    <PasswordInput v-model="formPassword.password_confirmation" theme="primary"
                        inputLabel="Confirmation du mot de passe" tooltip="Confirmez votre nouveau mot de passe"
                        @keyup.enter="updatePassword" />

                    <div class="flex items-center gap-4">
                        <Btn theme="primary" label="Enregistrer" tooltip="Mettre à jour le mot de passe"
                            @click="updatePassword" />
                        <Btn theme="neutral" label="Annuler" tooltip="Annuler la modification"
                            @click="formPassword.reset()" />
                    </div>
                </div>
            </div>

            <!-- Section Rôle (Admin uniquement) -->
            <div v-if="verifyRole(page.props.auth.user.role, ROLES.ADMIN)" class="mt-6">
                <h3 class="text-lg font-medium text-primary-100">
                    Gestion du rôle
                </h3>
                <p class="mt-1 text-sm text-primary-200">
                    Modifiez le rôle de l'utilisateur.
                </p>

                <div class="mt-6 space-y-4">
                    <Dropdown v-model="formRole.role" theme="primary" inputLabel="Rôle"
                        tooltip="Sélectionnez le rôle de l'utilisateur">
                        <option v-for="role in Object.values(ROLES)" :key="role" :value="role">
                            {{ getRoleTranslation(role) }}
                        </option>
                    </Dropdown>

                    <div class="flex items-center gap-4">
                        <Btn theme="primary" label="Enregistrer" tooltip="Mettre à jour le rôle" @click="updateRole" />
                        <Btn theme="neutral" label="Annuler" tooltip="Annuler la modification"
                            @click="formRole.reset()" />
                    </div>
                </div>
            </div>
        </form>
    </section>
</template>

<style scoped>
.avatar-overlay {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}

.group:hover .avatar-overlay {
    opacity: 1;
}
</style>
