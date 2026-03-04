/**
 * useCopyToClipboard Composable
 * 
 * @description
 * Composable pour copier du texte dans le presse-papier et afficher une notification.
 * 
 * @example
 * const { copyToClipboard } = useCopyToClipboard();
 * await copyToClipboard('https://example.com', 'URL copiée !');
 */
import { useUxFeedback } from '@/Composables/utils/useUxFeedback';

/**
 * Copie du texte dans le presse-papier et affiche une notification
 * @param {string} text - Texte à copier
 * @param {string} [successMessage] - Message de succès (optionnel)
 * @returns {Promise<boolean>} True si la copie a réussi
 */
export function useCopyToClipboard() {
    const { notifySuccess, notifyError } = useUxFeedback();

    const copyToClipboard = async (text, successMessage = 'Copié dans le presse-papier') => {
        try {
            // Utiliser l'API Clipboard moderne si disponible
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(text);
            } else {
                // Fallback pour les navigateurs plus anciens
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Erreur lors de la copie:', err);
                    throw err;
                } finally {
                    document.body.removeChild(textArea);
                }
            }

            notifySuccess(successMessage);

            return true;
        } catch (error) {
            console.error('Erreur lors de la copie dans le presse-papier:', error);
            
            notifyError('Erreur lors de la copie dans le presse-papier');

            return false;
        }
    };

    return {
        copyToClipboard
    };
}

export default useCopyToClipboard;

