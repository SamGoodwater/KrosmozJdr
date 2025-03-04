<script setup>
import { ref, computed, watch } from 'vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import useEditableField from '@/Composables/useEditableField';
import { success, error } from '@/Utils/notificationManager';

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

// Création du formulaire partagé
const form = useForm({
    name: user.value.name,
    email: user.value.email,
});

// Création des champs éditables
const fields = {
    name: useEditableField(user.value.name, {
        field: 'name',
        route: route('user.update'),
        onSuccess: (response) => {
            user.value.name = response.data.name;
            success('Le nom a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour du nom')
    }),
    email: useEditableField(user.value.email, {
        field: 'email',
        route: route('user.update'),
        onSuccess: (response) => {
            user.value.email = response.data.email;
            success('L\'email a été mis à jour avec succès');
        },
        onError: () => error('Une erreur est survenue lors de la mise à jour de l\'email')
    }),
};

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
        onSuccess: () => {
            formPassword.reset();
            success('Le mot de passe a été mis à jour avec succès');
        },
        onError: () => {
            error('Une erreur est survenue lors de la mise à jour du mot de passe');
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

const avatar = ref(null);

const updateAvatar = () => {
    const formData = new FormData();
    formData.append('avatar', avatar.value.files[0]);

    axios.post(route('profile.updateAvatar'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(() => {
        avatar.value = null;
        success('L\'avatar a été mis à jour avec succès');
    }).catch(() => {
        error('Une erreur est survenue lors de la mise à jour de l\'avatar');
    });
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-content-300">
                {{ isAdminEdit ? `Modification du profil de ${user.name}` : 'Informations du profil' }}
            </h2>

            <p class="mt-1 text-sm text-content-600">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form
            @submit.prevent class="mt-6 space-y-6"
            autocomplete="off"
        >
            <div>
                <InputLabel for="name" value="Pseudo" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    :field="fields.name"
                    required
                    autofocus
                    :useFieldComposable="true"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Adresse mail" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    :field="fields.email"
                    required
                    :useFieldComposable="true"
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

            <div>
                <InputLabel for="avatar" value="Avatar" />
                <input type="file" id="avatar" ref="avatar" class="mt-1 block w-full" />
                <Btn label="Mettre à jour l'avatar" @click.prevent="updateAvatar" />
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
