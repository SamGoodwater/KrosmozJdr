<script setup>
/**
 * NotificationContainer Organism
 *
 * @description
 * Organism qui affiche toutes les notifications toast groupées par placement, en utilisant NotificationToast.
 * - Utilise useNotificationStore (Pinia ou composable maison)
 * - Affiche un conteneur par placement (top-end, top-start, bottom-end, bottom-start)
 * - Utilise Teleport pour placer les notifications dans le body (optionnel)
 * - Passe les props nécessaires à NotificationToast (id, message, type, actions, onClose, placement, delay, etc.)
 * - Gère la suppression de notification via onClose
 * - Slot par défaut pour customisation avancée (optionnel)
 * - Accessibilité : aria-live, role, etc.
 *
 * @example
 * <NotificationContainer />
 */
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import NotificationToast from '@/Pages/Molecules/feedback/NotificationToast.vue';
import { computed } from 'vue';

const { notificationsByPlacement, removeNotification } = useNotificationStore();

const placements = [
    'top-end',
    'top-start',
    'bottom-end',
    'bottom-start',
];

const getVertical = (placement) => placement.includes('top') ? 'top' : 'bottom';
const getHorizontal = (placement) => placement.includes('end') ? 'end' : 'start';
</script>

<template>
    <div>
        <Teleport to="body" v-for="placement in placements" :key="placement">
            <div :class="[
                'fixed z-50 pointer-events-none',
                getVertical(placement) === 'top' ? 'top-4' : 'bottom-4',
                getHorizontal(placement) === 'end' ? 'right-4' : 'left-4',
                'flex flex-col gap-2',
            ]" :style="{ maxWidth: 'calc(100vw - 2rem)' }"
                :aria-live="getVertical(placement) === 'top' ? 'polite' : 'assertive'" role="region">
                <NotificationToast v-for="notif in notificationsByPlacement[placement] || []" :key="notif.id"
                    v-bind="notif" :onClose="() => removeNotification(notif.id)">
                    <template v-if="$slots.default" #default>
                        <slot name="default" :notification="notif" />
                    </template>
                </NotificationToast>
            </div>
        </Teleport>
    </div>
</template>

<style scoped></style>
