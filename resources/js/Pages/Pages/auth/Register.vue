<script setup>
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import TextInput from "@/Pages/Atoms/inputs/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

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
    setPageTitle('Inscription');
});
</script>

<template>
    <form @submit.prevent="submit">
        <div>
            <TextInput id="name" theme="secondary autofocus required text" class="mt-1 block w-full" v-model="form.name"
                aria-placeholder="Pseudo" autocomplete="pseudo" :useFieldComposable="false" inputLabel="Pseudo"
                :errorMessage="form.errors.name" />
        </div>

        <div class="mt-4">
            <TextInput id="email" theme="secondary required email" class="mt-1 block w-full" v-model="form.email"
                aria-placeholder="exemple@exemple.fr" autocomplete="email" :useFieldComposable="false"
                inputLabel="Email" :errorMessage="form.errors.email" />
        </div>

        <div class="mt-4">
            <TextInput id="password" theme="secondary required password" class="mt-1 block w-full"
                v-model="form.password" aria-placeholder="Mot de passe" autocomplete="new-password"
                :useFieldComposable="false" inputLabel="Mot de passe" :errorMessage="form.errors.password" />
        </div>

        <div class="mt-4">
            <TextInput id="password_confirmation" theme="secondary required password" class="mt-1 block w-full"
                v-model="form.password_confirmation" aria-placeholder="Confirmer le mot de passe"
                autocomplete="new-password" :useFieldComposable="false" inputLabel="Confirmer le mot de passe"
                :errorMessage="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 block text-center">
            <div>
                <Route route="login">
                    <Btn theme="link md neutral" label="Déjà inscrit ?" />
                </Route>
            </div>

            <div>
                <Btn type="submit" theme="primary glass" class="my-4" :disabled="form.processing"
                    label="S'enregistrer" />
            </div>
        </div>

        <div class="mt-2 text-gray-600/80 dark:text-gray-400/60">
            <p>Confidentialité des données</p>
            <p class="max-w-80">
                <small>Nous nous engageons à ne partager aucune donnée avec des
                    tiers. Vos informations ne seront pas utilisées à des fins
                    statistiques. Aucune autre plateforme n'a accès aux
                    informations que vous sauvegardez ici.</small>
            </p>
        </div>
    </form>
</template>
