/**
 * useDownloadPdf Composable
 * 
 * @description
 * Composable pour télécharger des PDFs d'entités.
 * Gère le téléchargement pour une ou plusieurs entités.
 * 
 * @example
 * const { downloadPdf, isDownloading } = useDownloadPdf('item');
 * await downloadPdf(entityId); // Télécharge un PDF pour une entité
 * await downloadPdf([entityId1, entityId2]); // Télécharge un PDF pour plusieurs entités
 */
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

/**
 * @param {string} entityType - Le type d'entité (item, spell, monster, etc.)
 * @returns {Object} { downloadPdf, isDownloading }
 */
export function useDownloadPdf(entityType) {
    const isDownloading = ref(false);
    const { success, error } = useNotificationStore();

    /**
     * Télécharge un PDF pour une ou plusieurs entités
     * @param {number|number[]} entityIdOrIds - ID unique ou tableau d'IDs
     * @param {string} filename - Nom de fichier optionnel
     */
    const downloadPdf = async (entityIdOrIds, filename = null) => {
        if (isDownloading.value) {
            return;
        }

        isDownloading.value = true;

        try {
            const entityTypePlural = entityType === 'panoply' ? 'panoplies' : `${entityType}s`;
            const routeName = `entities.${entityTypePlural}.pdf`;

            // Si c'est un tableau, on passe les IDs en query string
            if (Array.isArray(entityIdOrIds)) {
                const ids = entityIdOrIds.map(id => {
                    // Gérer les instances de modèles et les objets bruts
                    return typeof id === 'object' && id !== null ? (id.id ?? id.id) : id;
                }).filter(id => id !== null && id !== undefined);

                if (ids.length === 0) {
                    throw new Error('Aucun ID valide fourni');
                }

                // Utiliser le premier ID pour la route et passer les autres en query string
                const baseUrl = route(routeName, { [entityType]: ids[0] });
                const separator = baseUrl.includes('?') ? '&' : '?';
                const idsParam = ids.map(id => `ids[]=${encodeURIComponent(id)}`).join('&');
                const url = `${baseUrl}${separator}${idsParam}`;

                // Ouvrir dans une nouvelle fenêtre pour déclencher le téléchargement
                window.open(url, '_blank');
            } else {
                // Une seule entité
                const entityId = typeof entityIdOrIds === 'object' && entityIdOrIds !== null
                    ? (entityIdOrIds.id ?? entityIdOrIds.id)
                    : entityIdOrIds;

                const url = route(routeName, { [entityType]: entityId });

                // Utiliser window.open pour déclencher le téléchargement
                window.open(url, '_blank');
            }

            success('Téléchargement du PDF en cours...', { duration: 3000, placement: 'top-right' });
        } catch (err) {
            console.error('Erreur lors du téléchargement du PDF:', err);
            error('Erreur lors du téléchargement du PDF', { duration: 5000, placement: 'top-right' });
        } finally {
            // Délai pour permettre au téléchargement de commencer
            setTimeout(() => {
                isDownloading.value = false;
            }, 1000);
        }
    };

    return {
        downloadPdf,
        isDownloading,
    };
}

export default useDownloadPdf;

