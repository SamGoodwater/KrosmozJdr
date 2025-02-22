<script setup>
import { ref, computed } from 'vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    isAdminEdit: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const user = computed(() => page.props.user);
const verifiedEmail = computed(() => page.props.verifiedEmail);

const name = computed(() => user.value.name);
const email = computed(() => user.value.email);

const form = useForm({
    name: name.value,
    email: email.value,
});

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const formPassword = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    formPassword.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => formPassword.reset(),
        onError: () => {
            if (formPassword.errors.password) {
                formPassword.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (formPassword.errors.current_password) {
                formPassword.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};

const updateProfile = () => {
    const currentUserId = usePage().props.auth.user.id;

    if (props.isAdminEdit && currentUserId !== user.value.id) {
        form.patch(route('user.admin.update', user.value.id));
    } else {
        form.patch(route('user.update'));
    }
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ isAdminEdit ? `Modification du profil de ${user.name}` : 'Informations du profil' }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form
            @submit.prevent="updateProfile"
            class="mt-6 space-y-6"
            autocomplete="off"
        >
            <div>
                <InputLabel for="name" value="Nom" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="given-name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Adresse email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.value.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Votre adresse email n'est pas vérifiée.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cliquez ici pour renvoyer l'email de vérification.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Un nouvel email de vérification a été envoyé à votre adresse email.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Btn
                    :disabled="form.processing"
                    label="Enregistrer"
                    @click.prevent="updateProfile"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Enregistré.
                    </p>
                </Transition>
            </div>
        </form>


        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Mot de passe actuel" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="formPassword.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="formPassword.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nouveau mot de passe" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="formPassword.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="formPassword.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmer le mot de passe"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="formPassword.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="formPassword.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <Btn :disabled="formPassword.processing" label="Enregistrer" />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="formPassword.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Enregistré.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
