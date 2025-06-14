/**
 * Service de gestion des images côté frontend
 *
 * @description
 * Service pour gérer les images dans l'application.
 * - Récupération des URLs d'images
 * - Génération des URLs de thumbnails
 * - Support des icônes FontAwesome
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
    /**
     * Récupère l'URL d'une image
     *
     * @param {string} path - Chemin de l'image
     * @returns {Promise<string>} URL de l'image
     */
    static async getImageUrl(path) {
        if (!path) return "";

        // Si c'est une icône FontAwesome, retourner le chemin tel quel
        if (path.startsWith("fa-")) {
            return path;
        }

        // Sinon, construire l'URL de l'image
        return `/storage/images/${path}`;
    }

    /**
     * Génère l'URL d'un thumbnail
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
        return `/storage/thumbnails/${path}${queryString ? `?${queryString}` : ""}`;
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
