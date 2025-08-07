<script setup>
import { computed, inject, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
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

// Injection du store de notifications
const notificationStore = inject('notificationStore', null);

// Références aux InputField pour contrôler la validation
const identifierField = ref(null);
const passwordField = ref(null);

// Règles de validation granulaire pour l'identifiant
const identifierValidationRules = computed(() => [
    {
        rule: 'required',
        message: 'Email ou pseudo requis',
        state: 'error',
        trigger: 'blur'
    }
]);

// Règles de validation granulaire pour le mot de passe
const passwordValidationRules = computed(() => [
    {
        rule: 'required',
        message: 'Mot de passe requis',
        state: 'error',
        trigger: 'blur'
    }
]);

// Validation basée sur les erreurs serveur
const identifierValidation = computed(() => {
    if (form.errors.identifier) {
        return {
            state: 'error',
            message: form.errors.identifier,
            showNotification: false
        };
    }
    return null;
});

const passwordValidation = computed(() => {
    if (form.errors.password) {
        return {
            state: 'error',
            message: form.errors.password,
            showNotification: false
        };
    }
    return null;
});

// Soumission du formulaire
function submit() {
    // Validation avant soumission
    const isIdentifierValid = form.identifier && form.identifier.trim().length > 0;
    const isPasswordValid = form.password && form.password.length > 0;
    
    if (!isIdentifierValid || !isPasswordValid) {
        if (notificationStore) {
            notificationStore.error('Veuillez remplir tous les champs requis', {
                duration: 5000,
                placement: 'top-center'
            });
        }
        return;
    }
    
    // Soumission avec gestion des erreurs serveur
    form.post(route('login'), {
        onError: (errors) => {
            // Notification globale pour les erreurs d'authentification
            if (errors.identifier && notificationStore) {
                notificationStore.error('Erreur de connexion : ' + errors.identifier, {
                    duration: 8000,
                    placement: 'top-center'
                });
            }
        },
        onSuccess: () => {
            if (notificationStore) {
                notificationStore.success('Connexion réussie !', {
                    duration: 3000,
                    placement: 'top-center'
                });
            }
        }
    });
}

// Computed pour l'état du bouton
const isFormValid = computed(() => {
    const hasIdentifier = !!form.identifier && form.identifier.trim().length > 0;
    const hasPassword = !!form.password && form.password.length > 0;
    
    return hasIdentifier && hasPassword;
});

</script>

<template>
    <Head title="Log in" />

    <div class="flex flex-col justify-start items-center pt-6 sm:pt-0">

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-lg">

            <h2 class="text-center text-2xl font-bold">
                Connexion
            </h2>

            <div class="mb-4 text-sm text-gray-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="flex flex-col gap-8">
                    <InputField
                        ref="identifierField"
                        label="Email ou pseudo"
                        placeholder="Email ou pseudo"
                        v-model="form.identifier"
                        type="text"
                        name="identifier"
                        required
                        autofocus
                        autocomplete="username"
                        :validation-rules="identifierValidationRules"
                        :validation="identifierValidation"
                        :parent-control="true"
                        tabindex="1"
                    />

                    <InputField
                        ref="passwordField"
                        label="Mot de passe sécurisé"
                        placeholder="Mot de passe"
                        v-model="form.password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        :validation-rules="passwordValidationRules"
                        :validation="passwordValidation"
                        :parent-control="true"
                        class="mt-4"
                        tabindex="2"
                        helper="Ne partagez jamais votre mot de passe avec quelqu'un d'autre."
                    />

                    <Checkbox
                        v-model="form.remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        color="primary"
                        size="md"
                        label="Se souvenir de moi"
                        defaultLabelPosition="end"
                        tabindex="3"
                    />
        
                </div>

                <div class="flex flex-col gap-4 justify-center items-center mt-4">
                    <Btn
                        v-if="canResetPassword"
                        type="button"
                        color="neutral"
                        size="md"
                        variant="link"
                        tabindex="4"
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
                        tabindex="5"
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
                        tabindex="6"
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
