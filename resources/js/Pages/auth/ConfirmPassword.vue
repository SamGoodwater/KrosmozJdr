<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    /** URL de redirection après confirmation (session `url.intended`). */
    intendedUrl: { type: String, default: '/' },
});

const password = ref('');
const error = ref(null);
const loading = ref(false);

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

const passwordValidation = computed(() => {
    if (!error.value) {
        return null;
    }

    return {
        state: 'error',
        message: error.value,
        showNotification: false,
    };
});

async function submit() {
    error.value = null;

    if (!password.value.trim()) {
        error.value = 'Le mot de passe est requis.';
        return;
    }

    loading.value = true;

    try {
        const { data } = await axios.post(
            route('user.password.confirm'),
            { password: password.value },
            {
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
            },
        );

        if (data?.confirmed) {
            router.visit(props.intendedUrl || '/', {
                preserveState: false,
                preserveScroll: false,
            });
            return;
        }

        error.value = 'Une erreur est survenue.';
    } catch (err) {
        const msg = err?.response?.data?.errors?.password
            ?? err?.response?.data?.message
            ?? 'Le mot de passe est incorrect.';
        error.value = Array.isArray(msg) ? msg[0] : msg;
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Confirmation du mot de passe" />

    <div class="mx-auto max-w-md space-y-6 py-8">
        <div>
            <h1 class="text-xl font-semibold text-base-content">
                Zone sécurisée
            </h1>
            <p class="mt-2 text-sm text-base-content/70">
                Confirme ton mot de passe pour accéder à cette partie de l'application.
            </p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <InputField
                id="password"
                v-model="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                autofocus
                label="Mot de passe"
                :validation="passwordValidation"
            />

            <div class="flex justify-end">
                <Btn
                    type="submit"
                    color="primary"
                    :disabled="loading"
                    :class="{ 'opacity-50': loading }"
                >
                    {{ loading ? 'Vérification…' : 'Confirmer' }}
                </Btn>
            </div>
        </form>
    </div>
</template>
