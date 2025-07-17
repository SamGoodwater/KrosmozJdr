/**
 * labelManager.js — Gestionnaire de labels pour les composants d'input
 *
 * @description
 * Module spécialisé pour la gestion des labels dans les composants d'input.
 * Fournit des helpers pour valider, normaliser et traiter les configurations
 * de labels avec leurs positions multiples.
 *
 * @example
 * import { validateLabel, processLabelConfig } from '@/Utils/atomic-design/labelManager';
 * 
 * // Valider une configuration
 * const isValid = validateLabel({ top: 'Nom', inStart: 'M.' });
 * 
 * // Traiter une configuration
 * const config = processLabelConfig('Nom'); // { floating: 'Nom' }
 */

/**
 * Positions de labels valides pour les composants d'input
 */
export const validLabelPositions = ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd', 'floating'];

/**
 * Valide une configuration de label
 * @param {String|Object} label - Label à valider
 * @returns {Boolean} - True si valide
 */
export function validateLabel(label) {
    if (typeof label === 'string') return true;
    if (typeof label === 'object' && label !== null) {
        return Object.keys(label).every(key => validLabelPositions.includes(key));
    }
    return false;
}

/**
 * Normalise une configuration de label
 * @param {String|Object} label - Label à normaliser
 * @param {String} defaultPosition - Position par défaut pour les strings ('floating', 'top', etc.)
 * @returns {Object} - Configuration normalisée
 */
export function normalizeLabel(label, defaultPosition = 'top') {
    if (typeof label === 'string') {
        return { [defaultPosition]: label };
    }
    return label || {};
}

/**
 * Valide et nettoie une configuration de label (supprime les combinaisons interdites)
 * @param {String|Object} label - Label à traiter
 * @param {String} defaultPosition - Position par défaut pour les strings
 * @returns {Object} - Configuration validée et nettoyée
 */
export function processLabelConfig(label, defaultPosition = 'top') {
    const config = normalizeLabel(label, defaultPosition);
    
    // Si floating est défini, on supprime inStart et inEnd (règle d'exclusion)
    if (config.floating) {
        delete config.inStart;
        delete config.inEnd;
    }
    
    return config;
}

/**
 * Vérifie si une position de label est valide
 * @param {String} position - Position à vérifier
 * @returns {Boolean} - True si valide
 */
export function isValidLabelPosition(position) {
    return validLabelPositions.includes(position);
}

/**
 * Extrait les positions utilisées dans une configuration
 * @param {String|Object} label - Configuration de label
 * @returns {Array} - Liste des positions utilisées
 */
export function getUsedLabelPositions(label) {
    const config = normalizeLabel(label);
    return Object.keys(config);
}

/**
 * Vérifie si une configuration contient des positions interdites
 * @param {String|Object} label - Configuration de label
 * @returns {Boolean} - True si contient des positions interdites
 */
export function hasConflictingLabelPositions(label) {
    const config = normalizeLabel(label);
    return config.floating && (config.inStart || config.inEnd);
} 