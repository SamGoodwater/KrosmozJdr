import axios from "axios";
import "https://kit.fontawesome.com/a416056d6c.js";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Gestion robuste des expirations de session en SPA (Inertia).
 *
 * @description
 * Quand la session Laravel expire, le token CSRF stocké côté client devient invalide.
 * Les requêtes XHR peuvent alors répondre 419 (Page Expired). Sur une SPA, cela donne
 * l'impression d'une déconnexion "aléatoire" (souvent 1–2h, par défaut `SESSION_LIFETIME=120`).
 *
 * On force alors un rechargement complet pour récupérer un token CSRF valide et
 * laisser Laravel ré-authentifier l'utilisateur via le cookie "remember me" (si présent).
 *
 * @example
 * // Aucun usage direct : l'interceptor est global.
 */
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error?.response?.status;

        // 419 = CSRF token mismatch / session expirée
        if (status === 419 && typeof window !== "undefined") {
            window.location.reload();
            return;
        }

        // 401 = non authentifié (souvent suite à expiration de session sur un appel axios)
        if (status === 401 && typeof window !== "undefined") {
            const currentPath = window.location?.pathname || "";
            if (currentPath !== "/login" && currentPath !== "/register") {
                const redirect = error?.response?.data?.redirect || "/login";
                window.location.href = redirect;
                return;
            }
        }

        return Promise.reject(error);
    }
);
