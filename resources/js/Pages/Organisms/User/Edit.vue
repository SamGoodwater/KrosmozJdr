<script setup>
import { ref, computed, watch } from 'vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import useEditableField from '@/Composables/useEditableField';
import { success, error } from '@/Utils/notificationManager';
import FileInput from '@/Pages/Atoms/inputs/FileInput.vue';
import Avatar from '@/Pages/Molecules/images/Avatar.vue';
import VerifyMailAlert from '@/Pages/Molecules/auth/VerifyMailAlert.vue';
import axios from 'axios';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    isAdminEdit: {
        type: Boolean,
        default: false
    }
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
            if (!props.isAdminEdit || (props.isAdminEdit && user.value.id === page.props.auth.user.id)) {
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
            if (!props.isAdminEdit || (props.isAdminEdit && user.value.id === page.props.auth.user.id)) {
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
        onError: () => {
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

    isPending.value = true;
    try {
        const response = await axios.post(
            props.isAdminEdit
                ? route('user.admin.updateAvatar', { user: user.value.id })
                : route('user.updateAvatar'),
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
        );

        if (response.data.success) {
            user.value = response.data.data;
            if (!props.isAdminEdit || (props.isAdminEdit && user.value.id === page.props.auth.user.id)) {
                page.props.auth.user.avatar = response.data.data.avatar;
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
            props.isAdminEdit
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
            user.value = response.data.data;
            if (!props.isAdminEdit || (props.isAdminEdit && user.value.id === page.props.auth.user.id)) {
                page.props.auth.user.avatar = response.data.data.avatar;
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

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-content-300">
                {{ isAdminEdit ? `Modification du profil de ${user.name}` : 'Informations du profil' }}
            </h2>

            <p class="mt-1 text-sm text-content-600">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form
            @submit.prevent class="mt-6 space-y-6"
            autocomplete="off"
        >
            <div class="flex flex-row gap-4">
                <div class="flex flex-col gap-4 w-1/2">
                    <InputLabel for="avatar" value="Avatar" />
                    <div class="relative group">
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
                        >
                            <Avatar
                                :source="user.avatar"
                                :alt-text="user.name"
                                size="3xl"
                                theme="rounded-full"
                            />
                        </FileInput>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-1/2">
                    <div>
                        <InputLabel for="name" value="Pseudo" />
                            <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            :field="fields.name"
                            required
                            autofocus
                            :useFieldComposable="true"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Adresse mail" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            :field="fields.email"
                            required
                            :useFieldComposable="true"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="!user.is_verified">
                        <VerifyMailAlert />
                    </div>
                </div>
            </div>
        </form>

                    <!-- Trait de séparation -->
        <hr class="border-gray-300 dark:border-gray-700 my-4" />

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Mot de passe actuel" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="formPassword.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="formPassword.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nouveau mot de passe" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="formPassword.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="formPassword.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmer le mot de passe"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="formPassword.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="formPassword.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <Btn :disabled="formPassword.processing" label="Enregistrer" />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="formPassword.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Enregistré.
                    </p>
                </Transition>
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
