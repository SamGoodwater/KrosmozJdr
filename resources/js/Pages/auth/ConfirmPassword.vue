<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm.store'), {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
    });
};

const passwordValidation = computed(() => {
    if (!form.errors.password) {
        return null;
    }

    return {
        state: 'error',
        message: form.errors.password,
        showNotification: false,
    };
});
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
                v-model="form.password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                autofocus
                label="Mot de passe"
                :validation="passwordValidation"
                @keyup.enter="submit"
            />

            <div class="flex justify-end">
                <Btn
                    type="submit"
                    color="primary"
                    :disabled="form.processing"
                    :class="{ 'opacity-50': form.processing }"
                >
                    {{ form.processing ? 'Vérification…' : 'Confirmer' }}
                </Btn>
            </div>
        </form>
    </div>
</template>
