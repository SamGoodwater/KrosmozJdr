import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Actions admin protégées par confirmation mot de passe (session).
 *
 * @description
 * Si le mode admin est déjà déverrouillé (`auth.password_recently_confirmed`, cadenas vert),
 * l'action s'exécute immédiatement. Sinon, ouvre la modale de confirmation.
 *
 * @example
 * const {
 *   showPasswordModal,
 *   passwordModalTitle,
 *   requirePassword,
 *   onPasswordConfirmed,
 * } = useProtectedAdminAction();
 *
 * requirePassword('Confirmer l\'archivage', 'Entre ton mot de passe…', 'Archiver', () => {
 *   router.delete(route('user.admin.delete', userId));
 * });
 */
export function useProtectedAdminAction() {
    const page = usePage();
    const sessionUnlocked = computed(() => Boolean(page.props.auth?.password_recently_confirmed));
    const localUnlocked = ref(false);
    const isAdminUnlocked = computed(() => sessionUnlocked.value || localUnlocked.value);

    const showPasswordModal = ref(false);
    const pendingPasswordAction = ref(null);
    const passwordModalTitle = ref('Confirmer ton mot de passe');
    const passwordModalMessage = ref('Entre ton mot de passe pour continuer.');
    const passwordModalConfirmLabel = ref('Confirmer');

    /**
     * @param {string} title
     * @param {string} message
     * @param {string} confirmLabel
     * @param {() => void} action
     */
    const requirePassword = (title, message, confirmLabel, action) => {
        if (isAdminUnlocked.value) {
            action();
            return;
        }

        passwordModalTitle.value = title;
        passwordModalMessage.value = message;
        passwordModalConfirmLabel.value = confirmLabel;
        pendingPasswordAction.value = action;
        showPasswordModal.value = true;
    };

    const onPasswordConfirmed = () => {
        localUnlocked.value = true;
        const action = pendingPasswordAction.value;
        pendingPasswordAction.value = null;
        action?.();
    };

    const onPasswordModalCancel = () => {
        pendingPasswordAction.value = null;
    };

    return {
        isAdminUnlocked,
        showPasswordModal,
        passwordModalTitle,
        passwordModalMessage,
        passwordModalConfirmLabel,
        requirePassword,
        onPasswordConfirmed,
        onPasswordModalCancel,
    };
}
