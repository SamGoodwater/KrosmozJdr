/**
 * useEntityTableSettings Composable
 * 
 * @description
 * Gère les préférences de colonnes pour les tableaux d'entités.
 * Permet de masquer/afficher des colonnes et de persister les préférences dans localStorage.
 * 
 * @example
 * const { visibleColumns, toggleColumn, resetColumns } = useEntityTableSettings('items');
 */
import { ref, computed, watch } from 'vue';

const STORAGE_PREFIX = 'entity_table_settings_';

/**
 * Charge les préférences depuis localStorage
 * @param {string} entityType - Type d'entité (ex: 'items', 'spells')
 * @param {Array} defaultColumns - Colonnes par défaut
 * @returns {Object} Objet avec les colonnes visibles
 */
function loadSettings(entityType, defaultColumns) {
    try {
        const key = STORAGE_PREFIX + entityType;
        const saved = localStorage.getItem(key);
        if (saved) {
            const settings = JSON.parse(saved);
            // Vérifier que toutes les colonnes par défaut sont présentes
            const visibleColumns = {};
            defaultColumns.forEach(col => {
                visibleColumns[col.key] = settings.visibleColumns?.[col.key] ?? true;
            });
            return { visibleColumns };
        }
    } catch (error) {
        console.warn(`[useEntityTableSettings] Erreur lors du chargement des préférences pour ${entityType}:`, error);
    }
    
    // Par défaut, toutes les colonnes sont visibles
    const visibleColumns = {};
    defaultColumns.forEach(col => {
        visibleColumns[col.key] = true;
    });
    return { visibleColumns };
}

/**
 * Sauvegarde les préférences dans localStorage
 * @param {string} entityType - Type d'entité
 * @param {Object} settings - Paramètres à sauvegarder
 */
function saveSettings(entityType, settings) {
    try {
        const key = STORAGE_PREFIX + entityType;
        localStorage.setItem(key, JSON.stringify(settings));
    } catch (error) {
        console.warn(`[useEntityTableSettings] Erreur lors de la sauvegarde des préférences pour ${entityType}:`, error);
    }
}

/**
 * Composable pour gérer les paramètres de tableau d'entités
 * @param {string} entityType - Type d'entité (ex: 'items', 'spells')
 * @param {Array} columns - Configuration des colonnes
 * @returns {Object} Méthodes et états pour gérer les colonnes
 */
export function useEntityTableSettings(entityType, columns) {
    // Charger les préférences depuis localStorage
    const defaultSettings = loadSettings(entityType, columns);
    const visibleColumns = ref({ ...defaultSettings.visibleColumns });

    // Colonnes filtrées selon la visibilité
    const filteredColumns = computed(() => {
        return columns.filter(col => visibleColumns.value[col.key] !== false);
    });

    /**
     * Bascule la visibilité d'une colonne
     * @param {string} columnKey - Clé de la colonne
     */
    const toggleColumn = (columnKey) => {
        // Colonne principale toujours visible
        const mainColumn = columns.find((c) => c?.isMain);
        if (mainColumn?.key && columnKey === mainColumn.key) {
            visibleColumns.value[columnKey] = true;
            return;
        }
        visibleColumns.value[columnKey] = !visibleColumns.value[columnKey];
    };

    /**
     * Définit la visibilité d'une colonne
     * @param {string} columnKey - Clé de la colonne
     * @param {boolean} visible - Visibilité
     */
    const setColumnVisibility = (columnKey, visible) => {
        // Colonne principale toujours visible
        const mainColumn = columns.find((c) => c?.isMain);
        if (mainColumn?.key && columnKey === mainColumn.key) {
            visibleColumns.value[columnKey] = true;
            return;
        }
        visibleColumns.value[columnKey] = visible;
    };

    /**
     * Réinitialise toutes les colonnes à visible
     */
    const resetColumns = () => {
        columns.forEach(col => {
            visibleColumns.value[col.key] = true;
        });
    };

    /**
     * Vérifie si une colonne est visible
     * @param {string} columnKey - Clé de la colonne
     * @returns {boolean}
     */
    const isColumnVisible = (columnKey) => {
        return visibleColumns.value[columnKey] !== false;
    };

    // Sauvegarder automatiquement les changements
    watch(
        visibleColumns,
        (newValue) => {
            saveSettings(entityType, { visibleColumns: newValue });
        },
        { deep: true }
    );

    return {
        visibleColumns: computed(() => visibleColumns.value),
        filteredColumns,
        toggleColumn,
        setColumnVisibility,
        resetColumns,
        isColumnVisible
    };
}

export default useEntityTableSettings;

