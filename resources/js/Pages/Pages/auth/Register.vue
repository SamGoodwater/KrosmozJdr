<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import InputField from "@/Pages/Atoms/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const { setPageTitle } = usePageTitle();

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
    <form @submit.prevent="submit">
        <InputField
            id="name"
            color="secondary"
            autofocus
            required
            class="mt-1 block w-full"
            v-model="form.name"
            autocomplete="pseudo"
            name="name"
            label="Pseudo"
            :validator="form.errors.name"
        />

        <div class="mt-4">
            <InputField
                id="email"
                color="secondary"
                required
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                autocomplete="email"
                name="email"
                label="Email"
                :validator="form.errors.email"
            />
        </div>

        <div class="mt-4">
            <InputField
                id="password"
                color="secondary"
                required
                type="password"
                class="mt-1 block w-full"
                v-model="form.password"
                autocomplete="new-password"
                name="password"
                label="Mot de passe"
                :validator="form.errors.password"
            />
        </div>

        <div class="mt-4">
            <InputField
                id="password_confirmation"
                color="secondary"
                required
                type="password"
                class="mt-1 block w-full"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                name="password_confirmation"
                label="Confirmer le mot de passe"
                :validator="form.errors.password_confirmation"
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
</template>
