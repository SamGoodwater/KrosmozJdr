import { onMounted, onUnmounted, inject } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

/**
 * useFlashNotifications — Affiche les messages flash Laravel en toasts
 *
 * @description
 * Écoute chaque visite Inertia réussie et affiche les props `flash` (success, error, etc.)
 * via le store de notifications toast. Plus fiable qu'un watch sur page.props (redirect,
 * reload partiel, même URL avec hash).
 *
 * @param {Object} [store] - Store de notifications (ex. retour de useNotificationProvider).
 */
export function useFlashNotifications(store = null) {
    const notificationStore = store ?? inject('notificationStore', null) ?? useNotificationStore();

    if (!notificationStore) {
        return;
    }

    const toastOptions = {
        success: { duration: 8000, placement: 'top-right' },
        error: { duration: 9000, placement: 'top-right' },
        warning: { duration: 7000, placement: 'top-right' },
        info: { duration: 7000, placement: 'top-right' },
    };

    /**
     * @param {Record<string, unknown>|undefined|null} flash
     */
    function showFlashToasts(flash) {
        if (!flash || typeof flash !== 'object') {
            return;
        }

        const successMsg = flash.success != null ? String(flash.success).trim() : '';
        if (successMsg) {
            notificationStore.success(successMsg, toastOptions.success);
        } else if (flash.status === 'verification-link-sent') {
            notificationStore.success(
                'Un nouveau lien de vérification a été envoyé à ton adresse email.',
                toastOptions.success,
            );
        }

        const errorMsg = flash.error != null ? String(flash.error).trim() : '';
        if (errorMsg) {
            notificationStore.error(errorMsg, toastOptions.error);
        }
        const warningMsg = flash.warning != null ? String(flash.warning).trim() : '';
        if (warningMsg) {
            notificationStore.warning(warningMsg, toastOptions.warning);
        }
        const infoMsg = flash.info != null ? String(flash.info).trim() : '';
        if (infoMsg) {
            notificationStore.info(infoMsg, toastOptions.info);
        }
    }

    /** @param {import('@inertiajs/core').GlobalEvent<'success'>} event */
    function handleSuccess(event) {
        showFlashToasts(event.detail.page.props.flash);
    }

    onMounted(() => {
        router.on('success', handleSuccess);
    });

    onUnmounted(() => {
        router.off('success', handleSuccess);
    });
}
