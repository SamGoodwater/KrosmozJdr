<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
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

// Validation computed pour chaque champ
const nameValidation = computed(() => {
    if (!form.errors.name) return null;
    return {
        state: 'error',
        message: form.errors.name,
        showNotification: false
    };
});

const emailValidation = computed(() => {
    if (!form.errors.email) return null;
    return {
        state: 'error',
        message: form.errors.email,
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

const submit = () => {
    form.post(route("register"), {
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
            required
            v-model="form.name"
            autocomplete="pseudo"
            name="name"
            label="Pseudo"
            placeholder="Pseudo"
            :validation="nameValidation"
            tabindex="1"
        />

        <div class="mt-4">
            <InputField
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
                tabindex="2"
            />
        </div>

        <div class="mt-4">
            <InputField
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
                required
                type="password"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                name="password_confirmation"
                label="Confirmer le mot de passe"
                placeholder="Confirmer le mot de passe"
                :validation="passwordConfirmationValidation"
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
