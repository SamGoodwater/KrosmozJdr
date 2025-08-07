<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, computed, ref } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const { setPageTitle } = usePageTitle();
const notificationStore = useNotificationStore();

// Refs pour contrôler la validation des champs
const nameField = ref(null);
const emailField = ref(null);
const passwordField = ref(null);
const passwordConfirmationField = ref(null);

// État de validation pour contrôler quand afficher les erreurs locales
const validationState = ref({
    name: false,
    email: false,
    password: false,
    password_confirmation: false
});

// Validation computed pour chaque champ (validation locale + serveur)
const nameValidation = computed(() => {
    const name = form.name;
    
    // Validation serveur (toujours affichée)
    if (form.errors.name) {
        return { state: 'error', message: form.errors.name, showNotification: false };
    }
    
    // Validation locale (seulement si le champ a été touché ET qu'il y a une erreur)
    if (validationState.value.name) {
        if (!name) {
            return { state: 'error', message: 'Le pseudo est requis', showNotification: false };
        }
        if (name.length < 3) {
            return { state: 'error', message: 'Le pseudo doit contenir au moins 3 caractères', showNotification: false };
        }
    }
    
    return null;
});

const emailValidation = computed(() => {
    const email = form.email;
    
    // Validation serveur (toujours affichée)
    if (form.errors.email) {
        return { state: 'error', message: form.errors.email, showNotification: false };
    }
    
    // Validation locale (seulement si le champ a été touché ET qu'il y a une erreur)
    if (validationState.value.email) {
        if (!email) {
            return { state: 'error', message: 'L\'email est requis', showNotification: false };
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return { state: 'error', message: 'Format d\'email invalide', showNotification: false };
        }
    }
    
    return null;
});

const passwordValidation = computed(() => {
    const password = form.password;
    
    // Validation serveur (toujours affichée)
    if (form.errors.password) {
        return { state: 'error', message: form.errors.password, showNotification: false };
    }
    
    // Validation locale (seulement si le champ a été touché ET qu'il y a une erreur)
    if (validationState.value.password) {
        if (!password) {
            return { state: 'error', message: 'Le mot de passe est requis', showNotification: false };
        }
        if (password.length < 8) {
            return { state: 'error', message: 'Le mot de passe doit contenir au moins 8 caractères', showNotification: false };
        }
    }
    
    return null;
});

const passwordConfirmationValidation = computed(() => {
    const password = form.password;
    const passwordConfirmation = form.password_confirmation;
    
    // Validation serveur (toujours affichée)
    if (form.errors.password_confirmation) {
        return { state: 'error', message: form.errors.password_confirmation, showNotification: false };
    }
    
    // Validation locale (seulement si le champ a été touché ET qu'il y a une erreur)
    if (validationState.value.password_confirmation) {
        if (!passwordConfirmation) {
            return { state: 'error', message: 'La confirmation du mot de passe est requise', showNotification: false };
        }
        if (password && passwordConfirmation && password !== passwordConfirmation) {
            return { state: 'error', message: 'Les mots de passe ne correspondent pas', showNotification: false };
        }
    }
    
    return null;
});

const submit = () => {
    // Activer la validation sur tous les champs
    validationState.value = {
        name: true,
        email: true,
        password: true,
        password_confirmation: true
    };
    
    // Validation locale avant envoi (vérifier directement les valeurs)
    const name = form.name;
    const email = form.email;
    const password = form.password;
    const passwordConfirmation = form.password_confirmation;
    
    let hasErrors = false;
    
    // Validation du nom
    if (!name || name.length < 3) {
        hasErrors = true;
    }
    
    // Validation de l'email
    if (!email) {
        hasErrors = true;
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            hasErrors = true;
        }
    }
    
    // Validation du mot de passe
    if (!password || password.length < 8) {
        hasErrors = true;
    }
    
    // Validation de la confirmation
    if (!passwordConfirmation || password !== passwordConfirmation) {
        hasErrors = true;
    }
    
    if (hasErrors) {
        if (notificationStore) {
            notificationStore.error('Veuillez corriger les erreurs dans le formulaire', { duration: 5000, placement: 'top-center' });
        }
        console.error('Formulaire invalide');
        return;
    }
    
    form.post(route("register"), {
        onError: (errors) => {
            if (notificationStore) {
                const errorMessages = Object.values(errors).join(', ');
                notificationStore.error('Erreur lors de l\'inscription : ' + errorMessages, { duration: 8000, placement: 'top-center' });
            }
            console.error('Erreurs serveur:', errors);
        },
        onSuccess: () => {
            if (notificationStore) {
                notificationStore.success('Inscription réussie ! Bienvenue !', { duration: 3000, placement: 'top-center' });
            }
            console.log('Inscription réussie !');
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// Gestionnaires d'événements pour activer la validation
const handleFieldBlur = (fieldName) => {
    validationState.value[fieldName] = true;
};

onMounted(() => {
    setPageTitle("Inscription");
});
</script>

<template>
    <div class="flex flex-col items-center justify-center h-full w-full">

        <h2 class="text-title py-8">Inscription</h2>

    <form @submit.prevent="submit">
        <InputField
            ref="nameField"
            id="name"
            variant="glass"
            color="secondary"
            autofocus
            required
            v-model="form.name"
            autocomplete="pseudo"
            name="name"
            label="Pseudo"
            placeholder="Pseudo"
            :validation="nameValidation"
            :validation-enabled="true"
            tabindex="1"
            @blur="handleFieldBlur('name')"
        />

        <div class="mt-4">
            <InputField
                ref="emailField"
                id="email"
                variant="glass"
                color="secondary"
                required
                type="email"
                v-model="form.email"
                autocomplete="email"
                name="email"
                label="Email"
                placeholder="Email"
                :validation="emailValidation"
                :validation-enabled="true"
                tabindex="2"
                @blur="handleFieldBlur('email')"
            />
        </div>

        <div class="mt-4">
            <InputField
                ref="passwordField"
                id="password"
                variant="glass"
                color="secondary"
                required
                type="password"
                v-model="form.password"
                autocomplete="new-password"
                name="password"
                label="Mot de passe"
                placeholder="Mot de passe"
                :validation="passwordValidation"
                :validation-enabled="true"
                tabindex="3"
                @blur="handleFieldBlur('password')"
            >
                <template #helper>
                    <div class="flex items-center gap-2">
                        <span>Privilégiez une passphrase ou un mot de passe fort</span>
                        <Tooltip placement="top" color="info">
                            <Icon source="fa-question-circle" alt="Aide passphrase" size="sm" class="text-info cursor-help" />
                            <template #content>
                                <div class="max-w-xs">
                                    <strong>Qu'est-ce qu'une passphrase ?</strong>
                                    <p class="text-sm mt-1">
                                        Une passphrase est une phrase complète (ex: "Mon chat s'appelle Whiskers 2024!") 
                                        plutôt qu'un mot de passe court. Elle est plus longue mais plus facile à retenir 
                                        et plus sécurisée grâce à sa complexité naturelle.
                                    </p>
                                </div>
                            </template>
                        </Tooltip>
                    </div>
                </template>
            </InputField>
        </div>

        <div class="mt-4">
            <InputField
                ref="passwordConfirmationField"
                id="password_confirmation"
                variant="glass"
                color="secondary"
                required
                type="password"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                name="password_confirmation"
                label="Confirmer le mot de passe"
                placeholder="Confirmer le mot de passe"
                :validation="passwordConfirmationValidation"
                :validation-enabled="true"
                tabindex="4"
                @blur="handleFieldBlur('password_confirmation')"
            />
        </div>

        <div class="mt-4 block text-center">
            <div>
                <Route route="login">
                    <Btn color="neutral" variant="ghost" size="md" tabindex="5"
                        >Déjà inscrit ?</Btn
                    >
                </Route>
            </div>

            <div>
                <Btn
                    type="submit"
                    color="primary"
                    variant="glass"
                    class="my-4"
                    :disabled="form.processing"
                    tabindex="6"
                >
                    S'enregistrer
                </Btn>
            </div>
        </div>

        <div class="mt-2 text-gray-600/80 dark:text-gray-400/60">
            <p>Confidentialité des données</p>
            <p class="max-w-80">
                <small
                    >Nous nous engageons à ne partager aucune donnée avec des
                    tiers. Vos informations ne seront pas utilisées à des fins
                    statistiques. Aucune autre plateforme n'a accès aux
                    informations que vous sauvegardez ici.</small
                >
                </p>
            </div>
        </form>
    </div>
</template>
