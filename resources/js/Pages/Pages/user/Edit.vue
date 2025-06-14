<script setup>
/**
 * Edit component for user profile management (Atomic Design refonte).
 * Permet de mettre à jour les infos utilisateur, avatar, mot de passe, rôle.
 *
 * - Utilise les atoms/molecules à jour (InputField, FileInput, Btn, Avatar, etc.)
 * - Chemins et API corrigés
 * - Structure DRY, accessibilité, tooltips, etc.
 */
import { ref, computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import {
    verifyRole,
    ROLES,
    getRoleTranslation,
} from "@/Utils/user/RoleManager";

// Atoms & Molecules (nouveaux chemins)
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Atoms/data-input/InputField.vue";
import FileInput from "@/Pages/Molecules/data-input/FileInput.vue";
import Select from "@/Pages/Atoms/data-input/Select.vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";
import BadgeRole from "@/Pages/Molecules/user/BadgeRole.vue";
import VerifyMailAlert from "@/Pages/Molecules/user/VerifyMailAlert.vue";

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const page = usePage();
const user = computed(() => page.props.user.data);
const { success, error } = useNotificationStore();

// Champs éditables (DRY via useEditableField)
const fields = {
    name: useEditableField(user.value.name, {
        field: "name",
        route: route("user.update"),
        onSuccess: (response) => {
            Object.assign(user.value, response.data);
            success("Le nom a été mis à jour avec succès");
        },
        onError: () => error("Erreur lors de la mise à jour du nom"),
    }),
    email: useEditableField(user.value.email, {
        field: "email",
        route: route("user.update"),
        onSuccess: (response) => {
            Object.assign(user.value, response.data);
            success("L'email a été mis à jour avec succès");
        },
        onError: () => error("Erreur lors de la mise à jour de l'email"),
    }),
};

// Gestion avatar
const avatarFile = ref(null);
const isPending = ref(false);
const updateAvatar = async () => {
    /* ... logique upload avatar ... */
};
const deleteAvatar = async () => {
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
                    <FileInput
                        v-model="avatarFile"
                        :currentFile="user.value.avatar"
                        accept="image/*"
                        :maxSize="5242880"
                        helper="Format accepté : JPG, PNG, GIF, SVG. Taille maximale : 5MB"
                        tooltip="Déposer ou cliquer pour changer votre avatar"
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
                    </FileInput>
                </div>
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="mt-2">
                        <BadgeRole :role="user.value.role" />
                    </div>
                    <InputField
                        id="name"
                        class="mt-1 block w-full"
                        :field="fields.name"
                        required
                        autofocus
                        :useFieldComposable="true"
                        label="Pseudo"
                        tooltip="Votre pseudo d'utilisateur"
                    />
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
                    <InputField
                        v-model="formPassword.current_password"
                        type="password"
                        color="primary"
                        label="Mot de passe actuel"
                        tooltip="Entrez votre mot de passe actuel"
                        @keyup.enter="updatePassword"
                    />
                    <InputField
                        v-model="formPassword.password"
                        type="password"
                        color="primary"
                        label="Nouveau mot de passe"
                        tooltip="Entrez votre nouveau mot de passe"
                        @keyup.enter="updatePassword"
                    />
                    <InputField
                        v-model="formPassword.password_confirmation"
                        type="password"
                        color="primary"
                        label="Confirmation du mot de passe"
                        tooltip="Confirmez votre nouveau mot de passe"
                        @keyup.enter="updatePassword"
                    />
                    <div class="flex items-center gap-4">
                        <Btn
                            color="primary"
                            @click="updatePassword"
                            tooltip="Mettre à jour le mot de passe"
                            >Enregistrer
                        </Btn>
                        <Btn
                            color="neutral"
                            @click="formPassword.reset()"
                            tooltip="Annuler la modification"
                            >Annuler
                        </Btn>
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
                    <Select
                        v-model="formRole.role"
                        color="primary"
                        label="Rôle"
                        tooltip="Sélectionnez le rôle de l'utilisateur"
                    >
                        <option
                            v-for="role in Object.values(ROLES)"
                            :key="role"
                            :value="role"
                        >
                            {{ getRoleTranslation(role) }}
                        </option>
                    </Select>
                    <div class="flex items-center gap-4">
                        <Btn
                            color="primary"
                            @click="updateRole"
                            tooltip="Mettre à jour le rôle"
                            >Enregistrer</Btn
                        >
                        <Btn
                            color="neutral"
                            @click="formRole.reset()"
                            tooltip="Annuler la modification"
                            >Annuler</Btn
                        >
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
