<script setup>
import InputError from "@/Pages/Components/inputs/InputError.vue";
import InputLabel from "@/Pages/Components/inputs/InputLabel.vue";
import Btn from "@/Pages/Components/actions/Btn.vue";
import Route from "@/Pages/Components/text/Route.vue";
import TextInput from "@/Pages/Components/inputs/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    form.post(route("register.add"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
onMounted(() => {
    document.title = "Inscription";
});
</script>

<template>
    <form @submit.prevent="submit">
        <div>
            <InputLabel for="name" value="Pseudo" />
            <TextInput
                id="name"
                theme="secondary autofocus required text"
                class="mt-1 block w-full"
                v-model="form.name"
                autocomplete="pseudo"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
            <InputLabel for="email" value="Email" />
            <TextInput
                id="email"
                theme="secondary required email"
                class="mt-1 block w-full"
                v-model="form.email"
                autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" value="Mot de passe / Passphrase" />

            <TextInput
                id="password"
                theme="secondary required password"
                class="mt-1 block w-full"
                v-model="form.password"
                autocomplete="new-password"
            />

            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputLabel for="password_confirmation" value="Confirme  ton mot de passe / Passphrase" />
            <TextInput
                id="password_confirmation"
                theme="secondary required password"
                class="mt-1 block w-full"
                v-model="form.password_confirmation"
                autocomplete="new-password"
            />
            <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
            />
        </div>

        <div class="mt-4 block text-center">
            <div>
                <Route route="login.show">
                    <Btn theme="link md secondary" label="Déjà inscrit ?" />
                </Route>
            </div>

            <div>
                <Btn
                    theme="secondary glass submit"
                    class="my-4"
                    :disabled="form.processing"
                    label="S'enregistrer"
                />
            </div>
        </div>

        <div class="mt-2 text-gray-600/80 dark:text-gray-400/60">
            <p>Confidentialité des données</p>
            <p class="max-w-80"><small>Nous nous engageons à ne partager aucune donnée avec des tiers. Vos informations ne seront pas utilisées à des fins statistiques. Aucune autre plateforme n'a accès aux informations que vous sauvegardez ici.</small></p>
        </div>
    </form>
</template>
