<script setup>
/**
 * ConfirmModal (Molecule)
 *
 * @description
 * Modal de confirmation réutilisable (ex: suppression).
 *
 * @props {Boolean} open
 * @props {String} title
 * @props {String} message
 * @props {String} confirmLabel
 * @props {String} cancelLabel
 * @props {String} confirmColor - couleur Btn (ex: 'error', 'primary')
 * @props {String} confirmIcon - icône FontAwesome (ex: 'fa-solid fa-trash')
 *
 * @emits close
 * @emits confirm
 * @emits cancel
 */
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: "Confirmer" },
    message: { type: String, default: "Es-tu sûr ?" },
    /** Lignes de récapitulatif (ex. impact suppression). */
    details: { type: Array, default: () => [] },
    confirmLabel: { type: String, default: "Confirmer" },
    cancelLabel: { type: String, default: "Annuler" },
    confirmColor: { type: String, default: "error" },
    confirmIcon: { type: String, default: "fa-solid fa-trash" },
});

const emit = defineEmits(["close", "confirm", "cancel"]);
</script>

<template>
    <Modal :open="open" size="sm" placement="middle-center" close-on-esc @close="emit('close')">
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">
                    {{ title }}
                </div>
                <Btn size="sm" variant="ghost" @click="emit('close')">Fermer</Btn>
            </div>
        </template>

        <div class="space-y-4">
            <p class="text-sm text-base-content/80 whitespace-pre-line">
                {{ message }}
            </p>
            <ul
                v-if="details.length"
                class="list-disc space-y-1 pl-5 text-sm text-base-content/70"
            >
                <li v-for="(line, index) in details" :key="`detail-${index}`">
                    {{ line }}
                </li>
            </ul>

            <div class="flex justify-end gap-2">
                <Btn variant="ghost" @click="emit('cancel')">
                    {{ cancelLabel }}
                </Btn>
                <Btn :color="confirmColor" @click="emit('confirm')">
                    <Icon :source="confirmIcon" pack="solid" alt="Confirmer" class="mr-2" />
                    {{ confirmLabel }}
                </Btn>
            </div>
        </div>
    </Modal>
</template>

