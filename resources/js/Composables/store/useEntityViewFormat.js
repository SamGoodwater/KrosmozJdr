/**
 * useEntityViewFormat Composable
 *
 * @description
 * Gère les préférences de format d'affichage des entités (full, minimal, text) en modal.
 * Sauvegarde les préférences dans localStorage.
 *
 * @example
 * const { viewFormat, setViewFormat } = useEntityViewFormat('item');
 * viewFormat.value = 'minimal';
 */
import { ref, watch } from 'vue';

const DEFAULT_FORMAT = 'full';
const STORAGE_PREFIX = 'entity_view_format_';
const DEFAULT_MINIMAL_DISPLAY_MODE = 'hover';
const STORAGE_MINIMAL_MODE_PREFIX = 'entity_view_minimal_display_mode_';
const GLOBAL_ENTITY_KEY = 'global';

/** @param {string|null|undefined} stored */
function normalizeStoredViewFormat(stored) {
    if (!stored) return null;
    if (stored === 'large' || stored === 'compact') return 'full';
    if (stored === 'full' || stored === 'minimal' || stored === 'text') return stored;
    return null;
}

/**
 * @param {string} entityType - Le type d'entité (optionnel, pour des préférences par type)
 * @returns {Object} { viewFormat, setViewFormat, availableFormats, minimalDisplayMode, setMinimalDisplayMode, availableMinimalDisplayModes }
 */
export function useEntityViewFormat(entityType = 'default') {
    const storageKey = `${STORAGE_PREFIX}${entityType}`;
    const minimalModeStorageKey = `${STORAGE_MINIMAL_MODE_PREFIX}${entityType}`;
    const globalStorageKey = `${STORAGE_PREFIX}${GLOBAL_ENTITY_KEY}`;
    const globalMinimalModeStorageKey = `${STORAGE_MINIMAL_MODE_PREFIX}${GLOBAL_ENTITY_KEY}`;

    const storedFormat = normalizeStoredViewFormat(
        localStorage.getItem(storageKey) || localStorage.getItem(globalStorageKey),
    );
    const viewFormat = ref(storedFormat || DEFAULT_FORMAT);

    const storedMinimalMode = localStorage.getItem(minimalModeStorageKey) || localStorage.getItem(globalMinimalModeStorageKey);
    const minimalDisplayMode = ref(storedMinimalMode || DEFAULT_MINIMAL_DISPLAY_MODE);

    const availableFormats = [
        { value: 'full', label: 'Complet', icon: 'fa-solid fa-window-maximize' },
        { value: 'minimal', label: 'Minimal', icon: 'fa-solid fa-minus' },
        { value: 'text', label: 'Texte', icon: 'fa-solid fa-align-left' },
    ];

    const availableMinimalDisplayModes = [
        { value: 'hover', label: 'Compact → étendu au survol' },
        { value: 'extended', label: 'Toujours étendu' },
        { value: 'compact', label: 'Toujours compact' },
    ];

    /**
     * @param {string} format - 'full' | 'minimal' | 'text'
     */
    const setViewFormat = (format) => {
        if (availableFormats.some(f => f.value === format)) {
            viewFormat.value = format;
            localStorage.setItem(storageKey, format);
            localStorage.setItem(globalStorageKey, format);
        }
    };

    /**
     * @param {'compact'|'hover'|'extended'} mode
     */
    const setMinimalDisplayMode = (mode) => {
        if (availableMinimalDisplayModes.some(m => m.value === mode)) {
            minimalDisplayMode.value = mode;
            localStorage.setItem(minimalModeStorageKey, mode);
            localStorage.setItem(globalMinimalModeStorageKey, mode);
        }
    };

    watch(viewFormat, (newFormat) => {
        localStorage.setItem(storageKey, newFormat);
        localStorage.setItem(globalStorageKey, newFormat);
    });

    watch(minimalDisplayMode, (newMode) => {
        localStorage.setItem(minimalModeStorageKey, newMode);
        localStorage.setItem(globalMinimalModeStorageKey, newMode);
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
