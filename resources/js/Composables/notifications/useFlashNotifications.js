import { watch, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * useFlashNotifications — Affiche les messages flash Laravel en toasts
 *
 * @description
 * Écoute les props partagées `flash` (success, error, warning, info) et les affiche
 * via le store de notifications toast. À appeler dans le layout principal (Main.vue)
 * après le provider de notifications.
 *
 * @example
 * // Dans Main.vue
 * useNotificationProvider();
 * useFlashNotifications();
 */
export function useFlashNotifications() {
    const page = usePage();
    const notificationStore = inject('notificationStore', null);

    if (!notificationStore) {
        return;
    }

    watch(
        () => ({
            success: page.props.flash?.success,
            error: page.props.flash?.error,
            warning: page.props.flash?.warning,
            info: page.props.flash?.info,
        }),
        (flash) => {
            if (flash.success) {
                notificationStore.success(flash.success, { duration: 6000 });
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
