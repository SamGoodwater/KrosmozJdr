/**
 * UpdatePasswordForm component that handles user password updates.
 * Provides a form for changing the current password with validation.
 *
 * Features:
 * - Current password verification
 * - New password confirmation
 * - Form validation and error handling
 * - Success feedback
 * - Responsive design
 *
 * Props:
 * - theme: Theme configuration for styling
 *
 * Events:
 * - @success: Emitted when password is successfully updated
 * - @error: Emitted when an error occurs during update
 */
<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { extractTheme, combinePropsWithTheme } from '@/Utils/extractTheme';
import { commonProps } from '@/Utils/commonProps';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import Container from '@/Pages/Atoms/panels/Container.vue';

const props = defineProps({
    ...commonProps,
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('success');
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
            emit('error', form.errors);
        },
    });
};
</script>

<template>
    <section class="space-y-6 transition-all duration-200">
        <header class="mb-6 p-4 rounded-lg bg-primary-900/20 backdrop-blur-sm">
            <h2 class="text-lg font-medium text-primary-100">
                Mettre à jour le mot de passe
            </h2>

            <p class="mt-1 text-sm text-primary-200">
                Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div class="space-y-2">
                <InputLabel
                    for="current_password"
                    value="Mot de passe actuel"
                    theme="primary"
                />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    theme="primary"
                    tooltip="Entrez votre mot de passe actuel"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div class="space-y-2">
                <InputLabel
                    for="password"
                    value="Nouveau mot de passe"
                    theme="primary"
                />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    theme="primary"
                    tooltip="Le mot de passe doit contenir au moins 8 caractères"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="space-y-2">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmer le mot de passe"
                    theme="primary"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    theme="primary"
                    tooltip="Confirmez votre nouveau mot de passe"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <Btn
                    theme="primary"
                    :disabled="form.processing"
                    label="Enregistrer"
                    tooltip="Mettre à jour le mot de passe"
                />

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-success-500"
                    >
                        Mot de passe mis à jour avec succès.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
