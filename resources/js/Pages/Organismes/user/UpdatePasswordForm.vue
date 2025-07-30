<script setup>
/**
 * @description
 * UpdatePasswordForm Organism. Formulaire de mise à jour du mot de passe utilisateur.
 * - Vérification du mot de passe actuel
 * - Confirmation du nouveau mot de passe
 * - Validation et gestion des erreurs
 * - Feedback utilisateur (succès/erreur)
 * - Responsive design
 *
 * Features:
 * - Current password verification
 * - New password confirmation
 * - Form validation and error handling
 * - Success feedback
 * - Responsive design
 *
 * Events:
 * - @success: Emitted when password is successfully updated
 * - @error: Emitted when an error occurs during update
 */
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const emit = defineEmits(['success', 'error']);

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

// Validation computed pour chaque champ
const currentPasswordValidation = computed(() => {
    if (!form.errors.current_password) return null;
    return {
        state: 'error',
        message: form.errors.current_password,
        showNotification: false
    };
});

const passwordValidation = computed(() => {
    if (!form.errors.password) return null;
    return {
        state: 'error',
        message: form.errors.password,
        showNotification: false
    };
});

const passwordConfirmationValidation = computed(() => {
    if (!form.errors.password_confirmation) return null;
    return {
        state: 'error',
        message: form.errors.password_confirmation,
        showNotification: false
    };
});
</script>

<template>
    <Container class="max-w-xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
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
                    <InputLabel for="current_password" value="Mot de passe actuel" theme="primary" />
                    <Tooltip content="Entrez votre mot de passe actuel" placement="top">
                        <InputField id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                            type="password" autocomplete="current-password"
                            :validation="currentPasswordValidation"
                            aria-label="Mot de passe actuel"
                            :class="'mt-1 block w-full'" />
                    </Tooltip>
                </div>

                <div class="space-y-2">
                    <InputLabel for="password" value="Nouveau mot de passe" theme="primary" />
                    <Tooltip content="Le mot de passe doit contenir au moins 8 caractères" placement="top">
                        <InputField id="password" ref="passwordInput" v-model="form.password" type="password"
                            autocomplete="new-password"
                            :validation="passwordValidation"
                            aria-label="Nouveau mot de passe"
                            :class="'mt-1 block w-full'" />
                    </Tooltip>
                </div>

                <div class="space-y-2">
                    <InputLabel for="password_confirmation" value="Confirmer le mot de passe" theme="primary" />
                    <Tooltip content="Confirmez votre nouveau mot de passe" placement="top">
                        <InputField id="password_confirmation" v-model="form.password_confirmation" type="password"
                            autocomplete="new-password"
                            :validation="passwordConfirmationValidation"
                            aria-label="Confirmer le mot de passe"
                            :class="'mt-1 block w-full'" />
                    </Tooltip>
                </div>

                <div class="flex items-center gap-4">
                    <Tooltip content="Mettre à jour le mot de passe" placement="top">
                        <Btn theme="primary" :disabled="form.processing" label="Enregistrer" />
                    </Tooltip>

                    <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                        <Alert v-if="form.recentlySuccessful" color="success" variant="soft" class="text-sm">
                            Mot de passe mis à jour avec succès.
                        </Alert>
                    </Transition>
                </div>
            </form>
        </section>
    </Container>
</template>
