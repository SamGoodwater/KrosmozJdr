import { MediaManager } from "@/Utils/MediaManager";

let isInitialized = false;

export default {
    install: async (app) => {
        if (!isInitialized) {
            try {
                console.info("Initialisation du MediaManager...");
                await MediaManager.refreshCache();
                await MediaManager.preload("image");
                isInitialized = true;
                console.info("MediaManager initialisé avec succès");
            } catch (error) {
                console.error(
                    "Erreur lors de l'initialisation du MediaManager:",
                    error,
                );
            }
        }

        // Rendre le MediaManager accessible globalement dans l'application
        app.config.globalProperties.$mediaManager = MediaManager;
    },
};
