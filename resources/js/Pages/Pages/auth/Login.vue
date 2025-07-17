<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { useValidation } from '@/Composables/form/useValidation';
import { quickValidation } from '@/Utils/atomic-design/validationManager';
import Checkbox from '@/Pages/Molecules/data-input/CheckboxField.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    identifier: '',
    password: '',
    remember: false,
});

// Utilisation du nouveau composable de validation
const { validateField, handleServerErrors } = useValidation();

// Validation en temps réel
const identifierValidation = ref(null);
const passwordValidation = ref(null);

// Validation de l'identifiant (email ou pseudo)
function validateIdentifier() {
    const identifier = form.identifier;
    
    if (!identifier) {
        identifierValidation.value = quickValidation.local.error('Email ou pseudo requis');
        return false;
    }
    
    // Si ça ressemble à un email, on valide le format
    if (identifier.includes('@')) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(identifier)) {
            identifierValidation.value = quickValidation.local.error('Format d\'email invalide');
            return false;
        }
    }
    
    // Si l'identifiant est valide, on peut afficher un succès avec notification
    if (identifier.length > 2) {
        identifierValidation.value = quickValidation.withNotification.success('Identifiant valide !', {
            notificationDuration: 2000
        });
    }
    
    return true;
}

// Validation du mot de passe
function validatePassword() {
    const password = form.password;
    
    if (!password) {
        passwordValidation.value = quickValidation.local.error('Le mot de passe est requis');
        return false;
    }
    
    if (password.length < 8) {
        passwordValidation.value = quickValidation.local.error('Le mot de passe doit contenir au moins 8 caractères');
        return false;
    }
    
    // Vérification de la complexité
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    
    if (!hasUpperCase || !hasLowerCase || !hasNumbers) {
        passwordValidation.value = quickValidation.local.warning('Le mot de passe pourrait être plus sécurisé');
        return false;
    }
    
    // Mot de passe fort
    passwordValidation.value = quickValidation.withNotification.success('Mot de passe sécurisé !', {
        notificationDuration: 2000
    });
    
    return true;
}

// Soumission du formulaire
function submit() {
    // Validation avant soumission
    const isIdentifierValid = validateIdentifier();
    const isPasswordValid = validatePassword();
    
    if (!isIdentifierValid || !isPasswordValid) {
        // Afficher une notification d'erreur générale
        validateField('form', quickValidation.withNotification.error('Veuillez corriger les erreurs dans le formulaire'));
        return;
    }
    
    // Soumission avec gestion des erreurs serveur
    form.post(route('login'), {
        onError: (errors) => {
            // Gestion automatique des erreurs serveur avec notifications
            handleServerErrors(errors, {
                showNotifications: true,
                notificationDuration: 8000
            });
            
            // Mise à jour des validations locales
            if (errors.identifier) {
                identifierValidation.value = quickValidation.local.error(errors.identifier);
            }
            if (errors.password) {
                passwordValidation.value = quickValidation.local.error(errors.password);
            }
        },
        onSuccess: () => {
            // Notification de succès
            validateField('login', quickValidation.withNotification.success('Connexion réussie !', {
                notificationDuration: 3000
            }));
        }
    });
}

// Computed pour l'état du bouton
const isFormValid = computed(() => {
    return form.identifier && form.password && 
           identifierValidation.value?.state !== 'error' && 
           passwordValidation.value?.state !== 'error';
});
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-lg">
            <div class="mb-4 text-sm text-gray-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="flex flex-col gap-8">
                    <InputField
                        label="Email ou pseudo"
                        v-model="form.identifier"
                        type="text"
                        name="identifier"
                        required
                        autofocus
                        autocomplete="username"
                        :validation="identifierValidation"
                        @blur="validateIdentifier"
                    >
                    </InputField>

                    <InputField
                        label="Mot de passe sécurisé"
                        v-model="form.password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        :validation="passwordValidation"
                        @blur="validatePassword"
                        class="mt-4"
                    >
                        <template #helper>
                            Ne partagez jamais votre mot de passe avec quelqu'un d'autre.
                        </template>
                    </InputField>

                    <Checkbox
                        v-model="form.remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        color="primary"
                        size="md"
                        label="Se souvenir de moi"
                    />
        
                </div>

                <div class="flex flex-col gap-4 justify-center items-center mt-4">
                    <Btn
                        v-if="canResetPassword"
                        type="button"
                        color="neutral"
                        size="md"
                        variant="link"
                    >
                        <Route route='password.request'>
                            <i class="fa-solid fa-lock mr-2"></i>
                            Mot de passe oublié ?
                        </Route>
                    </Btn>
                        
                    <Btn
                        type="submit"
                        :disabled="form.processing || !isFormValid"
                        color="primary"
                    >
                        <i class="fa-solid fa-sign-in-alt mr-2"></i>
                        {{ form.processing ? 'Connexion...' : 'Se connecter' }}
                    </Btn>

                    <p>Pas encore de compte ?</p>
                    <Btn
                        type="button"
                        color="primary"
                        size="sm"
                        variant="outline"
                    >
                        <Route route='register'>
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Créer un compte
                        </Route>
                    </Btn>
                </div>
            </form>
        </div>
    </div>
</template>
