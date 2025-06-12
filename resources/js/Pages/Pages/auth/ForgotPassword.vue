<script setup>
// Atomic Design refonte : imports atoms à jour
import { Head, useForm } from "@inertiajs/vue3";
import InputField from '@/Pages/Atoms/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>

    <Head title="Mot de passe oublié" />

    <div class="mb-4 text-sm text-gray-600">
        Mot de passe oublié ? Indiquez votre adresse email et nous vous enverrons un lien pour réinitialiser votre
        mot de passe.
    </div>

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>

    <form @submit.prevent="submit">
        <InputField id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
            autocomplete="username" label="Email" :validator="form.errors.email" />
        <div class="mt-4 flex items-center justify-end">
            <Btn :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Envoyer le lien de réinitialisation
            </Btn>
        </div>
    </form>
</template>
