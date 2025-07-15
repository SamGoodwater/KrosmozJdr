<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Checkbox from "@/Pages/Atoms/data-input/Checkbox.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

defineProps({
    status: String,
});

const form = useForm({
    identifier: "",
    password: "",
    remember: false,
});

const { setPageTitle } = usePageTitle();

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

onMounted(() => {
    setPageTitle("Connexion");
});
</script>

<template>
    <div class="flex flex-col items-center justify-center h-full w-full">

        <h2 class="text-title py-8">Connexion</h2>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form
            @submit.prevent="submit"
            method="POST"
            autocomplete="on"
            id="login-form"
        >
            <div class="flex flex-col gap-2">
                <InputField
                    id="identifier"
                    color="primary"
                    autofocus
                    required
                    placeholder="Email ou Pseudo"
                    v-model="form.identifier"
                    autocomplete="username"
                    name="identifier"
                    label="Email ou Pseudo"
                    :validator="form.errors.identifier"
                />
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <InputField
                    id="password"
                    type="password"
                    color="primary"
                    required
                    v-model="form.password"
                    name="password"
                    label="Mot de passe"
                    :validator="form.errors.password"
                />
            </div>

            <div class="mt-4">
                <Checkbox
                    id="remember"
                    size="md"
                    name="remember"
                    v-model="form.remember"
                    label="Se rappeler de mes identifiants"
                />
            </div>

            <div class="mt-4 flex flex-col items-center justify-center gap-5">
                <Route route="password.request">
                    <Btn color="neutral" variant="link" size="md"
                        >Mot de passe oublié ?</Btn
                    >
                </Route>

                <Btn
                    color="primary"
                    variant="glass"
                    type="submit"
                    class="ms-4"
                    :disabled="form.processing"
                >
                    Se connecter
                </Btn>

                <Route route="register">
                    <Btn color="neutral" variant="link" size="md"
                        >Pas encore de compte ?</Btn
                    >
                </Route>
            </div>
        </form>
    </div>
</template>
