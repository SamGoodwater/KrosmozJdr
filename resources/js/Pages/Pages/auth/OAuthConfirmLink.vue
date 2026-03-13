<script setup>
/**
 * Page de confirmation de liaison OAuth.
 * Affichée quand un utilisateur se connecte avec un provider (ex. GitHub)
 * dont l'email correspond à un compte existant (ex. créé via Discord).
 * Permet de confirmer ou annuler la liaison.
 */
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

defineProps({
    email: { type: String, required: true },
    provider: { type: String, required: true },
    providerLabel: { type: String, required: true },
    existingProviders: { type: Array, default: () => [] },
});

const isSubmitting = ref(false);

function confirmLink() {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    router.post(route('oauth.confirm-link.post'), {}, {
        preserveScroll: true,
        onFinish: () => { isSubmitting.value = false; },
    });
}

function cancelLink() {
    router.visit(route('oauth.cancel-link'));
}
</script>

<template>
    <Head title="Confirmer la liaison du compte" />

    <div class="flex flex-col justify-start items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-lg">
            <h2 class="text-center text-2xl font-bold mb-4">
                Compte existant
            </h2>

            <div class="alert alert-info mb-6">
                <i class="fa-solid fa-circle-info text-xl"></i>
                <div>
                    <p class="font-medium">Un compte avec cette adresse email existe déjà.</p>
                    <p class="text-sm mt-1">
                        <span v-if="existingProviders.length > 0">
                            Il est actuellement connecté via {{ existingProviders.join(', ') }}.
                        </span>
                        <span v-else>
                            Il utilise la connexion classique (email + mot de passe).
                        </span>
                    </p>
                    <p class="text-sm mt-2">
                        Veux-tu lier ce compte <strong>{{ providerLabel }}</strong> pour pouvoir te connecter avec les deux ?
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <Btn
                    color="primary"
                    class="w-full"
                    :disabled="isSubmitting"
                    @click="confirmLink"
                >
                    <i class="fa-solid fa-link mr-2"></i>
                    Oui, lier mon compte {{ providerLabel }}
                </Btn>
                <Btn
                    color="neutral"
                    variant="outline"
                    class="w-full"
                    @click="cancelLink"
                >
                    <i class="fa-solid fa-times mr-2"></i>
                    Non, annuler
                </Btn>
            </div>

            <p class="mt-6 text-center text-sm text-base-content/600">
                <Route route="login" class="link link-primary">
                    Retour à la connexion
                </Route>
            </p>
        </div>
    </div>
</template>
