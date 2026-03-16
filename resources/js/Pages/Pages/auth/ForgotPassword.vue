<script setup>
/**
 * Page de demande de lien de réinitialisation de mot de passe.
 * Envoie un email uniquement si le compte possède un mot de passe (pas OAuth-only).
 */
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

defineProps({
    status: String,
    statusType: {
        type: String,
        default: 'success',
    },
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

    <div class="flex flex-col justify-start items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-box">
            <h2 class="text-center text-2xl font-bold">
                Mot de passe oublié
            </h2>

            <div class="mb-4 text-sm text-base-content/70">
                Indique ton adresse email et on t'enverra un lien pour réinitialiser ton mot de passe
                (uniquement pour les comptes avec mot de passe).
            </div>

            <div v-if="status" :class="['mb-4 text-sm font-medium p-3 rounded-lg', statusType === 'info' ? 'bg-info/20 text-info' : 'bg-success/20 text-success']">
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
                <div class="mt-4 flex flex-col gap-4 justify-center items-center">
                    <Btn
                        type="submit"
                        :disabled="form.processing"
                        color="primary"
                        :class="{ 'opacity-25': form.processing }"
                    >
                        <i class="fa-solid fa-envelope mr-2"></i>
                        {{ form.processing ? 'Envoi...' : 'Envoyer le lien de réinitialisation' }}
                    </Btn>
                    <Btn
                        type="button"
                        color="neutral"
                        size="md"
                        variant="link"
                    >
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
