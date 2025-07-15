<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>

    <Head title="Confirmation du mot de passe" />

    <div class="mb-4 text-sm text-gray-600">
        Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
    </div>

    <form @submit.prevent="submit">
        <InputField id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
            autocomplete="current-password" autofocus label="Mot de passe" :validator="form.errors.password" />

        <div class="mt-4 flex justify-end">
            <Btn class="ms-4" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Confirmer
            </Btn>
        </div>
    </form>
</template>
