<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

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
    <Head title="Réinitialiser le mot de passe" />

    <div class="flex flex-col justify-start items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-box">
            <h2 class="text-center text-2xl font-bold mb-6">
                Nouveau mot de passe
            </h2>

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

        <div class="mt-6 flex flex-col gap-4 justify-center items-center">
            <Btn
                type="submit"
                :disabled="form.processing"
                color="primary"
                :class="{ 'opacity-25': form.processing }"
            >
                <i class="fa-solid fa-key mr-2"></i>
                {{ form.processing ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}
            </Btn>
            <Btn type="button" color="neutral" size="md" variant="link">
                <Route route="login">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Retour à la connexion
                </Route>
            </Btn>
        </div>
    </form>
        </div>
    </div>
</template>
