<script setup>
import Checkbox from "@/Pages/Atoms/inputs/Checkbox.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import TextInput from "@/Pages/Atoms/inputs/TextInput.vue";
import PasswordInput from "@/Pages/Atoms/inputs/PasswordInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

defineProps({
    status: {
        type: String,
    },
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
    setPageTitle('Connexion');
});
</script>

<template>
    <div>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" method="POST" autocomplete="on" id="login-form">
            <div class="flex flex-col gap-2">
                <TextInput id="identifier" theme="secondary autofocus required" placeholder="Email ou Pseudo"
                    v-model="form.identifier" autocomplete="username" name="identifier" :useFieldComposable="false"
                    inputLabel="Email ou Pseudo" :errorMessage="form.errors.identifier" />
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <PasswordInput id="password" theme="secondary required" v-model="form.password" name="password"
                    inputLabel="Mot de passe" :errorMessage="form.errors.password" />
            </div>

            <div class="mt-4">
                <Checkbox id="remember" class="ms-2" theme="sm" name="remember" :value="form.remember"
                    @update:value="(val) => form.remember = val" inputLabel="Se rappeler de mes identifiants"
                    :useFieldComposable="false" />
            </div>

            <div class="mt-4 flex flex-col items-center justify-center gap-3">
                <Route route="password.request">
                    <Btn theme="link sm neutral" label="Mot de passe oublié ? " />
                </Route>

                <Btn theme="primary glass submit" class="ms-4" :disabled="form.processing" type="submit"
                    label="Se connecter" />

                <Route route="register">
                    <Btn theme="link sm neutral" label="Pas encore de compte ?" />
                </Route>
            </div>
        </form>
    </div>
</template>
