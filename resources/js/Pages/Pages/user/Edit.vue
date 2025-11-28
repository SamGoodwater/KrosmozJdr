<script setup>
/**
 * @description
 * Page d'édition du profil utilisateur.
 * - Édition des informations de base (nom, email, avatar)
 * - Actions administrateurs (mot de passe, rôle)
 * - Structure DRY, accessibilité, tooltips, etc.
 */
import { ref, watch, computed, onMounted, nextTick } from 'vue';
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
// Le user est passé via page.props.user (UserResource)
// Note: Les données peuvent être dans user.data (structure Inertia/Resource)
const user = computed(() => {
    const userData = page.props.user || {};
    // Si les données sont dans user.data, on les extrait
    if (userData.data && typeof userData.data === 'object' && userData.data.id) {
        return userData.data;
    }
    // Sinon, on retourne directement userData
    return userData;
});

// Formulaire unifié pour le profil (nom + email)
// Initialiser avec des valeurs vides, puis remplir via watch
const formProfile = useForm({
    name: '',
    email: '',
});

// Rôle (admin)
const formRole = useForm({ role: 1 });

// Fonction pour initialiser/mettre à jour les formulaires
const initializeForms = (userData) => {
    // Extraire les données si elles sont dans userData.data
    const data = (userData?.data && typeof userData.data === 'object' && userData.data.id) 
        ? userData.data 
        : userData;
    
    if (!data || Object.keys(data).length === 0 || !data.id) {
        console.log('Edit.vue - initializeForms: userData is empty or invalid', data);
        return;
    }
    
    // Mettre à jour directement les propriétés du formulaire
    // Cela fonctionne mieux avec v-model dans les composants
    if (data.name !== undefined) {
        formProfile.name = data.name;
    }
    if (data.email !== undefined) {
        formProfile.email = data.email;
    }
    if (data.role !== undefined) {
        formRole.role = data.role;
    }
};

// Surveiller le computed user pour initialiser les formulaires dès que les données sont disponibles
watch(user, (newUser) => {
    // Le computed user devrait maintenant retourner directement les données (sans data)
    if (newUser && newUser.id) {
        initializeForms(newUser);
    }
}, { immediate: true, deep: true });

// Initialiser aussi après le montage pour être sûr
onMounted(() => {
    nextTick(() => {
        const currentUser = user.value;
        if (currentUser && Object.keys(currentUser).length > 0 && currentUser.id) {
            initializeForms(currentUser);
        }
    });
});

const updateProfile = () => {
    // Déterminer la route selon le contexte (profil courant ou admin modifiant un autre utilisateur)
    const userId = user.value?.id;
    const currentUserId = page.props.auth?.user?.id;
    const routeName = (userId && userId !== currentUserId) 
        ? 'user.admin.update' 
        : 'user.update';
    const routeParams = (userId && userId !== currentUserId) ? [userId] : [];
    
    formProfile.patch(route(routeName, ...routeParams), {
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

// Computed pour déterminer si on modifie son propre profil
const isSelfUpdate = computed(() => {
    const userId = user.value?.id;
    const currentUserId = page.props.auth?.user?.id;
    return userId && userId === currentUserId;
});

// Computed pour déterminer si l'utilisateur est admin
const isAdmin = computed(() => {
    const userRole = page.props.auth?.user?.role;
    return userRole === 4 || userRole === 5; // admin = 4, super_admin = 5
});

const updatePassword = () => {
    // Déterminer la route selon le contexte (profil courant ou admin modifiant un autre utilisateur)
    const userId = user.value?.id;
    const currentUserId = page.props.auth?.user?.id;
    const routeName = (userId && userId !== currentUserId) 
        ? 'user.admin.updatePassword' 
        : 'user.updatePassword';
    const routeParams = (userId && userId !== currentUserId) ? [userId] : [];
    
    formPassword.patch(route(routeName, ...routeParams), {
        preserveScroll: true,
        onSuccess: () => {
            success('Mot de passe mis à jour avec succès.');
            formPassword.reset();
        },
        onError: () => error('Erreur lors de la mise à jour du mot de passe.'),
    });
};

const updateRole = () => {
    formRole.patch(route('user.admin.updateRole', user.value?.id || page.props.auth?.user?.id), {
        preserveScroll: true,
        onSuccess: () => success('Rôle mis à jour avec succès.'),
        onError: () => error('Erreur lors de la mise à jour du rôle.'),
    });
};

// Validation computed pour les champs du profil
const nameValidation = computed(() => {
    if (!formProfile.errors.name) return null;
    return {
        state: 'error',
        message: formProfile.errors.name,
        showNotification: false
    };
});

const emailValidation = computed(() => {
    if (!formProfile.errors.email) return null;
    return {
        state: 'error',
        message: formProfile.errors.email,
        showNotification: false
    };
});

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
                    verifyRole(page.props.auth?.user?.role || 1, ROLES.ADMIN)
                        ? `Modification du profil de ${user?.name || 'Utilisateur'}`
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
                            :currentFile="user?.avatar"
                            accept="image/*"
                            :maxSize="5242880"
                            helper="Format accepté : JPG, PNG, GIF, SVG, WEBP. Taille maximale : 5MB"
                            @error="(message) => error(message)"
                            @delete="deleteAvatar"
                            class="mt-1"
                            variant="ghost"
                            color="primary"
                            inputLabel="Avatar"
                        >
                            <template #default>
                                <Avatar
                                    :src="user?.avatar"
                                    :label="user?.name"
                                    :alt="user?.name"
                                    size="3xl"
                                    rounded="full"
                                />
                            </template>
                        </File>
                    </Tooltip>
                </div>
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="mt-2">
                        <BadgeRole :role="user?.role_name || 'user'" />
                    </div>
                    <Tooltip content="Votre pseudo d'utilisateur" placement="top">
                        <InputField
                            id="name"
                            class="mt-1 block w-full"
                            v-model="formProfile.name"
                            required
                            autofocus
                            label="Pseudo"
                            :validation="nameValidation"
                        />
                    </Tooltip>
                    <Tooltip content="Votre adresse email" placement="top">
                        <InputField
                            id="email"
                            class="mt-1 block w-full"
                            v-model="formProfile.email"
                            type="email"
                            label="Adresse mail"
                            :validation="emailValidation"
                        />
                    </Tooltip>
                    <div v-if="user && !user.is_verified">
                        <VerifyMailAlert />
                    </div>
                </div>
            </div>
            
            <!-- Bouton de sauvegarde pour le profil -->
            <div class="flex items-center gap-4 mt-6">
                <Tooltip content="Enregistrer les modifications du profil" placement="top">
                    <Btn
                        color="primary"
                        @click="updateProfile"
                    >
                        Enregistrer le profil
                    </Btn>
                </Tooltip>
                <Tooltip content="Annuler les modifications" placement="top">
                    <Btn
                        color="neutral"
                        @click="formProfile.reset()"
                    >
                        Annuler
                    </Btn>
                </Tooltip>
            </div>
            
            <!-- Section mot de passe -->
            <div class="mt-6">
                <hr class="border-gray-300 dark:border-gray-700 my-4" />
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-content-300">
                        {{ isAdmin && !isSelfUpdate ? 'Actions administrateurs - Mot de passe' : 'Modifier le mot de passe' }}
                    </h3>
                    <p class="mt-1 text-sm text-content-600">
                        {{ isAdmin && !isSelfUpdate ? 'Modifiez le mot de passe de l\'utilisateur.' : 'Modifiez votre mot de passe.' }}
                    </p>
                </div>
                <div class="mt-6 space-y-4">
                    <!-- Champ current_password seulement si l'utilisateur modifie son propre mot de passe -->
                    <Tooltip 
                        v-if="isSelfUpdate" 
                        content="Entrez votre mot de passe actuel" 
                        placement="top"
                    >
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
            <!-- Section admin : rôle (uniquement pour les admins modifiant un autre utilisateur) -->
            <div
                v-if="isAdmin && !isSelfUpdate"
                class="mt-6"
            >
                <hr class="border-gray-300 dark:border-gray-700 my-4" />
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-content-300">
                        Actions administrateurs - Rôle
                    </h3>
                    <p class="mt-1 text-sm text-content-600">
                        Modifiez le rôle de l'utilisateur.
                    </p>
                </div>
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
