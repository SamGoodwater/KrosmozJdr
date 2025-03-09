<script setup>
import { ref, computed, watch } from 'vue';

import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import useEditableField from '@/Composables/useEditableField';
import { success, error } from '@/Utils/notificationManager';
import FileInput from '@/Pages/Atoms/inputs/FileInput.vue';
import Avatar from '@/Pages/Molecules/images/Avatar.vue';
import VerifyMailAlert from '@/Pages/Molecules/auth/VerifyMailAlert.vue';
import axios from 'axios';
import PasswordInput from '@/Pages/Atoms/inputs/PasswordInput.vue';
import Dropdown from '@/Pages/Atoms/actions/Dropdown.vue';
import BadgeRole from "@/Pages/Organisms/User/Molecules/badgeRole.vue";
import { verifyRole, ROLES, getRoleTranslation } from '@/Utils/Roles';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.user.data);

// Création du formulaire partagé
const form = useForm({
    name: user.value.name,
    email: user.value.email,
});

// Création des champs éditables
const fields = {
    name: useEditableField(user.value.name, {
        field: 'name',
        route: route('user.update'),
        onSuccess: (response) => {
            // Mise à jour de l'utilisateur modifié
            Object.assign(user.value, response.data);

            // Si c'est l'utilisateur connecté qui est modifié (soit un utilisateur normal, soit un admin qui modifie son propre compte)
            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) || (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
                Object.assign(page.props.auth.user, response.data);
            }
            success('Le nom a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour du nom')
    }),
    email: useEditableField(user.value.email, {
        field: 'email',
        route: route('user.update'),
        onSuccess: (response) => {
            // Mise à jour de l'utilisateur modifié
            Object.assign(user.value, response.data);

            // Si c'est l'utilisateur connecté qui est modifié (soit un utilisateur normal, soit un admin qui modifie son propre compte)
            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) || (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
                Object.assign(page.props.auth.user, response.data);
            }
            success('L\'email a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour de l\'email')
    }),
};

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

const avatarFile = ref(null);
const isHovering = ref(false);
const isPending = ref(false);

const updateAvatar = async () => {
    if (!avatarFile.value || isPending.value) return;

    const formData = new FormData();
    formData.append('file', avatarFile.value);

    // Ajouter un paramètre pour supprimer l'ancien fichier
    if (user.value.avatar) {
        formData.append('deleteOldFile', user.value.avatar);
    }

    // Ajouter un timestamp pour éviter le cache
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
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                params: {
                    timestamp: timestamp // Ajouter le timestamp dans les paramètres
                }
            }
        );

        if (response.data.success) {
            // Forcer le rafraîchissement de l'image
            const newAvatarUrl = response.data.data.avatar + `?timestamp=${timestamp}`;
            user.value.avatar = newAvatarUrl;

            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) || (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
                page.props.auth.user.avatar = newAvatarUrl + `?timestamp=${timestamp}`;
            }
            avatarFile.value = null;
            success('L\'avatar a été mis à jour avec succès');
        } else {
            error(response.data.message);
        }
    } catch (err) {
        if (err.response?.data?.errors) {
            const validationErrors = Object.values(err.response.data.errors).flat();
            error(validationErrors[0] || 'Erreur de validation');
        } else {
            error(err.response?.data?.message || 'Une erreur est survenue lors de la mise à jour de l\'avatar');
        }
    } finally {
        isPending.value = false;
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
            // Mettre explicitement l'avatar à null
            user.value.avatar = null;

            if (!verifyRole(page.props.auth.user.role, ROLES.ADMIN) || (verifyRole(page.props.auth.user.role, ROLES.ADMIN) && user.value.id === page.props.auth.user.id)) {
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

// Ajouter un watcher pour déclencher l'upload automatiquement
watch(avatarFile, (newFile) => {
    if (newFile) {
        updateAvatar();
    }
});

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
    <section>
        <header>
            <h2 class="text-lg font-medium text-content-300">
                {{ verifyRole(page.props.auth.user.role, ROLES.ADMIN) ? `Modification du profil de ${user.name}` : 'Informations du profil' }}
            </h2>

            <p class="mt-1 text-sm text-content-600">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form @submit.prevent class="mt-6 space-y-6" autocomplete="off">
            <div class="flex flex-row gap-4">
                <div class="flex flex-col gap-4 w-1/2">
                    <FileInput
                        ref="avatar"
                        accept="image/*"
                        :maxSize="5242880"
                        tooltip="Déposer ou cliquer pour changer votre avatar"
                        helper="Format accepté : JPG, PNG, GIF, SVG. Taille maximale : 5MB"
                        @error="(message) => error(message)"
                        v-model="avatarFile"
                        :currentFile="user.avatar"
                        @delete="deleteAvatar"
                        class="mt-1"
                        theme="ghost"
                        inputLabel="Avatar"
                    >
                        <Avatar
                            :source="user.avatar"
                            :alt-text="user.name"
                            size="3xl"
                            theme="rounded-full"
                        />
                    </FileInput>
                </div>

                <div class="flex flex-col gap-4 w-1/2">
                    <div class="mt-2">
                        <BadgeRole :role="user.role" />
                    </div>
                    <div>
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            :field="fields.name"
                            required
                            autofocus
                            :useFieldComposable="true"
                            inputLabel="Pseudo"
                        />
                    </div>

                    <div>
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            :field="fields.email"
                            required
                            :useFieldComposable="true"
                            inputLabel="Adresse mail"
                        />
                    </div>

                    <div v-if="!user.is_verified">
                        <VerifyMailAlert />
                    </div>
                </div>
            </div>
        </form>

        <hr class="border-gray-300 dark:border-gray-700 my-4" />

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <PasswordInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="formPassword.current_password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    required
                    inputLabel="Mot de passe actuel"
                    :errorMessage="formPassword.errors.current_password"
                />
            </div>

            <div>
                <PasswordInput
                    id="password"
                    ref="passwordInput"
                    v-model="formPassword.password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    required
                    inputLabel="Nouveau mot de passe"
                    :errorMessage="formPassword.errors.password"
                />
            </div>

            <div>
                <PasswordInput
                    id="password_confirmation"
                    v-model="formPassword.password_confirmation"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    required
                    inputLabel="Confirmer le mot de passe"
                    :errorMessage="formPassword.errors.password_confirmation"
                />
            </div>

            <div class="flex items-center gap-4">
                <Btn type="submit" :disabled="formPassword.processing" label="Enregistrer" />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="formPassword.recentlySuccessful" class="text-sm text-gray-600">
                        Modifier le mot de passe.
                    </p>
                </Transition>
            </div>
        </form>

        <div v-if="verifyRole(page.props.auth.user.role, ROLES.ADMIN)" class="mt-6">
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <div class="mt-6">
                <h3 class="text-lg font-medium text-content-300">
                    Actions administrateurs
                </h3>
            </div>

            <Dropdown
                :label="formRole.role"
                placement="bottom-end"
                color="base-100"
                inputLabel="Modifier le rôle de l'utilisateur"
                :errorMessage="formRole.errors.role"
            >
                <template #list>
                    <li v-for="(roleValue, roleKey) in $page.props.roles" :key="roleKey">
                        <button
                            type="button"
                            @click="formRole.role = roleValue; updateRole()"
                            class="w-full text-left px-4 py-2 hover:bg-base-200"
                            :class="{ 'bg-base-200': formRole.role === roleValue }"
                        >
                            {{ getRoleTranslation(roleValue) }}
                        </button>
                    </li>
                </template>
            </Dropdown>
        </div>
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
