import { ref, computed } from "vue";

/**
 * useNotificationStore — Composable global pour les notifications toast (atomic design)
 *
 * @description
 * Gère la liste des notifications toast (success, error, info, warning, etc.), leur ajout/suppression, l'auto-dismiss, le placement, etc.
 * Compatible avec NotificationToast et NotificationContainer.
 *
 * @typedef {Object} Notification
 * @property {number} id - Identifiant unique
 * @property {string} message - Message à afficher
 * @property {string} [type] - Type de notification ('success', 'error', 'info', 'warning', '')
 * @property {string} [placement] - Placement ('top-end', 'top-start', 'bottom-end', 'bottom-start')
 * @property {number} [delay] - Délai avant auto-dismiss (ms, 0 = pas d'auto-dismiss)
 * @property {Array|undefined} [actions] - Actions custom (boutons, etc.)
 * @property {Object} [extra] - Données additionnelles
 */

const notifications = ref([]);

function addNotification({
    message,
    type = "",
    placement = "top-end",
    delay = 3000,
    actions = undefined,
    ...extra
}) {
    if (!message) return;
    const id = Date.now() + Math.floor(Math.random() * 10000);
    const notification = {
        id,
        message,
        type,
        placement,
        delay,
        actions,
        ...extra,
    };
    notifications.value.push(notification);
    if (delay > 0) {
        setTimeout(() => removeNotification(id), delay);
    }
    return id;
}

function removeNotification(id) {
    notifications.value = notifications.value.filter((n) => n.id !== id);
}

function success(message, options = {}) {
    return addNotification({ message, type: "success", ...options });
}
function error(message, options = {}) {
    return addNotification({ message, type: "error", ...options });
}
function info(message, options = {}) {
    return addNotification({ message, type: "info", ...options });
}
function warning(message, options = {}) {
    return addNotification({ message, type: "warning", ...options });
}

/**
 * Retourne les notifications groupées par placement
 * @returns {Object} { 'top-end': [...], ... }
 */
const notificationsByPlacement = computed(() => {
    return notifications.value.reduce((acc, n) => {
        if (!acc[n.placement]) acc[n.placement] = [];
        acc[n.placement].push(n);
        return acc;
    }, {});
});

export function useNotificationStore() {
    return {
        notifications,
        notificationsByPlacement,
        addNotification,
        removeNotification,
        success,
        error,
        info,
        warning,
    };
}
