<script setup>
// import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from "@inertiajs/vue3";
import InputField from '@/Pages/Atoms/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>

    <Head title="Reset Password" />

    <form @submit.prevent="submit">
        <InputField id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
            autocomplete="username" label="Email" :validator="form.errors.email" />

        <div class="mt-4">
            <InputField id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                autocomplete="new-password" label="Mot de passe" :validator="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputField id="password_confirmation" type="password" class="mt-1 block w-full"
                v-model="form.password_confirmation" required autocomplete="new-password"
                label="Confirmer le mot de passe" :validator="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Btn :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Réinitialiser le mot de passe
            </Btn>
        </div>
    </form>
</template>
