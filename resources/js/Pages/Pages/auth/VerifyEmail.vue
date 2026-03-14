<script setup>
import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import Route from "@/Pages/Atoms/action/Route.vue";

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
});

const page = usePage();
const form = useForm({});

const submit = () => {
    form.post(route("verification.send"), {
        preserveState: false,
    });
};

/** Status depuis la prop ou le flash Inertia (après redirect). */
const verificationLinkSent = computed(
    () =>
        props.status === "verification-link-sent" ||
        page.props.flash?.status === "verification-link-sent",
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

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <form @submit.prevent="submit" class="contents">
            <Btn
                type="submit"
                :disabled="form.processing"
                :class="{ 'opacity-25': form.processing }"
            >
                Renvoyer l'email de vérification
            </Btn>
        </form>
        <Route
            route="logout"
            method="post"
            color="neutral"
            variant="ghost"
        >
            Se déconnecter
        </Route>
    </div>
</template>
