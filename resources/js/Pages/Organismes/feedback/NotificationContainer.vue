<script setup>
/**
 * NotificationContainer Organism
 *
 * @description
 * Organism qui affiche toutes les notifications toast groupées par placement, en utilisant NotificationToast.
 * - Utilise useNotificationStore (Pinia ou composable maison)
 * - Affiche un conteneur par placement (top-left, top-right, bottom-left, bottom-right)
 * - Utilise Teleport pour placer les notifications dans le body
 * - Gère le scroll automatique si dépassement de hauteur
 * - Passe les props nécessaires à NotificationToast
 * - Gère la suppression de notification via onClose
 * - Slot par défaut pour customisation avancée (optionnel)
 * - Accessibilité : aria-live, role, etc.
 * - Utilise le composant Toast personnalisé avec classe 'toast-custom'
 *
 * @example
 * <NotificationContainer />
 */
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import NotificationToast from '@/Pages/Molecules/feedback/NotificationToast.vue';
import { computed } from 'vue';

const { notificationsByPlacement, removeNotification } = useNotificationStore();

// Les 4 placements supportés
const placements = [
    'top-left',
    'top-right', 
    'bottom-left',
    'bottom-right',
];

// Fonctions utilitaires pour le placement
const getVertical = (placement) => placement.includes('top') ? 'top' : 'bottom';
const getHorizontal = (placement) => placement.includes('right') ? 'right' : 'left';

// Classes CSS pour le positionnement
const getPositionClasses = (placement) => {
    const vertical = getVertical(placement);
    const horizontal = getHorizontal(placement);
    
    return {
        container: [
            'fixed z-50 pointer-events-none',
            vertical === 'top' ? 'top-4' : 'bottom-4',
            horizontal === 'right' ? 'right-4' : 'left-4',
            'flex flex-col gap-2',
        ],
        scrollContainer: [
            'max-h-[calc(100vh-2rem)]',
            'overflow-y-auto',
            'scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-transparent',
            'pointer-events-auto',
            // Utiliser grid pour contrôler l'alignement des notifications contractées
            'grid grid-cols-1 gap-2',
            // Justification selon le placement
            horizontal === 'right' ? 'justify-items-end' : 'justify-items-start',
        ]
    };
};

// Accessibilité selon le placement
const getAriaLive = (placement) => {
    const vertical = getVertical(placement);
    return vertical === 'top' ? 'polite' : 'assertive';
};
</script>

<template>
    <div>
        <Teleport to="body" v-for="placement in placements" :key="placement">
            <div 
                :class="getPositionClasses(placement).container"
                :style="{ maxWidth: 'calc(100vw - 2rem)' }"
                :aria-live="getAriaLive(placement)" 
                role="region"
                :aria-label="`Notifications ${placement}`"
            >
                <!-- Container avec scroll -->
                <div :class="getPositionClasses(placement).scrollContainer">
                    <NotificationToast 
                        v-for="notif in notificationsByPlacement[placement] || []" 
                        :key="notif.id"
                        v-bind="notif" 
                        :onClose="() => removeNotification(notif.id)"
                    >
                        <template v-if="$slots.default" #default>
                            <slot name="default" :notification="notif" />
                        </template>
                    </NotificationToast>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
/* Styles pour le scrollbar personnalisé */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 2px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: hsl(var(--color) / 0.3);
    border-radius: 0.125rem;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background-color: hsl(var(--bc) / 0.5);
}
</style>
