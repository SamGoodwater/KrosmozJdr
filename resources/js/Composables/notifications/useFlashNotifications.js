import { watch, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

/**
 * useFlashNotifications — Affiche les messages flash Laravel en toasts
 *
 * @description
 * Écoute les props partagées `flash` (success, error, warning, info) et les affiche
 * via le store de notifications toast. À appeler dans le layout principal (Main.vue)
 * après le provider de notifications.
 *
 * Note : Le composant qui appelle provide() ne peut pas s'injecter ses propres valeurs.
 * Passer explicitement le store depuis useNotificationProvider().
 *
 * @example
 * // Dans Main.vue
 * const notificationStore = useNotificationProvider();
 * useFlashNotifications(notificationStore);
 *
 * @param {Object} [store] - Store de notifications (ex. retour de useNotificationProvider).
 *        Si absent, tente inject() puis useNotificationStore() en fallback.
 */
export function useFlashNotifications(store = null) {
    const page = usePage();
    const notificationStore = store ?? inject('notificationStore', null) ?? useNotificationStore();

    if (!notificationStore) {
        return;
    }

    watch(
        () => ({
            success: page.props.flash?.success,
            error: page.props.flash?.error,
            warning: page.props.flash?.warning,
            info: page.props.flash?.info,
            status: page.props.flash?.status,
        }),
        (flash) => {
            if (flash.success) {
                notificationStore.success(flash.success, { duration: 6000 });
            } else if (flash.status === 'verification-link-sent') {
                notificationStore.success('Un nouveau lien de vérification a été envoyé à ton adresse email.', { duration: 6000 });
            }
            if (flash.error) {
                notificationStore.error(flash.error, { duration: 8000 });
            }
            if (flash.warning) {
                notificationStore.warning(flash.warning, { duration: 6000 });
            }
            if (flash.info) {
                notificationStore.info(flash.info, { duration: 6000 });
            }
        },
        { immediate: true, deep: true },
    );
}
