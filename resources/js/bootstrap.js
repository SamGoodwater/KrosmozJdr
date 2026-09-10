import axios from "axios";
import "https://kit.fontawesome.com/a416056d6c.js";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Gestion des expirations de session sur les appels axios métier.
 *
 * @description
 * Inertia 3 n’utilise plus axios pour les visites (`router` / `useForm`) : un 419
 * Inertia est géré côté Laravel (`Inertia::location` dans `bootstrap/app.php`).
 * Cet interceptor ne couvre que les XHR axios (effets, tableaux API, etc.).
 * 419 → reload pour un CSRF frais ; 401 → redirection login.
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
