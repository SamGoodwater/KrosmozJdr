<script setup>
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import { success, error } from '@/Utils/notificationManager';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({});

const resendVerification = () => {
    form.post(route('verification.send'), {
        preserveScroll: true,
        onSuccess: () => {
            success('Un nouveau lien de vérification a été envoyé à votre adresse email.');
        },
        onError: () => {
            error('Une erreur est survenue lors de l\'envoi du lien de vérification.');
        },
    });
};
</script>

<template>
    <Tooltip>
        <template #content>
            <p class="w-64">
                Ton adresse email n'est pas vérifiée. Cela peut causer des problèmes lors de l'utilisation de l'application.
                <br>
                En cliquant sur le bouton ci-dessous, un mail te sera envoyé.
                <br>
                <span class="font-bold">Pour vérifier ton adresse email, clique sur le lien dans le mail.</span>
            </p>
        </template>

        <div role="alert" class="alert bg-warning/80 text-content-light py-2 px-4">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Mail non vérifié.</span>
                <div>
            <Btn
                theme="link"
                class="text-secondary-950"
                label="Vérifier maintenant"
                @click="resendVerification"
                :disabled="form.processing"
            />
                </div>
        </div>
    </Tooltip>
</template>
