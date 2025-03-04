import { ref, h, render } from "vue";
import Notification from "@/Pages/Atoms/feedback/Notification.vue";

// Créer un store global pour les notifications
const notifications = ref([]);

const getNotificationContainer = (placement) => {
    let containerId;

    if (placement.includes("top")) {
        if (placement.includes("start")) {
            containerId = "notifications-top-start";
        } else {
            containerId = "notifications-top-end";
        }
    } else {
        if (placement.includes("start")) {
            containerId = "notifications-bottom-start";
        } else {
            containerId = "notifications-bottom-end";
        }
    }

    return document.getElementById(containerId);
};

/**
 * Ajoute une nouvelle notification
 * @param {Object} options - Les options de la notification
 * @param {string} options.message - Le message à afficher
 * @param {string} [options.theme=""] - Le thème de la notification
 * @param {string} [options.placement="bottom-end"] - La position de la notification
 * @param {number} [options.delay=3000] - Le délai avant la fermeture automatique (0 pour désactiver la fermeture automatique)
 * @param {string} [options.route=""] - La route vers laquelle rediriger lors du clic
 * @returns {number} L'ID de la notification créée
 */
const addNotification = ({
    message,
    theme = "",
    placement = "top-end",
    delay = 3000,
    route = "",
}) => {
    if (!message) {
        return;
    }

    const id = Date.now();
    const notification = {
        id,
        message,
        theme,
        placement,
        delay,
        route,
    };

    try {
        // Récupérer le conteneur approprié
        const container = getNotificationContainer(placement);
        if (!container) {
            console.error(
                "Conteneur de notifications non trouvé pour le placement:",
                placement,
            );
            return;
        }

        // Créer et rendre le composant de notification
        const notificationVNode = h(Notification, {
            ...notification,
            onClose: () => removeNotification(id),
        });

        const notificationElement = document.createElement("div");
        notificationElement.setAttribute("data-notification-id", id.toString());

        // Ajouter la notification au début ou à la fin selon le placement
        if (placement.includes("top")) {
            container.appendChild(notificationElement);
        } else {
            container.insertBefore(notificationElement, container.firstChild);
        }

        render(notificationVNode, notificationElement);

        if (delay > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, delay);
        }

        return id;
    } catch (error) {
        console.error("Erreur lors de l'ajout de la notification:", error);
    }
};

/**
 * Supprime une notification par son ID
 * @param {number} id - L'ID de la notification à supprimer
 */
const removeNotification = (id) => {
    try {
        const element = document.querySelector(
            `[data-notification-id="${id}"]`,
        );
        if (element) {
            render(null, element); // Démonte le composant Vue
            element.remove();
        }
    } catch (error) {
        console.error(
            "Erreur lors de la suppression de la notification:",
            error,
        );
    }
};

// Export des fonctions et du store
export const useNotifications = () => {
    return {
        notifications,
        addNotification,
        removeNotification,
    };
};

/**
 * Crée une notification basique
 * @param {string} message - Le message à afficher
 * @param {Object} options - Les options supplémentaires (delay, placement, route, etc.)
 */
export const notify = (message, options = {}) => {
    return addNotification({ message, ...options });
};

/**
 * Crée une notification de succès
 * @param {string} message - Le message à afficher
 * @param {Object} options - Les options supplémentaires (delay, placement, route, etc.)
 */
export const success = (message, options = {}) => {
    return notify(message, {
        theme: "success",
        ...options,
    });
};

/**
 * Crée une notification d'erreur
 * @param {string} message - Le message à afficher
 * @param {Object} options - Les options supplémentaires (delay, placement, route, etc.)
 */
export const error = (message, options = {}) => {
    return notify(message, {
        theme: "error",
        ...options,
    });
};

/**
 * Crée une notification d'information
 * @param {string} message - Le message à afficher
 * @param {Object} options - Les options supplémentaires (delay, placement, route, etc.)
 */
export const info = (message, options = {}) => {
    return notify(message, {
        theme: "info",
        ...options,
    });
};

/**
 * Crée une notification d'avertissement
 * @param {string} message - Le message à afficher
 * @param {Object} options - Les options supplémentaires (delay, placement, route, etc.)
 */
export const warning = (message, options = {}) => {
    return notify(message, {
        theme: "warning",
        ...options,
    });
};
