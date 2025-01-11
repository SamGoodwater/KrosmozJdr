<script setup>
import Checkbox from "@/Pages/Components/inputs/Checkbox.vue";
import InputError from "@/Pages/Components/inputs/InputError.vue";
import InputLabel from "@/Pages/Components/inputs/InputLabel.vue";
import Btn from "@/Pages/Components/Actions/Btn.vue";
import Route from "@/Pages/Components/text/Route.vue";
import TextInput from "@/Pages/Components/inputs/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login.connect"), {
        onFinish: () => form.reset("password"),
    });
};

onMounted(() => {
    document.title = "Connexion";
});
</script>

<template>
    <div>
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

            <div class="mt-4 flex items-center justify-center">
                <Route route="auth.forget_password_show">
                    <Btn theme="link sm gray-600"> Mot de passe oublié ? </Btn>
                </Route>

                <Btn
                    theme="minor-600 glass submit"
                    class="ms-4"
                    :disabled="form.processing"
                    label="Se connecter"
                />
            </div>
        </form>
    </div>
</template>
