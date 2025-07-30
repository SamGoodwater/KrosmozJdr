<script setup>
/**
 * @description
 * UpdateProfileInformationForm Organism. Formulaire de mise à jour des informations du profil utilisateur.
 * - Mise à jour du nom et de l'email
 * - Gestion de la vérification email
 * - Validation et gestion des erreurs
 * - Feedback utilisateur (succès/erreur)
 * - Responsive design
 *
 * @prop {Boolean} mustVerifyEmail - Indique si la vérification email est requise
 * @prop {String} status - Statut de la vérification email
 */
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const emit = defineEmits(['success', 'error']);

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    }
});

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

// Validation computed pour chaque champ
const nameValidation = computed(() => {
    if (!form.errors.name) return null;
    return {
        state: 'error',
        message: form.errors.name,
        showNotification: false
    };
});

const emailValidation = computed(() => {
    if (!form.errors.email) return null;
    return {
        state: 'error',
        message: form.errors.email,
        showNotification: false
    };
});
</script>

<template>
    <Container class="max-w-xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
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
                    <Tooltip content="Votre nom d'affichage" placement="top">
                        <InputField id="name" v-model="form.name" type="text" required autofocus autocomplete="name"
                            :validation="nameValidation"
                            aria-label="Nom"
                            :class="'mt-1 block w-full'" />
                    </Tooltip>
                </div>

                <div class="space-y-2">
                    <InputLabel for="email" value="Adresse email" theme="primary" />
                    <Tooltip content="Votre adresse email" placement="top">
                        <InputField id="email" v-model="form.email" type="email" required autocomplete="username"
                            :validation="emailValidation"
                            aria-label="Adresse email"
                            :class="'mt-1 block w-full'" />
                    </Tooltip>
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null"
                    class="p-4 rounded-lg bg-warning-900/20 backdrop-blur-sm">
                    <p class="text-sm text-warning-100">
                        Votre adresse email n'est pas vérifiée.
                        <Tooltip content="Renvoyer l'email de vérification" placement="top">
                            <Route route="verification.send" method="post"
                                class="text-warning-200 hover:text-warning-100 underline">
                                Cliquez ici pour renvoyer l'email de vérification.
                            </Route>
                        </Tooltip>
                    </p>
                    <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-success-500">
                        Un nouvel email de vérification a été envoyé à votre adresse email.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Tooltip content="Mettre à jour les informations du profil" placement="top">
                        <Btn theme="primary" :disabled="form.processing" label="Enregistrer" />
                    </Tooltip>

                    <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                        <Alert v-if="form.recentlySuccessful" color="success" variant="soft" class="text-sm">
                            Profil mis à jour avec succès.
                        </Alert>
                    </Transition>
                </div>
            </form>
        </section>
    </Container>
</template>
