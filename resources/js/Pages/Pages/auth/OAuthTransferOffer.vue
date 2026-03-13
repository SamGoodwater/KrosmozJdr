<script setup>
/**
 * Page proposant le transfert d'une liaison OAuth déjà associée à un autre compte.
 * Affichée quand l'utilisateur connecté (ex. Admin) tente de lier un provider (ex. GitHub)
 * qui est déjà lié à un autre compte (ex. Goodwater).
 */
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

defineProps({
    provider: { type: String, required: true },
    providerLabel: { type: String, required: true },
    otherUserName: { type: String, required: true },
});

const isSubmitting = ref(false);

function confirmTransfer() {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    router.post(route('oauth.transfer'), {}, {
        preserveScroll: true,
        onFinish: () => { isSubmitting.value = false; },
    });
}

function cancelTransfer() {
    router.visit(route('oauth.cancel-transfer'));
}
</script>

<template>
    <Head title="Compte déjà lié à un autre utilisateur" />

    <div class="flex flex-col justify-start items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 sm:rounded-lg">
            <h2 class="text-center text-2xl font-bold mb-4">
                Compte {{ providerLabel }} déjà lié
            </h2>

            <div class="alert alert-warning mb-6">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <div>
                    <p class="font-medium">Ce compte {{ providerLabel }} est déjà lié à <strong>{{ otherUserName }}</strong>.</p>
                    <p class="text-sm mt-2">
                        Tu peux transférer la liaison vers ton compte actuel. Le compte <strong>{{ otherUserName }}</strong>
                        ne pourra plus se connecter via {{ providerLabel }} (il pourra toujours utiliser son email et mot de passe s'il en a un).
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <Btn
                    color="primary"
                    class="w-full"
                    :disabled="isSubmitting"
                    @click="confirmTransfer"
                >
                    <i class="fa-solid fa-arrow-right-arrow-left mr-2"></i>
                    Transférer la liaison vers mon compte
                </Btn>
                <Btn
                    color="neutral"
                    variant="outline"
                    class="w-full"
                    @click="cancelTransfer"
                >
                    <i class="fa-solid fa-times mr-2"></i>
                    Annuler
                </Btn>
            </div>

            <p class="mt-6 text-center text-sm text-base-content/600">
                <Route :href="route('user.settings') + '#connections'" class="link link-primary">
                    Retour aux paramètres
                </Route>
            </p>
        </div>
    </div>
</template>
