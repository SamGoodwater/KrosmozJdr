<script setup>
/**
 * AdminRunAction — Action principale d’une page de tâche admin (slot `primary` de PageHeader).
 *
 * @description
 * Tant que le mot de passe n’est pas confirmé : « Confirmer » (émet `confirm`).
 * Ensuite : « Lancer » (ou `label`), désactivé si une tâche tourne déjà.
 *
 * @example
 * <PageHeader title="Sauvegarde">
 *   <template #primary>
 *     <AdminRunAction :unlocked="unlocked" :busy="busy" :processing="form.processing"
 *         @confirm="showConfirmModal = true" @run="submit" />
 *   </template>
 * </PageHeader>
 *
 * @props {Boolean} unlocked - Mot de passe confirmé récemment
 * @props {Boolean} busy - Une tâche est déjà en cours
 * @props {Boolean} processing - Envoi en cours
 * @props {Boolean} disabled - Bloque le lancement (ex. aucun périmètre choisi)
 * @props {String} label - Libellé du lancement (défaut : « Lancer »)
 * @props {String} color - Couleur du bouton de lancement (défaut : primary)
 * @emits confirm, run
 */
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { ACTION } from '@/Utils/atomic-design/actionLabels';

defineProps({
    unlocked: { type: Boolean, default: false },
    busy: { type: Boolean, default: false },
    processing: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    label: { type: String, default: ACTION.run.label },
    color: { type: String, default: 'primary' },
});

const emit = defineEmits(['confirm', 'run']);
</script>

<template>
    <Btn v-if="!unlocked" color="primary" size="sm" type="button" @click="emit('confirm')">
        <i :class="ACTION.confirm.icon" class="mr-1.5" aria-hidden="true"></i>
        {{ ACTION.confirm.label }}
    </Btn>
    <Btn
        v-else
        :color="color"
        size="sm"
        type="button"
        :disabled="disabled || busy || processing"
        @click="emit('run')"
    >
        <i :class="ACTION.run.icon" class="mr-1.5" aria-hidden="true"></i>
        {{ busy ? 'Tâche déjà en cours…' : processing ? ACTION.run.processing : label }}
    </Btn>
</template>
