<script setup>
import InputError from "@/Pages/Atoms/inputs/InputError.vue";
import InputLabel from "@/Pages/Atoms/inputs/InputLabel.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import TextInput from "@/Pages/Atoms/inputs/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    form.post(route("register"), {
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
                aria-placeholder="Pseudo"
                autocomplete="pseudo"
                :useFieldComposable="false"
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
                aria-placeholder="exemple@exemple.fr"
                autocomplete="email"
                :useFieldComposable="false"
            />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" value="Mot de passe" />
            <TextInput
                id="password"
                theme="secondary required password"
                class="mt-1 block w-full"
                v-model="form.password"
                aria-placeholder="Mot de passe"
                autocomplete="new-password"
                :useFieldComposable="false"
            />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputLabel
                for="password_confirmation"
                value="Confirmer le mot de passe"
            />
            <TextInput
                id="password_confirmation"
                theme="secondary required password"
                class="mt-1 block w-full"
                v-model="form.password_confirmation"
                aria-placeholder="Confirmer le mot de passe"
                autocomplete="new-password"
                :useFieldComposable="false"
            />
            <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
            />
        </div>

        <div class="mt-4 block text-center">
            <div>
                <Route route="login">
                    <Btn theme="link md neutral" label="Déjà inscrit ?" />
                </Route>
            </div>

            <div>
                <Btn
                    type="submit"
                    theme="primary glass"
                    class="my-4"
                    :disabled="form.processing"
                    label="S'enregistrer"
                />
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
