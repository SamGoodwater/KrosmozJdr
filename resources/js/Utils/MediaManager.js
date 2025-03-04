export class MediaManager {
    static cache = new Map();
    static loadPromises = new Map();

    /**
     * Obtient le CSRF token
     * @returns {string}
     */
    static getCsrfToken() {
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        return tokenElement ? tokenElement.content : "";
    }

    /**
     * Charge les fichiers médias d'un type spécifique
     * @param {string} type - Le type de média (image, video, audio, document)
     * @returns {Promise<Object>}
     */
    static async load(type = "image") {
        if (this.loadPromises.has(type)) {
            return this.loadPromises.get(type);
        }

        const promise = new Promise(async (resolve, reject) => {
            try {
                const response = await fetch(`/api/media/${type}`);
                if (!response.ok) {
                    throw new Error(
                        `Erreur lors du chargement des médias de type ${type}`,
                    );
                }
                const data = await response.json();
                this.cache.set(type, data);
                resolve(data);
            } catch (error) {
                console.error("MediaManager - Erreur de chargement:", error);
                reject(error);
            } finally {
                this.loadPromises.delete(type);
            }
        });

        this.loadPromises.set(type, promise);
        return promise;
    }

    /**
     * Récupère un fichier média
     * @param {string} path - Le chemin complet du fichier (sans extension)
     * @param {string} type - Le type de média
     * @returns {Promise<string>} - Le chemin du fichier
     */
    static async get(path, type = "image") {
        try {
            // S'assurer que nous avons la liste des images
            if (!this.cache.has(type)) {
                await this.load(type);
            }

            const cachedFiles = this.cache.get(type);
            if (!cachedFiles) {
                throw new Error(`Aucun fichier de type ${type} dans le cache`);
            }

            // Séparer le chemin en segments
            const segments = path.split("/");
            const fileName = segments.pop(); // Dernier segment = nom du fichier
            const directory = segments.join("/"); // Reste = chemin du dossier

            // Construire le chemin de recherche
            const searchPath = directory ? `images/${directory}` : "images";

            // Vérifier si l'image existe dans la liste
            if (cachedFiles[searchPath]?.[fileName]) {
                return cachedFiles[searchPath][fileName];
            }

            // Si l'image n'existe pas, utiliser no_found depuis la racine images
            if (cachedFiles["images"]?.["no_found"]) {
                return cachedFiles["images"]["no_found"];
            }

            console.error(
                "Image par défaut no_found non trouvée dans le cache",
            );
            return "";
        } catch (error) {
            console.error("MediaManager - Erreur:", error);
            return "";
        }
    }

    /**
     * Rafraîchit le cache des médias
     */
    static async refreshCache() {
        try {
            const token = this.getCsrfToken();
            if (!token) {
                console.warn(
                    "MediaManager - CSRF token non trouvé, impossible de rafraîchir le cache",
                );
                return;
            }

            const response = await fetch("/api/media/refresh-cache", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error("Erreur lors du rafraîchissement du cache");
            }

            // Vide le cache local
            this.cache.clear();
            this.loadPromises.clear();

            // Recharge les données
            await this.load("image");
        } catch (error) {
            console.error("MediaManager - Erreur de rafraîchissement:", error);
        }
    }

    /**
     * Précharge les médias d'un type spécifique
     * @param {string} type - Le type de média à précharger
     */
    static preload(type = "image") {
        this.load(type).catch(console.error);
    }
}
