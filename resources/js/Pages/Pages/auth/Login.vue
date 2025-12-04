<script setup>
import { computed, ref, watch, nextTick, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Checkbox from '@/Pages/Molecules/data-input/CheckboxField.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

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

// Utilisation directe du store de notifications
const notificationStore = useNotificationStore();

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
    // Les erreurs peuvent venir sur 'identifier' ou 'email' (rate limiting)
    const error = form.errors.identifier || form.errors.email;
    if (error) {
        // Extraire le message si c'est un tableau
        const errorMessage = Array.isArray(error) ? error[0] : error;
        return {
            state: 'error',
            message: errorMessage,
            showNotification: false
        };
    }
    return null;
});

const passwordValidation = computed(() => {
    if (form.errors.password) {
        // Extraire le message si c'est un tableau
        const errorMessage = Array.isArray(form.errors.password) 
            ? form.errors.password[0] 
            : form.errors.password;
        return {
            state: 'error',
            message: errorMessage,
            showNotification: false
        };
    }
    return null;
});

// Watch pour forcer la mise à jour de la validation quand les erreurs changent
watch(() => form.errors, (newErrors, oldErrors) => {
    // Ne traiter que si les erreurs ont réellement changé
    const hasIdentifierError = !!(newErrors.identifier || newErrors.email);
    const hasPasswordError = !!newErrors.password;
    
    if (hasIdentifierError || hasPasswordError) {
        // Attendre le prochain tick pour s'assurer que les refs sont disponibles
        nextTick(() => {
            // Forcer la validation de l'identifiant si erreur
            if (hasIdentifierError) {
                if (identifierField.value) {
                    identifierField.value.setInteracted?.();
                    // Forcer la validation manuelle pour afficher l'erreur
                    identifierField.value.validate?.('manual');
                }
            }
            // Forcer la validation du mot de passe si erreur
            if (hasPasswordError) {
                if (passwordField.value) {
                    passwordField.value.setInteracted?.();
                    // Forcer la validation manuelle pour afficher l'erreur
                    passwordField.value.validate?.('manual');
                }
            }
        });
    }
}, { deep: true, immediate: false });

// Soumission du formulaire
function submit() {
    // Validation avant soumission
    const isIdentifierValid = form.identifier && form.identifier.trim().length > 0;
    const isPasswordValid = form.password && form.password.length > 0;
    
    if (!isIdentifierValid || !isPasswordValid) {
        notificationStore.error('Veuillez remplir tous les champs requis', {
            duration: 5000,
            placement: 'top-right'
        });
        return;
    }
    
    // Soumission avec gestion des erreurs serveur
    form.post(route('login'), {
        preserveScroll: true,
        onError: (errors) => {
            // Déterminer le message d'erreur approprié
            let errorMessage = 'Ces identifiants ne correspondent pas à nos enregistrements.';
            
            // Vérifier si errors existe et n'est pas vide
            if (errors && typeof errors === 'object' && Object.keys(errors).length > 0) {
                // Priorité 1 : Erreur d'identifiant (mauvais email/pseudo ou mot de passe)
                if (errors.identifier) {
                    const identifierError = Array.isArray(errors.identifier) 
                        ? errors.identifier[0] 
                        : errors.identifier;
                    errorMessage = identifierError || errorMessage;
                }
                // Priorité 2 : Rate limiting (erreur sur 'email')
                else if (errors.email) {
                    const emailError = Array.isArray(errors.email) 
                        ? errors.email[0] 
                        : errors.email;
                    errorMessage = emailError || errorMessage;
                }
                // Priorité 3 : Erreur de mot de passe
                else if (errors.password) {
                    const passwordError = Array.isArray(errors.password) 
                        ? errors.password[0] 
                        : errors.password;
                    errorMessage = passwordError || errorMessage;
                }
                // Priorité 4 : Première erreur disponible
                else {
                    const firstErrorKey = Object.keys(errors)[0];
                    const firstError = errors[firstErrorKey];
                    errorMessage = Array.isArray(firstError) 
                        ? firstError[0] 
                        : (firstError || errorMessage);
                }
            }
            
            // Toujours afficher une notification d'erreur, même si les erreurs ne sont pas dans le format attendu
            try {
                notificationStore.error(errorMessage, {
                    duration: 8000,
                    placement: 'top-right'
                });
            } catch (error) {
                // Fallback : afficher une alerte si la notification échoue
                alert(errorMessage);
            }
            
            // Forcer la validation des champs après l'erreur pour afficher les erreurs dans le DOM
            nextTick(() => {
                // Forcer la validation de l'identifiant
                if (errors.identifier || errors.email) {
                    if (identifierField.value) {
                        identifierField.value.setInteracted?.();
                        identifierField.value.validate?.();
                    }
                }
                // Forcer la validation du mot de passe
                if (errors.password) {
                    if (passwordField.value) {
                        passwordField.value.setInteracted?.();
                        passwordField.value.validate?.();
                    }
                }
            });
        },
        onSuccess: () => {
            notificationStore.success('Connexion réussie !', {
                duration: 3000,
                placement: 'top-right'
            });
        },
        onFinish: () => {
            // Cette fonction est appelée après onSuccess ou onError
            // Utile pour réinitialiser l'état du formulaire si nécessaire
        }
    });
}

// Computed pour l'état du bouton
const isFormValid = computed(() => {
    const hasIdentifier = !!form.identifier && form.identifier.trim().length > 0;
    const hasPassword = !!form.password && form.password.length > 0;
    
    return hasIdentifier && hasPassword;
});

// Focus programmatique sur le champ identifier au montage pour éviter le warning autofocus
onMounted(() => {
    nextTick(() => {
        if (identifierField.value) {
            identifierField.value.focus?.();
        }
    });
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
