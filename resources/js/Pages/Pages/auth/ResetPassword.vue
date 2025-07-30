<script setup>
// import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
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

// Validation computed pour chaque champ
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
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>

    <Head title="Reset Password" />

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

        <div class="mt-4">
            <InputField 
                id="password" 
                type="password" 
                class="mt-1 block w-full" 
                v-model="form.password" 
                required
                autocomplete="new-password" 
                label="Mot de passe" 
                :validation="passwordValidation" 
            />
        </div>

        <div class="mt-4">
            <InputField 
                id="password_confirmation" 
                type="password" 
                class="mt-1 block w-full"
                v-model="form.password_confirmation" 
                required 
                autocomplete="new-password"
                label="Confirmer le mot de passe" 
                :validation="passwordConfirmationValidation" 
            />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Btn :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Réinitialiser le mot de passe
            </Btn>
        </div>
    </form>
</template>
