<script setup>
import { computed } from "vue";
// import GuestLayout from '@/Layouts/GuestLayout.vue';
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { Head, useForm } from "@inertiajs/vue3";
import Route from "@/Pages/Atoms/action/Route.vue";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);
</script>

<template>
    <Head title="Vérification de l'email" />

    <div class="mb-4 text-sm text-gray-600">
        Merci pour ton inscription ! Avant de commencer, vérifie
        ton adresse email en cliquant sur le lien qu'on vient de t'envoyer.
        Si tu n'as pas reçu l'email, on peut t'en renvoyer
        un.
    </div>

    <div
        class="mb-4 text-sm font-medium text-green-600"
        v-if="verificationLinkSent"
    >
        Un nouveau lien de vérification a été envoyé à l'adresse email que tu
        as fournie lors de l'inscription.
    </div>

    <form @submit.prevent="submit">
        <div class="mt-4 flex items-center justify-between">
            <Btn
                :disabled="form.processing"
                :class="{ 'opacity-25': form.processing }"
            >
                Renvoyer l'email de vérification
            </Btn>

            <Route
                route="logout"
                method="post"
                as="button"
                color="neutral"
                variant="ghost"
            >
                Se déconnecter
            </Route>
        </div>
    </form>
</template>
