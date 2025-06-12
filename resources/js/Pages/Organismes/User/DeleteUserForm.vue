<script setup>
/**
 * @description
 * DeleteUserForm Organism. Formulaire de suppression du compte utilisateur avec confirmation par mot de passe.
 * - Vérification du mot de passe avant suppression
 * - Modale de confirmation avec message d'avertissement
 * - Validation et gestion des erreurs
 * - Responsive design
 *
 * Props:
 * - theme: Theme configuration for styling
 *
 * Events:
 * - @success: Emitted when account is successfully deleted
 * - @error: Emitted when an error occurs during deletion
 */
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import InputField from '@/Pages/Atoms/data-input/InputField.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';


const emit = defineEmits(['success', 'error']);

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit('success');
        },
        onError: () => {
            passwordInput.value.focus();
            emit('error', form.errors);
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Container class="max-w-xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
        <section class="space-y-6 transition-all duration-200">
            <header class="mb-6 p-4 rounded-lg bg-error-900/20 backdrop-blur-sm">
                <h2 class="text-lg font-medium text-error-100">
                    Supprimer le compte
                </h2>
                <p class="mt-1 text-sm text-error-200">
                    Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.
                    Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous
                    souhaitez conserver.
                </p>
            </header>

            <Btn theme="error" @click="confirmUserDeletion" label="Supprimer le compte"
                tooltip="Cette action est irréversible" tooltip-position="right" />

            <Modal :show="confirmingUserDeletion" @close="closeModal">
                <Container class="p-6 space-y-6 bg-base-100 rounded-lg shadow-lg">
                    <h2 class="text-lg font-medium text-error-100">
                        Êtes-vous sûr de vouloir supprimer votre compte ?
                    </h2>
                    <p class="mt-1 text-sm text-error-200">
                        Une fois votre compte supprimé, toutes ses ressources et données seront définitivement
                        supprimées.
                        Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement
                        votre
                        compte.
                    </p>
                    <div class="mt-6">
                        <InputLabel for="password" value="Mot de passe" class="sr-only" />
                        <InputField id="password" ref="passwordInput" v-model="form.password" type="password"
                            placeholder="Mot de passe" theme="error"
                            tooltip="Entrez votre mot de passe pour confirmer la suppression" aria-label="Mot de passe"
                            :aria-invalid="!!form.errors.password" @keyup.enter="deleteUser"
                            :class="'mt-1 block w-3/4'" />
                        <Validator :message="form.errors.password" :visible="!!form.errors.password" class="mt-2" />
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <Btn theme="secondary" @click="closeModal" label="Annuler" tooltip="Annuler la suppression" />
                        <Btn theme="error" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            @click="deleteUser" label="Supprimer le compte"
                            tooltip="Confirmer la suppression définitive" />
                    </div>
                </Container>
            </Modal>
        </section>
    </Container>
</template>
