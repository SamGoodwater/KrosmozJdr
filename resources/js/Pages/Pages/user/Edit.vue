<script setup>
/**
 * @description
 * Page d'édition du profil utilisateur.
 * - Édition des informations de base (nom, email, avatar)
 * - Actions administrateurs (mot de passe, rôle)
 * - Structure DRY, accessibilité, tooltips, etc.
 */
import { ref, watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { verifyRole, getRoleTranslation, ROLES } from '@/Utils/user/RoleManager';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import File from '@/Pages/Molecules/data-input/FileField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Avatar from '@/Pages/Atoms/data-display/Avatar.vue';
import BadgeRole from '@/Pages/Molecules/user/BadgeRole.vue';
import VerifyMailAlert from '@/Pages/Molecules/user/VerifyMailAlert.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const page = usePage();
const { success, error } = useNotificationStore();

// Utilisateur à éditer
const user = ref(page.props.user);

// Champs de base
const fields = {
    name: useForm({ name: user.value.name }),
    email: useForm({ email: user.value.email }),
};

const updateProfile = () => {
    fields.name.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => success('Profil mis à jour avec succès.'),
        onError: () => error('Erreur lors de la mise à jour du profil.'),
    });
};

// Avatar
const avatarFile = ref(null);
const updateAvatar = () => {
    /* ... logique suppression avatar ... */
};
watch(avatarFile, (newFile) => {
    if (newFile) updateAvatar();
});

// Mot de passe
const formPassword = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});
const updatePassword = () => {
    /* ... logique update password ... */
};

// Rôle (admin)
const formRole = useForm({ role: user.value.role });
const updateRole = () => {
    /* ... logique update role ... */
};

// Validation computed pour les champs de mot de passe
const currentPasswordValidation = computed(() => {
    if (!formPassword.errors.current_password) return null;
    return {
        state: 'error',
        message: formPassword.errors.current_password,
        showNotification: false
    };
});

const passwordValidation = computed(() => {
    if (!formPassword.errors.password) return null;
    return {
        state: 'error',
        message: formPassword.errors.password,
        showNotification: false
    };
});

const passwordConfirmationValidation = computed(() => {
    if (!formPassword.errors.password_confirmation) return null;
    return {
        state: 'error',
        message: formPassword.errors.password_confirmation,
        showNotification: false
    };
});

// Validation computed pour le rôle
const roleValidation = computed(() => {
    if (!formRole.errors.role) return null;
    return {
        state: 'error',
        message: formRole.errors.role,
        showNotification: false
    };
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-content-300">
                {{
                    verifyRole(page.props.auth.user.role, ROLES.ADMIN)
                        ? `Modification du profil de ${user.value.name}`
                        : "Informations du profil"
                }}
            </h2>
            <p class="mt-1 text-sm text-content-600">
                Mettez à jour les informations de votre compte et votre adresse
                email.
            </p>
        </header>
        <form @submit.prevent class="mt-6 space-y-6" autocomplete="off">
            <div class="flex flex-row gap-4">
                <div class="flex flex-col gap-4 w-1/2">
                    <Tooltip content="Déposer ou cliquer pour changer votre avatar" placement="top">
                        <File
                            v-model="avatarFile"
                            :currentFile="user.value.avatar"
                            accept="image/*"
                            :maxSize="5242880"
                            helper="Format accepté : JPG, PNG, GIF, SVG. Taille maximale : 5MB"
                            @error="(message) => error(message)"
                            @delete="deleteAvatar"
                            class="mt-1"
                            variant="ghost"
                            color="primary"
                            inputLabel="Avatar"
                        >
                            <template #default>
                                <Avatar
                                    v-if="user.value.avatar"
                                    :src="user.value.avatar"
                                    :alt="user.value.name"
                                    size="3xl"
                                    rounded="full"
                                />
                            </template>
                        </File>
                    </Tooltip>
                </div>
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="mt-2">
                        <BadgeRole :role="user.value.role" />
                    </div>
                    <Tooltip content="Votre pseudo d'utilisateur" placement="top">
                        <InputField
                            id="name"
                            class="mt-1 block w-full"
                            :field="fields.name"
                            required
                            autofocus
                            :useFieldComposable="true"
                            label="Pseudo"
                        />
                    </Tooltip>
                    <InputField
                        id="email"
                        class="mt-1 block w-full"
                        :field="fields.email"
                        :useFieldComposable="true"
                        label="Adresse mail"
                    />
                    <div v-if="!user.value.is_verified">
                        <VerifyMailAlert />
                    </div>
                </div>
            </div>
            <!-- Section admin : mot de passe -->
            <div
                v-if="verifyRole(page.props.auth.user.role, ROLES.ADMIN)"
                class="mt-6"
            >
                <hr class="border-gray-300 dark:border-gray-700 my-4" />
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-content-300">
                        Actions administrateurs
                    </h3>
                </div>
                <div class="mt-6 space-y-4">
                    <Tooltip content="Entrez votre mot de passe actuel" placement="top">
                        <InputField
                            v-model="formPassword.current_password"
                            type="password"
                            variant="glass"
                            color="primary"
                            label="Mot de passe actuel"
                            :validation="currentPasswordValidation"
                            @keyup.enter="updatePassword"
                        />
                    </Tooltip>
                    <Tooltip content="Entrez votre nouveau mot de passe" placement="top">
                        <InputField
                            v-model="formPassword.password"
                            type="password"
                            variant="glass"
                            color="primary"
                            label="Nouveau mot de passe"
                            :validation="passwordValidation"
                            @keyup.enter="updatePassword"
                        />
                    </Tooltip>
                    <Tooltip content="Confirmez votre nouveau mot de passe" placement="top">
                        <InputField
                            v-model="formPassword.password_confirmation"
                            type="password"
                            variant="glass"
                            color="primary"
                            label="Confirmation du mot de passe"
                            :validation="passwordConfirmationValidation"
                            @keyup.enter="updatePassword"
                        />
                    </Tooltip>
                    <div class="flex items-center gap-4">
                        <Tooltip content="Mettre à jour le mot de passe" placement="top">
                            <Btn
                                color="primary"
                                @click="updatePassword"
                                >Enregistrer
                            </Btn>
                        </Tooltip>
                        <Tooltip content="Annuler la modification" placement="top">
                            <Btn
                                color="neutral"
                                @click="formPassword.reset()"
                                >Annuler
                            </Btn>
                        </Tooltip>
                    </div>
                </div>
            </div>
            <!-- Section admin : rôle -->
            <div
                v-if="verifyRole(page.props.auth.user.role, ROLES.ADMIN)"
                class="mt-6"
            >
                <h3 class="text-lg font-medium text-primary-100">
                    Gestion du rôle
                </h3>
                <p class="mt-1 text-sm text-primary-200">
                    Modifiez le rôle de l'utilisateur.
                </p>
                <div class="mt-6 space-y-4">
                    <Tooltip content="Sélectionnez le rôle de l'utilisateur" placement="top">
                        <SelectField
                            v-model="formRole.role"
                            variant="glass"
                            color="primary"
                            label="Rôle"
                            :options="Object.values(ROLES).map(role => ({ 
                                value: role, 
                                label: getRoleTranslation(role) 
                            }))"
                            :validation="roleValidation"
                        />
                    </Tooltip>
                    <div class="flex items-center gap-4">
                        <Tooltip content="Mettre à jour le rôle" placement="top">
                            <Btn
                                color="primary"
                                @click="updateRole"
                                >Enregistrer</Btn
                            >
                        </Tooltip>
                        <Tooltip content="Annuler la modification" placement="top">
                            <Btn
                                color="neutral"
                                @click="formRole.reset()"
                                >Annuler</Btn
                            >
                        </Tooltip>
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
