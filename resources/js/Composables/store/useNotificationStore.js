import { ref, computed } from "vue";

/**
 * useNotificationStore — Composable global pour les notifications toast avancées (atomic design)
 *
 * @description
 * Gère la liste des notifications toast avec animations avancées, cycle full/contracted, 
 * barre de progression, et placements multiples.
 * Compatible avec NotificationToast et NotificationContainer.
 *
 * @typedef {Object} Notification
 * @property {number} id - Identifiant unique
 * @property {string} message - Message à afficher
 * @property {string} [type] - Type de notification ('success', 'error', 'info', 'warning', 'primary', 'secondary')
 * @property {string} [placement] - Placement ('top-left', 'top-right', 'bottom-left', 'bottom-right')
 * @property {number} [duration] - Durée d'affichage (ms, 12000 = 12s par défaut, 0 = permanent)
 * @property {string} [icon] - Icône personnalisée (optionnel)
 * @property {Function} [onClick] - Action au click (optionnel)
 * @property {Array|undefined} [actions] - Actions custom (boutons, etc.)
 * @property {Object} [extra] - Données additionnelles
 * @property {number} [createdAt] - Timestamp de création
 * @property {number} [fullDisplayTime] - Temps en mode full (40% de duration)
 * @property {number} [contractedDisplayTime] - Temps en mode contracted (60% de duration)
 *
 * @note Si duration = 0, la notification reste affichée indéfiniment jusqu'à fermeture manuelle
 * @note Les notifications permanentes restent toujours en mode 'full' et ont une barre de progression à 100%
 */

const MAX_NOTIFICATIONS = 20;
const DEFAULT_DURATION = 8000; // 8 secondes
const FULL_DISPLAY_RATIO = 0.4; // 40% du temps en mode full
const CONTRACTED_DISPLAY_RATIO = 0.6; // 60% du temps en mode contracted

const notifications = ref([]);

function addNotification({
    message,
    type = "",
    placement = "top-right",
    duration = DEFAULT_DURATION,
    icon = null,
    onClick = null,
    actions = undefined,
    ...extra
}) {

    if (!message) return;
    
    const id = Date.now() + Math.floor(Math.random() * 10000);
    const createdAt = Date.now();
    const fullDisplayTime = duration * FULL_DISPLAY_RATIO;
    const contractedDisplayTime = duration * CONTRACTED_DISPLAY_RATIO;
    
    const notification = {
        id,
        message,
        type,
        placement,
        duration,
        icon,
        onClick,
        actions,
        createdAt,
        fullDisplayTime,
        contractedDisplayTime,
        ...extra,
    };
    
    // Ajouter la notification au début (plus récente en haut)
    notifications.value.unshift(notification);
    
    // Limiter le nombre de notifications par placement
    limitNotificationsByPlacement(placement);
    
    // Auto-dismiss après la durée spécifiée (sauf si duration = 0)
    if (duration > 0) {
        setTimeout(() => removeNotification(id), duration);
    }
    
    return id;
}

function limitNotificationsByPlacement(placement) {
    const notificationsForPlacement = notifications.value.filter(n => n.placement === placement);
    
    if (notificationsForPlacement.length > MAX_NOTIFICATIONS) {
        // Supprimer les notifications les plus anciennes pour ce placement
        const toRemove = notificationsForPlacement.slice(MAX_NOTIFICATIONS);
        toRemove.forEach(notification => {
            removeNotification(notification.id);
        });
    }
}

function removeNotification(id) {
    notifications.value = notifications.value.filter((n) => n.id !== id);
}

// Méthodes utilitaires pour les types courants
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

function primary(message, options = {}) {
    return addNotification({ message, type: "primary", ...options });
}

function secondary(message, options = {}) {
    return addNotification({ message, type: "secondary", ...options });
}

// Méthodes pour les notifications permanentes (duration = 0)
function permanentSuccess(message, options = {}) {
    return addNotification({ message, type: "success", duration: 0, ...options });
}

function permanentError(message, options = {}) {
    return addNotification({ message, type: "error", duration: 0, ...options });
}

function permanentInfo(message, options = {}) {
    return addNotification({ message, type: "info", duration: 0, ...options });
}

function permanentWarning(message, options = {}) {
    return addNotification({ message, type: "warning", duration: 0, ...options });
}

function permanentPrimary(message, options = {}) {
    return addNotification({ message, type: "primary", duration: 0, ...options });
}

function permanentSecondary(message, options = {}) {
    return addNotification({ message, type: "secondary", duration: 0, ...options });
}

/**
 * Retourne les notifications groupées par placement
 * @returns {Object} { 'top-left': [...], 'top-right': [...], 'bottom-left': [...], 'bottom-right': [...] }
 */
const notificationsByPlacement = computed(() => {
    return notifications.value.reduce((acc, n) => {
        if (!acc[n.placement]) acc[n.placement] = [];
        acc[n.placement].push(n);
        return acc;
    }, {});
});

/**
 * Calcule le temps restant pour une notification
 * @param {Object} notification - L'objet notification
 * @returns {number} Temps restant en millisecondes (0 si duration = 0)
 */
function getRemainingTime(notification) {
    // Si duration = 0, la notification est permanente
    if (notification.duration === 0) {
        return 0;
    }
    
    const elapsed = Date.now() - notification.createdAt;
    return Math.max(0, notification.duration - elapsed);
}

/**
 * Calcule le pourcentage de progression pour la barre de progression
 * @param {Object} notification - L'objet notification
 * @returns {number} Pourcentage entre 0 et 100 (100 si duration = 0)
 */
function getProgressPercentage(notification) {
    // Si duration = 0, la barre de progression reste à 100%
    if (notification.duration === 0) {
        return 100;
    }
    
    const remaining = getRemainingTime(notification);
    return Math.max(0, Math.min(100, (remaining / notification.duration) * 100));
}

/**
 * Détermine si une notification est en mode full ou contracted
 * @param {Object} notification - L'objet notification
 * @returns {string} 'full' ou 'contracted'
 */
function getNotificationState(notification) {
    // Si duration = 0, la notification reste toujours en mode full
    if (notification.duration === 0) {
        return 'full';
    }
    
    const elapsed = Date.now() - notification.createdAt;
    return elapsed < notification.fullDisplayTime ? 'full' : 'contracted';
}

export function useNotificationStore() {
    return {
        notifications,
        notificationsByPlacement,
        addNotification,
        removeNotification,
        getRemainingTime,
        getProgressPercentage,
        getNotificationState,
        success,
        error,
        info,
        warning,
        primary,
        secondary,
        permanentSuccess,
        permanentError,
        permanentInfo,
        permanentWarning,
        permanentPrimary,
        permanentSecondary,
        // Constantes exportées
        MAX_NOTIFICATIONS,
        DEFAULT_DURATION,
        FULL_DISPLAY_RATIO,
        CONTRACTED_DISPLAY_RATIO,
    };
}
