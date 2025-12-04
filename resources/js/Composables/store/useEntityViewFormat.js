/**
 * useEntityViewFormat Composable
 * 
 * @description
 * Gère les préférences de format d'affichage des entités (large, compact, minimal, text)
 * Sauvegarde les préférences dans localStorage
 * 
 * @example
 * const { viewFormat, setViewFormat } = useEntityViewFormat('item');
 * viewFormat.value = 'compact';
 */
import { ref, watch } from 'vue';

const DEFAULT_FORMAT = 'large';
const STORAGE_PREFIX = 'entity_view_format_';

/**
 * @param {string} entityType - Le type d'entité (optionnel, pour des préférences par type)
 * @returns {Object} { viewFormat, setViewFormat, availableFormats }
 */
export function useEntityViewFormat(entityType = 'default') {
    const storageKey = `${STORAGE_PREFIX}${entityType}`;
    
    // Charger depuis localStorage ou utiliser la valeur par défaut
    const storedFormat = localStorage.getItem(storageKey);
    const viewFormat = ref(storedFormat || DEFAULT_FORMAT);
    
    const availableFormats = [
        { value: 'large', label: 'Complet', icon: 'fa-solid fa-window-maximize' },
        { value: 'compact', label: 'Compact', icon: 'fa-solid fa-compress' },
        { value: 'minimal', label: 'Minimal', icon: 'fa-solid fa-minus' },
        { value: 'text', label: 'Texte', icon: 'fa-solid fa-align-left' }
    ];
    
    /**
     * Définit le format d'affichage
     * @param {string} format - Le format ('large', 'compact', 'minimal', 'text')
     */
    const setViewFormat = (format) => {
        if (availableFormats.some(f => f.value === format)) {
            viewFormat.value = format;
            localStorage.setItem(storageKey, format);
        }
    };
    
    // Sauvegarder automatiquement les changements
    watch(viewFormat, (newFormat) => {
        localStorage.setItem(storageKey, newFormat);
    });
    
    return {
        viewFormat,
        setViewFormat,
        availableFormats
    };
}

export default useEntityViewFormat;

