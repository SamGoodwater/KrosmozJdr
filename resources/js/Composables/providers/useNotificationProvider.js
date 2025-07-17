import { provide } from 'vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

/**
 * useNotificationProvider — Provider pour injecter le store de notifications
 *
 * @description
 * Provider qui injecte le store de notifications dans toute l'application.
 * À utiliser dans le composant racine (App.vue) pour rendre les notifications
 * disponibles partout via injection.
 *
 * @example
 * // Dans App.vue
 * <script setup>
 * import { useNotificationProvider } from '@/Composables/providers/useNotificationProvider';
 * 
 * // Injecte le store de notifications
 * useNotificationProvider();
 * </script>
 *
 * // Dans n'importe quel composant enfant
 * <script setup>
 * import { inject } from 'vue';
 * 
 * const notificationStore = inject('notificationStore');
 * notificationStore.success('Message de succès !');
 * </script>
 */
export function useNotificationProvider() {
    const notificationStore = useNotificationStore();
    
    // Injecte le store avec la clé 'notificationStore'
    provide('notificationStore', notificationStore);
    
    return notificationStore;
} 