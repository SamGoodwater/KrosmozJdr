<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Checkbox from '@/Pages/Molecules/data-input/CheckboxField.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

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

// Refs pour accéder aux composants InputField
const identifierFieldRef = ref(null);
const passwordFieldRef = ref(null);

// Validation de l'identifiant (email ou pseudo) avec la nouvelle API
const identifierValidation = computed(() => {
    const identifier = form.identifier;
    
    if (!identifier) {
        return {
            condition: false,
            messages: {
                error: { text: 'Email ou pseudo requis', notified: false }
            }
        };
    }
    
    // Si ça ressemble à un email, on valide le format
    if (identifier.includes('@')) {
        return {
            condition: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            messages: {
                success: { text: 'Email valide', notified: false },
                error: { text: 'Format d\'email invalide', notified: false }
            }
        };
    }
    
    // Pour un pseudo, validation simple
    return {
        condition: (val) => val.length >= 3,
        messages: {
            success: { text: 'Pseudo valide', notified: false },
            error: { text: 'Pseudo trop court (minimum 3 caractères)', notified: false }
        }
    };
});

// Validation du mot de passe avec la nouvelle API
const passwordValidation = computed(() => {
    const password = form.password;
    
    if (!password) {
        return {
            condition: false,
            messages: {
                error: { text: 'Le mot de passe est requis', notified: false }
            }
        };
    }
    
    return {
        condition: (val) => {
            if (val.length < 8) return 'error';
            
            // Vérification de la complexité
            const hasUpperCase = /[A-Z]/.test(val);
            const hasLowerCase = /[a-z]/.test(val);
            const hasNumbers = /\d/.test(val);
            
            if (!hasUpperCase || !hasLowerCase || !hasNumbers) {
                return 'warning';
            }
            
            return 'success';
        },
        messages: {
            success: { text: 'Mot de passe sécurisé !', notified: false },
            warning: { text: 'Le mot de passe pourrait être plus sécurisé', notified: false },
            error: { text: 'Le mot de passe doit contenir au moins 8 caractères', notified: false }
        }
    };
});

// Soumission du formulaire
function submit() {
    // Validation avant soumission
    const isIdentifierValid = form.identifier && form.identifier.trim().length > 0;
    const isPasswordValid = form.password && form.password.length >= 8;
    
    if (!isIdentifierValid || !isPasswordValid) {
        // Afficher une notification d'erreur générale
        // Note: Ici on pourrait utiliser un système de notification global
        console.error('Formulaire invalide');
        return;
    }
    
    // Soumission avec gestion des erreurs serveur
    form.post(route('login'), {
        onError: (errors) => {
            // Gestion des erreurs serveur
            console.error('Erreurs serveur:', errors);
            
            // Note: Ici on pourrait utiliser un système de notification global
            // pour afficher les erreurs serveur
        },
        onSuccess: () => {
            // Notification de succès
            console.log('Connexion réussie !');
        }
    });
}

// Computed pour l'état du bouton
const isFormValid = computed(() => {
    // Validation simple : les champs doivent avoir une valeur
    const hasIdentifier = !!form.identifier && form.identifier.trim().length > 0;
    const hasPassword = !!form.password && form.password.length >= 8;
    
    return hasIdentifier && hasPassword;
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
                        ref="identifierFieldRef"
                        label="Email ou pseudo"
                        placeholder="Email ou pseudo"
                        v-model="form.identifier"
                        type="text"
                        name="identifier"
                        required
                        autofocus
                        autocomplete="username"
                        :validation="identifierValidation"
                        tabindex="1"
                    />

                    <InputField
                        ref="passwordFieldRef"
                        label="Mot de passe sécurisé"
                        placeholder="Mot de passe"
                        v-model="form.password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        :validation="passwordValidation"
                        class="mt-4"
                        tabindex="2"
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
