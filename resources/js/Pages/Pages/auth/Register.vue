<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
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

// Règles de validation granulaire
const validationRules = {
    name: [
        {
            rule: 'required',
            message: 'Le pseudo est requis',
            state: 'error',
            trigger: 'blur',
            priority: 10
        },
        {
            rule: (value) => value && value.length >= 3,
            message: 'Le pseudo doit contenir au moins 3 caractères',
            state: 'error',
            trigger: 'blur',
            priority: 8
        }
    ],
    
    email: [
        {
            rule: 'required',
            message: 'L\'email est requis',
            state: 'error',
            trigger: 'blur',
            priority: 10
        },
        {
            rule: 'email',
            message: 'Format d\'email invalide',
            state: 'error',
            trigger: 'blur',
            priority: 8
        },
        {
            rule: (value) => value && !value.includes('temp'),
            message: 'Évitez les emails temporaires',
            state: 'warning',
            trigger: 'blur',
            priority: 5,
            showNotification: false
        }
    ],
    
    password: [
        {
            rule: 'required',
            message: 'Le mot de passe est requis',
            state: 'error',
            trigger: 'blur',
            priority: 10
        },
        {
            rule: (value) => value && value.length >= 8,
            message: 'Le mot de passe doit contenir au moins 8 caractères',
            state: 'error',
            trigger: 'blur',
            priority: 8
        },
        {
            rule: (value) => /[A-Z]/.test(value),
            message: 'Ajoutez au moins une majuscule',
            state: 'warning',
            trigger: 'change',
            priority: 5,
            showNotification: false
        },
        {
            rule: (value) => /\d/.test(value),
            message: 'Ajoutez des chiffres',
            state: 'info',
            trigger: 'change',
            priority: 3,
            showNotification: false
        }
    ],
    
    password_confirmation: [
        {
            rule: 'required',
            message: 'La confirmation est requise',
            state: 'error',
            trigger: 'blur',
            priority: 10
        },
        {
            rule: (value) => value && value === form.password,
            message: 'Les mots de passe ne correspondent pas',
            state: 'error',
            trigger: 'change',
            priority: 8
        }
    ]
};

const submit = () => {
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

onMounted(() => {
    setPageTitle("Inscription");
});
</script>

<template>
    <div class="flex flex-col items-center justify-center h-full w-full">

        <h2 class="text-title py-8">Inscription</h2>

    <form @submit.prevent="submit">
        <InputField
            id="name"
            variant="glass"
            color="secondary"
            autofocus
            v-model="form.name"
            autocomplete="pseudo"
            name="name"
            label="Pseudo"
            placeholder="Pseudo"
            :validation-rules="validationRules.name"
            :validation="form.errors.name ? { state: 'error', message: form.errors.name } : null"
            :parent-control="false"
            tabindex="1"
        />

        <div class="mt-4">
            <InputField
                id="email"
                variant="glass"
                color="secondary"
                type="email"
                v-model="form.email"
                autocomplete="email"
                name="email"
                label="Email"
                placeholder="Email"
                :validation-rules="validationRules.email"
                :validation="form.errors.email ? { state: 'error', message: form.errors.email } : null"
                :parent-control="false"
                tabindex="2"
            />
        </div>

        <div class="mt-4">
            <InputField
                id="password"
                variant="glass"
                color="secondary"
                type="password"
                v-model="form.password"
                autocomplete="new-password"
                name="password"
                label="Mot de passe"
                placeholder="Mot de passe"
                :validation-rules="validationRules.password"
                :validation="form.errors.password ? { state: 'error', message: form.errors.password } : null"
                :parent-control="false"
                tabindex="3"
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
                id="password_confirmation"
                variant="glass"
                color="secondary"
                type="password"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                name="password_confirmation"
                label="Confirmer le mot de passe"
                placeholder="Confirmer le mot de passe"
                :validation-rules="validationRules.password_confirmation"
                :validation="form.errors.password_confirmation ? { state: 'error', message: form.errors.password_confirmation } : null"
                :parent-control="false"
                tabindex="4"
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
