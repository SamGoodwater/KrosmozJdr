<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

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
            :validation="nameValidation"
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
                :validation="emailValidation"
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
                :validation="passwordValidation"
            />
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
                :validation="passwordConfirmationValidation"
            />
        </div>

        <div class="mt-4 block text-center">
            <div>
                <Route route="login">
                    <Btn color="neutral" variant="ghost" size="md"
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
