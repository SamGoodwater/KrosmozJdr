<script setup>
defineOptions({ inheritAttrs: false });

/**
 * NotificationToast Molecule (Toast + Alert)
 *
 * @description
 * Molecule pour afficher une notification toast stylée DaisyUI, composée d'un Toast atomique et d'un Alert atomique.
 * - Props : id, message, type (success, error, info, warning, ''), actions (array ou slot), onClose (callback), placement, delay, etc.
 * - Utilise l'atom Toast comme conteneur (position, animation, etc.)
 * - Utilise l'atom Alert pour le contenu (icône, couleur, message, actions)
 * - Bouton de fermeture (croix) si onClose fourni
 * - Slot default pour contenu custom
 * - Accessibilité (role alert, aria-live, etc.)
 *
 * @see https://daisyui.com/components/toast/
 * @see https://daisyui.com/components/alert/
 *
 * @example
 * <NotificationToast message="Enregistré !" type="success" :onClose="closeFn" />
 *
 * @props {Number} id - Identifiant unique de la notification
 * @props {String} message - Message à afficher
 * @props {String} type - Type de notification ('success', 'error', 'info', 'warning', '')
 * @props {Array|undefined} actions - Actions custom (boutons, etc.)
 * @props {Function|undefined} onClose - Callback fermeture (optionnel)
 * @props {String} placement - Placement du toast (optionnel)
 * @props {Number} delay - Délai avant auto-dismiss (optionnel)
 * @slot default - Contenu custom (remplace le message)
 * @slot actions - Slot pour actions custom
 */
import { computed } from 'vue';
import Toast from '@/Pages/Atoms/feedback/Toast.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    id: { type: [Number, String], required: true },
    message: { type: String, default: '' },
    type: { type: String, default: '', validator: v => ['', 'success', 'error', 'info', 'warning'].includes(v) },
    actions: { type: [Array, null], default: null },
    onClose: { type: Function, default: null },
    placement: { type: String, default: '' },
    delay: { type: Number, default: 0 },
});

const colorMap = {
    success: 'success',
    error: 'error',
    info: 'info',
    warning: 'warning',
    '': '',
};
const color = computed(() => colorMap[props.type] || '');
</script>

<template>
    <Toast :vertical="placement?.includes('top') ? 'top' : 'bottom'"
        :horizontal="placement?.includes('end') ? 'end' : 'start'">
        <Alert :color="color" :role="'alert'" class="w-full flex items-center gap-2">
            <template v-if="$slots.default">
                <slot />
            </template>
            <template v-else>
                <span class="flex-1">{{ message }}</span>
            </template>
            <template #action>
                <template v-if="actions && actions.length">
                    <template v-for="(action, idx) in actions" :key="idx">
                        <Btn v-bind="action" @click="action.onClick" size="sm" variant="ghost" />
                    </template>
                </template>
                <slot name="actions" />
                <Btn v-if="onClose" icon="fa-xmark" size="sm" variant="ghost" color="neutral" @click="onClose"
                    :aria-label="'Fermer'" />
            </template>
        </Alert>
    </Toast>
</template>

<style scoped></style>
