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
const DEFAULT_MINIMAL_DISPLAY_MODE = 'hover';
const STORAGE_MINIMAL_MODE_PREFIX = 'entity_view_minimal_display_mode_';

/**
 * @param {string} entityType - Le type d'entité (optionnel, pour des préférences par type)
 * @returns {Object} { viewFormat, setViewFormat, availableFormats, minimalDisplayMode, setMinimalDisplayMode, availableMinimalDisplayModes }
 */
export function useEntityViewFormat(entityType = 'default') {
    const storageKey = `${STORAGE_PREFIX}${entityType}`;
    const minimalModeStorageKey = `${STORAGE_MINIMAL_MODE_PREFIX}${entityType}`;
    
    // Charger depuis localStorage ou utiliser la valeur par défaut
    const storedFormat = localStorage.getItem(storageKey);
    const viewFormat = ref(storedFormat || DEFAULT_FORMAT);

    const storedMinimalMode = localStorage.getItem(minimalModeStorageKey);
    const minimalDisplayMode = ref(storedMinimalMode || DEFAULT_MINIMAL_DISPLAY_MODE);
    
    const availableFormats = [
        { value: 'large', label: 'Complet', icon: 'fa-solid fa-window-maximize' },
        { value: 'compact', label: 'Compact', icon: 'fa-solid fa-compress' },
        { value: 'minimal', label: 'Minimal', icon: 'fa-solid fa-minus' },
        { value: 'text', label: 'Texte', icon: 'fa-solid fa-align-left' }
    ];

    const availableMinimalDisplayModes = [
        { value: 'hover', label: 'Compact → étendu au survol' },
        { value: 'extended', label: 'Toujours étendu' },
        { value: 'compact', label: 'Toujours compact' },
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

    /**
     * Définit le mode d’affichage de la vue Minimal
     * @param {'compact'|'hover'|'extended'} mode
     */
    const setMinimalDisplayMode = (mode) => {
        if (availableMinimalDisplayModes.some(m => m.value === mode)) {
            minimalDisplayMode.value = mode;
            localStorage.setItem(minimalModeStorageKey, mode);
        }
    };
    
    // Sauvegarder automatiquement les changements
    watch(viewFormat, (newFormat) => {
        localStorage.setItem(storageKey, newFormat);
    });

    watch(minimalDisplayMode, (newMode) => {
        localStorage.setItem(minimalModeStorageKey, newMode);
    });
    
    return {
        viewFormat,
        setViewFormat,
        availableFormats,
        minimalDisplayMode,
        setMinimalDisplayMode,
        availableMinimalDisplayModes,
    };
}

export default useEntityViewFormat;

