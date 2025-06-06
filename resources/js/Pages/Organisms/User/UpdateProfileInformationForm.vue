/**
* UpdateProfileInformationForm component that handles user profile information updates.
* Provides a form for updating name and email with validation and email verification.
*
* Features:
* - Name and email update
* - Email verification status
* - Form validation and error handling
* - Success feedback
* - Responsive design
*
* Props:
* - theme: Theme configuration for styling
* - mustVerifyEmail: Boolean indicating if email verification is required
* - status: String indicating the current verification status
*
* Events:
* - @success: Emitted when profile is successfully updated
* - @error: Emitted when an error occurs during update
*/
<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { extractTheme, combinePropsWithTheme } from '@/Utils/extractTheme';
import { commonProps } from '@/Utils/commonProps';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import Container from '@/Pages/Atoms/panels/Container.vue';

const props = defineProps({
    ...commonProps,
    mustVerifyEmail: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    }
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const updateProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
        },
        onError: () => {
            emit('error', form.errors);
        }
    });
};
</script>

<template>
    <section class="space-y-6 transition-all duration-200">
        <header class="mb-6 p-4 rounded-lg bg-primary-900/20 backdrop-blur-sm">
            <h2 class="text-lg font-medium text-primary-100">
                Informations du profil
            </h2>

            <p class="mt-1 text-sm text-primary-200">
                Mettez à jour les informations de votre compte et votre adresse email.
            </p>
        </header>

        <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
            <div class="space-y-2">
                <InputLabel for="name" value="Nom" theme="primary" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" theme="primary" tooltip="Votre nom d'affichage" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <InputLabel for="email" value="Adresse email" theme="primary" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" theme="primary" tooltip="Votre adresse email" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null"
                class="p-4 rounded-lg bg-warning-900/20 backdrop-blur-sm">
                <p class="text-sm text-warning-100">
                    Votre adresse email n'est pas vérifiée.
                    <Route route="verification.send" method="post"
                        class="text-warning-200 hover:text-warning-100 underline"
                        tooltip="Renvoyer l'email de vérification">
                        Cliquez ici pour renvoyer l'email de vérification.
                    </Route>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-success-500">
                    Un nouvel email de vérification a été envoyé à votre adresse email.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Btn theme="primary" :disabled="form.processing" label="Enregistrer"
                    tooltip="Mettre à jour les informations du profil" />

                <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-success-500">
                        Profil mis à jour avec succès.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
