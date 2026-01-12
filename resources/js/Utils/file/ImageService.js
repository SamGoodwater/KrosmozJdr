/**
 * Service de gestion des images côté frontend
 *
 * @description
 * Service pour gérer les images dans l'application.
 * - Récupération des URLs d'images
 * - Génération des URLs de thumbnails
 * - Support des icônes FontAwesome
 * - Cache côté client
 * - Gestion des erreurs avec retry
 *
 * @example
 * // Récupérer l'URL d'une image
 * const imageUrl = await ImageService.getImageUrl('images/photo.jpg');
 *
 * // Générer l'URL d'un thumbnail
 * const thumbnailUrl = await ImageService.getThumbnailUrl('images/photo.jpg', {
 *     width: 300,
 *     height: 300,
 *     fit: 'cover',
 *     quality: 80
 * });
 */
export class ImageService {
    static #cache = new Map();
    static #CACHE_TTL = 3600000; // 1 heure en millisecondes
    static #MAX_RETRIES = 3;
    static #RETRY_DELAY = 1000; // 1 seconde

    /**
     * Récupère l'URL d'une image avec cache
     *
     * @param {string} path - Chemin de l'image
     * @returns {Promise<string>} URL de l'image
     */
    static async getImageUrl(path) {
        if (!path) return "";

        // Vérifier le cache
        const cacheKey = `image_${path}`;
        const cached = this.#cache.get(cacheKey);
        if (cached && Date.now() - cached.timestamp < this.#CACHE_TTL) {
            return cached.url;
        }

        // Si c'est une icône FontAwesome, retourner le chemin tel quel
        if (path.startsWith("fa-")) {
            return path;
        }

        // Sinon, construire l'URL de l'image avec retry
        let retries = 0;
        while (retries < this.#MAX_RETRIES) {
            try {
                const url = `/storage/images/${path}`;
                // Vérifier si l'image existe
                const response = await fetch(url, { method: 'HEAD' });
                if (response.ok) {
                    // Mettre en cache
                    this.#cache.set(cacheKey, {
                        url,
                        timestamp: Date.now()
                    });
                    return url;
                }
                // Image non trouvée : retourner une chaîne vide sans lancer d'erreur
                // (c'est un cas normal, pas une erreur)
                if (response.status === 404) {
                    return "";
                }
                // Pour les autres erreurs HTTP, retry
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            } catch (error) {
                retries++;
                // Si c'est une erreur réseau ou autre erreur que 404, retry
                if (retries < this.#MAX_RETRIES) {
                    await new Promise(resolve => setTimeout(resolve, this.#RETRY_DELAY * retries));
                    continue;
                }
                // Après tous les retries, si c'est toujours une erreur réseau, logger
                if (error.message && !error.message.includes('Image not found')) {
                    console.error('ImageService - Erreur de chargement:', error);
                }
                return "";
            }
        }
        return "";
    }

    /**
     * Génère l'URL d'un thumbnail avec cache
     *
     * @param {string} path - Chemin de l'image source
     * @param {Object} options - Options de transformation
     * @param {number} options.width - Largeur du thumbnail
     * @param {number} options.height - Hauteur du thumbnail
     * @param {string} options.fit - Mode de redimensionnement (cover, contain, fill, none, scale-down)
     * @param {number} options.quality - Qualité de l'image (1-100)
     * @returns {Promise<string>} URL du thumbnail
     */
    static async getThumbnailUrl(path, options = {}) {
        if (!path) return "";

        // Vérifier le cache
        const cacheKey = `thumbnail_${path}_${JSON.stringify(options)}`;
        const cached = this.#cache.get(cacheKey);
        if (cached && Date.now() - cached.timestamp < this.#CACHE_TTL) {
            return cached.url;
        }

        // Si c'est une icône FontAwesome, retourner le chemin tel quel
        if (path.startsWith("fa-")) {
            return path;
        }

        // Construire l'URL du thumbnail avec les options
        const queryParams = new URLSearchParams();

        if (options.width) queryParams.append("width", options.width);
        if (options.height) queryParams.append("height", options.height);
        if (options.fit) queryParams.append("fit", options.fit);
        if (options.quality) queryParams.append("quality", options.quality);

        const queryString = queryParams.toString();
        const url = `/storage/thumbnails/${path}${queryString ? `?${queryString}` : ""}`;

        // Vérifier si le thumbnail existe avec retry
        let retries = 0;
        while (retries < this.#MAX_RETRIES) {
            try {
                const response = await fetch(url, { method: 'HEAD' });
                if (response.ok) {
                    // Mettre en cache
                    this.#cache.set(cacheKey, {
                        url,
                        timestamp: Date.now()
                    });
                    return url;
                }
                // Thumbnail non trouvé ou accès refusé : retourner une chaîne vide sans lancer d'erreur
                if (response.status === 404 || response.status === 403) {
                    return "";
                }
                // Pour les autres erreurs HTTP, retry
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            } catch (error) {
                retries++;
                // Si c'est une erreur réseau ou autre erreur que 404/403, retry
                if (retries < this.#MAX_RETRIES) {
                    await new Promise(resolve => setTimeout(resolve, this.#RETRY_DELAY * retries));
                    continue;
                }
                // Après tous les retries, si c'est toujours une erreur réseau, logger
                // (mais pas pour 404/403 qui sont des cas normaux)
                if (error.message && !error.message.includes('404') && !error.message.includes('403')) {
                    console.error('ImageService - Erreur de chargement du thumbnail:', error);
                }
                return "";
            }
        }
        return "";
    }

    /**
     * Vérifie si un chemin correspond à une icône FontAwesome
     *
     * @param {string} path - Chemin à vérifier
     * @returns {boolean} True si c'est une icône FontAwesome
     */
    static isFontAwesome(path) {
        return path.startsWith("fa-");
    }

    /**
     * Extrait le pack FontAwesome d'un chemin
     *
     * @param {string} path - Chemin de l'icône
     * @returns {string} Pack FontAwesome (solid, regular, brands, duotone)
     */
    static getFontAwesomePack(path) {
        if (path.startsWith("fa-solid")) return "solid";
        if (path.startsWith("fa-regular")) return "regular";
        if (path.startsWith("fa-brands")) return "brands";
        if (path.startsWith("fa-duotone")) return "duotone";
        return "solid";
    }
}
