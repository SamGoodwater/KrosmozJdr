/**
 * Service de gestion des images côté frontend
 *
 * @description
 * Service pour gérer les images dans l'application.
 * - Récupération des URLs d'images
 * - Génération des URLs de thumbnails
 * - Support des icônes FontAwesome
 * - Cache côté client
 * - URLs construites sans requête HEAD (évite 403 inutiles)
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

/** URL de l'image par défaut si fichier introuvable ou erreur (storage/app/public/images/no_found.svg). */
export const FALLBACK_IMAGE_URL = '/storage/images/no_found.svg';

export class ImageService {
    static #cache = new Map();
    static #CACHE_TTL = 3600000; // 1 heure en millisecondes

    /**
     * Dossier réel sous `storage/app/public/images/icons/` : **caracteristics** (sans « h »).
     * Normalise les variantes `characteristics` (anglais) ou anciennes typos pour l’URL `/storage/images/...`.
     *
     * @param {string} path
     * @returns {string}
     */
    static normalizeIconsSubpath(path) {
        if (!path || typeof path !== "string") return path;
        let p = path.replace(/\\/g, "/");
        p = p.replace(/^icons\/characteristics\//i, "icons/caracteristics/");
        p = p.replace(/^icons\/caracteristiques\//i, "icons/caracteristics/");
        return p;
    }

    /**
     * Récupère l'URL d'une image avec cache
     *
     * @param {string} path - Chemin de l'image
     * @returns {Promise<string>} URL de l'image
     */
    static async getImageUrl(path) {
        if (!path) return "";

        const raw = String(path).trim();
        if (raw.startsWith("http://") || raw.startsWith("https://")) {
            const absKey = `image_${raw}`;
            const hit = this.#cache.get(absKey);
            if (hit && Date.now() - hit.timestamp < this.#CACHE_TTL) {
                return hit.url;
            }
            this.#cache.set(absKey, { url: raw, timestamp: Date.now() });
            return raw;
        }
        if (raw.startsWith("/")) {
            const absKey = `image_${raw}`;
            const hit = this.#cache.get(absKey);
            if (hit && Date.now() - hit.timestamp < this.#CACHE_TTL) {
                return hit.url;
            }
            this.#cache.set(absKey, { url: raw, timestamp: Date.now() });
            return raw;
        }

        path = this.normalizeIconsSubpath(raw);

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

        const url = `/storage/images/${path}`;

        // Pas de HEAD : les colonnes `image` exposent souvent une URL absolue Spatie ; sinon un
        // chemin relatif valide sous `public/storage/images/`. Un HEAD peut répondre 403 alors
        // que le GET fonctionne — ce qui vidait l’URL dans ImageViewer (`source`). Le navigateur
        // charge l’URL ; l’Atom Image gère @error (fallback).
        this.#cache.set(cacheKey, { url, timestamp: Date.now() });
        return url;
    }

    /**
     * @param {string} raw
     * @returns {string}
     */
    static #diskRelativeImagePath(raw) {
        let p = String(raw).trim().replace(/\\/g, "/");
        if (p.startsWith("/storage/images/")) {
            p = p.slice("/storage/images/".length);
        } else if (p.startsWith("storage/images/")) {
            p = p.slice("storage/images/".length);
        }
        return p.replace(/^\/+/, "");
    }

    /**
     * Génère l'URL de la route `GET /media/thumbnails/{path}` (miniatures dynamiques) avec cache.
     * Paramètres alignés sur `ImageController::thumbnail` : `w`, `h`, `fit`, `q`, `fm`.
     *
     * @param {string} path - Chemin relatif au disque `public` ou URL absolue ; préfixe `/storage/images/` retiré si présent.
     * @param {Object} options - Options de transformation
     * @param {number} [options.width] - Largeur (`w`, défaut 300)
     * @param {number} [options.height] - Hauteur (`h`, défaut 300)
     * @param {string} [options.fit] - `cover` ou `contain`
     * @param {number} [options.quality] - Qualité (`q`)
     * @param {string} [options.fm] - Format de sortie (`fm`, défaut `webp`), alias `format`
     * @returns {Promise<string>} URL du thumbnail
     */
    static async getThumbnailUrl(path, options = {}) {
        if (!path) return "";

        const raw = String(path).trim();
        if (raw.startsWith("http://") || raw.startsWith("https://")) {
            return raw;
        }

        let rel = this.#diskRelativeImagePath(raw);
        rel = this.normalizeIconsSubpath(rel);

        // Vérifier le cache
        const cacheKey = `thumbnail_${rel}_${JSON.stringify(options)}`;
        const cached = this.#cache.get(cacheKey);
        if (cached && Date.now() - cached.timestamp < this.#CACHE_TTL) {
            return cached.url;
        }

        // Si c'est une icône FontAwesome, retourner le chemin tel quel
        if (rel.startsWith("fa-")) {
            return rel;
        }

        // Route Laravel `media.thumbnail` : paramètres alignés sur ImageController
        const queryParams = new URLSearchParams();
        const w = options.width ?? 300;
        const h = options.height ?? 300;
        queryParams.set("w", String(w));
        queryParams.set("h", String(h));
        if (options.fit) {
            queryParams.set("fit", options.fit);
        }
        if (options.quality != null && options.quality !== "") {
            queryParams.set("q", String(options.quality));
        }
        const fm = options.fm ?? options.format ?? "webp";
        queryParams.set("fm", fm);

        const encodedPath = rel
            .split("/")
            .filter((segment) => segment.length > 0)
            .map((segment) => encodeURIComponent(segment))
            .join("/");

        const queryString = queryParams.toString();
        const url = `/media/thumbnails/${encodedPath}${queryString ? `?${queryString}` : ""}`;

        this.#cache.set(cacheKey, {
            url,
            timestamp: Date.now(),
        });
        return url;
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
