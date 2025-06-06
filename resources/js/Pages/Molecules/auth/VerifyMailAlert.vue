<script setup>
import { useForm } from '@inertiajs/vue3';
import { success, error } from '@/Utils/notification/NotificationManager';

// Composants Atoms
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';
import Icon from '@/Pages/Atoms/images/Icon.vue';

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
    <BaseTooltip
        :tooltip="{ custom: true }"
        tooltip-position="bottom"
    >
        <div
            role="alert"
            class="alert bg-warning/80 text-content-light py-2 px-4 flex items-center gap-2"
        >
            <Icon
                icon="fa-solid fa-triangle-exclamation"
                class="w-5 h-5"
            />
            <span>Mail non vérifié.</span>
            <div class="ml-auto">
                <Btn
                    theme="link"
                    class="text-secondary-950 hover:text-secondary-900 transition-colors"
                    label="Vérifier maintenant"
                    @click="resendVerification"
                    :disabled="form.processing"
                />
            </div>
        </div>

        <template #tooltip>
            <div class="w-64 p-2">
                <p class="text-content">
                    Ton adresse email n'est pas vérifiée. Cela peut causer des problèmes lors de l'utilisation de l'application.
                </p>
                <p class="text-content mt-2">
                    En cliquant sur le bouton ci-dessous, un mail te sera envoyé.
                </p>
                <p class="text-content font-bold mt-2">
                    Pour vérifier ton adresse email, clique sur le lien dans le mail.
                </p>
            </div>
        </template>
    </BaseTooltip>
</template>
