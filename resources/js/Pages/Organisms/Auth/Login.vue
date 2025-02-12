<script setup>
import Checkbox from "@/Pages/Atoms/inputs/Checkbox.vue";
import InputError from "@/Pages/Atoms/inputs/InputError.vue";
import InputLabel from "@/Pages/Atoms/inputs/InputLabel.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import TextInput from "@/Pages/Atoms/inputs/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

onMounted(() => {
    document.title = "Connexion";
});
</script>

<template>
    <div>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    theme="secondary autofocus required email"
                    placeholder="exemple@exemple.fr"
                    v-model="form.email"
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    theme="secondary required password"
                    placeholder="*************"
                    v-model="form.password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <Checkbox
                    class="ms-2"
                    theme="sm "
                    name="remember"
                    v-model="form.remember"
                    label="Se rappeler de mes identifiants"
                >
                </Checkbox>
            </div>

            <div class="mt-4 flex flex-col items-center justify-center gap-3">
                <Route route="password.request">
                    <Btn
                        theme="link sm neutral"
                        label="Mot de passe oublié ? "
                    />
                </Route>

                <Btn
                    theme="primary glass submit"
                    class="ms-4"
                    :disabled="form.processing"
                    type="submit"
                    label="Se connecter"
                />

                <Route route="register">
                    <Btn
                        theme="link sm neutral"
                        label="Pas encore de compte ?"
                    />
                </Route>
            </div>
        </form>
    </div>
</template>
