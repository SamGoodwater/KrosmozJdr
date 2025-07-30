<script setup>
// Atomic Design refonte : imports atoms à jour
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

// Validation computed pour l'email
const emailValidation = computed(() => {
    if (!form.errors.email) return null;
    return {
        state: 'error',
        message: form.errors.email,
        showNotification: false
    };
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
        <InputField 
            id="email" 
            type="email" 
            class="mt-1 block w-full" 
            v-model="form.email" 
            required 
            autofocus
            autocomplete="username" 
            label="Email" 
            :validation="emailValidation" 
        />
        <div class="mt-4 flex items-center justify-end">
            <Btn :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Envoyer le lien de réinitialisation
            </Btn>
        </div>
    </form>
</template>
