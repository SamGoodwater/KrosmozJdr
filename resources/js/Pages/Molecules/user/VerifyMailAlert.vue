<script setup>
/**
* VerifyMailAlert Molecule (Atomic Design, DaisyUI)
*
* @description
* Affiche une alerte si l'email de l'utilisateur n'est pas vérifié, avec bouton pour renvoyer le mail de vérification.
* - Utilise l'atom Icon pour l'icône d'alerte
* - Utilise l'atom Btn pour l'action
* - Utilise l'atom Tooltip pour l'aide contextuelle sur le bouton
* - Structure DaisyUI, accessibilité (role="alert")
*
* @see Icon, Btn, Tooltip
*/
import { useForm } from '@inertiajs/vue3';
import { success, error } from '@/Utils/notification/NotificationManager';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

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
    <div role="alert" class="alert bg-warning/80 text-content-light py-2 px-4 flex items-center gap-2">
        <Icon source="fa-solid fa-triangle-exclamation" alt="Alerte : mail non vérifié" size="md" class="w-5 h-5" />
        <span>Mail non vérifié.</span>
        <div class="ml-auto">
            <Tooltip placement="bottom">
                <Btn color="secondary" variant="link"
                    class="text-secondary-950 hover:text-secondary-900 transition-colors" :disabled="form.processing"
                    @click="resendVerification">
                    Vérifier maintenant
                </Btn>
                <template #content>
                    <div class="w-64 p-2">
                        <p class="text-content">
                            Ton adresse email n'est pas vérifiée. Cela peut causer des problèmes lors de l'utilisation
                            de l'application.
                        </p>
                        <p class="text-content mt-2">
                            En cliquant sur le bouton ci-dessous, un mail te sera envoyé.
                        </p>
                        <p class="text-content font-bold mt-2">
                            Pour vérifier ton adresse email, clique sur le lien dans le mail.
                        </p>
                    </div>
                </template>
            </Tooltip>
        </div>
    </div>
</template>
