/**
 * DeleteUserForm component that handles user account deletion.
 * Provides a confirmation modal with password verification.
 *
 * Features:
 * - Password verification before deletion
 * - Confirmation modal with warning message
 * - Error handling and form validation
 * - Responsive design
 *
 * Props:
 * - theme: Theme configuration for styling
 *
 * Events:
 * - @success: Emitted when account is successfully deleted
 * - @error: Emitted when an error occurs during deletion
 */
<script setup>
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { extractTheme, combinePropsWithTheme } from '@/Utils/extractTheme';
import { commonProps } from '@/Utils/commonProps';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Modal from '@/Pages/Atoms/panels/Modal.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import Container from '@/Pages/Atoms/panels/Container.vue';

const props = defineProps({
    ...commonProps,
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

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
    <section class="space-y-6 transition-all duration-200">
        <header class="mb-6 p-4 rounded-lg bg-error-900/20 backdrop-blur-sm">
            <h2 class="text-lg font-medium text-error-100">
                Supprimer le compte
            </h2>

            <p class="mt-1 text-sm text-error-200">
                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.
                Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.
            </p>
        </header>

        <Btn
            theme="error"
            @click="confirmUserDeletion"
            label="Supprimer le compte"
            tooltip="Cette action est irréversible"
            tooltip-position="right"
        />

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <Container class="p-6 space-y-6 bg-base-100 rounded-lg shadow-lg">
                <h2 class="text-lg font-medium text-error-100">
                    Êtes-vous sûr de vouloir supprimer votre compte ?
                </h2>

                <p class="mt-1 text-sm text-error-200">
                    Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.
                    Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Mot de passe"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Mot de passe"
                        @keyup.enter="deleteUser"
                        theme="error"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <Btn
                        theme="secondary"
                        @click="closeModal"
                        label="Annuler"
                        tooltip="Annuler la suppression"
                    />

                    <Btn
                        theme="error"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                        label="Supprimer le compte"
                        tooltip="Confirmer la suppression définitive"
                    />
                </div>
            </Container>
        </Modal>
    </section>
</template>
